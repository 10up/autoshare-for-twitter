const slug = 'autoshare-for-twitter';
const classicSlug = 'classic-editor';

describe( 'Admin can login and make sure plugin is activated', () => {
	it( 'Can activate plugin if it is deactivated', () => {
		cy.visitAdminPage( 'plugins.php' );
		cy.get( 'tr[data-slug="' + slug + '"] .deactivate a' ).click();
		cy.get( 'tr[data-slug="' + slug + '"] .activate a' ).click();
		cy.get( 'tr[data-slug="' + slug + '"] .deactivate a' ).should( 'be.visible' );
	} );
} );

describe( 'Activate Classic Editor for testing', () => {
	it( 'Can activate plugin if it is deactivated', () => {
		cy.visitAdminPage( 'plugins.php' );
		cy.get( 'tr[data-slug="' + classicSlug + '"] .deactivate a' ).click();
		cy.get( 'tr[data-slug="' + classicSlug + '"] .activate a' ).click();
		cy.get( 'tr[data-slug="' + classicSlug + '"] .deactivate a' ).should( 'be.visible' );
	} );
} );

describe( 'Plugin settings page has the necessary fields', () => {
	it( 'Can see all the fields on the settings page', () => {
		cy.visitAdminPage( 'options-general.php?page=autoshare-for-twitter' );
		cy.get( 'input[name="autoshare-for-twitter[api_key]"]' ).should( 'be.visible' );
		cy.get( 'input[name="autoshare-for-twitter[api_secret]"]' ).should( 'be.visible' );
	} );
} );

describe( 'Configure the plugin', () => {
	it( 'Configure the plugin secrets', () => {
		cy.visitAdminPage( 'options-general.php?page=autoshare-for-twitter' );
		cy.get( '.large-text:nth-child(1) .large-text' ).type( Cypress.env('TWITTER_API_KEY') );
		cy.get( '.large-text:nth-child(2) .large-text' ).type( Cypress.env('TWITTER_API_SECRET') );
		cy.get( '.large-text:nth-child(3) .large-text' ).type( Cypress.env('TWITTER_ACCESS_TOKEN') );
		cy.get( '.large-text:nth-child(4) .large-text' ).type( Cypress.env('TWITTER_ACCESS_SECRET') );
		cy.get( '.regular-text' ).type( 'gh_issue_help' );
		cy.get( '#submit' ).click();
	} );
} );