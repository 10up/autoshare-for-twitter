import { useSelect, useDispatch, dispatch } from '@wordpress/data';
import { useEffect, useCallback } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import { enableAutoshareKey, errorText, restUrl, tweetBodyKey, allowTweetImageKey } from 'admin-autoshare-for-twitter';
import { debounce } from 'lodash';
import { STORE } from '../store';

export function useTweetText() {
	const { tweetText } = useSelect( ( select ) => {
		return {
			tweetText: select( STORE ).getTweetText(),
		};
	} );

	const { setTweetText } = useDispatch( STORE );

	return [ tweetText, setTweetText ];
}

export function useTwitterAutoshareEnabled() {
	const { autoshareEnabled } = useSelect( ( select ) => {
		return {
			autoshareEnabled: select( STORE ).getAutoshareEnabled(),
		};
	} );

	const { setAutoshareEnabled } = useDispatch( STORE );

	return [ autoshareEnabled, setAutoshareEnabled ];
}

export function useTwitterTextOverriding() {
	const { overriding } = useSelect( ( select ) => {
		return {
			overriding: select( STORE ).getOverriding(),
		};
	} );

	const { setOverriding } = useDispatch( STORE );

	return [ overriding, setOverriding ];
}

export function useSavingTweetData() {
	function setSaving( saving ) {
		dispatch( STORE ).setSaving( saving );

		if ( saving ) {
			dispatch( 'core/editor' ).lockPostSaving();
		} else {
			dispatch( 'core/editor' ).unlockPostSaving();
		}
	}

	const { saving } = useSelect( ( select ) => {
		return {
			saving: select( STORE ).getSaving(),
		};
	} );

	return [ saving, setSaving ];
}

export function useAllowTweetImage() {
	const { allowTweetImage } = useSelect( ( select ) => {
		return {
			allowTweetImage: select( STORE ).getAllowTweetImage(),
		};
	} );

	const { setAllowTweetImage } = useDispatch( STORE );

	return [ allowTweetImage, setAllowTweetImage ];
}

export function useTwitterAutoshareErrorMessage() {
	const { errorMessage } = useSelect( ( select ) => {
		return {
			errorMessage: select( STORE ).getErrorMessage(),
		};
	} );

	const { setErrorMessage } = useDispatch( STORE );

	return [ errorMessage, setErrorMessage ];
}

export function useHasFeaturedImage() {
	const { imageId } = useSelect( ( select ) => {
		return {
			imageId: select( 'core/editor' ).getEditedPostAttribute( 'featured_media' ),
		};
	} );

	const hasFeaturedImage = imageId > 0;

	return hasFeaturedImage;
}

export function useSaveTwitterData() {
	const [ autoshareEnabled ] = useTwitterAutoshareEnabled();
	const [ allowTweetImage ] = useAllowTweetImage();
	const [ tweetText ] = useTweetText();
	const [ , setErrorMessage ] = useTwitterAutoshareErrorMessage();
	const [ , setSaving ] = useSavingTweetData();

	const { hasFeaturedImage } = useSelect( ( select ) => {
		const imageId = select( 'core/editor' ).getEditedPostAttribute( 'featured_media' );

		return {
			hasFeaturedImage: imageId > 0,
		};
	} );

	async function saveData( autoshareEnabledArg, tweetTextArg, allowTweetImageArg ) {
		const body = {};
		body[ enableAutoshareKey ] = autoshareEnabledArg;
		body[ tweetBodyKey ] = tweetTextArg;
		body[ allowTweetImageKey ] = allowTweetImageArg;

		try {
			const response = await apiFetch( {
				url: restUrl,
				data: body,
				method: 'POST',
				parse: false, // We'll check the response for errors.
			} );

			if ( ! response.ok ) {
				throw response;
			}

			await response.json();

			setErrorMessage( '' );
			setSaving( false );
		} catch ( e ) {
			setErrorMessage(
				e.statusText ? `${ errorText } ${ e.status }: ${ e.statusText }` : __( 'An error occurred.', 'autoshare-for-twitter' ),
			);

			setSaving( false );
		}
	}

	const saveDataDebounced = useCallback( debounce( saveData, 250 ), [] );

	useEffect( () => {
		saveDataDebounced( autoshareEnabled, tweetText, allowTweetImage );
	}, [ autoshareEnabled, tweetText, hasFeaturedImage, allowTweetImage ] );
}
