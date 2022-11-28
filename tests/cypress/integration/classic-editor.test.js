import { getRandomText } from "../support/functions";

describe('Test Autoshare for Twitter with Classic Editor.', () => {
	before(() => {
		cy.login();
	});

	it('Update settings to keep the default editor as Classic editor', () => {
		cy.visit('/wp-admin/options-writing.php#classic-editor-options');
		cy.get('#classic-editor-classic').click();
		cy.get('#classic-editor-allow').click();
		cy.get('#submit').click();
	});

	// Run test cases with default Autoshare enabled and disabled both.
	const defaultBehaviors = [false, true];
	defaultBehaviors.forEach( (defaultBehavior) => {	
		it(`Can ${(defaultBehavior ? 'Enable': 'Disable')} default Autoshare`, () => {
			cy.visit('/wp-admin/options-general.php?page=autoshare-for-twitter');
			cy.get('input:checkbox[name="autoshare-for-twitter[enable_default]"]').should('exist');
			if (true === defaultBehavior) {
				cy.get('input:checkbox[name="autoshare-for-twitter[enable_default]"]').check();
			} else {
				cy.get('input:checkbox[name="autoshare-for-twitter[enable_default]"]').uncheck();
			}
			cy.get('#submit').click();
		});

		it('Tests that new post is not tweeted when box is unchecked', () => {
			// Start create post.
			cy.classicStartCreatePost();

			// Check enable checkbox for auto-share.
			cy.enableCheckbox('#autoshare-for-twitter-enable', defaultBehavior, false);

			// publish
			cy.get('#publish').click();
			cy.get('#wpadminbar').should('be.visible');

			// Post-publish.
			cy.get('#autoshare_for_twitter_metabox').should('be.visible');
			cy.get('#autoshare_for_twitter_metabox').contains('This post was not tweeted');
		});


		it('Tests that new post is tweeted when box is checked', () => {
			// Start create post.
			cy.classicStartCreatePost();	

			// Check enable checkbox for auto-share.
			cy.enableCheckbox('#autoshare-for-twitter-enable', defaultBehavior, true);
			cy.get('#publish').click();

			// Post-publish.
			cy.get('#autoshare_for_twitter_metabox',).should('be.visible');
			cy.get('#autoshare_for_twitter_metabox',).contains('Tweeted on');
		});


		it('Tests that draft post is not tweeted when box is unchecked', () => {
			// Start create post.
			cy.classicStartCreatePost();
			
			// Save Draft
			cy.get('#save-post').click();

			// Uncheck the checkbox and publish
			cy.enableCheckbox('#autoshare-for-twitter-enable', defaultBehavior, false);
			cy.get('#publish').click();

			// Post-publish.
			cy.get('#autoshare_for_twitter_metabox').should('be.visible');
			cy.get('#autoshare_for_twitter_metabox').contains('This post was not tweeted');
		});


		it('Tests that draft post is tweeted when box is checked', () => {
			// Start create post.
			cy.classicStartCreatePost();

			// Save Draft
			cy.get('#save-post').click();
			
			// Check the checkbox and publish
			cy.enableCheckbox('#autoshare-for-twitter-enable', defaultBehavior, true);
			cy.get('#publish').click();

			// Post-publish.
			cy.get('#autoshare_for_twitter_metabox').should('be.visible');
			cy.get('#autoshare_for_twitter_metabox').contains('Tweeted on');
		});

		it('Tweet Now should work fine', () => {
			// Start create post.
			cy.classicStartCreatePost();
				
			// Save Draft
			cy.get('#save-post').click();
	
			// Uncheck the checkbox and publish
			cy.enableCheckbox('#autoshare-for-twitter-enable', defaultBehavior, false);
			cy.get('#publish').click();
	
			// Post-publish.
			cy.get('#autoshare_for_twitter_metabox').should('be.visible');
			cy.get('#autoshare_for_twitter_metabox').contains('This post was not tweeted');
	
			cy.get('#autoshare_for_twitter_metabox button.tweet-now-button').contains('Tweet Now').click();
			cy.get('#autoshare-for-twitter-override-body textarea').should('be.visible')
				.clear()
				.type(`Random Tweet ${getRandomText(6)}`);
			cy.get('.autoshare-for-twitter-tweet-now-wrapper #tweet_now').should('be.visible').click();
			cy.get('.autoshare-for-twitter-status-log-data').contains('Tweeted on');
		});
	});
});