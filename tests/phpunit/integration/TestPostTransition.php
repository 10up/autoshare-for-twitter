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

use function TenUp\AutoshareForTwitter\Core\Post_Transition\maybe_publish_tweet;

/**
 * TestPostTransition
 *
 * @group post_transition
 *
 * @sincd 1.0.0
 */
class TestPostTransition extends WP_UnitTestCase {
	/**
	 * Setup.
	 */
	public function setUp() {
		wp_set_current_user( 1 );

		parent::setUp();
	}

	/**
	 * Provides test data.
	 *
	 * @return array
	 */
	public function maybe_publish_tweet_provider() {
		return [
			[
				// Post transitioning from publish to draft should not tweet.
				[ 'post_status' => 'publish' ],
				'draft',
				'publish',
				false,
			],
			[
				// Already-published post should not tweet.
				[ 'post_status' => 'publish' ],
				'publish',
				'publish',
				false,
			],
			[
				// Post transitioning from draft to publish should tweet if autotweet_enabled is true.
				[
					'post_status' => 'draft',
					'post_title'  => 'TEST',
					'post_author' => 1,
				],
				'publish',
				'draft',
				true,
			],
		];
	}

	/**
	 * Tests the maybe_publish_tweet function.
	 *
	 * @dataProvider maybe_publish_tweet_provider
	 *
	 * @param array   $post_args Args to pass to the create post function.
	 * @param string  $new_status The new post status.
	 * @param string  $old_status The old post status.
	 * @param boolean $expected_should_tweet Whether the post should be tweeted.
	 */
	public function test_maybe_publish_tweet(
		$post_args,
		$new_status,
		$old_status,
		$expected_should_tweet
	) {
		global $wp_filter;
		$the_post                   = $this->factory->post->create_and_get( $post_args );

		$hooks = count( $wp_filter['save_post']->callbacks[10] );
		maybe_publish_tweet( $new_status, $old_status, $the_post );
		$new_hooks = count( $wp_filter['save_post']->callbacks[10] );

		if ( $expected_should_tweet ) {
			$this->assertGreaterThan( $hooks, $new_hooks );
		} else {
			$this->assertEquals( $hooks, $new_hooks );
		}

	}
}
