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
use const TenUp\AutoshareForTwitter\Core\Post_Meta\TWEET_ACCOUNTS_KEY;

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
	public function setUp(): void {
		$this->assertTrue(
			check_method_exists(
				'transition_post_status',
				'TenUp\AutoshareForTwitter\Core\Post_Transition\maybe_publish_tweet'
			)
		);
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
				'1',
				'draft',
				'publish',
				false,
			],
			[
				// Already-published post should not tweet.
				[ 'post_status' => 'publish' ],
				'1',
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
				'1',
				'publish',
				'draft',
				true,
			],
			[
				// Post transitioning from draft to publish should not tweet if autotweet is not true.
				[ 'post_status' => 'draft' ],
				'0',
				'publish',
				'draft',
				false,
			],
		];
	}

	/**
	 * Tests the maybe_publish_tweet function.
	 *
	 * @dataProvider maybe_publish_tweet_provider
	 *
	 * @param array   $post_args Args to pass to the create post function.
	 * @param boolean $autoshare_enabled_form_data Updated autoshare enabled meta value.
	 * @param string  $new_status The new post status.
	 * @param string  $old_status The old post status.
	 * @param boolean $expected_should_tweet Whether the post should be tweeted.
	 */
	public function test_maybe_publish_tweet(
		$post_args,
		$autoshare_enabled_form_data,
		$new_status,
		$old_status,
		$expected_should_tweet
	) {
		$the_post                   = $this->factory->post->create_and_get( $post_args );
		$post_was_tweeted           = false;
		$pre_status_update_callback = function() use ( &$post_was_tweeted ) {
			$post_was_tweeted = true;

			// Minimum valid response.
			return (object) [
				'id'         => 1,
				'created_at' => time(),
			];
		};
		add_filter( 'autoshare_for_twitter_pre_status_update', $pre_status_update_callback );

		$post_form_data_callback = function() use ( $autoshare_enabled_form_data ) {
			return [
				ENABLE_AUTOSHARE_FOR_TWITTER_KEY => $autoshare_enabled_form_data,
				TWEET_ACCOUNTS_KEY               => [ 'DUMMY_ACCOUNT_ID' ],
			];
		};
		add_filter( 'autoshare_post_form_data', $post_form_data_callback );

		maybe_publish_tweet( $new_status, $old_status, $the_post );

		if ( $expected_should_tweet ) {
			$this->assertTrue( $post_was_tweeted );
		} else {
			$this->assertFalse( $post_was_tweeted );
		}

		remove_filter( 'autoshare_for_twitter_pre_status_update', $pre_status_update_callback );
		remove_filter( 'autoshare_post_form_data', $post_form_data_callback );
	}
}
