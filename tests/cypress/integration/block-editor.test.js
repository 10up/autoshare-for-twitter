describe('Test Autoshare for Twitter with Block Editor.', () => {
	before(()=>{
		cy.login();
		// Ignore WP 5.2 Synchronous XHR error.
		Cypress.on('uncaught:exception', (err, runnable) => {
			if (err.message.includes("Failed to execute 'send' on 'XMLHttpRequest': Failed to load 'http://localhost:8889/wp-admin/admin-ajax.php': Synchronous XHR in page dismissal") ){
				return false;
			}
		});
	});

	it('Update settings to keep the default editor as block editor', () => {
		cy.visit('/wp-admin/options-writing.php#classic-editor-options');
		cy.get('#classic-editor-block').click();
		cy.get('#classic-editor-allow').click();
		cy.get('#submit').click();
	});


	it('Can disable default Autoshare', () => {
		cy.visit('/wp-admin/options-general.php?page=autoshare-for-twitter');
		cy.get('input:checkbox[name="autoshare-for-twitter[enable_default]"]').should('exist');
		cy.get('input:checkbox[name="autoshare-for-twitter[enable_default]"]').uncheck();
		cy.get('#submit').click();
	});


	it('Tests that new post is not tweeted when box is unchecked', () => {
		// Start create new post by enter post title
		cy.startCreatePost();
		
		cy.get('.editor-post-publish-panel__toggle').should('be.visible');
		cy.get('.editor-post-publish-panel__toggle').click();

		// Publish
		cy.get('.editor-post-publish-button').should('be.visible');
		cy.get('.editor-post-publish-button').click();

		// Post-publish.
		cy.get('.autoshare-for-twitter-post-status').should('be.visible');
		cy.get('.autoshare-for-twitter-post-status').contains('This post was not tweeted.');
	});


	it('Tests that new post is tweeted when box is checked', () => {
		// Start create new post by enter post title
		cy.startCreatePost();

		// Open pre-publish Panel.
		cy.get('.editor-post-publish-panel__toggle').should('be.visible');
		cy.get('.editor-post-publish-panel__toggle').click();
		cy.get('.components-panel__body:contains("Autoshare:")').should('exist');
		cy.get('.components-panel__body:contains("Autoshare:")').click();

		// Check enable checkbox for auto-share.
		cy.get('.autoshare-for-twitter-prepublish__checkbox input:checkbox').should('be.visible');
		cy.get('.autoshare-for-twitter-prepublish__checkbox input:checkbox').check();

		// Publish.
		cy.get('[aria-disabled="false"].editor-post-publish-button').should('be.visible');
		cy.get('.editor-post-publish-button').click();

		// Post-publish.
		cy.get('.autoshare-for-twitter-post-status').should('be.visible');
		cy.get('.autoshare-for-twitter-post-status').contains('Tweeted on');
	});


	it('Tests that Draft post is not tweeted when box is unchecked', () => {
		// Start create new post by enter post title
		cy.startCreatePost();

		// Save Draft
		cy.get('.editor-post-save-draft').should('be.visible');
		cy.get('.editor-post-save-draft').click();
		cy.get('.editor-post-saved-state').should('have.text', 'Saved');

		// Open pre-publish Panel.
		cy.get('.editor-post-publish-panel__toggle').should('be.visible');
		cy.get('.editor-post-publish-panel__toggle').click();
		cy.get('.components-panel__body:contains("Autoshare:")').should('exist');
		cy.get('.components-panel__body:contains("Autoshare:")').click();

		// Uncheck enable checkbox for auto-share.
		cy.get('.autoshare-for-twitter-prepublish__checkbox input:checkbox').should('be.visible');
		cy.get('.autoshare-for-twitter-prepublish__checkbox input:checkbox').uncheck();

		// Publish.
		cy.get('[aria-disabled="false"].editor-post-publish-button').should('be.visible');
		cy.get('.editor-post-publish-button').click();

		// Post-publish.
		cy.get('.autoshare-for-twitter-post-status').should('be.visible');
		cy.get('.autoshare-for-twitter-post-status').contains('This post was not tweeted.');
	});

	
	it('Tests that Draft post is tweeted when box is checked', () => {
		// Start create new post by enter post title
		cy.startCreatePost();

		// Save Draft
		cy.get('.editor-post-save-draft').should('be.visible');
		cy.get('.editor-post-save-draft').click();
		cy.get('.editor-post-saved-state').should('have.text', 'Saved');
		
		// Open pre-publish Panel.
		cy.get('.editor-post-publish-panel__toggle').should('be.visible');
		cy.get('.editor-post-publish-panel__toggle').click();
		cy.get('.components-panel__body:contains("Autoshare:")').should('exist');
		cy.get('.components-panel__body:contains("Autoshare:")').click();

		// Check enable checkbox for auto-share.
		cy.get('.autoshare-for-twitter-prepublish__checkbox input:checkbox').should('be.visible');
		cy.get('.autoshare-for-twitter-prepublish__checkbox input:checkbox').check();

		// Publish.
		cy.get('[aria-disabled="false"].editor-post-publish-button').should('be.visible');
		cy.get('.editor-post-publish-button').click();

		// Post-publish.
		cy.get('.autoshare-for-twitter-post-status').should('be.visible');
		cy.get('.autoshare-for-twitter-post-status').contains('Tweeted on');
	});
});