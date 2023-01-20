<?php
/**
 * Tests functions in utils.php.
 *
 * @since 1.0.0
 * @package TenUp\AutoshareForTwitter
 */

namespace TenUp\AutoshareForTwitter\Tests;

use \WP_UnitTestCase;
use function TenUp\AutoshareForTwitter\Core\set_post_type_supports_with_custom_columns;
use function TenUp\AutoshareForTwitter\Utils\get_autoshare_for_twitter_meta;
use function TenUp\AutoshareForTwitter\Utils\opted_into_autoshare_for_twitter;
use function TenUp\AutoshareForTwitter\Utils\update_autoshare_for_twitter_meta;
use function TenUp\AutoshareForTwitter\Utils\delete_autoshare_for_twitter_meta;
use function TenUp\AutoshareForTwitter\Utils\autoshare_enabled;
use function TenUp\AutoshareForTwitter\Utils\tweet_image_allowed;
use function TenUp\AutoshareForTwitter\Utils\get_available_post_types;
use function TenUp\AutoshareForTwitter\Utils\get_autoshare_for_twitter_settings;
use const TenUp\AutoshareForTwitter\Core\Post_Meta\META_PREFIX;
use const \TenUp\AutoshareForTwitter\Core\Post_Meta\TWEET_ALLOW_IMAGE;

/**
 * TestUtils class.
 *
 * @sincd 1.0.0
 */
class TestUtils extends WP_UnitTestCase {

	/**
	 * Tests the get_autoshare_for_twitter_meta utility function.
	 *
	 * @since 1.0.0
	 */
	public function test_get_autoshare_for_twitter_meta() {
		$post = $this->factory->post->create();

		$this->assertEquals( null, get_autoshare_for_twitter_meta( $post, 'some-key' ) );

		update_post_meta( $post, sprintf( '%s_%s', META_PREFIX, 'some-key' ), 'some-data' );

		$this->assertEquals( 'some-data', get_autoshare_for_twitter_meta( $post, 'some-key' ) );
	}

	/**
	 * Tests the update_autoshare_for_twitter_meta utility function.
	 *
	 * @since 1.0.0
	 */
	public function test_update_autoshare_for_twitter_meta() {
		$post = $this->factory->post->create();
		update_autoshare_for_twitter_meta( $post, 'some-update-key', 1234 );

		$this->assertEquals( 1234, get_post_meta( $post, sprintf( '%s_%s', META_PREFIX, 'some-update-key' ), true ) );
	}

	/**
	 * Tests the delete_autoshare_for_twitter_meta utility function.
	 *
	 * @since 1.0.0
	 */
	public function test_delete_autoshare_for_twitter_meta() {
		$post = $this->factory->post->create();
		update_post_meta( $post, sprintf( '%s_%s', META_PREFIX, 'some-delete-key' ), '4321' );

		delete_autoshare_for_twitter_meta( $post, 'some-delete-key' );

		$this->assertFalse( metadata_exists( 'post', $post, sprintf( '%s_%s', META_PREFIX, 'some-delete-key' ) ) );
	}

	/**
	 * Tests the opted_into_autoshare_for_twitter function.
	 *
	 * @since 1.0.0
	 */
	public function test_opted_into_autoshare_for_twitter() {
		$post = $this->factory->post->create();

		$this->assertTrue( opted_into_autoshare_for_twitter( $post ) );

		$post_type  = register_non_default_post_type();
		$other_post = $this->factory->post->create( compact( 'post_type' ) );

		$this->assertFalse( opted_into_autoshare_for_twitter( $other_post ) );
	}

	/**
	 * Tests the autoshare_enabled function.
	 *
	 * @since 1.1.0
	 */
	public function test_autoshare_enabled() {
		$post = $this->factory->post->create();

		$authoshare_enabled = function( $enabled, $post_type, $id ) use ( $post ) {
			if ( intval( $post ) === intval( $id ) ) {
				return true;
			}

			return true;
		};
		add_filter( 'autoshare_for_twitter_enabled_default', $authoshare_enabled, 99, 3 );
		$this->assertTrue( autoshare_enabled( $post ) );
		remove_filter( 'autoshare_for_twitter_enabled_default', $authoshare_enabled, 99 );

		$post_type  = register_non_default_post_type();
		$other_post = $this->factory->post->create( compact( 'post_type' ) );
		$this->assertFalse( autoshare_enabled( $other_post ) );
	}

	/**
	 * Tests the tweet_image_allowed function.
	 *
	 * @since 1.3.0
	 */
	public function test_tweet_image_allowed() {
		$post_id = $this->factory->post->create();

		update_autoshare_for_twitter_meta( $post_id, TWEET_ALLOW_IMAGE, 'yes' );
		$this->assertTrue( tweet_image_allowed( $post_id ) );

		update_autoshare_for_twitter_meta( $post_id, TWEET_ALLOW_IMAGE, 'no' );
		$this->assertFalse( tweet_image_allowed( $post_id ) );

		delete_autoshare_for_twitter_meta( $post_id, TWEET_ALLOW_IMAGE );
		$is_allowed = (bool) get_autoshare_for_twitter_settings( 'enable_upload' );
		$this->assertSame( $is_allowed, tweet_image_allowed( $post_id ) );

		add_filter( 'autoshare_for_twitter_tweet_image_allowed', '__return_false' );
		$this->assertFalse( tweet_image_allowed( $post_id ) );
		remove_filter( 'autoshare_for_twitter_tweet_image_allowed', '__return_false' );
	}

	/**
	 * Tests the get_autoshare_for_twitter_settings function.
	 *
	 * @since 1.1.0
	 */
	public function test_get_autoshare_for_twitter_settings() {
		// Test that posts and pages support the feature by default, but not other post types.
		reset_post_type_support();
		set_post_type_supports_with_custom_columns();

		$support_post_types = get_available_post_types();
		$default_settings   = get_autoshare_for_twitter_settings();

		$this->assertTrue( empty( array_diff( $default_settings['post_types'], $support_post_types ) ) );
	}
}
