<?php
/**
 * 10up autotweet test bootstrap.
 *
 * @since 1.0.0
 * @package TenUp\Auto_Tweet
 */

namespace TenUp\Auto_Tweet\Tests;

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}
if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	echo "Could not find $_tests_dir/includes/functions.php, have you run bin/install-wp-tests.sh ?" . PHP_EOL;
	exit( 1 );
}

require_once $_tests_dir . '/includes/functions.php';

/**
 * Bootstrap Plugin plugin
 *
 * @since 1.0.0
 */
function load_plugin() {
	require_once dirname( __DIR__ ) . '/tenup-auto-tweet.php';
	require_once __DIR__ . '/helpers.php';
}
tests_add_filter( 'muplugins_loaded', __NAMESPACE__ . '\load_plugin' );

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';
