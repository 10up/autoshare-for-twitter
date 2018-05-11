<?php
/**
 * Plugin Name: 10up Auto Tweet
 * Plugin URI:
 * Description: Adds the ability to automatically publish a status update to Twitter.
 * Version:     0.1.0
 * Author:      10up
 * Author URI:  https://10up.com
 * License:     GPLv2 or later
 * Text Domain: tenup-auto-tweet
 * Domain Path: /languages
 */

// Useful global constants
define( 'TENUP_AUTO_TWEET_VERSION', '0.1.0' );
define( 'TENUP_AUTO_TWEET_URL',     plugin_dir_url( __FILE__ ) );
define( 'TENUP_AUTO_TWEET_PATH',    dirname( __FILE__ ) . '/' );
define( 'TENUP_AUTO_TWEET_INC',     TENUP_AUTO_TWEET_PATH . 'includes/' );

// Include files
require_once TENUP_AUTO_TWEET_INC . 'functions/core.php';


// Activation/Deactivation
register_activation_hook( __FILE__, '\TenupAutoTweet\Core\activate' );
register_deactivation_hook( __FILE__, '\TenupAutoTweet\Core\deactivate' );

// Bootstrap
TenupAutoTweet\Core\setup();
