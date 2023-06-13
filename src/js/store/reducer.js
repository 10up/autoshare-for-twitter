import { enabled, allowTweetImage, tweetAccounts } from 'admin-autoshare-for-twitter';

import {
	SET_AUTOSHARE_FOR_TWITTER_ENABLED,
	SET_ERROR_MESSAGE,
	SET_TWEET_TEXT,
	SET_SAVING,
	SET_LOADED,
	SET_OVERRIDING,
	SET_ALLOW_TWEET_IMAGE,
	SET_TWEET_ACCOUNTS,
} from './constants';

export const DEFAULT_STATE = {
	autoshareEnabled: ! ! enabled,
	errorMessage: '',
	loaded: false,
	overriding: false,
	overrideLength: 0,
	tweetText: '',
	allowTweetImage: ! ! allowTweetImage,
	tweetAccounts: tweetAccounts || [],
};

export default function reducer( state = DEFAULT_STATE, action ) {
	switch ( action.type ) {
		case SET_AUTOSHARE_FOR_TWITTER_ENABLED:
			return {
				...state,
				autoshareEnabled: action.autoshareEnabled,
			};

		case SET_ERROR_MESSAGE:
			return {
				...state,
				errorMessage: action.errorMessage,
			};

		case SET_LOADED: {
			return {
				...state,
				loaded: true,
			};
		}

		case SET_OVERRIDING: {
			return {
				...state,
				overriding: action.overriding,
			};
		}

		case SET_SAVING: {
			return {
				...state,
				saving: action.saving,
			};
		}

		case SET_TWEET_TEXT: {
			return {
				...state,
				tweetText: action.tweetText,
			};
		}

		case SET_ALLOW_TWEET_IMAGE: {
			return {
				...state,
				allowTweetImage: action.allowTweetImage,
			};
		}

		case SET_TWEET_ACCOUNTS: {
			return {
				...state,
				tweetAccounts: action.tweetAccounts,
			};
		}
	}
}
