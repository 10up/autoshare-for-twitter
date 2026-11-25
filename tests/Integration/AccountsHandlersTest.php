<?php
/**
 * Tests the admin-post handlers that connect and disconnect X accounts.
 *
 * @package TenUp\AutoshareForTwitter
 */

namespace TenUp\AutoshareForTwitter\Tests;

use Exception;
use WP_UnitTestCase;
use WPDieException;
use TenUp\AutoshareForTwitter\Core\Twitter_Accounts;

/**
 * Account handler tests.
 *
 * Only the refusals are tested here: the success paths end in a redirect and
 * `exit`, and the connect flow needs X. E2E covers disconnecting as an admin.
 */
class AccountsHandlersTest extends WP_UnitTestCase {

	/**
	 * The stored accounts before each test.
	 *
	 * @var array
	 */
	private $accounts;

	/**
	 * Connects two accounts and makes sure nothing can reach X or exit.
	 */
	public function set_up() {
		parent::set_up();

		$this->accounts = connect_test_accounts();

		// Never call X, even if a check under test is broken.
		add_filter( 'autoshare_for_twitter_pre_disconnect_account', '__return_true' );

		// The handlers `exit` after redirecting. Throw instead so a broken check fails the test rather than ending the run.
		add_filter(
			'wp_redirect',
			static function ( $location ) {
				throw new Exception( 'Redirected to ' . $location );
			}
		);
	}

	/**
	 * Clears the request superglobals.
	 */
	public function tear_down() {
		unset( $_GET['autoshare_twitter_disconnect_nonce'], $_GET['account_id'], $_GET['autoshare_twitter_authorize_nonce'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Test cleanup.
		parent::tear_down();
	}

	/**
	 * Runs a handler and returns the wp_die() message.
	 *
	 * @param string $method Twitter_Accounts method.
	 *
	 * @return string
	 */
	private function run_handler( $method ) {
		try {
			( new Twitter_Accounts() )->$method();
		} catch ( WPDieException $e ) {
			return $e->getMessage();
		}

		$this->fail( "$method() did not stop the request." );
	}

	/**
	 * An editor can't disconnect an account, even with a valid nonce.
	 */
	public function test_editor_cannot_disconnect_an_account() {
		wp_set_current_user( $this->factory->user->create( array( 'role' => 'editor' ) ) );
		$_GET['autoshare_twitter_disconnect_nonce'] = wp_create_nonce( 'autoshare_twitter_disconnect_action' );
		$_GET['account_id']                         = 'A1';

		$this->run_handler( 'twitter_disconnect' );

		$this->assertSame( $this->accounts, get_option( 'autoshare_for_twitter_accounts' ) );
	}

	/**
	 * An admin's disconnect request without a valid nonce is refused (CSRF).
	 */
	public function test_disconnect_without_valid_nonce_is_refused() {
		wp_set_current_user( $this->factory->user->create( array( 'role' => 'administrator' ) ) );
		$_GET['autoshare_twitter_disconnect_nonce'] = 'not-a-nonce';
		$_GET['account_id']                         = 'A1';

		$this->run_handler( 'twitter_disconnect' );

		$this->assertSame( $this->accounts, get_option( 'autoshare_for_twitter_accounts' ) );
	}

	/**
	 * An editor can't start connecting a new account.
	 *
	 * No nonce is sent: with a valid one, a broken capability check would go on
	 * to request an OAuth token from X. The message shows which check stopped it.
	 */
	public function test_editor_cannot_start_connecting_an_account() {
		wp_set_current_user( $this->factory->user->create( array( 'role' => 'editor' ) ) );

		$this->assertStringContainsString( 'sufficient permissions', $this->run_handler( 'twitter_authorize' ) );
	}

	/**
	 * An editor can't complete the OAuth callback to store an account.
	 */
	public function test_editor_cannot_complete_the_connect_callback() {
		wp_set_current_user( $this->factory->user->create( array( 'role' => 'editor' ) ) );

		$this->assertStringContainsString( 'sufficient permissions', $this->run_handler( 'authoshare_authorize_user_callback' ) );
		$this->assertSame( $this->accounts, get_option( 'autoshare_for_twitter_accounts' ) );
	}
}
