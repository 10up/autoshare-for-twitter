<?php
/**
 * Core plugin setup.
 *
 * @package TenUp\AutoshareForTwitter\Core
 */

namespace TenUp\AutoshareForTwitter\Core;

use TenUp\AutoshareForTwitter\Utils;

const POST_TYPE_SUPPORT_FEATURE = 'autoshare-for-twitter';

/**
 * The main setup action.
 */
function setup() {
	require_once plugin_dir_path( AUTOSHARE_FOR_TWITTER ) . 'includes/admin/assets.php';
	require_once plugin_dir_path( AUTOSHARE_FOR_TWITTER ) . 'includes/admin/settings.php';
	require_once plugin_dir_path( AUTOSHARE_FOR_TWITTER ) . 'includes/admin/post-meta.php';
	require_once plugin_dir_path( AUTOSHARE_FOR_TWITTER ) . 'includes/admin/post-transition.php';
	require_once plugin_dir_path( AUTOSHARE_FOR_TWITTER ) . 'includes/class-publish-tweet.php';
	require_once plugin_dir_path( AUTOSHARE_FOR_TWITTER ) . 'includes/rest.php';

	\TenUp\AutoshareForTwitter\Admin\Assets\add_hook_callbacks();
	\TenUp\AutoshareForTwitter\REST\add_hook_callbacks();

	/**
	 * Allow others to hook into the core setup action
	 */
	do_action( 'autoshare_for_twitter_setup' );

	add_action( 'init', __NAMESPACE__ . '\set_post_type_supports' );
}

/**
 * Fire up the module.
 *
 * @uses autoshare_for_twitter_loaded
 */
add_action( 'autoshare_for_twitter_loaded', __NAMESPACE__ . '\setup' );

/**
 * Adds autoshare support for default post types.
 *
 * @since 1.0.0
 */
function set_post_type_supports() {
	$enable_for = Utils\get_autoshare_for_twitter_settings( 'enable_for' );
	if ( 'all' === $enable_for ) {
		$post_types = Utils\get_available_post_types();
	} else {
		$post_types = Utils\get_autoshare_for_twitter_settings( 'post_types' );
	}
	foreach ( (array) $post_types as $post_type ) {
		add_post_type_support( $post_type, POST_TYPE_SUPPORT_FEATURE );
	}
}
