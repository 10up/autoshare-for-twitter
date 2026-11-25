/**
 * WordPress dependencies
 */
import { test, expect } from '@wordpress/e2e-test-utils-playwright';

/**
 * Internal dependencies
 */
import {
	SETTINGS_PAGE,
	SETTINGS_QUERY,
	configurePlugin,
	connectAccounts,
	resetPluginState,
	saveSettings,
} from '../../utils/helpers';

test.describe( 'Settings page', () => {
	test.beforeEach( async ( { requestUtils } ) => {
		await resetPluginState( requestUtils );
	} );

	test.afterAll( async ( { requestUtils } ) => {
		await resetPluginState( requestUtils );
	} );

	test( 'shows the API credential fields when the plugin is not configured', async ( {
		admin,
		page,
	} ) => {
		await admin.visitAdminPage( SETTINGS_PAGE, SETTINGS_QUERY );

		await expect(
			page.locator( 'input[name="autoshare-for-twitter[api_key]"]' )
		).toBeVisible();
		await expect(
			page.locator( 'input[name="autoshare-for-twitter[api_secret]"]' )
		).toBeVisible();
	} );

	test( 'saves API credentials and lists connected accounts', async ( {
		admin,
		page,
		requestUtils,
	} ) => {
		await admin.visitAdminPage( SETTINGS_PAGE, SETTINGS_QUERY );
		await page
			.locator( 'input[name="autoshare-for-twitter[api_key]"]' )
			.fill( 'TEST_TWITTER_API_KEY' );
		await page
			.locator( 'input[name="autoshare-for-twitter[api_secret]"]' )
			.fill( 'TEST_TWITTER_API_SECRET' );
		await saveSettings( page );

		// Credentials saved, no account connected yet.
		await expect(
			page.locator( '.twitter_accounts #the-list tr.no-items' )
		).toBeVisible();

		// The OAuth flow needs X, so store the accounts it would have saved.
		await connectAccounts( requestUtils );
		await page.reload();

		const rows = page.locator( '.twitter_accounts #the-list tr' );
		await expect( rows ).toHaveCount( 2 );
		await expect(
			page.locator( '.account-details strong' ).first()
		).toContainText( '@testtwitteruser' );
		await expect(
			page.locator( '.account-details strong' ).last()
		).toContainText( '@testtwitteruser2' );
	} );

	test( 'saves the "enable by default" setting', async ( {
		admin,
		page,
		requestUtils,
	} ) => {
		await configurePlugin( requestUtils, { enableDefault: true } );
		await admin.visitAdminPage( SETTINGS_PAGE, SETTINGS_QUERY );

		const enableDefault = page.locator(
			'input[type="checkbox"][name="autoshare-for-twitter[enable_default]"]'
		);
		await expect( enableDefault ).toBeChecked();

		await enableDefault.uncheck();
		await saveSettings( page );
		await admin.visitAdminPage( SETTINGS_PAGE, SETTINGS_QUERY );
		await expect( enableDefault ).not.toBeChecked();

		await enableDefault.check();
		await saveSettings( page );
		await admin.visitAdminPage( SETTINGS_PAGE, SETTINGS_QUERY );
		await expect( enableDefault ).toBeChecked();
	} );
} );
