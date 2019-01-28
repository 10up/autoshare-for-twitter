<?php
/**
 * Core plugin setup.
 *
 * @package TenUp\Auto_Tweet\Core
 */

namespace TenUp\Auto_Tweet\Core;

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
}

/**
 * Fire up the module.
 *
 * @uses auto_tweet_loaded
 */
add_action( 'tenup_auto_tweet_loaded', __NAMESPACE__ . '\setup' );
