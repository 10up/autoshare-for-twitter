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
		cy.get( '#title' ).type( 'Random Post Title' );
		cy.get( '#publish' ).click();

		cy.get( '#wpadminbar', { timeout: 10000 } ).should( 'be.visible' );
		cy.wait( 5000 );

		// Post-publish.
		cy.get( '#autoshare_for_twitter_metabox', { timeout: 10000 } ).should( 'be.visible' );
		cy.get( '#autoshare_for_twitter_metabox' ).contains( 'This post was not tweeted' );
	} );
} );

describe( 'Tests that new post is tweeted when box is checked', () => {
	it( 'Tests that new post is tweeted when box is checked', () => {
		cy.visitAdminPage( 'post-new.php' );
		cy.get( '#title' ).type( 'Random Post Title' );
		cy.get( '#autoshare-for-twitter-enable' ).click();
		cy.get( '#publish' ).click();

		// Post-publish.
		cy.get( '#autoshare_for_twitter_metabox', { timeout: 10000 } ).should( 'be.visible' );
		cy.get( '#autoshare_for_twitter_metabox' ).contains( 'Tweeted on' );
	} );
} );


