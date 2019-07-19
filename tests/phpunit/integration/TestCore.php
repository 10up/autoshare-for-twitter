<?php
/**
 * Tests functions in core.php.
 *
 * @since 1.0.0
 * @package TenUp\Auto_Tweet
 */

namespace TenUp\Auto_Tweet\Tests;

use \WP_UnitTestCase;
use function TenUp\Auto_Tweet\Core\set_default_post_type_supports;
use const TenUp\Auto_Tweet\Core\POST_TYPE_SUPPORT_FEATURE;

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
	public function test_set_default_post_type_supports() {
		global $_wp_post_type_features;

		$saved__wp_post_type_features = $_wp_post_type_features;

		$non_default_post_type = register_non_default_post_type();

		// Test that the feature is not supported by default.
		reset_post_type_support();
		$this->assertFalse( post_type_supports( 'post', POST_TYPE_SUPPORT_FEATURE ) );

		// Test that posts and pages support the feature by default, but not other post types.
		reset_post_type_support();
		set_default_post_type_supports();
		$this->assertTrue( post_type_supports( 'post', POST_TYPE_SUPPORT_FEATURE ) );
		$this->assertTrue( post_type_supports( 'page', POST_TYPE_SUPPORT_FEATURE ) );
	
		$this->assertFalse( post_type_supports( $non_default_post_type, POST_TYPE_SUPPORT_FEATURE ) );

		// Test that the default supported post types can be filtered.
		reset_post_type_support();
		$filter_post_type_supports = function( $post_types ) use ( $non_default_post_type ) {
			return [ $non_default_post_type ];
		};
		add_filter( 'tenup_autotweet_default_post_types', $filter_post_type_supports );

		set_default_post_type_supports();
		$this->assertFalse( post_type_supports( 'post', POST_TYPE_SUPPORT_FEATURE ) );
		$this->assertFalse( post_type_supports( 'page', POST_TYPE_SUPPORT_FEATURE ) );
		$this->assertTrue( post_type_supports( $non_default_post_type, POST_TYPE_SUPPORT_FEATURE ) );

		// Clean up.
		remove_filter( 'tenup_autotweet_default_post_types', $filter_post_type_supports );
		$_wp_post_type_features = $saved__wp_post_type_features;
	}

}
