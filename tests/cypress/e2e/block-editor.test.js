describe('Test Autoshare for Twitter with Block Editor.', () => {
	before(() => {
		cy.login();
		// Ignore WP 5.2 Synchronous XHR error.
		Cypress.on('uncaught:exception', (err, runnable) => {
			if (err.message.includes("Failed to execute 'send' on 'XMLHttpRequest': Failed to load 'http://localhost:8889/wp-admin/admin-ajax.php': Synchronous XHR in page dismissal") ) {
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

	// Run test cases with default Autoshare enabled and disabled both.
	const defaultBehaviors = [false, true];
	defaultBehaviors.forEach((defaultBehavior) => {
		it(`Can ${defaultBehavior ? 'Enable' : 'Disable'} default Autoshare`, () => {
			cy.visit('/wp-admin/options-general.php?page=autoshare-for-twitter');

			const defaultSelector = 'input:checkbox[name="autoshare-for-twitter[enable_default]"]';
			cy.get(defaultSelector).should('exist');
			if (true === defaultBehavior) {
				cy.get(defaultSelector).check();
			} else {
				cy.get(defaultSelector).uncheck();
			}
			cy.get('#submit').click();
		});

		it('Tests that new post is not tweeted when box is unchecked', () => {
			// Start create new post by enter post title
			cy.startCreatePost();

			// Open pre-publish Panel.
			cy.openPrePublishPanel();
			
			// Check enable checkbox for auto-share.
			cy.enableCheckbox('.autoshare-for-twitter-toggle-control input:checkbox', defaultBehavior, false);

			// Publish
			cy.get('[aria-disabled="false"].editor-post-publish-button').should('be.visible');
			cy.get('.editor-post-publish-button').click();

			// Post-publish.
			cy.get('.autoshare-for-twitter-post-status').should('be.visible');
			cy.get('.autoshare-for-twitter-post-status').contains('This post was not tweeted.');
		});

		it('Tests that new post is tweeted when box is checked', () => {
			// Start create new post by enter post title
			cy.startCreatePost();

			// Open pre-publish Panel.
			cy.openPrePublishPanel();

			// Check enable checkbox for auto-share.
			cy.enableCheckbox('.autoshare-for-twitter-toggle-control input:checkbox', defaultBehavior, true);

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
			cy.openPrePublishPanel();

			// Uncheck enable checkbox for auto-share.
			cy.enableCheckbox('.autoshare-for-twitter-toggle-control input:checkbox', defaultBehavior, false);

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
			cy.openPrePublishPanel();

			// Check enable checkbox for auto-share.
			cy.enableCheckbox('.autoshare-for-twitter-toggle-control input:checkbox', defaultBehavior, true);

			// Publish.
			cy.get('[aria-disabled="false"].editor-post-publish-button').should('be.visible');
			cy.get('.editor-post-publish-button').click();

			// Post-publish.
			cy.get('.autoshare-for-twitter-post-status').should('be.visible');
			cy.get('.autoshare-for-twitter-post-status').contains('Tweeted on');
		});
	});
});
