import { getRandomText } from "../support/functions";

const slug = 'autoshare-for-twitter';

describe( 'Visit Classic Editor settings page', () => {
	it( 'Update settings to keep the default editor as block editor', () => {
		cy.visitAdminPage( 'options-writing.php#classic-editor-options' );
		cy.get( '#classic-editor-classic' ).click();
		cy.get( '#classic-editor-allow' ).click();
		cy.get( '#submit' ).click();
	} );
} );

describe( 'Tests that new post is not tweeted when box is unchecked', () => {
	it( 'Tests that new post is not tweeted when box is unchecked', () => {
		cy.visitAdminPage( 'post-new.php' );
		let postTitle = getRandomText(6);
		cy.get( '#title' ).type( 'Random Post Title' + postTitle );
		cy.get( '#publish' ).click();

		cy.get( '#wpadminbar', { timeout: 5000 } ).should( 'be.visible' );
		cy.wait( 3000 );

		// Post-publish.
		cy.get( '#autoshare_for_twitter_metabox', { timeout: 5000 } ).should( 'be.visible' );
		cy.get( '#autoshare_for_twitter_metabox' ).contains( 'This post was not tweeted' );
	} );
} );
