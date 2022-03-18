import { getRandomText } from "../support/functions";

describe( 'Tests that new post is not tweeted when box is unchecked', () => {
	it( 'Autoshare disable default', () => {
		cy.visitAdminPage( 'options-general.php?page=autoshare-for-twitter' );
		cy.wait( 5000 );
		cy.get( 'input[name="autoshare-for-twitter[enable_default]"]' ).eq( 1 ).click();
		cy.get( '#submit' ).click();
	} );

	it( 'Tests that new post is not tweeted when box is unchecked', () => {
		cy.visitAdminPage( 'post-new.php' );
		cy.get( 'button[aria-label="Close dialog"' ).click();
		let postTitle = getRandomText(5);
		cy.get( 'h1.wp-block-post-title' ).type( 'Text' + postTitle );

		cy.get( '[aria-disabled="false"].editor-post-publish-panel__toggle', { timeout: 10000 } ).should( 'be.visible' );
		cy.get( '.editor-post-publish-panel__toggle' ).click();
		cy.wait( 5000 );

		// Pre-publish.
		cy.get( '[aria-disabled="false"].editor-post-publish-button', { timeout: 10000 } ).should( 'be.visible' );
		cy.get( '.editor-post-publish-button' ).click();

		// Post-publish.
		cy.get( '.autoshare-for-twitter-post-status', { timeout: 10000 } ).should( 'be.visible' );
		cy.get( '.autoshare-for-twitter-post-status' ).contains( 'This post was not tweeted.' );
	} );
} );
