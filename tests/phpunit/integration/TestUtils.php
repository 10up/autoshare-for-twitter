<?php
/**
 * Tests functions in utils.php.
 *
 * @since 1.0.0
 * @package TenUp\Autoshare
 */

namespace TenUp\Autoshare\Tests;

use \WP_UnitTestCase;
use function TenUp\Autoshare\Utils\{get_autoshare_meta, opted_into_autoshare, update_autoshare_meta, delete_autoshare_meta};
use const TenUp\Autoshare\Core\Post_Meta\META_PREFIX;

/**
 * TestUtils class.
 *
 * @sincd 1.0.0
 */
class TestUtils extends WP_UnitTestCase {

	/**
	 * Tests the get_autoshare_meta utility function.
	 *
	 * @since 1.0.0
	 */
	public function test_get_autoshare_meta() {
		$post = $this->factory->post->create();

		$this->assertEquals( null, get_autoshare_meta( $post, 'some-key' ) );

		update_post_meta( $post, sprintf( '%s_%s', META_PREFIX, 'some-key' ), 'some-data' );

		$this->assertEquals( 'some-data', get_autoshare_meta( $post, 'some-key' ) );
	}

	/**
	 * Tests the update_autoshare_meta utility function.
	 *
	 * @since 1.0.0
	 */
	public function test_update_autoshare_meta() {
		$post = $this->factory->post->create();
		update_autoshare_meta( $post, 'some-update-key', 1234 );

		$this->assertEquals( 1234, get_post_meta( $post, sprintf( '%s_%s', META_PREFIX, 'some-update-key' ), true ) );
	}

	/**
	 * Tests the delete_autoshare_meta utility function.
	 *
	 * @since 1.0.0
	 */
	public function test_delete_autoshare_meta() {
		$post = $this->factory->post->create();
		update_post_meta( $post, sprintf( '%s_%s', META_PREFIX, 'some-delete-key' ), '4321' );

		delete_autoshare_meta( $post, 'some-delete-key' );

		$this->assertFalse( metadata_exists( 'post', $post, sprintf( '%s_%s', META_PREFIX, 'some-delete-key' ) ) );
	}

	/**
	 * Tests the opted_into_autoshare function.
	 *
	 * @since 1.0.0
	 */
	public function test_opted_into_autoshare() {
		$post = $this->factory->post->create();

		$this->assertTrue( opted_into_autoshare( $post ) );

		$post_type  = register_non_default_post_type();
		$other_post = $this->factory->post->create( compact( 'post_type' ) );

		$this->assertFalse( opted_into_autoshare( $other_post ) );
	}
}
