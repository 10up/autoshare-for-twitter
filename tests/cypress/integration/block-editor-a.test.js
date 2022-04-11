import { getRandomText } from "../support/functions";

describe('Visit Classic Editor settings page', () => {
	it('Update settings to keep the default editor as classic editor', () => {
		cy.visitAdminPage('options-writing.php#classic-editor-options');
		cy.get('#classic-editor-block').click();
		cy.get('#classic-editor-allow').click();
		cy.get('#submit').click();
	});
});

describe('Tests that new post is not tweeted when box is unchecked', () => {
	it('Autoshare disable default', () => {
		cy.visitAdminPage('options-general.php?page=autoshare-for-twitter');
		cy.get('input:checkbox[name="autoshare-for-twitter[enable_default]"]').should('exist');
		cy.get('input:checkbox[name="autoshare-for-twitter[enable_default]"]').uncheck();
		cy.get('#submit').click();
	});

	it('Tests that new post is not tweeted when box is unchecked', () => {
		cy.visitAdminPage('post-new.php');
		const titleInput = 'h1.editor-post-title__input, #post-title-0';

		// Make sure editor loaded properly.
		cy.get(titleInput).should('exist');

		cy.closeWelcomeGuide();
		cy.get(titleInput).clear().type(`Random Post Title ${getRandomText(5)}`);
		
		cy.get('.editor-post-publish-panel__toggle').should('be.visible');
		cy.get('.editor-post-publish-panel__toggle').click();

		// Pre-publish.
		cy.get('.editor-post-publish-button').should('be.visible');
		cy.get('.editor-post-publish-button').click();

		// Post-publish.
		cy.get('.autoshare-for-twitter-post-status').should('be.visible');
		cy.get('.autoshare-for-twitter-post-status').contains('This post was not tweeted.');
	});
});