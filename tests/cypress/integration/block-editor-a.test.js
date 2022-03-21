import { getRandomText } from "../support/functions";

const slug = 'autoshare-for-twitter';

describe( 'Visit Classic Editor settings page', () => {
	it( 'Update settings to keep the default editor as classic editor', () => {
		cy.visitAdminPage( 'options-writing.php#classic-editor-options' );
		cy.get( '#classic-editor-block' ).click();
		cy.get( '#classic-editor-allow' ).click();
		cy.get( '#submit' ).click();
	} );
} );

describe( 'Tests that new post is not tweeted when box is unchecked', () => {
	it( 'Autoshare disable default', () => {
		cy.visitAdminPage( 'options-general.php?page=autoshare-for-twitter' );
		cy.wait( 5000 );
		cy.get( 'input[name="autoshare-for-twitter[enable_default]"]' ).eq( 1 ).click();
		cy.get( '#submit' ).click();
	} );

	it( 'Tests that new post is not tweeted when box is unchecked', () => {
		cy.visitAdminPage( 'post-new.php' );

		cy.wait( 10000 );
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

		let postTitle = getRandomText(5);

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

		cy.get( '.editor-post-publish-panel__toggle', { timeout: 10000 } ).should( 'be.visible' );
		cy.get( '.editor-post-publish-panel__toggle' ).click();
		cy.wait( 5000 );

		// Pre-publish.
		cy.get( '.editor-post-publish-button', { timeout: 10000 } ).should( 'be.visible' );
		cy.get( '.editor-post-publish-button' ).click();

		// Post-publish.
		cy.get( '.autoshare-for-twitter-post-status', { timeout: 10000 } ).should( 'be.visible' );
		cy.get( '.autoshare-for-twitter-post-status' ).contains( 'This post was not tweeted.' );
	} );
} );