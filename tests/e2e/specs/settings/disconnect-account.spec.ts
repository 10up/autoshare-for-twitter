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
	resetPluginState,
} from '../../utils/helpers';

test.describe( 'Disconnect account', () => {
	test.beforeEach( async ( { requestUtils } ) => {
		await configurePlugin( requestUtils );
	} );

	test.afterAll( async ( { requestUtils } ) => {
		await resetPluginState( requestUtils );
	} );

	test( 'admin can disconnect a connected X account', async ( {
		admin,
		page,
	} ) => {
		await admin.visitAdminPage( SETTINGS_PAGE, SETTINGS_QUERY );
		const rows = page.locator( '.twitter_accounts #the-list tr' );
		await expect( rows ).toHaveCount( 2 );

		await page.locator( '.column-action a.button' ).first().click();

		await expect( rows ).toHaveCount( 1 );
		await expect( page.locator( '.account-details strong' ) ).toContainText(
			'@testtwitteruser2'
		);
	} );
} );
