import { getRandomText } from "../support/functions";

const slug = 'autoshare-for-twitter';

describe( 'Tests that new post is tweeted when box is checked', () => {
	it( 'Tests that new post is tweeted when box is checked', () => {
		cy.visitAdminPage( 'post-new.php' );
		cy.get( 'button[aria-label="Close dialog"' ).click();
		let postTitle = getRandomText(6);
		cy.get( 'h1.wp-block-post-title' ).type( 'Random Post Title' + postTitle );

		cy.get( '[aria-disabled="false"].editor-post-publish-panel__toggle', { timeout: 10000 } ).should( 'be.visible' );
		cy.get( '.editor-post-publish-panel__toggle' ).click();
		cy.wait( 5000 );

		cy.get('.components-panel__body:nth-child(7) .editor-post-publish-panel__link').click();
		cy.get( '.autoshare-for-twitter-prepublish__checkbox-label', { timeout: 10000 } ).should( 'be.visible' );
		cy.get( '.autoshare-for-twitter-prepublish__checkbox-label' ).click();
		cy.wait( 5000 );

		// Pre-publish.
		cy.get( '[aria-disabled="false"].editor-post-publish-button', { timeout: 10000 } ).should( 'be.visible' );
		cy.get( '.editor-post-publish-button' ).click();

		// Post-publish.
		cy.get( '.autoshare-for-twitter-post-status', { timeout: 10000 } ).should( 'be.visible' );
		cy.get( '.autoshare-for-twitter-post-status' ).contains( 'Tweeted on' );
	} );
} );