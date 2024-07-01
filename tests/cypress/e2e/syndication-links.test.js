import { getRandomText } from "../support/functions";

describe('Admin can login and make sure plugin is activated', () => {
	beforeEach(() => {
		cy.login();
		cy.clearPluginSettings();
	});

	it('Can activate Syndication Links plugin if it is deactivated', () => {
		cy.activatePlugin('syndication-links');
	});
});

describe('Test Autopost for X with Syndication Links.', () => {
	before(() => {
		cy.login();
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

        it(`Tweet Now should work fine (Classic Editor) - Autoshare: ${(defaultBehavior ? 'Enable': 'Disable')}`, () => {
            // Use the right editor.
            cy.enableEditor('classic');

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

            // Syndication Links.
            cy.get('.autoshare-for-twitter-status-log-data a').then(($a) => {
                const url = $a.attr('href');

                cy.get('input[name="syndication_urls[]"]').should('exist');
                cy.get('input[name="syndication_urls[]"]').eq(1).should('exist');
                cy.get('input[name="syndication_urls[]"]').eq(1).should('have.value', url);
            });
		});

        it(`Tweet Now should work fine (Block Editor) - Autoshare: ${(defaultBehavior ? 'Enable': 'Disable')}`, () => {
            // Use the right editor
            cy.enableEditor('block');

			// Start create new post by enter post title
			cy.startCreatePost();
	
			// Open pre-publish Panel.
			cy.openPrePublishPanel();
	
			// Check enable checkbox for auto-share.
			cy.enableCheckbox('.autoshare-for-twitter-toggle-control input:checkbox', defaultBehavior, false);
	
			// Publish
			cy.publishPost();
	
			// Post-publish.
			cy.get('.autoshare-for-twitter-post-status').should('be.visible');
			cy.get('.autoshare-for-twitter-post-status').contains('This post has not been posted to X/Twitter.');
	
			cy.get('.editor-post-publish-panel button[aria-label="Close panel"]').click();
			cy.openAutoTweetPanel();
			cy.get('.autoshare-for-twitter-editor-panel button.autoshare-for-twitter-tweet-now').click();
			cy.get('.autoshare-for-twitter-editor-panel .autoshare-for-twitter-tweet-text textarea').clear().type(`Random Tweet ${getRandomText(6)}`, {force: true});
			cy.get('.autoshare-for-twitter-editor-panel button.autoshare-for-twitter-re-tweet').click();
			cy.get('.autoshare-for-twitter-log a').contains('Posted to X/Twitter on');

            // Syndication Links.
            cy.get('.autoshare-for-twitter-log a').then(($a) => {
                const url = $a.attr('href');

                cy.get('input[name="syndication_urls[]"]').should('exist');
                cy.get('input[name="syndication_urls[]"]').eq(1).should('exist');
                cy.get('input[name="syndication_urls[]"]').eq(1).should('have.value', url);
            });
		});
    });
});