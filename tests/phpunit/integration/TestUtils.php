<?php
/**
 * Tests functions in utils.php.
 *
 * @since 1.0.0
 * @package TenUp\AutoTweet
 */

namespace TenUp\AutoTweet\Tests;

use \WP_UnitTestCase;
use function TenUp\AutoTweet\Utils\{get_autotweet_meta, opted_into_autotweet, update_autotweet_meta, delete_autotweet_meta};
use const TenUp\AutoTweet\Core\Post_Meta\META_PREFIX;

/**
 * TestUtils class.
 *
 * @sincd 1.0.0
 */
class TestUtils extends WP_UnitTestCase {

	/**
	 * Tests the get_autotweet_meta utility function.
	 *
	 * @since 1.0.0
	 */
	public function test_get_autotweet_meta() {
		$post = $this->factory->post->create();

		$this->assertEquals( null, get_autotweet_meta( $post, 'some-key' ) );

		update_post_meta( $post, sprintf( '%s_%s', META_PREFIX, 'some-key' ), 'some-data' );

		$this->assertEquals( 'some-data', get_autotweet_meta( $post, 'some-key' ) );
	}

	/**
	 * Tests the update_autotweet_meta utility function.
	 *
	 * @since 1.0.0
	 */
	public function test_update_autotweet_meta() {
		$post = $this->factory->post->create();
		update_autotweet_meta( $post, 'some-update-key', 1234 );

		$this->assertEquals( 1234, get_post_meta( $post, sprintf( '%s_%s', META_PREFIX, 'some-update-key' ), true ) );
	}

	/**
	 * Tests the delete_autotweet_meta utility function.
	 *
	 * @since 1.0.0
	 */
	public function test_delete_autotweet_meta() {
		$post = $this->factory->post->create();
		update_post_meta( $post, sprintf( '%s_%s', META_PREFIX, 'some-delete-key' ), '4321' );

		delete_autotweet_meta( $post, 'some-delete-key' );

		$this->assertFalse( metadata_exists( 'post', $post, sprintf( '%s_%s', META_PREFIX, 'some-delete-key' ) ) );
	}

	/**
	 * Tests the opted_into_autotweet function.
	 *
	 * @since 1.0.0
	 */
	public function test_opted_into_autotweet() {
		$post = $this->factory->post->create();

		$this->assertTrue( opted_into_autotweet( $post ) );

		$post_type  = register_non_default_post_type();
		$other_post = $this->factory->post->create( compact( 'post_type' ) );

		$this->assertFalse( opted_into_autotweet( $other_post ) );
	}
}
