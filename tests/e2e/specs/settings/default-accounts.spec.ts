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
	classicStartCreatePost,
	configurePlugin,
	openPanel,
	openPrePublishPanel,
	resetPluginState,
	saveSettings,
	useEditor,
} from '../../utils/helpers';

test.describe( 'Connected accounts in the editors', () => {
	test.beforeEach( async ( { requestUtils } ) => {
		await configurePlugin( requestUtils );
	} );

	test.afterAll( async ( { requestUtils } ) => {
		await useEditor( requestUtils, 'block' );
		await resetPluginState( requestUtils );
	} );

	test( 'lists both accounts in the block and classic editors', async ( {
		admin,
		editor,
		page,
		requestUtils,
	} ) => {
		await useEditor( requestUtils, 'block' );
		await admin.createNewPost( { title: 'Accounts' } );

		await editor.openDocumentSettingsSidebar();
		const sidebarPanel = page.locator(
			'.autoshare-for-twitter-editor-panel'
		);
		await openPanel( sidebarPanel );
		await expect(
			sidebarPanel.locator( '.twitter-account-wrapper' )
		).toHaveCount( 2 );

		await openPrePublishPanel( page );
		await expect(
			page.locator(
				'.autoshare-for-twitter-pre-publish-panel .twitter-account-wrapper'
			)
		).toHaveCount( 2 );

		await useEditor( requestUtils, 'classic' );
		await classicStartCreatePost( page, admin );
		await expect(
			page.locator(
				'#autoshare_for_twitter_metabox .twitter-account-wrapper'
			)
		).toHaveCount( 2 );
	} );

	for ( const enable of [ true, false ] ) {
		test( `${
			enable ? 'checks' : 'leaves unchecked'
		} the account marked "Autopost by default" in the settings`, async ( {
			admin,
			editor,
			page,
			requestUtils,
		} ) => {
			await configurePlugin( requestUtils, {
				autoshareAccounts: enable ? [] : [ 'TEST_ACCOUNT_ID' ],
			} );

			await admin.visitAdminPage( SETTINGS_PAGE, SETTINGS_QUERY );
			const defaultAccount = page
				.locator(
					'input[name="autoshare-for-twitter[autoshare_accounts][]"]'
				)
				.first();
			await defaultAccount.setChecked( enable );
			await saveSettings( page );
			await expect(
				page.locator( '.notice.notice-success' )
			).toBeVisible();

			// Block editor.
			await useEditor( requestUtils, 'block' );
			await admin.createNewPost( { title: 'Default accounts' } );
			await editor.openDocumentSettingsSidebar();
			const sidebarPanel = page.locator(
				'.autoshare-for-twitter-editor-panel'
			);
			await openPanel( sidebarPanel );
			const toggles = sidebarPanel.locator(
				'.autoshare-for-twitter-account-toggle input[type="checkbox"]'
			);
			await expect( toggles.first() ).toBeChecked( { checked: enable } );
			await expect( toggles.last() ).not.toBeChecked();

			// Classic editor.
			await useEditor( requestUtils, 'classic' );
			await classicStartCreatePost( page, admin );
			const checkboxes = page.locator(
				'input[type="checkbox"].autoshare-for-twitter-account-checkbox'
			);
			await expect( checkboxes.first() ).toBeChecked( {
				checked: enable,
			} );
			await expect( checkboxes.last() ).not.toBeChecked();
		} );
	}
} );
