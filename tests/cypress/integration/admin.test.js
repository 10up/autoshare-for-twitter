describe('Admin can login and make sure plugin is activated', () => {
	beforeEach(() => {
		cy.login();
	});

	it('Can activate plugin if it is deactivated', () => {
		cy.activatePlugin('autoshare-for-twitter');
	});

	it('Can activate Classic Editor plugin if it is deactivated', () => {
		cy.activatePlugin('classic-editor');
	});
});

describe('Plugin settings page has the necessary fields', () => {
	it('Can see all the fields on the settings page', () => {
		cy.login();
		cy.visit('/wp-admin/options-general.php?page=autoshare-for-twitter');
		cy.get('input[name="autoshare-for-twitter[api_key]"]').should('be.visible');
		cy.get('input[name="autoshare-for-twitter[api_secret]"]').should('be.visible');
	});
});

describe('Configure the plugin', () => {
	it('Configure the plugin secrets', () => {
		cy.login();
		cy.visit('/wp-admin/options-general.php?page=autoshare-for-twitter');
		cy.get('.large-text:nth-child(1) .large-text').clear().type( Cypress.env('TWITTER_API_KEY') );
		cy.get('.large-text:nth-child(2) .large-text').clear().type( Cypress.env('TWITTER_API_SECRET') );
		cy.get('.large-text:nth-child(3) .large-text').clear().type( Cypress.env('TWITTER_ACCESS_TOKEN') );
		cy.get('.large-text:nth-child(4) .large-text').clear().type( Cypress.env('TWITTER_ACCESS_SECRET') );
		cy.get('.regular-text').clear().type('gh_issue_help');
		cy.get('#submit').click();
	});
});