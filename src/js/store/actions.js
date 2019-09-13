import { SET_AUTOTWEET_ENABLED, SET_ERROR_MESSAGE, SET_TWEET_TEXT, SET_SAVING, SET_LOADED } from './constants';

export const setAutotweetEnabled = ( autotweetEnabled ) => ( {
	type: SET_AUTOTWEET_ENABLED,
	autotweetEnabled,
} );

export const setErrorMessage = ( errorMessage ) => ( {
	type: SET_ERROR_MESSAGE,
	errorMessage,
} );

export const setLoaded = () => ( {
	type: SET_LOADED,
} );

export const setSaving = ( saving ) => ( {
	type: SET_SAVING,
	saving,
} );

export const setTweetText = ( tweetText ) => ( {
	type: SET_TWEET_TEXT,
	tweetText,
} );
