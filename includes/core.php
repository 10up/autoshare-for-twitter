<?php
/**
 * Core plugin setup.
 *
 * @package TenUp\Auto_Tweet\Core
 */

namespace TenUp\Auto_Tweet\Core;

use function TenUp\Auto_Tweet\Utils\post_type_support_feature_name;

/**
 * The main setup action.
 */
function setup() {

	// Includes and requires.
	require_once 'admin/settings.php';
	require_once 'admin/post-meta.php';
	require_once 'admin/post-transition.php';
	require_once 'class-publish-tweet.php';

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
		add_post_type_support( $post_type, post_type_support_feature_name() );
	}
}
