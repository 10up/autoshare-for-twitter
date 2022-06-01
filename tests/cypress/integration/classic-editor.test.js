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

			// Checkbox
			const isChecked = defaultBehavior ? 'be.checked' : 'not.be.checked';
			cy.get('#autoshare-for-twitter-enable').should('exist');
			cy.get('#autoshare-for-twitter-enable').should(isChecked);
			cy.get('#autoshare-for-twitter-enable').uncheck();

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

			// Checkbox
			const isChecked = defaultBehavior ? 'be.checked' : 'not.be.checked';
			cy.get('#autoshare-for-twitter-enable').should('exist').should(isChecked).check();
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
			const isChecked = defaultBehavior ? 'be.checked' : 'not.be.checked';
			cy.get('#autoshare-for-twitter-enable').should('exist');
			cy.get('#autoshare-for-twitter-enable').should(isChecked);
			cy.get('#autoshare-for-twitter-enable').uncheck();
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
			
			// Uncheck the checkbox and publish
			const isChecked = defaultBehavior ? 'be.checked' : 'not.be.checked';
			cy.get('#autoshare-for-twitter-enable').should('exist');
			cy.get('#autoshare-for-twitter-enable').should(isChecked);
			cy.get('#autoshare-for-twitter-enable').check();
			cy.get('#publish').click();

			// Post-publish.
			cy.get('#autoshare_for_twitter_metabox').should('be.visible');
			cy.get('#autoshare_for_twitter_metabox').contains('Tweeted on');
		});
	});
});