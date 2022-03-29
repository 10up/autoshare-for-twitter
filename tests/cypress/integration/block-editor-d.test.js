import { getRandomText } from "../support/functions";

describe( 'Tests that new post is tweeted when tweet trigger is changed multiple times', () => {
	it( 'Tests that new post is tweeted when tweet trigger is changed multiple times', () => {
		cy.visitAdminPage( 'post-new.php' );

		cy.wait( 3000 );
		cy.get("body").then($body => {
			if ($body.find('button[aria-label="Close dialog"]').length > 0) {
				cy.get( 'button[aria-label="Close dialog"]' ).click();
			}
		});

		// The tips dialog has different attribute in lower core version, add check to handle scenario.
		cy.get("body").then($body => {
			if ($body.find('button[aria-label="Disable tips"]').length > 0) {
				cy.get('button[aria-label="Disable tips"]').click();
			}
		});

		let postTitle = getRandomText(6);

		cy.get("body").then($body => {
			if ($body.find('h1.wp-block-post-title').length > 0) {
				cy.get( 'h1.wp-block-post-title' ).type( 'Random Post Title' + postTitle );
			}
		});

		cy.get("body").then($body => {
			if ($body.find('#post-title-0').length > 0) {
				cy.get( '#post-title-0' ).type( 'Random Post Title' + postTitle );
			}
		});

		cy.get( '.editor-post-save-draft' ).should( 'be.visible' );
		cy.get( '.editor-post-save-draft' ).click();
		cy.wait( 3000 );

		cy.get( '.editor-post-publish-panel__toggle', { timeout: 5000 } ).should( 'be.visible' );
		cy.get( '.editor-post-publish-panel__toggle' ).click();
		cy.wait( 3000 );

		// The auto share metabox is at different position in 5.2.11, so add extra step for it.
		cy.get("body").then($body => {
			if ($body.find(".components-panel__body:nth-child(6) .editor-post-publish-panel__link").length > 0) {
				cy.get('.components-panel__body:nth-child(6) .editor-post-publish-panel__link').click();
			}
		});

		cy.get("body").then($body => {
			if ($body.find(".components-panel__body:nth-child(7) .editor-post-publish-panel__link").length > 0) {
				// If on newer version revert back the click done to fix issue with focus.
				cy.get('.components-panel__body:nth-child(6) .editor-post-publish-panel__link').click();

				cy.get('.components-panel__body:nth-child(7) .editor-post-publish-panel__link').click();
			}
		});

		cy.get( '.autoshare-for-twitter-prepublish__checkbox-label', { timeout: 5000 } ).should( 'be.visible' );
		cy.get( '.autoshare-for-twitter-prepublish__checkbox-label' ).click();

		// Pre-publish.
		cy.get( '[aria-disabled="false"].editor-post-publish-button', { timeout: 5000 } ).should( 'be.visible' );
		cy.get( '.editor-post-publish-button' ).click();
		cy.wait( 3000 );

		// Post-publish.
		cy.get( '.autoshare-for-twitter-post-status', { timeout: 5000 } ).should( 'be.visible' );
		cy.get( '.autoshare-for-twitter-post-status' ).contains( 'Tweeted on' );
	} );
} );