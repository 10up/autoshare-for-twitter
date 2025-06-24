<?php
/**
 * Sets up a WP REST route handling autoshare metadata.
 *
 * @since 1.0.0
 * @package TenUp\AutoshareForTwitter
 */

namespace TenUp\AutoshareForTwitter\REST;

use WP_REST_Response;
use WP_REST_Server;
use const TenUp\AutoshareForTwitter\Core\Post_Meta\TWEET_BODY_KEY;
use const TenUp\AutoshareForTwitter\Core\Post_Meta\ENABLE_AUTOSHARE_FOR_TWITTER_KEY;
use const TenUp\AutoshareForTwitter\Core\Post_Meta\TWEET_ALLOW_IMAGE;
use const TenUp\AutoshareForTwitter\Core\Post_Meta\TWEET_ACCOUNTS_KEY;
use const TenUp\AutoshareForTwitter\Core\POST_TYPE_SUPPORT_FEATURE;

use function TenUp\AutoshareForTwitter\Core\Post_Meta\get_tweet_status_message;
use function TenUp\AutoshareForTwitter\Core\Post_Meta\save_autoshare_for_twitter_meta_data;
use function TenUp\AutoshareForTwitter\Utils\get_autoshare_for_twitter_meta;

/**
 * The namespace for plugin REST endpoints.
 *
 * @since 1.0.0
 */
const REST_NAMESPACE = 'autoshare';

/**
 * The plugin REST version.
 *
 * @since 1.0.0
 */
const REST_VERSION = 'v1';

/**
 * The REST route for autoshare metadata.
 *
 * @since 1.0.0
 */
const AUTOSHARE_FOR_TWITTER_REST_ROUTE = 'post-autoshare-for-twitter-meta';

/**
 * Adds WP hook callbacks.
 *
 * @since 1.0.0
 */
function add_hook_callbacks() {
	add_action( 'rest_api_init', __NAMESPACE__ . '\register_post_autoshare_for_twitter_meta_rest_route' );
	add_action( 'rest_api_init', __NAMESPACE__ . '\register_tweet_status_rest_field' );
}

/**
 * Registers the autoshare REST route.
 *
 * @since 1.0.0
 */
function register_post_autoshare_for_twitter_meta_rest_route() {
	register_rest_route(
		sprintf( '%s/%s', REST_NAMESPACE, REST_VERSION ),
		sprintf( '/%s/(?P<id>[\d]+)', AUTOSHARE_FOR_TWITTER_REST_ROUTE ),
		[
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => __NAMESPACE__ . '\update_post_autoshare_for_twitter_meta',
			'permission_callback' => __NAMESPACE__ . '\update_post_autoshare_for_twitter_meta_permission_check',
			'args'                => [
				'id'                             => [
					'description'       => __( 'Unique identifier for the object.', 'autoshare-for-twitter' ),
					'required'          => true,
					'sanitize_callback' => 'absint',
					'type'              => 'integer',
					'validate_callback' => 'rest_validate_request_arg',
				],
				TWEET_BODY_KEY                   => [
					'description'       => __( 'Tweet text, if overriding the default', 'autoshare-for-twitter' ),
					'required'          => true,
					'sanitize_callback' => 'sanitize_text_field',
					'type'              => 'string',
					'validate_callback' => 'rest_validate_request_arg',
				],
				ENABLE_AUTOSHARE_FOR_TWITTER_KEY => [
					'description'       => __( 'Whether autopost is enabled for the current post', 'autoshare-for-twitter' ),
					'required'          => true,
					'sanitize_callback' => 'absint',
					'type'              => 'boolean',
					'validate_callback' => 'rest_validate_request_arg',
				],
				TWEET_ALLOW_IMAGE                => [
					'description'       => __( 'Whether the tweet has an image.', 'autoshare-for-twitter' ),
					'required'          => true,
					'type'              => 'boolean',
					'validate_callback' => 'rest_validate_request_arg',
				],
				TWEET_ACCOUNTS_KEY               => [
					'description'       => __( 'Tweet enabled Twitter accounts.', 'autoshare-for-twitter' ),
					'required'          => true,
					'type'              => 'array',
					'sanitize_callback' => function( $value ) {
						if ( is_array( $value ) ) {
							return array_map( 'sanitize_text_field', $value );
						}
						return [];
					},
					'validate_callback' => 'rest_validate_request_arg',
				],
			],
		]
	);
}

/**
 * Provides the autoshare meta rest route for a provided post.
 *
 * @since 1.0.0
 * @param int $post_id Post ID.
 * @return string The REST route for a post.
 */
function post_autoshare_for_twitter_meta_rest_route( $post_id ) {
	return sprintf( '%s/%s/%s/%d', REST_NAMESPACE, REST_VERSION, AUTOSHARE_FOR_TWITTER_REST_ROUTE, intval( $post_id ) );
}

/**
 * Checks whether the current user has permission to update autoshare metadata.
 *
 * @since 1.0.0
 * @param WP_REST_Request $request A REST request containing post autoshare metadata to update.
 * @return boolean
 */
function update_post_autoshare_for_twitter_meta_permission_check( $request ) {
	return current_user_can( 'edit_post', $request['id'] );
}

/**
 * Updates autoshare metadata associated with a post.
 *
 * @since 1.0.0
 * @param WP_REST_Request $request A REST request containing post autoshare metadata to update.
 * @return WP_REST_Response REST response with information about the current autoshare status.
 */
function update_post_autoshare_for_twitter_meta( $request ) {
	$params = $request->get_params();

	save_autoshare_for_twitter_meta_data( $request['id'], $params );

	$enabled           = (bool) get_autoshare_for_twitter_meta( $request['id'], ENABLE_AUTOSHARE_FOR_TWITTER_KEY );
	$tweet_allow_image = (bool) ( 'yes' === get_autoshare_for_twitter_meta( $request['id'], TWEET_ALLOW_IMAGE ) );
	$accounts          = get_autoshare_for_twitter_meta( $request['id'], TWEET_ACCOUNTS_KEY );
	$accounts          = ! empty( $accounts ) ? $accounts : [];
	$message           = $enabled ?
		__( 'Autopost to X/Twitter enabled.', 'autoshare-for-twitter' ) :
		__( 'Autopost to X/Twitter disabled.', 'autoshare-for-twitter' );

	return rest_ensure_response(
		[
			'enabled'       => $enabled,
			'message'       => $message,
			'override'      => ! empty( get_autoshare_for_twitter_meta( $request['id'], TWEET_BODY_KEY ) ),
			'allowImage'    => $tweet_allow_image,
			'tweetAccounts' => $accounts,
		]
	);
}

/**
 * Adds a REST field returning the tweet status message array for the current post.
 *
 * @since 0.1.0
 */
function register_tweet_status_rest_field() {
	register_rest_field(
		get_post_types_by_support( POST_TYPE_SUPPORT_FEATURE ),
		'autoshare_for_twitter_status',
		[
			'get_callback' => function( $data ) {
				return get_tweet_status_message( $data['id'] );
			},
			'schema'       => [
				'context'     => [
					'edit',
				],
				'description' => __( 'Autoshare status message', 'autoshare-for-twitter' ),
				'type'        => 'object',
			],
		]
	);
}
