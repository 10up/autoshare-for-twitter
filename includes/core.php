<?php
/**
 * Core plugin setup.
 *
 * @package TenUp\Auto_Tweet\Core
 */

namespace TenUp\Auto_Tweet\Core;

const POST_TYPE_SUPPORT_FEATURE = 'tenup-autotweet';

/**
 * The main setup action.
 */
function setup() {
	require_once plugin_dir_path( AUTOTWEET ) . '/includes/admin/assets.php';
	require_once plugin_dir_path( AUTOTWEET ) . '/includes/admin/settings.php';
	require_once plugin_dir_path( AUTOTWEET ) . '/includes/admin/post-meta.php';
	require_once plugin_dir_path( AUTOTWEET ) . '/includes/admin/post-transition.php';
	require_once plugin_dir_path( AUTOTWEET ) . '/includes/class-publish-tweet.php';
	require_once plugin_dir_path( AUTOTWEET ) . '/includes/rest.php';

	\TenUp\AutoTweet\Admin\Assets\add_hook_callbacks();
	\TenUp\AutoTweet\REST\add_hook_callbacks();

	\TenUp\AutoTweet\Admin\Assets\add_hook_callbacks();

	/**
	 * Allow others to hook into the core setup action
	 */
	do_action( 'tenup_auto_tweet_setup' );

	add_action( 'init', __NAMESPACE__ . '\set_default_post_type_supports' );
}

/**
 * Fire up the module.
 *
 * @uses auto_tweet_loaded
 */
add_action( 'tenup_auto_tweet_loaded', __NAMESPACE__ . '\setup' );

/**
 * Adds autotweet support for default post types.
 *
 * @since 1.0.0
 */
function set_default_post_type_supports() {

	/**
	 * Filters post types supported by default.
	 *
	 * @since 1.0.0
	 * @param array Array of post types.
	 */
	$post_types_supported_by_default = apply_filters( 'tenup_autotweet_default_post_types', [ 'post', 'page' ] );

	foreach ( (array) $post_types_supported_by_default as $post_type ) {
		add_post_type_support( $post_type, POST_TYPE_SUPPORT_FEATURE );
	}
}
