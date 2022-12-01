import '@10up/cypress-wp-utils';
import './commands';

beforeEach(() => {
	Cypress.Cookies.defaults({
		preserve: /^wordpress.*?/,
	});
});