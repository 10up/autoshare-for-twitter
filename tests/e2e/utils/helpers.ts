/**
 * Shared E2E helpers.
 *
 * Plugin state is set through the helper plugin's fixture endpoint
 * (tests/e2e-plugins/autoshare-for-twitter-e2e-helpers). The same plugin mocks
 * every X API call, so publishing never reaches X.
 */

/**
 * External dependencies
 */
import { expect } from '@playwright/test';
import type { Locator, Page } from '@playwright/test';

/**
 * WordPress dependencies
 */
import type { Admin, RequestUtils } from '@wordpress/e2e-test-utils-playwright';

export const SETTINGS_PAGE = 'options-general.php';
export const SETTINGS_QUERY = 'page=autoshare-for-twitter';

export const ACCOUNT_ID = 'TEST_ACCOUNT_ID';

/** Two fake connected X accounts, stored the way the OAuth callback stores them. */
export const ACCOUNTS = {
	TEST_ACCOUNT_ID: {
		id: 'TEST_ACCOUNT_ID',
		name: 'Test Twitter User',
		username: 'testtwitteruser',
		profile_image_url: 'https://placehold.co/48x48?text=T1',
		oauth_token: 'TEST_OUTH_TOKEN',
		oauth_token_secret: 'TEST_OUTH_TOKEN_SECRET',
	},
	TEST_ACCOUNT_ID2: {
		id: 'TEST_ACCOUNT_ID2',
		name: 'Test Twitter User 2',
		username: 'testtwitteruser2',
		profile_image_url: 'https://placehold.co/48x48?text=T2',
		oauth_token: 'TEST_OUTH_TOKEN2',
		oauth_token_secret: 'TEST_OUTH_TOKEN_SECRET2',
	},
};

export const NOT_POSTED = 'This post has not been posted to X/Twitter';
export const POSTED = 'Posted to X/Twitter on';

type PluginState = {
	enableDefault?: boolean;
	autoshareAccounts?: string[];
};

async function setOptions(
	requestUtils: RequestUtils,
	options: Record< string, unknown >
) {
	await requestUtils.rest( {
		method: 'POST',
		path: '/autoshare-for-twitter-e2e/v1/state',
		data: { options },
	} );
}

/**
 * Deletes the plugin's settings and connected accounts.
 *
 * @param requestUtils Request utils.
 */
export async function resetPluginState( requestUtils: RequestUtils ) {
	await setOptions( requestUtils, {
		'autoshare-for-twitter': null,
		autoshare_for_twitter_accounts: null,
	} );
}

/**
 * Saves app credentials and two connected accounts.
 *
 * @param requestUtils            Request utils.
 * @param state                   Settings to store alongside the credentials.
 * @param state.enableDefault     Whether new posts autopost by default.
 * @param state.autoshareAccounts Account IDs that autopost by default.
 */
export async function configurePlugin(
	requestUtils: RequestUtils,
	{ enableDefault = true, autoshareAccounts = [] }: PluginState = {}
) {
	await setOptions( requestUtils, {
		'autoshare-for-twitter': {
			api_key: 'TEST_TWITTER_API_KEY',
			api_secret: 'TEST_TWITTER_API_SECRET',
			enable_default: enableDefault ? 1 : 0,
			enable_upload: 1,
			autoshare_accounts: autoshareAccounts,
		},
		autoshare_for_twitter_accounts: ACCOUNTS,
	} );
}

/**
 * Saves the connected accounts only.
 *
 * @param requestUtils Request utils.
 */
export async function connectAccounts( requestUtils: RequestUtils ) {
	await setOptions( requestUtils, {
		autoshare_for_twitter_accounts: ACCOUNTS,
	} );
}

/**
 * Picks the editor Classic Editor opens for every user.
 *
 * @param requestUtils Request utils.
 * @param editor       'block' or 'classic'.
 */
export async function useEditor(
	requestUtils: RequestUtils,
	editor: 'block' | 'classic'
) {
	await setOptions( requestUtils, {
		'classic-editor-replace': editor,
		'classic-editor-allow-users': 'disallow',
	} );
}

/**
 * Opens a new post in the classic editor and types a title.
 *
 * @param page  Page.
 * @param admin Admin utils.
 */
export async function classicStartCreatePost( page: Page, admin: Admin ) {
	await admin.visitAdminPage( 'post-new.php' );
	await page
		.locator( '#title' )
		.fill(
			`Random Post Title ${ Math.random().toString( 36 ).slice( 2, 8 ) }`
		);
}

/**
 * Waits for the block editor's debounced save of the Autopost meta.
 *
 * @param page Page.
 */
export function waitForMetaSave( page: Page ) {
	return page.waitForResponse(
		( response ) =>
			response.url().includes( 'post-autoshare-for-twitter-meta' ) &&
			response.request().method() === 'POST'
	);
}

/**
 * Submits the settings form and waits until options.php has saved it.
 *
 * @param page Page.
 */
export async function saveSettings( page: Page ) {
	const saved = page.waitForResponse(
		( response ) =>
			response.url().includes( '/wp-admin/options.php' ) &&
			response.request().method() === 'POST'
	);
	await page.getByRole( 'button', { name: 'Save Changes' } ).click();
	await saved;
	await page.waitForLoadState();
}

/**
 * Expands a collapsed editor panel (PluginDocumentSettingPanel, PluginPrePublishPanel).
 *
 * @param panel Panel locator.
 */
export async function openPanel( panel: Locator ) {
	const toggle = panel.locator( '.components-panel__body-toggle' );
	if ( 'false' === ( await toggle.getAttribute( 'aria-expanded' ) ) ) {
		await toggle.click();
	}
	await expect( toggle ).toHaveAttribute( 'aria-expanded', 'true' );
}

/**
 * Opens the block editor's pre-publish panel and expands the Autopost section.
 *
 * @param page Page.
 * @return The Autopost pre-publish section.
 */
export async function openPrePublishPanel( page: Page ) {
	await page
		.getByRole( 'region', { name: 'Editor top bar' } )
		.getByRole( 'button', { name: 'Publish', exact: true } )
		.click();
	const panel = page.locator( '.autoshare-for-twitter-pre-publish-panel' );
	await openPanel( panel );
	return panel;
}
