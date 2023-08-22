import '@10up/cypress-wp-utils';
import 'cypress-iframe';
import './commands';

beforeEach(() => {
	Cypress.Cookies.defaults({
		preserve: /^wordpress.*?/,
	});
});