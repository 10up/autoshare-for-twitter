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
	cy.get(titleInput).should('exist');
	cy.closeWelcomeGuide();

	cy.get(titleInput).clear().type(`Random Post Title ${getRandomText(6)}`);
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
		cy.get(checkboxSelector).should('be.checked');
	} else {
		cy.get(checkboxSelector).should('not.be.checked');
	}
	const alias = `enableCheckbox${new Date().getTime()}${Math.floor(Math.random() * 999)}`;
	cy.intercept('**/autoshare/v1/post-autoshare-for-twitter-meta/*').as(alias);
	if (true === check) {
		cy.get(checkboxSelector).check({force: true});
		if(defaultBehavior !== check){
			cy.wait(`@${alias}`).then(response => {
				expect(response.response?.body?.enabled).to.equal(check);
			});
		}
		cy.get(checkboxSelector).should('be.checked');
	} else {
		cy.get(checkboxSelector).uncheck({force: true});
		if(defaultBehavior !== check){
			cy.wait(`@${alias}`).then(response => {
				expect(response.response?.body?.enabled).to.equal(check);
			});
		}
		cy.get(checkboxSelector).should('not.be.checked');
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
