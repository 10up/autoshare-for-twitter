<?php
/**
 * Core plugin setup.
 *
 * @package TenUp\Autoshare\Core
 */

namespace TenUp\Autoshare\Core;

const POST_TYPE_SUPPORT_FEATURE = 'autoshare';

/**
 * The main setup action.
 */
function setup() {
	require_once plugin_dir_path( AUTOSHARE ) . '/includes/admin/assets.php';
	require_once plugin_dir_path( AUTOSHARE ) . '/includes/admin/settings.php';
	require_once plugin_dir_path( AUTOSHARE ) . '/includes/admin/post-meta.php';
	require_once plugin_dir_path( AUTOSHARE ) . '/includes/admin/post-transition.php';
	require_once plugin_dir_path( AUTOSHARE ) . '/includes/class-publish-tweet.php';
	require_once plugin_dir_path( AUTOSHARE ) . '/includes/rest.php';

	\TenUp\Autoshare\Admin\Assets\add_hook_callbacks();
	\TenUp\Autoshare\REST\add_hook_callbacks();

	\TenUp\Autoshare\Admin\Assets\add_hook_callbacks();

	/**
	 * Allow others to hook into the core setup action
	 */
	do_action( 'autoshare_setup' );

	add_action( 'init', __NAMESPACE__ . '\set_default_post_type_supports' );
}

/**
 * Fire up the module.
 *
 * @uses autoshare_loaded
 */
add_action( 'autoshare_loaded', __NAMESPACE__ . '\setup' );

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
	$post_types_supported_by_default = apply_filters( 'autoshare_default_post_types', [ 'post', 'page' ] );

	foreach ( (array) $post_types_supported_by_default as $post_type ) {
		add_post_type_support( $post_type, POST_TYPE_SUPPORT_FEATURE );
	}
}
