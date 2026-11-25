<?php
/**
 * Tests how X rate-limit headers are stored on the connected accounts.
 *
 * @package TenUp\AutoshareForTwitter
 */

namespace TenUp\AutoshareForTwitter\Tests;

use WP_UnitTestCase;
use function TenUp\AutoshareForTwitter\Core\update_account_rate_limits;

/**
 * Rate limit tests.
 */
class RateLimitsTest extends WP_UnitTestCase {

	/**
	 * Stores the headers from a tweet sent by account A1.
	 *
	 * @return array The stored accounts afterwards.
	 */
	private function record_tweet_from_a1() {
		$headers = array(
			'x_rate_limit_limit'            => '300',
			'x_rate_limit_remaining'        => '299',
			'x_rate_limit_reset'            => '1700000000',
			'x_app_limit_24hour_limit'      => '1500',
			'x_app_limit_24hour_remaining'  => '1499',
			'x_app_limit_24hour_reset'      => '1700000100',
			'x_user_limit_24hour_limit'     => '50',
			'x_user_limit_24hour_remaining' => '49',
			'x_user_limit_24hour_reset'     => '1700000200',
		);

		update_account_rate_limits( (object) array( 'id' => '1001' ), array(), $this->factory->post->create_and_get(), 'A1', $headers );

		return get_option( 'autoshare_for_twitter_accounts' );
	}

	/**
	 * Recording rate limits rewrites the accounts option after every tweet; the
	 * OAuth tokens must survive it or the accounts silently disconnect.
	 */
	public function test_recording_rate_limits_keeps_oauth_tokens() {
		connect_test_accounts();

		$accounts = $this->record_tweet_from_a1();

		$this->assertSame( 'TOKEN_ONE', $accounts['A1']['oauth_token'] );
		$this->assertSame( 'SECRET_ONE', $accounts['A1']['oauth_token_secret'] );
		$this->assertSame( 'TOKEN_TWO', $accounts['A2']['oauth_token'] );
		$this->assertSame( 'SECRET_TWO', $accounts['A2']['oauth_token_secret'] );
	}

	/**
	 * App limits are shared by every account; user limits belong only to the
	 * account that posted.
	 */
	public function test_user_limits_go_only_to_the_posting_account() {
		connect_test_accounts();

		$accounts = $this->record_tweet_from_a1();

		$this->assertSame( '1499', $accounts['A1']['rate_limits']['app_limit_24hour_remaining'] );
		$this->assertSame( '1499', $accounts['A2']['rate_limits']['app_limit_24hour_remaining'] );
		$this->assertSame( '49', $accounts['A1']['rate_limits']['user_limit_24hour_remaining'] );
		$this->assertArrayNotHasKey( 'user_limit_24hour_remaining', $accounts['A2']['rate_limits'] );
	}
}
