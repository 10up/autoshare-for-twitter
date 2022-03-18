import { getRandomText } from "../support/functions";

const slug = 'autoshare-for-twitter';

describe( 'Visit Classic Editor settings page', () => {
	it( 'Update settings to keep the default editor as classic editor', () => {
		cy.visitAdminPage( 'options-writing.php#classic-editor-options' );
		cy.get( '#classic-editor-block' ).click();
		cy.get( '#classic-editor-allow' ).click();
		cy.get( '#submit' ).click();
	} );
} );
