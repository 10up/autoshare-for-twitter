const slug = 'autoshare-for-twitter';

describe( 'Turn off default autoshare and make sure post is not tweeted', () => {
	it( 'Autoshare disable default', () => {
		cy.visitAdminPage( 'options-general.php?page=autoshare-for-twitter' );
		cy.get( 'input[name="autoshare-for-twitter[enable_default]"]' ).eq( 1 ).click();
		cy.get( '#submit' ).click();
	} );

	it( 'Tests that new post is not tweeted when box is unchecked', () => {
		cy.visitAdminPage( 'post-new.php' );
		cy.get( 'button[aria-label="Close dialog"' ).click();
		cy.get( '#post-title-0' ).type( 'Text' );

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

	it( 'Tests that new post is tweeted when box is checked', () => {
		cy.visitAdminPage( 'post-new.php' );
		cy.get( 'button[aria-label="Close dialog"' ).click();
		cy.get( '#post-title-0' ).type( 'Just another random test post title' );

		cy.get( '.editor-post-publish-panel__toggle', { timeout: 10000 } ).should( 'be.visible' );
		cy.get( '.editor-post-publish-panel__toggle' ).click();
		cy.wait( 5000 );

		cy.get( 'button' ).contains( 'Autoshare:' ).click();
		cy.get( '.autoshare-for-twitter-prepublish__checkbox-label', { timeout: 10000 } ).should( 'be.visible' );
		cy.get( '.autoshare-for-twitter-prepublish__checkbox-label' ).click();
		cy.wait( 5000 );

		cy.get( '[aria-disabled="false"].editor-post-publish-button__button', { timeout: 10000 } ).should( 'be.visible' );
		cy.get( '.editor-post-publish-button' ).click();

		// Post-publish.
		cy.get( '.autoshare-for-twitter-post-status' ).contains( 'This post was not tweeted.' ).should( 'not.exist' );
		cy.get( '.autoshare-for-twitter-post-status' ).contains( 'Tweeted on' );
	} );
} );

