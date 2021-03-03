<?php
/**
 * Tests functions in core.php.
 *
 * @since 1.0.0
 * @package TenUp\AutoshareForTwitter
 */

namespace TenUp\AutoshareForTwitter\Tests;

use \WP_UnitTestCase;
use function TenUp\AutoshareForTwitter\Core\set_post_type_supports_with_custom_columns;

/**
 * TestCore class.
 *
 * @since 1.0.0
 */
class TestCore extends WP_UnitTestCase {
	/**
	 * Tests the set_default_post_type_supports function.
	 *
	 * @since 1.0.0
	 */
	public function test_set_post_type_supports() {
		global $_wp_post_type_features;

		$saved__wp_post_type_features = $_wp_post_type_features;

		$non_default_post_type = register_non_default_post_type();

		// Test that the feature is not supported by default.
		reset_post_type_support();
		$this->assertFalse( post_type_supports( 'post', 'autoshare-for-twitter' ) );

		// Test that posts and pages support the feature by default, but not other post types.
		reset_post_type_support();
		set_post_type_supports_with_custom_columns();
		$this->assertTrue( post_type_supports( 'post', 'autoshare-for-twitter' ) );
		$this->assertTrue( post_type_supports( 'page', 'autoshare-for-twitter' ) );

		$this->assertFalse( post_type_supports( $non_default_post_type, 'autoshare-for-twitter' ) );

		// Test that the default supported post types can be filtered.
		reset_post_type_support();
		$filter_post_type_supports = function( $post_types ) use ( $non_default_post_type ) {
			return [ $non_default_post_type ];
		};
		add_filter( 'autoshare_for_twitter_default_post_types', $filter_post_type_supports );

		set_post_type_supports_with_custom_columns();
		$this->assertFalse( post_type_supports( 'post', 'autoshare-for-twitter' ) );
		$this->assertFalse( post_type_supports( 'page', 'autoshare-for-twitter' ) );
		$this->assertTrue( post_type_supports( $non_default_post_type, 'autoshare-for-twitter' ) );

		// Clean up.
		remove_filter( 'autoshare_for_twitter_default_post_types', $filter_post_type_supports );
		$_wp_post_type_features = $saved__wp_post_type_features; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	}

}
