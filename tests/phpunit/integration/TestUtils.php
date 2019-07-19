<?php
/**
 * Tests functions in utils.php.
 *
 * @since 1.0.0
 * @package TenUp\Auto_Tweet
 */

namespace TenUp\Auto_Tweet\Tests;

use \WP_UnitTestCase;
use function TenUp\Auto_Tweet\Utils\{get_auto_tweet_meta, opted_into_autotweet};
use const TenUp\Auto_Tweet\Core\Post_Meta\META_PREFIX;

/**
 * TestUtils class.
 *
 * @sincd 1.0.0
 */
class TestUtils extends WP_UnitTestCase {

	/**
	 * Tests the get_auto_tweet_meta utility function.
	 *
	 * @since 1.0.0
	 */
	public function test_get_auto_tweet_meta() {
		$post = $this->factory->post->create();

		$this->assertEquals( null, get_auto_tweet_meta( $post, 'some-key' ) );

		update_post_meta( $post, sprintf( '%s_%s', META_PREFIX, 'some-key' ), 'some-data' );

		$this->assertEquals( 'some-data', get_auto_tweet_meta( $post, 'some-key' ) );
	}

	/**
	 * Tests the opted_into_autotweet function.
	 *
	 * @since 1.0.0
	 */
	public function test_opted_into_autotweet() {
		$post = $this->factory->post->create();

		$this->assertTrue( opted_into_autotweet( $post ) );

		$post_type = register_non_default_post_type();
		$other_post = $this->factory->post->create( compact( 'post_type' ) );
		
		$this->assertFalse( opted_into_autotweet( $other_post ) );
	}
}
