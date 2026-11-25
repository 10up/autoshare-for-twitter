<?php
/**
 * Tests functionality implemented by the Publish_Tweet class.
 *
 * @since 0.1.0
 * @package TenUp\AutoshareForTwitter
 */

namespace TenUp\AutoshareForTwitter\Tests;

use TenUp\AutoshareForTwitter\Core\Publish_Tweet\Publish_Tweet;
use WP_UnitTestCase;
use function TenUp\AutoshareForTwitter\Utils\compose_tweet_body;

/**
 * Tests for the Publish_Tweet class.
 */
class PublishTweetTest extends WP_UnitTestCase {
	/**
	 * Instance under test.
	 *
	 * @var Publish_Tweet
	 */
	private $publish_tweet;

	/**
	 * Setup method.
	 */
	public function setUp(): void {
		parent::setUp();

		$this->publish_tweet = new Publish_Tweet();
	}

	/**
	 * Tear down method.
	 */
	public function tearDown(): void {
		parent::tearDown();
		$this->remove_added_uploads();
	}

	/**
	 * Tests the get_upload_data_media_id method.
	 *
	 * @return void
	 */
	public function test_get_upload_data_media_id() {
		$post       = $this->factory->post->create_and_get();
		$attachment = $this->factory->attachment->create_upload_object( DIR_TESTDATA . '/images/33772.jpg', $post->ID );
		set_post_thumbnail( $post, $attachment );

		add_filter( 'autoshare_for_twitter_attached_image', '__return_false' );
		$this->assertNull( $this->publish_tweet->get_upload_data_media_id( $post ) );
		remove_filter( 'autoshare_for_twitter_attached_image', '__return_false' );

		$filter_media_upload_id = function() {
			return 999;
		};

		add_filter( 'autoshare_for_twitter_pre_media_upload', $filter_media_upload_id );
		$this->assertEquals( 999, $this->publish_tweet->get_upload_data_media_id( $post ) );
		remove_filter( 'autoshare_for_twitter_pre_media_upload', $filter_media_upload_id );
	}

	/**
	 * Tests the get_largest_acceptable_image method.
	 */
	public function test_get_largest_acceptable_imagel() {
		$attachment             = $this->factory->attachment->create_upload_object( DIR_TESTDATA . '/images/33772.jpg' ); // ~172KB image.
		$set_150kb_max_filesize = function() {
			return 150000;
		};
		add_filter( 'autoshare_for_twitter_max_image_size', $set_150kb_max_filesize );
		$file = $this->publish_tweet->get_largest_acceptable_image(
			get_attached_file( $attachment ),
			wp_get_attachment_metadata( $attachment )['sizes']
		);

		$this->assertEquals( wp_upload_dir()['path'] . '/33772-1536x864.jpg', $file );
		$attachment = $this->factory->attachment->create_upload_object( DIR_TESTDATA . '/images/2004-07-22-DSC_0008.jpg' ); // ~109kb image.
		$file       = $this->publish_tweet->get_largest_acceptable_image(
			get_attached_file( $attachment ),
			wp_get_attachment_metadata( $attachment )['sizes']
		);
		$this->assertEquals( wp_upload_dir()['path'] . '/2004-07-22-DSC_0008.jpg', $file );
		remove_filter( 'autoshare_for_twitter_max_image_size', $set_150kb_max_filesize );
		$set_1kb_max_filesize = function() {
			return 1000;
		};
		add_filter( 'autoshare_for_twitter_max_image_size', $set_1kb_max_filesize );
		$file = $this->publish_tweet->get_largest_acceptable_image(
			get_attached_file( $attachment ),
			wp_get_attachment_metadata( $attachment )['sizes']
		);
		$this->assertNull( $file );

		remove_filter( 'autoshare_for_twitter_max_image_size', $set_1kb_max_filesize );
	}

	/**
	 * Tests adding query args to Tweet URL through the 'autoshare_for_twitter_post_url' filter.
	 */
	public function test_filter_url_with_params() {
		$post = $this->factory->post->create_and_get();

		$params_array = array(
			'utm_source'   => 'x',
			'utm_medium'   => 'social',
			'utm_campaign' => 'my_test',
			'utm_content'  => 'auto-tweet',
			'utm_term'     => 'post',
		);

		// Set up the expected URL result.
		$expected_url = add_query_arg( $params_array, get_permalink( $post ) );
		add_filter(
			'autoshare_for_twitter_post_url',
			function ( $url ) use ( $params_array ) {
				return add_query_arg( $params_array, $url );
			}
		);

		// Capture what would be sent to X instead of sending it.
		$sent = null;
		add_filter(
			'autoshare_for_twitter_pre_status_update',
			function ( $response, $update_data ) use ( &$sent ) {
				$sent = $update_data;
				return (object) array( 'id' => 1 );
			},
			10,
			2
		);

		$this->publish_tweet->status_update( compose_tweet_body( $post ), $post );

		$this->assertIsArray( $sent, 'The tweet was never handed to the X API.' );
		$this->assertStringEndsWith( ' ' . $expected_url, $sent['text'] );
	}
}
