import { getRandomText } from "../support/functions";

describe( 'Tests that new post is tweeted when box is checked', () => {
	it( 'Tests that new post is tweeted when box is checked', () => {
		cy.visitAdminPage( 'post-new.php' );
		let postTitle = getRandomText(8);
		// cy.get( '#title' ).type( 'Random Post Title' + postTitle );
		cy.get( 'input[name="post_title"]' ).type( 'Random Post Title' + postTitle );
		cy.wait( 3000 );
		cy.get( '#autoshare-for-twitter-enable' ).click();
		cy.get( '#publish' ).click();

		cy.wait( 3000 );

		// Post-publish.
		cy.get( '#autoshare_for_twitter_metabox', { timeout: 5000 } ).should( 'be.visible' );
		cy.get( '#autoshare_for_twitter_metabox', { timeout: 5000 } ).contains( 'Tweeted on' );
	} );
} );

describe( 'Tests that new post is not tweeted when tweet trigger is changed multiple times', () => {
	it( 'Tests that new post is not tweeted when tweet trigger is changed multiple times', () => {
		cy.visitAdminPage( 'post-new.php' );
		let postTitle = getRandomText(8);
		// cy.get( '#title' ).type( 'Random Post Title' + postTitle );
		cy.get( 'input[name="post_title"]' ).type( 'Random Post Title' + postTitle );
		cy.wait( 3000 );

		cy.get( '#autoshare-for-twitter-enable' ).click();
		cy.get( '#save-post' ).click();
		cy.wait( 3000 );

		cy.get( '#autoshare-for-twitter-enable' ).click();
		cy.wait( 3000 );
		cy.get( '#publish' ).click();



		// Post-publish.
		cy.get( '#autoshare_for_twitter_metabox', { timeout: 5000 } ).should( 'be.visible' );
		cy.get( '#autoshare_for_twitter_metabox' ).contains( 'This post was not tweeted' );
	} );
} );


describe( 'Tests that new post is not tweeted when tweet trigger is changed multiple times', () => {
	it( 'Tests that new post is not tweeted when tweet trigger is changed multiple times', () => {
		cy.visitAdminPage( 'post-new.php' );
		let postTitle = getRandomText(8);
		// cy.get( '#title' ).type( 'Random Post Title' + postTitle );
		cy.get( 'input[name="post_title"]' ).type( 'Random Post Title' + postTitle );
		cy.wait( 3000 );

		cy.get( '#save-post' ).click();
		cy.wait( 3000 );

		cy.get( '#autoshare-for-twitter-enable' ).click();
		cy.wait( 3000 );
		cy.get( '#publish' ).click();



		// Post-publish.
		cy.get( '#autoshare_for_twitter_metabox', { timeout: 5000 } ).should( 'be.visible' );
		cy.get( '#autoshare_for_twitter_metabox', { timeout: 5000 } ).contains( 'Tweeted on' );
	} );
} );


