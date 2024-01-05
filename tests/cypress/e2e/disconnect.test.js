describe('Admin can disconnect connected X accounts', () => {
	before(() => {
		cy.login();
        cy.configurePlugin();
	});

	beforeEach(() => {
		cy.login();
	});

	it('Admin can disconnect connected X accounts', () => {
		cy.visit('/wp-admin/options-general.php?page=autoshare-for-twitter');
		cy.get('.twitter_accounts #the-list tr').should('be.visible').should('have.length', 2);
        cy.get('.column-action a.button').should('be.visible').first().click();
        cy.get('.twitter_accounts #the-list tr').should('be.visible').should('have.length', 1);
	});
});
