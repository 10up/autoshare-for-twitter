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
import '@10up/cypress-wp-utils';
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
