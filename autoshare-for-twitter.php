<?php
/**
 * Plugin Name:       Autopost for X (formerly Autoshare for Twitter)
 * Description:       Automatically shares the post title or custom message and a link to the post to X/Twitter.
 * Disclaimer:        TWITTER, TWEET, RETWEET and the Twitter logo are trademarks of Twitter, Inc. or its affiliates.
 * Version:           2.2.1
 * Requires at least: 5.7
 * Requires PHP:      7.4
 * Author:            10up
 * Author URI:        https://10up.com
 * License:           GPL-2.0-or-later
 * License URI:       https://spdx.org/licenses/GPL-2.0-or-later.html
 * Text Domain:       autoshare-for-twitter
 *
 * @package TenUp\AutoshareForTwitter
 */

namespace TenUp\AutoshareForTwitter;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'AUTOSHARE_FOR_TWITTER', __FILE__ );
define( 'AUTOSHARE_FOR_TWITTER_VERSION', '2.2.1' );
define( 'AUTOSHARE_FOR_TWITTER_URL', plugin_dir_url( __FILE__ ) );
define( 'AUTOSHARE_FOR_TWITTER_PATH', plugin_dir_path( __FILE__ ) );
define( 'AUTOSHARE_FOR_TWITTER_INC', AUTOSHARE_FOR_TWITTER_PATH . 'includes/' );
define( 'AUTOSHARE_FOR_TWITTER_URL_LENGTH', 23 );

/**
 * Get the minimum version of PHP required by this plugin.
 *
 * @return string Minimum version required.
 */
function minimum_php_requirement() {
	return '7.4';
}

/**
 * Whether PHP installation meets the minimum requirements
 *
 * @return bool True if meets minimum requirements, false otherwise.
 */
function site_meets_php_requirements() {
	return version_compare( phpversion(), minimum_php_requirement(), '>=' );
}

// Ensuring our PHP version requirement is met first before loading plugin.
if ( ! site_meets_php_requirements() ) {
	add_action(
		'admin_notices',
		function () {
			?>
			<div class="notice notice-error">
				<p>
					<?php
					echo wp_kses_post(
						sprintf(
						/* translators: %s: Minimum required PHP version */
							__( 'Autopost for X/Twitter requires PHP version %s or later. Please upgrade PHP or disable the plugin.', 'autoshare-for-twitter' ),
							esc_html( minimum_php_requirement() )
						)
					);
					?>
				</p>
			</div>
			<?php
		}
	);

	return;
}

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

/**
 * Register an activation hook that we can hook into.
 */
register_activation_hook(
	__FILE__,
	function () {
		// Don't need to show migration notice to new users.
		update_option( 'autoshare_migrate_to_v2_api_notice_dismissed', true );
	}
);
