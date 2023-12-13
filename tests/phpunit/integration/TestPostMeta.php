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
use function TenUp\AutoshareForTwitter\Core\Post_Meta\markup_error;
use function TenUp\AutoshareForTwitter\Core\Post_Meta\markup_published;
use function TenUp\AutoshareForTwitter\Core\Post_Meta\markup_unknown;
use function TenUp\AutoshareForTwitter\Core\Post_Meta\save_autoshare_for_twitter_meta_data;
use function TenUp\AutoshareForTwitter\Utils\get_autoshare_for_twitter_meta;
use function TenUp\AutoshareForTwitter\Utils\date_from_twitter;
use function TenUp\AutoshareForTwitter\Utils\link_from_twitter;

/**
 * TestUtils class.
 *
 * @sincd 1.0.0
 *
 * @group post_meta
 */
class TestPostMeta extends WP_UnitTestCase {

	/**
	 * Test all methods and hooks in setup().
	 */
	public function test_setup_hooks() {
		$this->assertTrue(
			check_method_exists(
				'add_meta_boxes',
				'TenUp\AutoshareForTwitter\Core\Post_Meta\autoshare_for_twitter_metabox'
			)
		);

		$this->assertTrue(
			check_method_exists(
				'save_post',
				'TenUp\AutoshareForTwitter\Core\Post_Meta\save_tweet_meta'
			)
		);
	}

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
		// Make sure the rendered markup is as expected in post metabox.
		$twitter_status = get_autoshare_for_twitter_meta( $post, TWITTER_STATUS_KEY );
		$this->assertEquals(
			sprintf(
				'<div class="autoshare-for-twitter-status-log-data"><strong>%s</strong><br/> <span>%s</span> (<a href="%s" target="_blank">View</a>)<strong></strong></div>',
				esc_html__( 'Posted to X/Twitter on', 'autoshare-for-twitter' ),
				esc_html( date_from_twitter( $twitter_status['created_at'] ) ),
				esc_url( link_from_twitter( $twitter_status ) )
			),
			markup_published( $twitter_status )
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

		$noo = get_tweet_status_message( $post );
		$this->assertEquals(
			[
				'message' => [
					[
						'message' => 'Failed to tweet; There was an error.',
						'status'  => 'error',
						'handle'  => '',
					],
				],
			],
			get_tweet_status_message( $post )
		);
		// Make sure the rendered markup is as expected in post metabox.
		$twitter_status = get_autoshare_for_twitter_meta( $post, TWITTER_STATUS_KEY );
		$this->assertEquals(
			sprintf(
				'<div class="autoshare-for-twitter-status-log-data"><strong>%s</strong><br/><pre>%s</pre></div>',
				esc_html__( 'Failed to tweet', 'autoshare-for-twitter' ),
				esc_html( $twitter_status['message'] )
			),
			markup_error( $twitter_status )
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
		// Make sure the rendered markup is as expected in post metabox.
		$twitter_status = get_autoshare_for_twitter_meta( $post, TWITTER_STATUS_KEY );
		$this->assertEquals( '<div class="autoshare-for-twitter-status-log-data">' . $twitter_status['message'] . '</div>', markup_unknown( $twitter_status ) );
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
						'message' => 'This post was not tweeted.',
						'status'  => 'other',
					],
				],
			],
			get_tweet_status_message( $post )
		);
		remove_filter( 'autoshare_for_twitter_meta', $other_filter );
	}

	/**
	 * Provides test data.
	 *
	 * @return array
	 */
	public function save_autoshare_for_twitter_meta_data_provider() {
		add_filter( 'autoshare_for_twitter_enabled_default', '__return_false', 20 );
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
		$id = $this->factory->post->create( $args );
		save_autoshare_for_twitter_meta_data( $id, $data );

		if ( $expected ) {
			$this->assertTrue( (bool) get_autoshare_for_twitter_meta( $id, ENABLE_AUTOSHARE_FOR_TWITTER_KEY ) );
		} else {
			$this->assertFalse( (bool) get_autoshare_for_twitter_meta( $id, ENABLE_AUTOSHARE_FOR_TWITTER_KEY ) );
		}
	}
}
