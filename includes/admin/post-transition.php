<?php
/**
 * Handler for POSTing a status update to Twitter.
 *
 * @package TenUp\Auto_Tweet\Core
 */

namespace TenUp\Auto_Tweet\Core\Post_Transition;

use TenUp\Auto_Tweet\Core\Publish_Tweet\Publish_Tweet;
use TenUp\Auto_Tweet\Core\Post_Meta as Meta;
use TenUp\Auto_Tweet\Utils as Utils;
use function TenUp\Auto_Tweet\Utils\delete_autotweet_meta;
use function TenUp\Auto_Tweet\Utils\update_autotweet_meta;

/**
 * Setup function.
 *
 * @return void
 */
function setup() {
	add_action( 'transition_post_status', __NAMESPACE__ . '\maybe_publish_tweet', 10, 3 );
}

/**
 * Publishes the tweet if the post has transititioned from unpublished to published.
 *
 * In WP 5, the main Twitter publish action must run later than the transition_post_status hook because, when saving
 * via REST, the post thumbnail and other metadata have not yet been saved.
 *
 * @see https://core.trac.wordpress.org/ticket/45114
 *
 * @since 1.0.0
 *
 * @param string  $new_status The new status.
 * @param string  $old_status The old status.
 * @param WP_Post $post       The current post.
 *
 * @return object
 */
function maybe_publish_tweet( $new_status, $old_status, $post ) {
	/**
	 * We're only interested in posts that are transitioning into publish.
	 */
	if ( 'publish' !== $new_status || 'publish' === $old_status ) {
		return;
	}

	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		add_action( sprintf( 'rest_after_insert_%s', $post->post_type ), __NAMESPACE__ . '\publish_tweet' );
	} else {
		publish_tweet( $post->ID );
	}
}

/**
 * Primary handler for the process of publishing to Twitter.
 *
 * @param int $post_id The current post ID.
 *
 * @return object
 */
function publish_tweet( $post_id ) {
	$post = get_post( $post_id );

	/**
	 * Don't bother enqueuing assets if the post type hasn't opted into auto-tweeting
	 */
	if ( ! Utils\opted_into_autotweet( $post->ID ) ) {
		return;
	}

	/**
	 * This should never happen since the nonce field wouldn't exist.
	 * But just in case one more check: check that the post doesn't
	 * have a twitter-status entry already.
	 */
	if ( Utils\already_published( $post->ID ) ) {
		return;
	}

	// Ensure we have a $post object.
	if ( ! $post ) {
		return;
	}

	/**
	 * One final check: was the "auto tweet" checkbox selected?
	 */
	if ( Utils\maybe_auto_tweet( $post->ID ) ) {
		$tweet = Utils\compose_tweet_body( $post );

		$publish          = new Publish_Tweet();
		$twitter_response = $publish->status_update( $tweet, $post );

		$response = validate_response( $twitter_response );
		if ( ! is_wp_error( $response ) ) {
			update_auto_tweet_meta( $post->ID, $response );

			/**
			 * Fires after the status update to Twitter is considered successful.
			 */
			do_action( 'tenup_auto_tweet_success' );

		} else {
			// something here about it failing so do not allow republishing just in case.
			update_auto_tweet_meta( $post->ID, $response );

			/**
			 * Fires if the response back from Twitter was an error.
			 */
			do_action( 'tenup_auto_tweet_failed' );
		}
	}
}

/**
 * Validate and build response message.
 *
 * @param object $response The api response to validate.
 *
 * @return mixed
 */
function validate_response( $response ) {

	// Update considered successful.
	if ( ! empty( $response->id ) ) {
		$validated_response = array(
			'id'         => $response->id,
			'created_at' => $response->created_at,
		);

	} else {
		$validated_response = new \WP_Error(
			'tenup_auto_tweet_failed',
			__( 'Something happened during Twitter update.', 'tenup_auto_tweet' ),
			$response->errors
		);
	}

	return $validated_response;
}

/**
 * Responsible for adding the validated response as post meta.
 *
 * @param int    $post_id The post id.
 * @param object $data    The tweet request data.
 */
function update_auto_tweet_meta( $post_id, $data ) {

	// No errors, Tweet considered successful.
	if ( ! is_wp_error( $data ) ) {
		$response = array(
			'status'     => 'published',
			'twitter_id' => (int) $data['id'],
			'created_at' => sanitize_text_field( $data['created_at'] ),
		);

		// Twitter sent back an error. Most likely a duplicate message.
	} elseif ( is_wp_error( $data ) ) {
		$error_message = $data->error_data['tenup_auto_tweet_failed'][0];
		$response      = array(
			'status'  => 'error',
			'message' => sanitize_text_field( 'Error: ' . $error_message->code . '. ' . $error_message->message ),
		);

		// The default fallback message.
	} else {
		$response = array(
			'status'  => 'unknown',
			'message' => __( 'This post was not published to Twitter.', 'tenup_auto_tweet' ),
		);
	}

	/**
	 * Allow for filtering the Twitter status post meta.
	 */
	$response = apply_filters( 'tenup_auto_tweet_post_status_meta', $response );

	/**
	 * Update the post meta entry that stores the response
	 * and remove the "Auto-tweet this post" value as a double-check.
	 */
	update_autotweet_meta( $post_id, Meta\TWITTER_STATUS_KEY, $response );
	delete_autotweet_meta( $post_id, Meta\ENABLE_AUTOTWEET_KEY );

	/**
	 * Fires after the response from Twitter has been written as meta to the post.
	 */
	do_action( 'tenup_auto_tweet_post_tweet_status_updated' );
}

/**
 * Fire up the module.
 *
 * @uses auto_tweet_setup
 */
add_action( 'tenup_auto_tweet_setup', __NAMESPACE__ . '\setup' );
