<?php
/**
 * Handler for POSTing a status update to Twitter.
 *
 * @package TenUp\AutoshareForTwitter\Core
 */

namespace TenUp\AutoshareForTwitter\Core\Post_Transition;

use TenUp\AutoshareForTwitter\Core\Publish_Tweet\Publish_Tweet;
use TenUp\AutoshareForTwitter\Core\AST_Staging\AST_Staging;
use TenUp\AutoshareForTwitter\Core\Post_Meta as Meta;
use TenUp\AutoshareForTwitter\Core\Twitter_Accounts as Twitter_Accounts;
use TenUp\AutoshareForTwitter\Utils as Utils;

use function TenUp\AutoshareForTwitter\Core\Post_Meta\get_tweet_status_message;
use function TenUp\AutoshareForTwitter\Utils\update_autoshare_for_twitter_meta;
use function TenUp\AutoshareForTwitter\Core\Post_Meta\save_tweet_meta;
use function TenUp\AutoshareForTwitter\Utils\get_autoshare_for_twitter_meta;
use function TenUp\AutoshareForTwitter\Core\Post_Meta\get_tweet_status_logs;

use const TenUp\AutoshareForTwitter\Core\Post_Meta\TWEET_BODY_KEY;

/**
 * Setup function.
 *
 * @return void
 */
function setup() {
	add_action( 'transition_post_status', __NAMESPACE__ . '\maybe_publish_tweet', 10, 3 );
	add_action( 'wp_ajax_tenup_autoshare_retweet', __NAMESPACE__ . '\retweet', 10, 3 );
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
 * @return void
 */
function maybe_publish_tweet( $new_status, $old_status, $post ) {
	/*
	 * Add filter to return early based on post or status
	 */
	if ( apply_filters( 'autoshare_for_twitter_disable_on_transition_post_status', false, $post, $old_status, $new_status ) ) {
		return;
	}

	/*
	 * We're only interested in posts that are transitioning into publish.
	 */
	if ( 'publish' !== $new_status || 'publish' === $old_status ) {
		return;
	}

	/*
	 * Don't bother enqueuing assets if the post type hasn't opted into autosharing
	 */
	if ( ! Utils\opted_into_autoshare_for_twitter( $post->ID ) ) {
		return;
	}

	// Don't publish tweets from staging/testing sites.
	if ( ! AST_Staging::is_production_site() ) {
		return;
	}

	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		add_action(
			sprintf( 'rest_after_insert_%s', $post->post_type ),
			function( $post ) {
				publish_tweet( $post->ID );
			}
		);
	} else {
		// Ensure Autoshare-related form data is saved before reaching the publish_tweet step.
		// This will already have been done in REST because post data is updated before transition post status.
		save_tweet_meta( $post->ID );
		publish_tweet( $post->ID );
		return;
	}
}

/**
 * Primary handler for the process of publishing to Twitter.
 *
 * @param int  $post_id The current post ID.
 * @param bool $force   Publish tweet regardless of autoshare enabled or disabled on post.
 *
 * @return bool
 */
function publish_tweet( $post_id, $force = false ) {
	$post = get_post( $post_id );

	/*
	 * Don't bother enqueuing assets if the post type hasn't opted into autosharing
	 */
	if ( ! Utils\opted_into_autoshare_for_twitter( $post->ID ) ) {
		return false;
	}

	// Don't publish tweets from staging/testing sites.
	if ( ! AST_Staging::is_production_site() ) {
		return false;
	}

	// Ensure we have a $post object.
	if ( ! $post ) {
		return false;
	}

	/*
	 * One final check: was the "auto tweet" checkbox selected?
	 */
	if (
		( Utils\autoshare_enabled( $post->ID ) || $force ) &&
		! empty( Utils\get_tweet_accounts( $post->ID ) )
	) {
		$tweet   = Utils\compose_tweet_body( $post );
		$publish = new Publish_Tweet();

		$connected_accounts = ( new Twitter_Accounts() )->get_twitter_accounts( true );
		$twitter_accounts   = Utils\get_tweet_accounts( $post->ID );
		if ( empty( $twitter_accounts ) ) {
			return false;
		}

		$failed = false;
		// Loop through each Tweet enabled Twitter accounts and publish the tweet.
		foreach ( $twitter_accounts as $account_id ) {
			$account  = $connected_accounts[ $account_id ] ?? [];
			$username = $account['username'] ?? '';

			try {
				$twitter_response = $publish->status_update( $tweet, $post, $account_id );

				$response = validate_response( $twitter_response );
			} catch ( \Exception $e ) {
				$response = new \WP_Error(
					'autoshare_for_twitter_failed',
					esc_html__( 'Something went wrong, please try again.', 'autoshare-for-twitter' ),
					array(
						(object) array( 'message' => $e->getMessage() ),
					)
				);
			}

			if ( ! is_wp_error( $response ) ) {
				update_autoshare_for_twitter_meta_from_response( $post->ID, $response, $username );

				/**
				 * Fires after the status update to Twitter is considered successful.
				 */
				do_action( 'autoshare_for_twitter_success' );
			} else {
				// something here about it failing so do not allow republishing just in case.
				update_autoshare_for_twitter_meta_from_response( $post->ID, $response, $username );

				/**
				 * Fires if the response back from Twitter was an error.
				 */
				do_action( 'autoshare_for_twitter_failed' );
				$failed = true;
			}
		}

		if ( ! $failed ) {
			// Clear the tweet body on successful publishing to Twitter.
			Utils\delete_autoshare_for_twitter_meta( $post->ID, TWEET_BODY_KEY );
			remove_action( 'save_post', 'TenUp\AutoshareForTwitter\Core\Post_Meta\save_tweet_meta', 10 );
		}

		return ( ! $failed );
	}

	return false;
}

/**
 * Handles Re-tweeting.
 */
function retweet() {
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'wp_rest' ) ) {
		wp_send_json_error( __( 'Nonce verification failed.', 'autoshare-for-twitter' ) );
	}

	$post_id      = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
	$is_retweeted = publish_tweet( $post_id, true );

	// Send status logs markup for classic editor.
	if ( isset( $_POST['is_classic'] ) && ! empty( $_POST['is_classic'] ) ) {
		$message = [ 'message' => get_tweet_status_logs( $post_id ) ];
	} else {
		$message = get_tweet_status_message( $post_id );
	}
	$message['is_retweeted'] = $is_retweeted;

	if ( $is_retweeted ) {
		wp_send_json_success( $message );
	} else {
		wp_send_json_error( $message );
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
			'created_at' => gmdate( 'c' ), // Twitter API v2 doesn't return created_at.
		);

	} else {
		$errors = $response->errors;
		if ( empty( $response->errors ) && ! empty( $response->detail ) ) {
			$errors = array(
				(object) array(
					'code'    => $response->status,
					'message' => $response->detail,
				),
			);
		}
		$validated_response = new \WP_Error(
			'autoshare_for_twitter_failed',
			__( 'Something happened during Twitter update.', 'autoshare-for-twitter' ),
			$errors
		);
	}

	return $validated_response;
}

/**
 * Responsible for adding the validated response as post meta.
 *
 * @param int    $post_id The post id.
 * @param object $data    The tweet request data.
 * @param string $handle  The Twitter account handle.
 */
function update_autoshare_for_twitter_meta_from_response( $post_id, $data, $handle = '' ) {

	// No errors, Tweet considered successful.
	if ( ! is_wp_error( $data ) ) {
		$response = array(
			'status'     => 'published',
			'twitter_id' => (int) $data['id'],
			'handle'     => $handle,
			'created_at' => sanitize_text_field( $data['created_at'] ),
		);

		// Twitter sent back an error. Most likely a duplicate message.
	} elseif ( is_wp_error( $data ) ) {
		$error_message = $data->error_data['autoshare_for_twitter_failed'][0];
		// translators: %d is the error code.
		$error_code_text = $error_message->code ? sprintf( __( 'Error: %d. ', 'autoshare-for-twitter' ), $error_message->code ) : '';

		$response = array(
			'status'  => 'error',
			'handle'  => $handle,
			'message' => sanitize_text_field( $error_code_text . $error_message->message ),
		);

		// The default fallback message.
	} else {
		$response = array(
			'status'  => 'unknown',
			'handle'  => $handle,
			'message' => __( 'This post was not published to Twitter.', 'autoshare-for-twitter' ),
		);
	}

	/**
	 * Allow for filtering the Twitter status post meta.
	 */
	$response = apply_filters( 'autoshare_for_twitter_post_status_meta', $response );

	$tweet_meta = get_autoshare_for_twitter_meta( $post_id, Meta\TWITTER_STATUS_KEY );

	if ( '' === $tweet_meta ) {
		$tweet_meta = array();
	}

	/**
	 * Handles meta for multiple tweets.
	 */
	if ( isset( $tweet_meta['twitter_id'] ) || isset( $tweet_meta['status'] ) ) {
		$tweet_meta = array(
			$tweet_meta,
			$response,
		);
	} else {
		$tweet_meta[] = $response;
	}

	/**
	 * Update the post meta entry that stores the response.
	 */
	update_autoshare_for_twitter_meta( $post_id, Meta\TWITTER_STATUS_KEY, $tweet_meta );

	/**
	 * Fires after the response from Twitter has been written as meta to the post.
	 */
	do_action( 'autoshare_for_twitter_post_tweet_status_updated' );
}

/**
 * Fire up the module.
 *
 * @uses autoshare_for_twitter_setup
 */
add_action( 'autoshare_for_twitter_setup', __NAMESPACE__ . '\setup' );
