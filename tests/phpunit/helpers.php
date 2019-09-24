<?php
/**
 * Helper functions for testing.
 *
 * @since 1.0.0
 * @package TenUp\AutoTweet
 */

namespace TenUp\AutoTweet\Tests;

use const TenUp\AutoTweet\Core\POST_TYPE_SUPPORT_FEATURE;

function register_non_default_post_type( $post_type = 'event', $args = [ 'public' => true ] ) {
	register_post_type( $post_type, $args );
	return $post_type;
}


function reset_post_type_support( $feature_to_reset = POST_TYPE_SUPPORT_FEATURE ) {
	global $_wp_post_type_features;

	foreach ( $_wp_post_type_features as &$post_type ) {
		unset( $post_type[ $feature_to_reset ] );
	}
}