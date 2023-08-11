// ***********************************************
// This example commands.js shows you how to
// create various custom commands and overwrite
// existing commands.
//
// For more comprehensive examples of custom
// commands please read more here:
// https://on.cypress.io/custom-commands
// ***********************************************
//
//
// -- This is a parent command --
// Cypress.Commands.add('login', (email, password) => { ... })
//
//
// -- This is a child command --
// Cypress.Commands.add('drag', { prevSubject: 'element'}, (subject, options) => { ... })
//
//
// -- This is a dual command --
// Cypress.Commands.add('dismiss', { prevSubject: 'optional'}, (subject, options) => { ... })
//
//
// -- This will overwrite an existing command --
// Cypress.Commands.overwrite('visit', (originalFn, url, options) => { ... })
import { getRandomText } from "../support/functions";

Cypress.Commands.add( 'startCreatePost', () => {
	cy.visit('/wp-admin/post-new.php');
	const titleInput = 'h1.editor-post-title__input, #post-title-0';

	// Make sure editor loaded properly.
	cy.getBlockEditor().find(titleInput).should('exist');
	cy.closeWelcomeGuide();

	cy.getBlockEditor()
		.find(titleInput)
		.clear()
		.type(`Random Post Title ${getRandomText(6)}`);
});


Cypress.Commands.add( 'classicStartCreatePost', () => {
	cy.visit('/wp-admin/post-new.php');
	let postTitle = getRandomText(8);
	cy.get('input[name="post_title"]').type('Random Post Title' + postTitle );
});

Cypress.Commands.add( 'openPrePublishPanel', () => {
	// Open pre-publish Panel.
	cy.get('.editor-post-publish-panel__toggle').should('be.visible');
	cy.get('.editor-post-publish-panel__toggle').click();
	cy.wait(500); // prevent clicking on category assign suggestion panel. ToDo: find more proper way to handle this.
	cy.get('.autoshare-for-twitter-pre-publish-panel').should('exist');
	cy.get('.autoshare-for-twitter-pre-publish-panel').click();	
});

Cypress.Commands.add( 'enableCheckbox', ( checkboxSelector, defaultBehavior, check = true ) => {
	// Check/Uncheck enable checkbox for auto-share.
	cy.get(checkboxSelector).should('exist');
	if (true === defaultBehavior) {
		cy.get(checkboxSelector).first().should('be.checked');
	} else {
		cy.get(checkboxSelector).first().should('not.be.checked');
	}

	cy.intercept('**/autoshare/v1/post-autoshare-for-twitter-meta/*').as('enableCheckbox');
	if (true === check) {
		cy.get(checkboxSelector).first().check({force: true});
		if(defaultBehavior !== check){
			cy.wait('@enableCheckbox');
		}
		cy.get(checkboxSelector).first().should('be.checked');
	} else {
		cy.get(checkboxSelector).first().uncheck({force: true});
		if(defaultBehavior !== check){
			cy.wait('@enableCheckbox');
		}
		cy.get(checkboxSelector).first().should('not.be.checked');
	}
});

Cypress.Commands.add( 'openAutoTweetPanel', ( inPrePublish = false ) => {
	// Open Autotweet Panel.
	let panelSelector = inPrePublish ? '.autoshare-for-twitter-pre-publish-panel' : '.autoshare-for-twitter-editor-panel';
	cy.get(`${panelSelector} button.components-button`).then($button => {
		const $panel = $button.parents('.components-panel__body');
		if (!$panel.hasClass('is-opened')) {
			cy.wrap($button)
				.click()
				.parents('.components-panel__body')
				.should('have.class', 'is-opened');
		}
	});
});

Cypress.Commands.add( 'markAccountForAutoshare', ( enable = true ) => {
	cy.visit('/wp-admin/options-general.php?page=autoshare-for-twitter');
	cy.get('.twitter_accounts #the-list tr').should('be.visible');
	const checkbox = cy.get('input[name="autoshare-for-twitter[autoshare_accounts][]"]').first();
	if ( enable ) {
		checkbox.should('exist').check();
	} else {
		checkbox.should('exist').uncheck();
	}
	cy.get('#submit').click();
	cy.get('.notice.notice-success').should('be.visible');
});

Cypress.Commands.add( 'enableEditor', ( editor = 'block' ) => {
	cy.visit('/wp-admin/options-writing.php#classic-editor-options');
		cy.get(`#classic-editor-${editor}`).click();
		cy.get('#classic-editor-allow').click();
		cy.get('#submit').click();
});

Cypress.Commands.add( 'enableTweetAccount', ( selector, check = true ) => {
	// Check/Uncheck enable checkbox for auto-share.
	const checkbox = cy.get(selector).first();
	checkbox.should('exist');
	cy.intercept('**/autoshare/v1/post-autoshare-for-twitter-meta/*').as('enableTweetAccount');
	if (true === check) {
		checkbox.check({force: true});
		cy.wait('@enableTweetAccount');
		checkbox.should('be.checked');
	} else {
		checkbox.uncheck({force: true});
		cy.wait('@enableTweetAccount');
		checkbox.should('not.be.checked');
	}
});

Cypress.Commands.add( 'configurePlugin', () => {
	cy.visit('/wp-admin/options-general.php?page=autoshare-for-twitter');
	cy.get('body').then($body => {
		const apiKeySelector = '.credentials-setup.connected';
        if ($body.find(apiKeySelector).length < 1) {
            cy.get('.large-text:nth-child(1) .large-text').clear().type( 'TEST_TWITTER_API_KEY' );
			cy.get('.large-text:nth-child(2) .large-text').clear().type( 'TEST_TWITTER_API_SECRET' );
			cy.get('#submit').click();
        }
    });

	cy.get('body').then($body => {
		const accountSelector = '.twitter_accounts #the-list tr';
        if ($body.find(accountSelector).length < 2) {
			cy.connectAccounts();
        }
    });
});

Cypress.Commands.add( 'clearPluginSettings', () => {
	cy.wpCli(`option delete autoshare-for-twitter autoshare_for_twitter_accounts`);
});

Cypress.Commands.add( 'connectAccounts', () => {
	cy.wpCli(`option update autoshare_for_twitter_accounts '{"TEST_ACCOUNT_ID":{"id":"TEST_ACCOUNT_ID","name":"Test Twitter User","username":"testtwitteruser","profile_image_url":"https://placehold.co\/48x48?text=T1","oauth_token":"TEST_OUTH_TOKEN","oauth_token_secret":"TEST_OUTH_TOKEN_SECRET"},"TEST_ACCOUNT_ID2":{"id":"TEST_ACCOUNT_ID2","name":"Test Twitter User 2","username":"testtwitteruser2","profile_image_url":"https://placehold.co\/48x48?text=T2","oauth_token":"TEST_OUTH_TOKEN2","oauth_token_secret":"TEST_OUTH_TOKEN_SECRET2"}}' --format=json`);
});

Cypress.Commands.add( 'publishPost', () => {
    cy.intercept({ method: 'POST' }, req => {
      const body = req.body;
      if (body.status === 'publish') {
        req.alias = 'publishPost';
      }
    });

	cy.get('[aria-disabled="false"].editor-post-publish-button').click();
    cy.wait('@publishPost');
});

Cypress.Commands.add('getBlockEditor', () => {
	cy.get('.edit-post-visual-editor').should('be.visible');
	return cy
		.get('body')
		.then(($body) => {
			if ($body.find('iframe[name="editor-canvas"]').length) {
				return cy.iframe('iframe[name="editor-canvas"]');
			}
			return $body;
		})
		.then(cy.wrap);
});

Cypress.Commands.add('closeWelcomeGuide', () => {
    // Wait for edit page to load
	cy.getBlockEditor();
	const closeButtonSelector = '.edit-post-welcome-guide .components-modal__header button';
    cy.get('body').then($body => {
        if ($body.find(closeButtonSelector).length > 0) {
            cy.get(closeButtonSelector).click();
        }
    });
});
