<?php
/**
 * Plugin Name:       10up Auto Tweet
 * Description:       Adds the ability to automatically publish a status update to Twitter.
 * Version:           0.1.0
 * Requires at least: 4.7
 * Requires PHP:      7.0
 * Author:            10up
 * Author URI:        https://10up.com
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 *
 * @package TenUp\Auto_Tweet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'TUAT_VERSION', '0.1.0' );
define( 'TUAT_URL', plugin_dir_url( __FILE__ ) );
define( 'TUAT_PATH', plugin_dir_path( __FILE__ ) );
define( 'TUAT_INC', TUAT_PATH . 'includes/' );

/**
 * Composer check.
 */
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}


// Include the main functionality
require_once plugin_dir_path( __FILE__ ) . 'includes/core.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/utils.php';

/**
 * Play nice with others.
 */
do_action( 'tenup_auto_tweet_loaded' );
