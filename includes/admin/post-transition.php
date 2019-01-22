<?php
/**
 * Handler for POSTing a status update to Twitter.
 *
 * @package 10upautotweet
 */

namespace TenUp\Auto_Tweet\Core\Post_Transition;

use TenUp\Auto_Tweet\Core\Publish_Tweet\Publish_Tweet;
use TenUp\Auto_Tweet\Core\Post_Meta as Meta;
use TenUp\Auto_Tweet\Utils as Utils;

/**
 * Setup function.
 *
 * @return void
 */
function setup() {
	add_action( 'transition_post_status', __NAMESPACE__ . '\publish_tweet', 10, 3 );
}

/**
 * Primary handler for the process of publishing to Twitter.
 *
 * @param string   $new_status The new status.
 * @param string   $old_status The old status.
 * @param \WP_Post $post       The post object.
 *
 * @return object
 */
function publish_tweet( $new_status, $old_status, $post ) {

	/**
	 * We're only interested in posts that are transitioning into publish.
	 */
	if ( 'publish' !== $new_status || 'publish' === $old_status ) {
		return;
	}

	/**
	 * Don't bother enqueuing assets if the post type hasn't opted into auto-tweeting
	 */
	if ( ! Utils\opted_into_auto_tweet( $post->ID ) ) {
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
function update_auto_tweet_meta( int $post_id, $data ) {

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
	update_post_meta( $post_id, Meta\META_PREFIX . '_' . Meta\STATUS_KEY, $response );
	delete_post_meta( $post_id, Meta\META_PREFIX . '_' . Meta\TWEET_KEY );

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
