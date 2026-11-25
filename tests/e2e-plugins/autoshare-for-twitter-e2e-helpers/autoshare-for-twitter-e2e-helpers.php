<?php
/**
 * Plugin Name: Autopost for X E2E Helpers
 * Description: Test-only support plugin. Mocks the X API requests and exposes a fixture endpoint for the E2E suite. Loaded by .wp-env.json and .wp-env.test.json, never shipped.
 * Version: 0.1.0
 *
 * @package autoshare-for-twitter
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * The plugin talks to X through TwitterOAuth (cURL), not the WordPress HTTP API,
 * so pre_http_request can't intercept it. Short-circuit every request through the
 * plugin's own filters instead.
 */

/**
 * Mock the Twitter media upload.
 */
add_filter(
	'autoshare_for_twitter_pre_media_upload',
	function () {
		return 'dummy_media_id';
	}
);

/**
 * Mock the Twitter status update.
 */
add_filter(
	'autoshare_for_twitter_pre_status_update',
	function () {
		return (object) array(
			'id' => 'dummy_tweet_id',
		);
	}
);

/**
 * Mock the Twitter invalidate token API.
 */
add_filter( 'autoshare_for_twitter_pre_disconnect_account', '__return_true' );

/*
 * Fixture endpoint for state core REST routes can't reach: the plugin's options
 * (app credentials, connected accounts) and the Classic Editor settings.
 * Specs call it with requestUtils.rest( { path: '/autoshare-for-twitter-e2e/v1/state', method: 'POST', data } ).
 * An option set to null is deleted.
 */
add_action(
	'rest_api_init',
	static function () {
		register_rest_route(
			'autoshare-for-twitter-e2e/v1',
			'/state',
			array(
				'methods'             => 'POST',
				'permission_callback' => static function () {
					return current_user_can( 'manage_options' );
				},
				'callback'            => static function ( WP_REST_Request $request ) {
					foreach ( (array) $request->get_param( 'options' ) as $name => $value ) {
						if ( null === $value ) {
							delete_option( sanitize_key( $name ) );
						} else {
							update_option( sanitize_key( $name ), $value );
						}
					}

					return rest_ensure_response( array( 'ok' => true ) );
				},
			)
		);
	}
);
