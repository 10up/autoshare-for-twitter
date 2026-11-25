/**
 * External dependencies
 */
import type { Locator, Page } from '@playwright/test';

/**
 * WordPress dependencies
 */
import { test, expect } from '@wordpress/e2e-test-utils-playwright';

/**
 * Internal dependencies
 */
import {
	ACCOUNT_ID,
	NOT_POSTED,
	POSTED,
	configurePlugin,
	openPanel,
	openPrePublishPanel,
	resetPluginState,
	useEditor,
	waitForMetaSave,
} from '../../utils/helpers';

/**
 * Sets the "Post to X/Twitter" toggle and waits for the meta save it triggers.
 *
 * @param page    Page.
 * @param panel   Autopost panel holding the toggle.
 * @param current Expected state before the change (the site default).
 * @param wanted  State to leave it in.
 */
async function setAutoshare(
	page: Page,
	panel: Locator,
	current: boolean,
	wanted: boolean
) {
	const toggle = panel
		.locator(
			'.autoshare-for-twitter-toggle-control input[type="checkbox"]'
		)
		.first();
	await expect( toggle ).toBeChecked( { checked: current } );
	if ( current !== wanted ) {
		const saved = waitForMetaSave( page );
		await toggle.setChecked( wanted );
		await saved;
	}
	await expect( toggle ).toBeChecked( { checked: wanted } );
}

/**
 * Sets the first account toggle and waits for the meta save it triggers.
 *
 * @param page   Page.
 * @param panel  Autopost panel holding the toggle.
 * @param wanted State to leave it in.
 */
async function setFirstAccount( page: Page, panel: Locator, wanted: boolean ) {
	const toggle = panel
		.locator(
			'.autoshare-for-twitter-account-toggle input[type="checkbox"]'
		)
		.first();
	const saved = waitForMetaSave( page );
	await toggle.setChecked( wanted );
	await saved;
	await expect( toggle ).toBeChecked( { checked: wanted } );
}

/**
 * Clicks Publish in the pre-publish panel and waits for the post-publish status.
 *
 * @param page Page.
 * @return The Autopost status in the post-publish panel.
 */
async function publish( page: Page ) {
	await page
		.getByRole( 'region', { name: 'Editor publish' } )
		.getByRole( 'button', { name: 'Publish', exact: true } )
		.click();
	const status = page
		.locator( '.post-publish-panel__postpublish' )
		.locator( '.autoshare-for-twitter-post-status' );
	await expect( status ).toBeVisible();
	return status;
}

test.describe( 'Block editor', () => {
	test.beforeAll( async ( { requestUtils } ) => {
		await useEditor( requestUtils, 'block' );
	} );

	test.afterAll( async ( { requestUtils } ) => {
		await resetPluginState( requestUtils );
		await requestUtils.deleteAllPosts();
	} );

	for ( const defaultOn of [ false, true ] ) {
		test.describe( `with Autopost ${
			defaultOn ? 'on' : 'off'
		} by default`, () => {
			test.beforeEach( async ( { admin, requestUtils } ) => {
				await configurePlugin( requestUtils, {
					enableDefault: defaultOn,
					autoshareAccounts: [ ACCOUNT_ID ],
				} );
				await admin.createNewPost( { title: 'Autopost block' } );
			} );

			test( 'does not post a new post when Autopost is off', async ( {
				page,
			} ) => {
				const panel = await openPrePublishPanel( page );
				await setAutoshare( page, panel, defaultOn, false );

				await expect( await publish( page ) ).toContainText(
					NOT_POSTED
				);
			} );

			test( 'posts a new post when Autopost is on', async ( {
				page,
			} ) => {
				const panel = await openPrePublishPanel( page );
				await setAutoshare( page, panel, defaultOn, true );

				await expect( await publish( page ) ).toContainText( POSTED );
			} );

			test( 'does not post a saved draft when Autopost is off', async ( {
				editor,
				page,
			} ) => {
				await editor.saveDraft();
				const panel = await openPrePublishPanel( page );
				await setAutoshare( page, panel, defaultOn, false );

				await expect( await publish( page ) ).toContainText(
					NOT_POSTED
				);
			} );

			test( 'posts a saved draft when Autopost is on', async ( {
				editor,
				page,
			} ) => {
				await editor.saveDraft();
				const panel = await openPrePublishPanel( page );
				await setAutoshare( page, panel, defaultOn, true );

				await expect( await publish( page ) ).toContainText( POSTED );
			} );

			test( 'does not post when every account is unchecked', async ( {
				page,
			} ) => {
				const panel = await openPrePublishPanel( page );
				await setAutoshare( page, panel, defaultOn, true );
				await setFirstAccount( page, panel, false );

				await expect( await publish( page ) ).toContainText(
					NOT_POSTED
				);
			} );

			test( 'posts when an account is checked in the editor', async ( {
				admin,
				page,
				requestUtils,
			} ) => {
				// No account autoposts by default, so the editor choice is what counts.
				await configurePlugin( requestUtils, {
					enableDefault: defaultOn,
				} );
				await admin.createNewPost( { title: 'Autopost block' } );

				const panel = await openPrePublishPanel( page );
				await setAutoshare( page, panel, defaultOn, true );
				await setFirstAccount( page, panel, true );

				await expect( await publish( page ) ).toContainText( POSTED );
			} );

			test( '"Post to X/Twitter now" posts an already published post', async ( {
				editor,
				page,
			} ) => {
				const prePublish = await openPrePublishPanel( page );
				await setAutoshare( page, prePublish, defaultOn, false );
				await expect( await publish( page ) ).toContainText(
					NOT_POSTED
				);

				await page
					.getByRole( 'region', { name: 'Editor publish' } )
					.getByRole( 'button', { name: 'Close panel' } )
					.click();
				await editor.openDocumentSettingsSidebar();
				const panel = page.locator(
					'.autoshare-for-twitter-editor-panel'
				);
				await openPanel( panel );
				await panel
					.locator( 'button.autoshare-for-twitter-tweet-now' )
					.click();
				await panel
					.locator( '.autoshare-for-twitter-tweet-text textarea' )
					.fill( 'Posted from the editor' );
				await panel
					.locator( 'button.autoshare-for-twitter-re-tweet' )
					.click();

				await expect(
					panel.locator( '.autoshare-for-twitter-log a' )
				).toContainText( POSTED );
			} );
		} );
	}

	test( 'keeps a custom message across reloads and clears it after publishing', async ( {
		admin,
		editor,
		page,
		requestUtils,
	} ) => {
		const customTweetBody = 'Custom message for X';
		await configurePlugin( requestUtils, {
			autoshareAccounts: [ ACCOUNT_ID ],
		} );
		await admin.createNewPost( { title: 'Custom message' } );

		await editor.openDocumentSettingsSidebar();
		const panel = page.locator( '.autoshare-for-twitter-editor-panel' );
		await openPanel( panel );
		await panel
			.locator( '.autoshare-for-twitter-prepublish__override-row button' )
			.click();
		const saved = waitForMetaSave( page );
		await panel
			.locator( '.autoshare-for-twitter-tweet-text textarea' )
			.fill( customTweetBody );
		await saved;
		await editor.saveDraft();

		await page.reload();
		await editor.openDocumentSettingsSidebar();
		await openPanel( panel );
		await expect(
			panel.locator( '.autoshare-for-twitter-tweet-text textarea' )
		).toHaveValue( customTweetBody );

		await openPrePublishPanel( page );
		await expect( await publish( page ) ).toContainText( POSTED );

		// The message is cleared once posted, so "Post now" starts empty.
		const postPublish = page.locator( '.post-publish-panel__postpublish' );
		await postPublish
			.locator( 'button.autoshare-for-twitter-tweet-now' )
			.click();
		await expect(
			postPublish.locator( '.autoshare-for-twitter-tweet-text textarea' )
		).toHaveValue( '' );
	} );
} );
