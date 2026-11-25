<?php
/**
 * Helper functions for testing.
 *
 * @since 1.0.0
 * @package TenUp\AutoshareForTwitter
 */

namespace TenUp\AutoshareForTwitter\Tests;

use const TenUp\AutoshareForTwitter\Core\POST_TYPE_SUPPORT_FEATURE;

/**
 * Registers a post type not included in WP core.
 *
 * @param string $post_type Post type name.
 * @param array  $args Register post type args.
 */
function register_non_default_post_type( $post_type = 'event', $args = [ 'public' => true ] ) {
	register_post_type( $post_type, $args );
	return $post_type;
}

/**
 * Clear post type support settings for additional unit tests.
 *
 * @param string $feature_to_reset The feature to reset.
 */
function reset_post_type_support( $feature_to_reset = POST_TYPE_SUPPORT_FEATURE ) {
	global $_wp_post_type_features;

	foreach ( $_wp_post_type_features as &$post_type ) {
		unset( $post_type[ $feature_to_reset ] );
	}
}

/**
 * Get final values of a given filter.
 *
 * @param string $hook          Name of the hook.
 * @param string $default_value Default hook return value.
 *
 * @return mixed
 */
function get_filter_applied_value( $hook = '', $default_value = '' ) {
	if ( empty( $hook ) ) {
		return false;
	}

	return apply_filters( $hook, $default_value );
}

/**
 * Stores two connected X accounts, as the OAuth callback would.
 *
 * @return array The stored accounts, keyed by account ID.
 */
function connect_test_accounts() {
	$accounts = array(
		'A1' => array(
			'id'                 => 'A1',
			'name'               => 'Account One',
			'username'           => 'account_one',
			'profile_image_url'  => 'https://example.org/one.png',
			'oauth_token'        => 'TOKEN_ONE',
			'oauth_token_secret' => 'SECRET_ONE',
		),
		'A2' => array(
			'id'                 => 'A2',
			'name'               => 'Account Two',
			'username'           => 'account_two',
			'profile_image_url'  => 'https://example.org/two.png',
			'oauth_token'        => 'TOKEN_TWO',
			'oauth_token_secret' => 'SECRET_TWO',
		),
	);
	update_option( 'autoshare_for_twitter_accounts', $accounts );

	return $accounts;
}

/**
 * Stands in for the X API.
 *
 * Records every tweet the plugin hands to the API and answers with queued
 * responses (a successful one when the queue is empty), so tests never reach X.
 * WP_UnitTestCase removes the filters after each test.
 */
class Tweet_Spy {

	/**
	 * The update data of every tweet sent, in order.
	 *
	 * @var array[]
	 */
	public $sent = array();

	/**
	 * Responses to return, in order.
	 *
	 * @var object[]
	 */
	public $responses = array();

	/**
	 * Starts intercepting status updates and media uploads.
	 */
	public function __construct() {
		add_filter( 'autoshare_for_twitter_pre_status_update', array( $this, 'status_update' ), 10, 2 );
		add_filter(
			'autoshare_for_twitter_pre_media_upload',
			static function () {
				return 'media-1';
			}
		);
	}

	/**
	 * Records a status update and returns the next response.
	 *
	 * @param mixed $response    Short-circuit value.
	 * @param array $update_data Data that would be sent to X.
	 *
	 * @return object
	 */
	public function status_update( $response, $update_data ) {
		$this->sent[] = $update_data;

		if ( ! empty( $this->responses ) ) {
			return array_shift( $this->responses );
		}

		return (object) array( 'id' => '1001' );
	}

	/**
	 * An error response in the shape X returns.
	 *
	 * @param int    $code    X error code.
	 * @param string $message X error message.
	 *
	 * @return object
	 */
	public static function error( $code, $message ) {
		return (object) array(
			'errors' => array(
				(object) array(
					'code'    => $code,
					'message' => $message,
				),
			),
		);
	}
}
