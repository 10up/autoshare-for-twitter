import { registerStore } from '@wordpress/data';

import reducer from './reducer';
import * as actions from './actions';
import * as selectors from './selectors';

export const STORE = '10up/autoshare';

export function createAutoshareStore() {
	const store = registerStore( STORE, { reducer, actions, selectors } );



return store;
}
