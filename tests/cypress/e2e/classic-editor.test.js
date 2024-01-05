import { getRandomText } from "../support/functions";

describe('Test Autopost for X with Classic Editor.', () => {
	before(() => {
		cy.login();
		
		cy.enableEditor('classic');
		cy.configurePlugin();
	});

	beforeEach(() => {
		cy.login();
		// Enable Autoshare on account.
		cy.markAccountForAutoshare();
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
			cy.get('#publish').should('not.be.disabled');
			cy.get('#publish').should('be.visible').click();

			// Post-publish.
			cy.get('#autoshare_for_twitter_metabox').should('be.visible');
			cy.get('#autoshare_for_twitter_metabox').contains('This post has not been posted to X/Twitter');
		});


		it('Tests that new post is tweeted when box is checked', () => {
			// Start create post.
			cy.classicStartCreatePost();	

			// Check enable checkbox for auto-share.
			cy.enableCheckbox('#autoshare-for-twitter-enable', defaultBehavior, true);
			cy.get('#publish').should('not.be.disabled');
			cy.get('#publish').should('be.visible').click();

			// Post-publish.
			cy.get('#autoshare_for_twitter_metabox',).should('be.visible');
			cy.get('#autoshare_for_twitter_metabox',).contains('Posted to X/Twitter on');
		});


		it('Tests that draft post is not tweeted when box is unchecked', () => {
			// Start create post.
			cy.classicStartCreatePost();
			
			// Save Draft
			cy.get('#save-post').click();

			// Uncheck the checkbox and publish
			cy.enableCheckbox('#autoshare-for-twitter-enable', defaultBehavior, false);
			cy.get('#publish').should('not.be.disabled');
			cy.get('#publish').should('be.visible').click();

			// Post-publish.
			cy.get('#autoshare_for_twitter_metabox').should('be.visible');
			cy.get('#autoshare_for_twitter_metabox').contains('This post has not been posted to X/Twitter');
		});


		it('Tests that draft post is tweeted when box is checked', () => {
			// Start create post.
			cy.classicStartCreatePost();

			// Save Draft
			cy.get('#save-post').click();
			
			// Check the checkbox and publish
			cy.enableCheckbox('#autoshare-for-twitter-enable', defaultBehavior, true);
			cy.get('#publish').should('not.be.disabled');
			cy.get('#publish').should('be.visible').click();

			// Post-publish.
			cy.get('#autoshare_for_twitter_metabox').should('be.visible');
			cy.get('#autoshare_for_twitter_metabox').contains('Posted to X/Twitter on');
		});

		it('Tests that new post is not tweeted when tweet accounts are unchecked', () => {
			// Start create post.
			cy.classicStartCreatePost();

			// Check enable checkbox for auto-share.
			cy.enableCheckbox('#autoshare-for-twitter-enable', defaultBehavior, true);
			cy.enableTweetAccount('input.autoshare-for-twitter-account-checkbox', false);

			// publish.
			cy.get('#publish').should('not.be.disabled');
			cy.get('#publish').should('be.visible').click();

			// Post-publish.
			cy.get('#autoshare_for_twitter_metabox').should('be.visible');
			cy.get('#autoshare_for_twitter_metabox').contains('This post has not been posted to X/Twitter');
		});

		it('Tests that new post is tweeted when tweet accounts are checked', () => {
			// Disable Autoshare on account.
			cy.markAccountForAutoshare(false);
			
			// Start create post.
			cy.classicStartCreatePost();

			// Check enable checkbox for auto-share.
			cy.enableCheckbox('#autoshare-for-twitter-enable', defaultBehavior, true);
			cy.enableTweetAccount('input.autoshare-for-twitter-account-checkbox', true);

			// publish
			cy.get('#publish').should('not.be.disabled');
			cy.get('#publish').should('be.visible').click();

			// Post-publish.
			cy.get('#autoshare_for_twitter_metabox',).should('be.visible');
			cy.get('#autoshare_for_twitter_metabox',).contains('Posted to X/Twitter on');
		});

		it('Tweet Now should work fine', () => {
			// Start create post.
			cy.classicStartCreatePost();
				
			// Save Draft
			cy.get('#save-post').click();
	
			// Uncheck the checkbox and publish
			cy.enableCheckbox('#autoshare-for-twitter-enable', defaultBehavior, false);
			cy.get('#publish').should('not.be.disabled');
			cy.get('#publish').should('be.visible').click();
	
			// Post-publish.
			cy.get('#autoshare_for_twitter_metabox').should('be.visible');
			cy.get('#autoshare_for_twitter_metabox').contains('This post has not been posted to X/Twitter');
	
			cy.get('#autoshare_for_twitter_metabox button.tweet-now-button').contains('Post to X/Twitter now').click();
			cy.get('#autoshare-for-twitter-override-body textarea').should('be.visible')
				.clear()
				.type(`Random Tweet ${getRandomText(6)}`);
			cy.get('.autoshare-for-twitter-tweet-now-wrapper #tweet_now').should('be.visible').click();
			cy.get('.autoshare-for-twitter-status-log-data').contains('Posted to X/Twitter on');
		});
	});

	it('Tests that custom tweet message remain persistent for Tweet', () => {
		const customTweetBody = `Custom Tweet ${getRandomText(6)}`;
		// Start create post.
		cy.classicStartCreatePost();

		// Set custom tweet message.
		cy.enableCheckbox('#autoshare-for-twitter-enable', true, true);
		cy.get('#autoshare-for-twitter-edit').click();
		cy.get('textarea#autoshare-for-twitter-text').clear().type(customTweetBody);

		// Save Draft
		cy.get('#save-post').click();

		// verify custom tweet message.
		cy.get('textarea#autoshare-for-twitter-text').should('have.value', customTweetBody);

		// publish
		cy.get('#publish').should('not.be.disabled');
		cy.get('#publish').should('be.visible').click();

		// Post-publish.
		cy.get('#autoshare_for_twitter_metabox',).should('be.visible');
		cy.get('#autoshare_for_twitter_metabox',).contains('Posted to X/Twitter on');

		// Verify custom tweet message is cleared on publish.
		cy.get('button.tweet-now-button').click();
		cy.get('textarea#autoshare-for-twitter-text').should('have.value', '');
	});
});
