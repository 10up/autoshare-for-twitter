<?php
/**
 * Tests that X credentials stay on the server.
 *
 * @package TenUp\AutoshareForTwitter
 */

namespace TenUp\AutoshareForTwitter\Tests;

use WP_UnitTestCase;
use TenUp\AutoshareForTwitter\Core\Twitter_Accounts;
use function TenUp\AutoshareForTwitter\Admin\Assets\localize_data;
use function TenUp\AutoshareForTwitter\Core\Admin\sanitize_settings;
use function TenUp\AutoshareForTwitter\Core\Admin\text_field_cb;

/**
 * Secrets tests.
 */
class SecretsTest extends WP_UnitTestCase {

	/**
	 * Script handle used to capture localized data.
	 */
	const HANDLE = 'autoshare-secrets-test';

	/**
	 * The account list meant for the browser has no OAuth tokens.
	 */
	public function test_account_info_for_the_browser_has_no_oauth_tokens() {
		connect_test_accounts();

		$accounts = ( new Twitter_Accounts() )->get_twitter_accounts( true );

		$this->assertSame( array( 'A1', 'A2' ), array_keys( $accounts ) );
		foreach ( $accounts as $account ) {
			$this->assertArrayNotHasKey( 'oauth_token', $account );
			$this->assertArrayNotHasKey( 'oauth_token_secret', $account );
		}
	}

	/**
	 * The data the editor scripts receive has no OAuth tokens or app secret.
	 */
	public function test_editor_script_data_has_no_secrets() {
		connect_test_accounts();
		update_option(
			'autoshare-for-twitter',
			array(
				'api_key'    => 'APP_KEY_VALUE',
				'api_secret' => 'APP_SECRET_VALUE',
			)
		);
		$GLOBALS['post'] = get_post( $this->factory->post->create() ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		wp_register_script( self::HANDLE, 'https://example.org/test.js', array(), '1', true );

		localize_data( self::HANDLE );
		$data = wp_scripts()->get_data( self::HANDLE, 'data' );

		$this->assertStringContainsString( 'account_one', $data, 'The accounts should be localized.' );
		foreach ( array( 'TOKEN_ONE', 'SECRET_ONE', 'TOKEN_TWO', 'SECRET_TWO', 'APP_SECRET_VALUE' ) as $secret ) {
			$this->assertStringNotContainsString( $secret, $data );
		}
	}

	/**
	 * The settings screen shows only the last five characters of the app secret.
	 */
	public function test_settings_field_masks_the_app_secret() {
		update_option( 'autoshare-for-twitter', array( 'api_secret' => 'abcdefghij12345' ) );

		ob_start();
		text_field_cb( array( 'name' => 'api_secret' ) );
		$html = ob_get_clean();

		$this->assertStringNotContainsString( 'abcdefghij12345', $html );
		$this->assertStringContainsString( 'value="**********12345"', $html );
	}

	/**
	 * Saving the settings form sends the masked secret back. The stored secret
	 * must survive that, or every settings save would break posting.
	 */
	public function test_saving_the_masked_secret_keeps_the_stored_secret() {
		update_option(
			'autoshare-for-twitter',
			array(
				'api_key'    => 'KEYKEYKEY99999',
				'api_secret' => 'abcdefghij12345',
			)
		);

		$saved = sanitize_settings(
			array(
				'api_key'    => '*********99999',
				'api_secret' => '**********12345',
			)
		);

		$this->assertSame( 'KEYKEYKEY99999', $saved['api_key'] );
		$this->assertSame( 'abcdefghij12345', $saved['api_secret'] );
	}

	/**
	 * A newly typed secret replaces the stored one.
	 */
	public function test_saving_a_new_secret_replaces_the_stored_secret() {
		update_option( 'autoshare-for-twitter', array( 'api_secret' => 'abcdefghij12345' ) );

		$saved = sanitize_settings( array( 'api_secret' => 'new-secret' ) );

		$this->assertSame( 'new-secret', $saved['api_secret'] );
	}
}
