/**
 * External dependencies
 */
import type { Page } from '@playwright/test';

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
	classicStartCreatePost,
	configurePlugin,
	resetPluginState,
	useEditor,
} from '../../utils/helpers';

/**
 * Sets the "Post to X/Twitter" checkbox. The click saves the meta over REST and
 * disables Publish until the save finishes.
 *
 * @param page    Page.
 * @param current Expected state before the change (the site default).
 * @param wanted  State to leave it in.
 */
async function setAutoshare( page: Page, current: boolean, wanted: boolean ) {
	const checkbox = page.locator( '#autoshare-for-twitter-enable' );
	await expect( checkbox ).toBeChecked( { checked: current } );
	if ( current !== wanted ) {
		await checkbox.setChecked( wanted );
		await expect( page.locator( '#publish' ) ).toBeEnabled();
	}
	await expect( checkbox ).toBeChecked( { checked: wanted } );
}

/**
 * Sets the first account checkbox.
 *
 * @param page   Page.
 * @param wanted State to leave it in.
 */
async function setFirstAccount( page: Page, wanted: boolean ) {
	const checkbox = page
		.locator( 'input.autoshare-for-twitter-account-checkbox' )
		.first();
	await checkbox.setChecked( wanted );
	await expect( page.locator( '#publish' ) ).toBeEnabled();
	await expect( checkbox ).toBeChecked( { checked: wanted } );
}

/**
 * Saves the post as a draft and waits for the reload.
 *
 * @param page Page.
 */
async function saveDraft( page: Page ) {
	await page.locator( '#save-post' ).click();
	await expect( page.locator( '#message' ) ).toContainText(
		'Post draft updated'
	);
}

/**
 * Publishes the post and returns the Autopost metabox after the reload.
 *
 * @param page Page.
 */
async function publish( page: Page ) {
	await expect( page.locator( '#publish' ) ).toBeEnabled();
	await page.locator( '#publish' ).click();
	await expect( page.locator( '#message' ) ).toContainText(
		'Post published'
	);
	return page.locator( '#autoshare_for_twitter_metabox' );
}

test.describe( 'Classic editor', () => {
	test.beforeAll( async ( { requestUtils } ) => {
		await useEditor( requestUtils, 'classic' );
	} );

	test.afterAll( async ( { requestUtils } ) => {
		await useEditor( requestUtils, 'block' );
		await resetPluginState( requestUtils );
		await requestUtils.deleteAllPosts();
	} );

	for ( const defaultOn of [ false, true ] ) {
		test.describe( `with Autopost ${
			defaultOn ? 'on' : 'off'
		} by default`, () => {
			test.beforeEach( async ( { admin, page, requestUtils } ) => {
				await configurePlugin( requestUtils, {
					enableDefault: defaultOn,
					autoshareAccounts: [ ACCOUNT_ID ],
				} );
				await classicStartCreatePost( page, admin );
			} );

			test( 'does not post a new post when Autopost is off', async ( {
				page,
			} ) => {
				await setAutoshare( page, defaultOn, false );

				await expect( await publish( page ) ).toContainText(
					NOT_POSTED
				);
			} );

			test( 'posts a new post when Autopost is on', async ( {
				page,
			} ) => {
				await setAutoshare( page, defaultOn, true );

				await expect( await publish( page ) ).toContainText( POSTED );
			} );

			test( 'does not post a saved draft when Autopost is off', async ( {
				page,
			} ) => {
				await saveDraft( page );
				await setAutoshare( page, defaultOn, false );

				await expect( await publish( page ) ).toContainText(
					NOT_POSTED
				);
			} );

			test( 'posts a saved draft when Autopost is on', async ( {
				page,
			} ) => {
				await saveDraft( page );
				await setAutoshare( page, defaultOn, true );

				await expect( await publish( page ) ).toContainText( POSTED );
			} );

			test( 'does not post when every account is unchecked', async ( {
				page,
			} ) => {
				await setAutoshare( page, defaultOn, true );
				await setFirstAccount( page, false );

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
				await classicStartCreatePost( page, admin );

				await setAutoshare( page, defaultOn, true );
				await setFirstAccount( page, true );

				await expect( await publish( page ) ).toContainText( POSTED );
			} );

			test( '"Post to X/Twitter now" posts an already published post', async ( {
				page,
			} ) => {
				await saveDraft( page );
				await setAutoshare( page, defaultOn, false );
				const metabox = await publish( page );
				await expect( metabox ).toContainText( NOT_POSTED );

				await metabox
					.getByRole( 'button', { name: 'Post to X/Twitter now' } )
					.click();
				await page
					.locator( '#autoshare-for-twitter-override-body textarea' )
					.fill( 'Posted from the editor' );
				await page
					.locator(
						'.autoshare-for-twitter-tweet-now-wrapper #tweet_now'
					)
					.click();

				await expect(
					page.locator( '.autoshare-for-twitter-status-log-data' )
				).toContainText( POSTED );
			} );
		} );
	}

	test( 'keeps a custom message on save and clears it after publishing', async ( {
		admin,
		page,
		requestUtils,
	} ) => {
		const customTweetBody = 'Custom message for X';
		await configurePlugin( requestUtils, {
			autoshareAccounts: [ ACCOUNT_ID ],
		} );
		await classicStartCreatePost( page, admin );

		await setAutoshare( page, true, true );
		await page.locator( '#autoshare-for-twitter-edit' ).click();
		await page
			.locator( 'textarea#autoshare-for-twitter-text' )
			.fill( customTweetBody );
		await saveDraft( page );

		await expect(
			page.locator( 'textarea#autoshare-for-twitter-text' )
		).toHaveValue( customTweetBody );

		await expect( await publish( page ) ).toContainText( POSTED );

		// The message is cleared once posted, so "Post now" starts empty.
		await page.locator( 'button.tweet-now-button' ).click();
		await expect(
			page.locator( 'textarea#autoshare-for-twitter-text' )
		).toHaveValue( '' );
	} );
} );
