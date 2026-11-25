<?php
/**
 * Tests what the plugin records after X answers.
 *
 * @package TenUp\AutoshareForTwitter
 */

namespace TenUp\AutoshareForTwitter\Tests;

use WP_UnitTestCase;
use TenUp\AutoshareForTwitter\Core\AST_Staging\AST_Staging;
use function TenUp\AutoshareForTwitter\Core\Post_Transition\publish_tweet;
use function TenUp\AutoshareForTwitter\Utils\get_autoshare_for_twitter_meta;
use function TenUp\AutoshareForTwitter\Utils\update_autoshare_for_twitter_meta;
use const TenUp\AutoshareForTwitter\Core\Post_Meta\ENABLE_AUTOSHARE_FOR_TWITTER_KEY;
use const TenUp\AutoshareForTwitter\Core\Post_Meta\TWEET_ACCOUNTS_KEY;
use const TenUp\AutoshareForTwitter\Core\Post_Meta\TWEET_BODY_KEY;
use const TenUp\AutoshareForTwitter\Core\Post_Meta\TWITTER_STATUS_KEY;

/**
 * Publish result tests.
 */
class PublishResultTest extends WP_UnitTestCase {

	/**
	 * Fake X API.
	 *
	 * @var Tweet_Spy
	 */
	private $spy;

	/**
	 * A published post with a custom message, set to autopost.
	 *
	 * @var int
	 */
	private $post_id;

	/**
	 * Prepares the post and the fake API.
	 */
	public function set_up() {
		parent::set_up();

		AST_Staging::set_autoshare_live_url_lock();
		connect_test_accounts();
		$this->spy     = new Tweet_Spy();
		$this->post_id = $this->factory->post->create( array( 'post_status' => 'publish' ) );
		update_autoshare_for_twitter_meta( $this->post_id, ENABLE_AUTOSHARE_FOR_TWITTER_KEY, 1 );
		update_autoshare_for_twitter_meta( $this->post_id, TWEET_BODY_KEY, 'Custom message' );
	}

	/**
	 * Returns the post's tweet log.
	 *
	 * @return array[]
	 */
	private function log() {
		return get_autoshare_for_twitter_meta( $this->post_id, TWITTER_STATUS_KEY );
	}

	/**
	 * A successful post is logged against the account and clears the custom
	 * message, so the next "Post now" starts from the title.
	 */
	public function test_success_is_logged_and_clears_the_custom_message() {
		update_autoshare_for_twitter_meta( $this->post_id, TWEET_ACCOUNTS_KEY, array( 'A1' ) );

		$this->assertTrue( publish_tweet( $this->post_id ) );

		$log = $this->log();
		$this->assertCount( 1, $log );
		$this->assertSame( 'published', $log[0]['status'] );
		$this->assertSame( 1001, $log[0]['twitter_id'] );
		$this->assertSame( 'account_one', $log[0]['handle'] );
		$this->assertSame( '', get_autoshare_for_twitter_meta( $this->post_id, TWEET_BODY_KEY ) );
		$this->assertSame( 1, did_action( 'autoshare_for_twitter_success' ) );
	}

	/**
	 * An error from X is logged with its code and message, and the custom
	 * message is kept so the user can retry.
	 */
	public function test_error_is_logged_and_keeps_the_custom_message() {
		update_autoshare_for_twitter_meta( $this->post_id, TWEET_ACCOUNTS_KEY, array( 'A1' ) );
		$this->spy->responses = array( Tweet_Spy::error( 187, 'Status is a duplicate.' ) );

		$this->assertFalse( publish_tweet( $this->post_id ) );

		$log = $this->log();
		$this->assertSame( 'error', $log[0]['status'] );
		$this->assertSame( 'Error: 187. Status is a duplicate.', $log[0]['message'] );
		$this->assertSame( 'Custom message', get_autoshare_for_twitter_meta( $this->post_id, TWEET_BODY_KEY ) );
		$this->assertSame( 1, did_action( 'autoshare_for_twitter_failed' ) );
	}

	/**
	 * With two accounts, one failure doesn't stop the other: both results are
	 * logged, and the post counts as failed so the message is kept.
	 */
	public function test_each_account_is_posted_and_logged_when_one_fails() {
		update_autoshare_for_twitter_meta( $this->post_id, TWEET_ACCOUNTS_KEY, array( 'A1', 'A2' ) );
		$this->spy->responses = array(
			Tweet_Spy::error( 186, 'Tweet needs to be a bit shorter.' ),
			(object) array( 'id' => '1001' ),
		);

		$this->assertFalse( publish_tweet( $this->post_id ) );

		$this->assertCount( 2, $this->spy->sent );
		$log = $this->log();
		$this->assertSame( array( 'error', 'published' ), wp_list_pluck( $log, 'status' ) );
		$this->assertSame( array( 'account_one', 'account_two' ), wp_list_pluck( $log, 'handle' ) );
		$this->assertSame( 'Custom message', get_autoshare_for_twitter_meta( $this->post_id, TWEET_BODY_KEY ) );
	}
}
