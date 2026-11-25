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
 * Check if added method to a hook exists.
 *
 * @param string $hook                 Name of the hook.
 * @param string $function_method_name Name of method.
 *
 * @return bool
 */
function check_method_exists( $hook = '', $function_method_name = '' ) {
	global $wp_filter;

	if ( empty( $hook ) || empty( $function_method_name ) ) {
		return false;
	}

	if ( ! isset( $wp_filter[ $hook ]->callbacks ) ) {
		return false;
	}

	foreach ( $wp_filter[ $hook ]->callbacks as $key => $callbacks ) {
		if ( ! is_array( $callbacks ) ) {
			return false;
		}

		foreach ( $callbacks as $callback ) {
			if ( $callback['function'] === $function_method_name ) {
				return true;
			}
		}
	}

	return false;
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
