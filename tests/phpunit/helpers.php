<?php
/**
 * Helper functions for testing.
 *
 * @since 1.0.0
 * @package TenUp\Autoshare
 */

namespace TenUp\Autoshare\Tests;

use const TenUp\Autoshare\Core\POST_TYPE_SUPPORT_FEATURE;

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
