<?php
/**
 * Sets up a WP REST route handling autotweet metadata.
 *
 * @since 1.0.0
 * @package TenUp\Auto_Tweet
 */

namespace TenUp\AutoTweet\REST;

use WP_REST_Response;
use WP_REST_Server;
use const TenUp\Auto_Tweet\Core\Post_Meta\TWEET_BODY_KEY;
use const TenUp\Auto_Tweet\Core\Post_Meta\ENABLE_AUTOTWEET_KEY;

use function TenUp\Auto_Tweet\Core\Post_Meta\save_autotweet_meta_data;

/**
 * The namespace for plugin REST endpoints.
 *
 * @since 1.0.0
 */
const REST_NAMESPACE = 'autotweet';

/**
 * The plugin REST version.
 *
 * @since 1.0.0
 */
const REST_VERSION = 'v1';

/**
 * The REST route for autotweet metadata.
 *
 * @since 1.0.0
 */
const AUTOTWEET_REST_ROUTE = 'post-autotweet-meta';

/**
 * Adds WP hook callbacks.
 *
 * @since 1.0.0
 */
function add_hook_callbacks() {
	add_action( 'rest_api_init', __NAMESPACE__ . '\register_post_autotweet_meta_rest_route' );
}

/**
 * Registers the autotweet REST route.
 *
 * @since 1.0.0
 */
function register_post_autotweet_meta_rest_route() {
	register_rest_route(
		sprintf( '%s/%s', REST_NAMESPACE, REST_VERSION ),
		sprintf( '/%s/(?P<id>[\d]+)', AUTOTWEET_REST_ROUTE ),
		[
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => __NAMESPACE__ . '\update_post_autotweet_meta',
			'permission_callback' => __NAMESPACE__ . '\update_post_autotweet_meta_permission_check',
			'args'                => [
				'id'                 => [
					'description'       => __( 'Unique identifier for the object.', 'tenup_auto_tweet' ),
					'required'          => true,
					'sanitize_callback' => 'absint',
					'type'              => 'integer',
					'validate_callback' => 'rest_validate_request_arg',
				],
				TWEET_BODY_KEY       => [
					'description'       => __( 'Tweet text, if overriding the default', 'tenup_auto_tweet' ),
					'required'          => true,
					'sanitize_callback' => 'sanitize_text_field',
					'type'              => 'string',
					'validate_callback' => 'rest_validate_request_arg',
				],
				ENABLE_AUTOTWEET_KEY => [
					'description'       => __( 'Whether autotweet is enabled for the current post', 'tenup_auto_tweet' ),
					'required'          => true,
					'sanitize_callback' => 'absint',
					'type'              => 'boolean',
					'validate_callback' => 'rest_validate_request_arg',
				],
			],
		]
	);
}

/**
 * Provides the autotweet meta rest route for a provided post.
 *
 * @since 1.0.0
 * @param int $post_id Post ID.
 * @return string The REST route for a post.
 */
function post_autotweet_meta_rest_route( $post_id ) {
	return sprintf( '%s/%s/%s/%d', REST_NAMESPACE, REST_VERSION, AUTOTWEET_REST_ROUTE, intval( $post_id ) );
}

/**
 * Checks whether the current user has permission to update autotweet metadata.
 *
 * @since 1.0.0
 * @param WP_REST_Request $request A REST request containing post autotweet metadata to update.
 * @return boolean
 */
function update_post_autotweet_meta_permission_check( $request ) {
	return current_user_can( 'edit_post', $request['id'] );
}

/**
 * Updates autotweet metadata associated with a post.
 *
 * @since 1.0.0
 * @param WP_REST_Request $request A REST request containing post autotweet metadata to update.
 * @return WP_REST_Response REST response with information about the current autotweet status.
 */
function update_post_autotweet_meta( $request ) {
	$params = $request->get_params();

	save_autotweet_meta_data( $request['id'], $params );

	return new \WP_REST_Response( null, 400 );
	return rest_ensure_response(
		[
			'enabled'  => $params[ ENABLE_AUTOTWEET_KEY ],
			'message'  => __( 'Auto-tweet enabled.', 'tenup_auto_tweet' ),
			'override' => ! empty( $params[ TWEET_BODY_KEY ] ),
		]
	);
}
