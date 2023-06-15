describe('Twitter accounts should visible in Autotweet Panel and should respect Autoshare settings', () => {
	before(() => {
		cy.login();
		cy.configurePlugin();
	});

	it('Can see Twitter accounts in Block editor', () => {
		//Block editor.
		cy.enableEditor('block');
		cy.startCreatePost();

		cy.openAutoTweetPanel();		
		cy.get('.autoshare-for-twitter-editor-panel .autoshare-for-twitter-accounts-wrapper').find('.twitter-account-wrapper').should('have.length', 2);

		cy.openPrePublishPanel( true);
		cy.get('.autoshare-for-twitter-pre-publish-panel .autoshare-for-twitter-accounts-wrapper').find('.twitter-account-wrapper').should('have.length', 2);

		// Classic editor.
		cy.enableEditor('classic');
		cy.classicStartCreatePost();
		cy.get('#autoshare_for_twitter_metabox .autoshare-for-twitter-accounts-wrapper').find('.twitter-account-wrapper').should('have.length', 2);
	});

	it('Can enable account autoshre by default', () => {

		// Enable Autoshare on account.
		cy.markAccountForAutoshare();

		// Verify that account is checked for Autoshare in Block editor
		cy.enableEditor('block');
		cy.startCreatePost();
		cy.openAutoTweetPanel();
		cy.get('.autoshare-for-twitter-account-toggle input:checkbox').first().should('exist').should('be.checked');
		cy.get('.autoshare-for-twitter-account-toggle input:checkbox').last().should('exist').should('not.be.checked');

		// Classic editor.
		cy.enableEditor('classic');
		cy.classicStartCreatePost();
		cy.get('input:checkbox.autoshare-for-twitter-account-checkbox').first().should('exist').should('be.checked');
		cy.get('input:checkbox.autoshare-for-twitter-account-checkbox').last().should('exist').should('not.be.checked');
	});

	it('Can disable account autoshre by default', () => {
		// Disable Autoshare on account.
		cy.markAccountForAutoshare( false );

		// Verify that account is unchecked for Autoshare in Block editor
		cy.enableEditor('block');
		cy.startCreatePost();
		cy.openAutoTweetPanel();
		cy.get('.autoshare-for-twitter-account-toggle input:checkbox').first().should('exist').should('not.be.checked');
		cy.get('.autoshare-for-twitter-account-toggle input:checkbox').last().should('exist').should('not.be.checked');

		// Classic editor.
		cy.enableEditor('classic');
		cy.classicStartCreatePost();
		cy.get('input:checkbox.autoshare-for-twitter-account-checkbox').first().should('exist').should('not.be.checked');
		cy.get('input:checkbox.autoshare-for-twitter-account-checkbox').last().should('exist').should('not.be.checked');
	});
});