<?php
/**
 * Tests functions admin/post-meta.php.
 *
 * @since 1.0.0
 * @package TenUp\AutoshareForTwitter
 */

namespace TenUp\AutoshareForTwitter\Tests;

use WP_UnitTestCase;
use TenUp\AutoshareForTwitter\Core\AST_Staging\AST_Staging;
use function TenUp\AutoshareForTwitter\Utils\update_autoshare_for_twitter_meta;

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
class PostTransitionTest extends WP_UnitTestCase {
	/**
	 * Setup.
	 */
	public function setUp(): void {
		parent::setUp();

		wp_set_current_user( 1 );
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

	/**
	 * Creates a scheduled post, as the editor leaves it before cron runs.
	 *
	 * @return int Post ID.
	 */
	private function create_scheduled_post() {
		AST_Staging::set_autoshare_live_url_lock();
		connect_test_accounts();

		return $this->factory->post->create(
			array(
				'post_status' => 'future',
				'post_date'   => gmdate( 'Y-m-d H:i:s', time() + DAY_IN_SECONDS ),
			)
		);
	}

	/**
	 * A scheduled post tweets with its saved settings when cron publishes it,
	 * where nobody is logged in.
	 */
	public function test_scheduled_post_tweets_when_cron_publishes_it() {
		$post_id = $this->create_scheduled_post();
		update_autoshare_for_twitter_meta( $post_id, ENABLE_AUTOSHARE_FOR_TWITTER_KEY, 1 );
		update_autoshare_for_twitter_meta( $post_id, TWEET_ACCOUNTS_KEY, array( 'A2' ) );
		$spy = new Tweet_Spy();

		wp_set_current_user( 0 );
		wp_publish_post( $post_id );

		$this->assertCount( 1, $spy->sent );
		$this->assertSame( 'publish', get_post_status( $post_id ) );
	}

	/**
	 * A post created without the editor (no account selection saved) goes to the
	 * accounts marked "Autopost by default" in the settings.
	 */
	public function test_post_without_account_selection_uses_default_accounts() {
		update_option(
			'autoshare-for-twitter',
			array(
				'enable_default'     => 1,
				'autoshare_accounts' => array( 'A1', 'A2' ),
			)
		);
		$post_id = $this->create_scheduled_post();
		$spy     = new Tweet_Spy();

		wp_set_current_user( 0 );
		wp_publish_post( $post_id );

		$this->assertCount( 2, $spy->sent );
	}
}
