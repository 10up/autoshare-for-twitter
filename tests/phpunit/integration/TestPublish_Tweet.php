<?php
/**
 * Tests functionality implemented by the Publish_Tweet class.
 * 
 * @since 0.1.0
 * @package TenUp\AutoTweet
 */

namespace TenUp\AutoTweet\Tests;

use TenUp\AutoTweet\Core\Publish_Tweet\Publish_Tweet;
use WP_UnitTestCase;

class TestPublish_Tweet extends WP_UnitTestCase {
	/**
	 * Setup method.
	 */
	public function setUp() {
		parent::setUp();
		
		$this->publish_tweet = new Publish_Tweet();
	}
	public function tearDown() {
		parent::tearDown();
		$this->remove_added_uploads();
	}
	
	/**
	 * Tests the get_upload_data_media_id method.
	 *
	 * @return void
	 */
	public function test_get_upload_data_media_id() {
		$post = $this->factory->post->create_and_get();
		$attachment = $this->factory->attachment->create_upload_object( DIR_TESTDATA .'/images/33772.jpg', $post->ID );
		set_post_thumbnail( $post, $attachment );

		add_filter( 'tenup_autotweet_attached_image', '__return_false' );
		$this->assertNull( $this->publish_tweet->get_upload_data_media_id( $post ) );
		remove_filter( 'tenup_autotweet_attached_image', '__return_false' );

		$filter_media_upload_id = function() {
			return 999;
		};

		add_filter( 'tenup_autotweet_pre_media_upload', $filter_media_upload_id );
		$this->assertEquals( 999, $this->publish_tweet->get_upload_data_media_id( $post ) );
		remove_filter( 'tenup_autotweet_pre_media_upload', $filter_media_upload_id );
	}

	/**
	 * Tests the get_largest_acceptable_image method.
	 */
	public function test_get_largest_acceptable_imagel() {
		$attachment = $this->factory->attachment->create_upload_object( DIR_TESTDATA .'/images/33772.jpg' ); // ~172KB image.
		$set_150kb_max_filesize = function() {
			return 150000;
		};
		add_filter( 'tenup_autotweet_max_image_size', $set_150kb_max_filesize );
		$file = $this->publish_tweet->get_largest_acceptable_image(
			get_attached_file( $attachment ),
			wp_get_attachment_metadata( $attachment )['sizes']
		);
		$this->assertEquals( sprintf( '/tmp/wordpress/wp-content/uploads/2019/09/33772-1024x576.jpg', date( 'Y' ), date( 'm' ) ), $file );
		$attachment = $this->factory->attachment->create_upload_object( DIR_TESTDATA .'/images/2004-07-22-DSC_0008.jpg' ); // ~109kb image.
		$file = $this->publish_tweet->get_largest_acceptable_image(
			get_attached_file( $attachment ),
			wp_get_attachment_metadata( $attachment )['sizes']
		);
		$this->assertEquals( sprintf( '/tmp/wordpress/wp-content/uploads/%s/%s/2004-07-22-DSC_0008.jpg', date( 'Y' ), date( 'm' ) ), $file );
		remove_filter( 'tenup_autotweet_max_image_size', $set_150kb_max_filesize );
		
		$set_1kb_max_filesize = function() {
			return 1000;
		};
		add_filter( 'tenup_autotweet_max_image_size', $set_1kb_max_filesize );
		$file = $this->publish_tweet->get_largest_acceptable_image(
			get_attached_file( $attachment ),
			wp_get_attachment_metadata( $attachment )['sizes']
		);
		$this->assertNull( $file );
		
		remove_filter( 'tenup_autotweet_max_image_size', $set_1kb_max_filesize );
	}
}
