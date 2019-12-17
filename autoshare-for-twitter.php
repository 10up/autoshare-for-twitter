<?php
/**
 * Plugin Name:       Autoshare for Twitter
 * Description:       Automatically tweets the post title or custom message and a link to the post.
 * Disclaimer:        TWITTER, TWEET, RETWEET and the Twitter logo are trademarks of Twitter, Inc. or its affiliates.
 * Version:           1.0.0
 * Requires at least: 4.7
 * Requires PHP:      7.0
 * Author:            10up
 * Author URI:        https://10up.com
 * License:           GPL-2.0-or-later
 * License URI:       https://spdx.org/licenses/GPL-2.0-or-later.html
 *
 * @package TenUp\AutoshareForTwitter
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'AUTOSHARE_FOR_TWITTER', __FILE__ );
define( 'AUTOSHARE_FOR_TWITTER_VERSION', '1.0.0' );
define( 'AUTOSHARE_FOR_TWITTER_URL', plugin_dir_url( __FILE__ ) );
define( 'AUTOSHARE_FOR_TWITTER_PATH', plugin_dir_path( __FILE__ ) );
define( 'AUTOSHARE_FOR_TWITTER_INC', AUTOSHARE_FOR_TWITTER_PATH . 'includes/' );

/**
 * Composer check.
 */
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}


// Include the main functionality.
require_once plugin_dir_path( __FILE__ ) . 'includes/core.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/utils.php';

/**
 * Play nice with others.
 */
do_action( 'autoshare_for_twitter_loaded' );
