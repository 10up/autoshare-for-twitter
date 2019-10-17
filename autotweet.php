<?php
/**
 * Plugin Name: Autotweet
 * Description: Automatically tweets the post title or custom message and a link to the post.
 * Version:     1.0.0
 * Requires at least: 4.7
 * Requires PHP:      7.0
 * Author:            10up
 * Author URI:        https://10up.com
 * License:           GPL-2.0-or-later
 * License URI:       https://spdx.org/licenses/GPL-2.0-or-later.html
 *
 * @package TenUp\AutoTweet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'AUTOTWEET', __FILE__ );
define( 'TUAT_VERSION', '1.0.0' );
define( 'TUAT_URL', plugin_dir_url( __FILE__ ) );
define( 'TUAT_PATH', plugin_dir_path( __FILE__ ) );
define( 'TUAT_INC', TUAT_PATH . 'includes/' );

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
do_action( 'autotweet_loaded' );
