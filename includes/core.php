<?php
/**
 * Core plugin setup.
 *
 * @package TenUp\AutoshareForTwitter\Core
 */

namespace TenUp\AutoshareForTwitter\Core;

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

	add_action( 'init', __NAMESPACE__ . '\set_default_post_type_supports' );
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
function set_default_post_type_supports() {

	/**
	 * Filters post types supported by default.
	 *
	 * @since 1.0.0
	 * @param array Array of post types.
	 */
	$post_types_supported_by_default = apply_filters( 'autoshare_for_twitter_default_post_types', [ 'post', 'page' ] );

	foreach ( (array) $post_types_supported_by_default as $post_type ) {
		add_post_type_support( $post_type, POST_TYPE_SUPPORT_FEATURE );
	}
}
