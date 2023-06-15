describe('Admin can login and make sure plugin is activated', () => {
	before(() => {
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
		cy.visit('/wp-admin/options-general.php?page=autoshare-for-twitter');
		cy.get('input[name="autoshare-for-twitter[api_key]"]').should('be.visible');
		cy.get('input[name="autoshare-for-twitter[api_secret]"]').should('be.visible');
	});
});

describe('Configure the plugin', () => {
	it('Configure the plugin secrets', () => {
		cy.visit('/wp-admin/options-general.php?page=autoshare-for-twitter');
		cy.get('.large-text:nth-child(1) .large-text').clear().type( 'TEST_TWITTER_API_KEY' );
		cy.get('.large-text:nth-child(2) .large-text').clear().type( 'TEST_TWITTER_API_SECRET' );
		cy.get('#submit').click();

		// Verify that the credentials are saved and no accounts are connected.
		cy.get('.twitter_accounts #the-list tr.no-items').should('be.visible');

		// Connect a twitter account and verify it shows up in the list.
		cy.wpCli(`option update autoshare_for_twitter_accounts '[{"id":"TEST_ACCOUNT_ID","name":"Test Twitter User","username":"testtwitteruser","profile_image_url":"https://placehold.co\/48x48?text=T1","oauth_token":"TEST_OUTH_TOKEN","oauth_token_secret":"TEST_OUTH_TOKEN_SECRET"},{"id":"TEST_ACCOUNT_ID2","name":"Test Twitter User 2","username":"testtwitteruser2","profile_image_url":"https://placehold.co\/48x48?text=T2","oauth_token":"TEST_OUTH_TOKEN2","oauth_token_secret":"TEST_OUTH_TOKEN_SECRET2"}]' --format=json`);
		cy.visit('/wp-admin/options-general.php?page=autoshare-for-twitter');

		cy.get('.twitter_accounts #the-list tr').should('be.visible').should('have.length', 2);
		cy.get('.account-details strong').first().should('be.visible').contains('@testtwitteruser');
		cy.get('.account-details strong').last().should('be.visible').contains('@testtwitteruser2');
	});
});