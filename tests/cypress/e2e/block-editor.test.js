import { getRandomText } from "../support/functions";

describe('Test Autoshare for Twitter with Block Editor.', () => {
	before(() => {
		cy.login();
		// Ignore WP 5.2 Synchronous XHR error.
		Cypress.on('uncaught:exception', (err, runnable) => {
			if (err.message.includes("Failed to execute 'send' on 'XMLHttpRequest': Failed to load 'http://localhost:8889/wp-admin/admin-ajax.php': Synchronous XHR in page dismissal") ) {
				return false;
			}
		});

		cy.enableEditor('block');
		cy.configurePlugin();
	});

	beforeEach(() => {
		// Enable Autoshare on account.
		cy.markAccountForAutoshare();
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
			cy.publishPost();

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
			cy.publishPost();

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
			cy.publishPost();

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
			cy.publishPost();

			// Post-publish.
			cy.get('.autoshare-for-twitter-post-status').should('be.visible');
			cy.get('.autoshare-for-twitter-post-status').contains('Tweeted on');
		});

		it('Tests that new post is not tweeted when tweet accounts are unchecked', () => {
			// Start create new post by enter post title
			cy.startCreatePost();

			// Open pre-publish Panel.
			cy.openPrePublishPanel();

			// Check enable checkbox for auto-share.
			cy.enableCheckbox('.autoshare-for-twitter-toggle-control input:checkbox', defaultBehavior, true);
			cy.enableTweetAccount('.autoshare-for-twitter-account-toggle input:checkbox', false);

			// Publish.
			cy.publishPost();

			// Post-publish.
			cy.get('.autoshare-for-twitter-post-status').should('be.visible');
			cy.get('.autoshare-for-twitter-post-status').contains('This post was not tweeted.');
		});

		it('Tests that new post is tweeted when tweet accounts are checked', () => {
			// Disable Autoshare on account.
			cy.markAccountForAutoshare(false);

			// Start create new post by enter post title
			cy.startCreatePost();

			// Open pre-publish Panel.
			cy.openPrePublishPanel();

			// Check enable checkbox for auto-share.
			cy.enableCheckbox('.autoshare-for-twitter-toggle-control input:checkbox', defaultBehavior, true);
			cy.enableTweetAccount('.autoshare-for-twitter-account-toggle input:checkbox', true);

			// Publish.
			cy.publishPost();

			// Post-publish.
			cy.get('.autoshare-for-twitter-post-status').should('be.visible');
			cy.get('.autoshare-for-twitter-post-status').contains('Tweeted on');
		});

		it('Tweet Now should work fine', () => {
			// Start create new post by enter post title
			cy.startCreatePost();
	
			// Open pre-publish Panel.
			cy.openPrePublishPanel();
	
			// Check enable checkbox for auto-share.
			cy.enableCheckbox('.autoshare-for-twitter-toggle-control input:checkbox', defaultBehavior, false);
	
			// Publish
			cy.publishPost();
	
			// Post-publish.
			cy.get('.autoshare-for-twitter-post-status').should('be.visible');
			cy.get('.autoshare-for-twitter-post-status').contains('This post was not tweeted.');
	
			cy.get('.editor-post-publish-panel button[aria-label="Close panel"]').click();
			cy.openDocumentSettingsPanel('Autotweet');
			cy.get('.autoshare-for-twitter-editor-panel button.autoshare-for-twitter-tweet-now').click();
			cy.get('.autoshare-for-twitter-editor-panel .autoshare-for-twitter-tweet-text textarea').clear().type(`Random Tweet ${getRandomText(6)}`);
			cy.get('.autoshare-for-twitter-editor-panel button.autoshare-for-twitter-re-tweet').click();
			cy.get('.autoshare-for-twitter-log a').contains('Tweeted on');
		});
	});
});
