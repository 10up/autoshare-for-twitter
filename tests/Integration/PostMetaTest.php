<?php
/**
 * Tests functions admin/post-meta.php.
 *
 * @since 1.0.0
 * @package TenUp\AutoshareForTwitter
 */

namespace TenUp\AutoshareForTwitter\Tests;

use WP_UnitTestCase;

use const TenUp\AutoshareForTwitter\Core\Post_Meta\ENABLE_AUTOSHARE_FOR_TWITTER_KEY;
use const TenUp\AutoshareForTwitter\Core\Post_Meta\TWEET_BODY_KEY;
use const TenUp\AutoshareForTwitter\Core\Post_Meta\TWITTER_STATUS_KEY;

use function TenUp\AutoshareForTwitter\Core\Post_Meta\get_tweet_status_message;
use function TenUp\AutoshareForTwitter\Core\Post_Meta\get_tweet_status_logs;
use function TenUp\AutoshareForTwitter\Core\Post_Meta\save_autoshare_for_twitter_meta_data;
use function TenUp\AutoshareForTwitter\Utils\get_autoshare_for_twitter_meta;
use function TenUp\AutoshareForTwitter\Utils\update_autoshare_for_twitter_meta;

/**
 * TestUtils class.
 *
 * @sincd 1.0.0
 *
 * @group post_meta
 */
class PostMetaTest extends WP_UnitTestCase {

	/**
	 * Tests the get_tweet_status_message function.
	 */
	public function test_get_tweet_status_message() {
		$this->assertEquals(
			[
				'message' => array(),
			],
			get_tweet_status_message( -1 )
		);

		$post = $this->factory->post->create( [ 'post_status' => 'publish' ] );

		$published_filter = function( $data, $id, $key ) use ( $post ) {
			if ( intval( $post ) === intval( $id ) && TWITTER_STATUS_KEY === $key ) {
				return [
					'status'     => 'published',
					'created_at' => '2017-01-01',
					'twitter_id' => 444,
					'handle'     => '',
				];
			}

			return $data;
		};
		add_filter( 'autoshare_for_twitter_meta', $published_filter, 10, 3 );
		$this->assertEquals(
			[
				'message' => [
					[
						'message' => 'Posted to X/Twitter on 2017-01-01 @ 12:00AM',
						'url'     => 'https://twitter.com//status/444',
						'status'  => 'published',
						'handle'  => '',
					],
				],
			],
			get_tweet_status_message( $post )
		);

		remove_filter( 'autoshare_for_twitter_meta', $published_filter );

		$failed_filter = function( $data, $id, $key ) use ( $post ) {
			if ( intval( $post ) === intval( $id ) && TWITTER_STATUS_KEY === $key ) {
				return [
					'status'  => 'error',
					'message' => 'There was an error.',
					'handle'  => '',
				];
			}

			return $data;
		};
		add_filter( 'autoshare_for_twitter_meta', $failed_filter, 10, 3 );

		$this->assertEquals(
			[
				'message' => [
					[
						'message' => 'Failed to post to X/Twitter; There was an error.',
						'status'  => 'error',
						'handle'  => '',
					],
				],
			],
			get_tweet_status_message( $post )
		);
		remove_filter( 'autoshare_for_twitter_meta', $failed_filter );

		$unknown_filter = function( $data, $id, $key ) use ( $post ) {
			if ( intval( $post ) === intval( $id ) && TWITTER_STATUS_KEY === $key ) {
				return [
					'status'  => 'unknown',
					'message' => 'There was an error.',
					'handle'  => '',
				];
			}

			return $data;
		};
		add_filter( 'autoshare_for_twitter_meta', $unknown_filter, 10, 3 );
		$this->assertEquals(
			[
				'message' => [
					[
						'message' => 'There was an error.',
						'status'  => 'unknown',
						'handle'  => '',
					],
				],
			],
			get_tweet_status_message( $post )
		);
		remove_filter( 'autoshare_for_twitter_meta', $unknown_filter );

		$other_filter = function( $data, $id, $key ) use ( $post ) {
			if ( intval( $post ) === intval( $id ) && TWITTER_STATUS_KEY === $key ) {
				return [
					'status' => 'other',
				];
			}

			return $data;
		};
		add_filter( 'autoshare_for_twitter_meta', $other_filter, 10, 3 );
		$this->assertEquals(
			[
				'message' => [
					[
						'message' => 'This post has not been posted to X/Twitter.',
						'status'  => 'other',
					],
				],
			],
			get_tweet_status_message( $post )
		);
		remove_filter( 'autoshare_for_twitter_meta', $other_filter );
	}

	/**
	 * An error message from X is shown as text in the classic editor's status
	 * log, never as HTML.
	 */
	public function test_error_message_from_x_is_escaped_in_status_log() {
		$post = $this->factory->post->create( array( 'post_status' => 'publish' ) );
		update_autoshare_for_twitter_meta(
			$post,
			TWITTER_STATUS_KEY,
			array(
				'status'  => 'error',
				'message' => '<img src=x onerror=alert(1)>Duplicate content',
				'handle'  => '',
			)
		);

		$logs = get_tweet_status_logs( $post );

		$this->assertStringContainsString( '&lt;img src=x onerror=alert(1)&gt;Duplicate content', $logs );
		$this->assertStringNotContainsString( '<img', $logs );
	}

	/**
	 * Provides test data.
	 *
	 * @return array
	 */
	public function save_autoshare_for_twitter_meta_data_provider() {
		return [
			[
				// Test autoshare is disabled when no data is passed.
				[ 'post_status' => 'publish' ],
				[],
				false,
			],
			[
				// Test autoshare is disabled when false is passed.
				[ 'post_status' => 'publish' ],
				[ ENABLE_AUTOSHARE_FOR_TWITTER_KEY => '0' ],
				false,
			],
			[
				// Test autoshare is disabled when only a tweet body is passed.
				[ 'post_status' => 'publish' ],
				[ TWEET_BODY_KEY => 'my cool tweet' ],
				false,
			],
			[
				// Test autoshare is enabled when true is passed.
				[ 'post_status' => 'publish' ],
				[ ENABLE_AUTOSHARE_FOR_TWITTER_KEY => '1' ],
				true,
			],
		];
	}

	/**
	 * Tests the save_autoshare_for_twitter_meta_data function.
	 *
	 * @dataProvider save_autoshare_for_twitter_meta_data_provider
	 *
	 * @param array   $args Create post args.
	 * @param array   $data Meta data to save.
	 * @param boolean $expected Expecte result.
	 */
	public function test_save_autoshare_for_twitter_meta_data( $args, $data, $expected ) {
		// Autopost is off by default here, so only an explicit opt-in enables it.
		add_filter( 'autoshare_for_twitter_enabled_default', '__return_false', 20 );

		$id = $this->factory->post->create( $args );
		save_autoshare_for_twitter_meta_data( $id, $data );

		if ( $expected ) {
			$this->assertTrue( (bool) get_autoshare_for_twitter_meta( $id, ENABLE_AUTOSHARE_FOR_TWITTER_KEY ) );
		} else {
			$this->assertFalse( (bool) get_autoshare_for_twitter_meta( $id, ENABLE_AUTOSHARE_FOR_TWITTER_KEY ) );
		}
	}
}
