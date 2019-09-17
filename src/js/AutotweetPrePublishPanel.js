import apiFetch from '@wordpress/api-fetch';
import { Button, CheckboxControl, Dashicon, TextareaControl } from '@wordpress/components';
import { withDispatch, withSelect } from '@wordpress/data';
import { compose } from '@wordpress/compose';
import { useEffect } from '@wordpress/element';
import { debounce } from 'lodash';
import { enableAutotweetKey, restUrl, tweetBodyKey } from 'admin-autotweet';
import { __ } from '@wordpress/i18n';

import { STORE } from './store';

export function AutotweetPrePublishPanel( {
	autotweetEnabled,
	errorMessage,
	overriding,
	overrideLength,
	saving,
	setAutotweetEnabled,
	setErrorMessage,
	setOverriding,
	setOverrideLength,
	setSaving,
	setTweetText,
	tweetText,
} ) {
	const saveData = debounce( async () => {
		const body = {};
		body[ enableAutotweetKey ] = autotweetEnabled;
		body[ tweetBodyKey ] = tweetText;

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
			// eslint-disable-next-line no-console
			console.log( e );
		}
	}, 1000 );

	useEffect( () => {
		saveData();
	}, [ autotweetEnabled, tweetText ] );

	const twitterIconClass = () => {
		const iconClass = autotweetEnabled ? 'enabled' : 'disabled';
		return `${ iconClass } ${ saving ? 'pending' : '' }`;
	};

	return (
		<>
			<div className="autotweet-prepublish__checkbox-row">
				<CheckboxControl
					className="autotweet-prepublish__checkbox"
					label={
						<span className="autotweet-prepublish__checkbox-label">
							<Dashicon icon="twitter" className={ twitterIconClass() } />
							{ __( 'Tweet this post?', 'autotweet' ) }
						</span>
					}
					checked={ autotweetEnabled }
					onChange={ ( checked ) => {
						setAutotweetEnabled( checked );
					} }
				/>
			</div>

			{ autotweetEnabled && (
				<div className="autotweet-prepublish__override-row">
					{ overriding && (
						<TextareaControl
							value={ tweetText }
							onChange={ ( value ) => {
								if ( value.length <= 280 ) {
									setTweetText( value );
									setOverrideLength( value.length );
								}
							} }
							label={
								<span className="autotweet-prepublish__message-label">
									<span>{ __( 'Custom message:', 'autotweet' ) }</span>
									<span id="tenup-auto-tweet-counter-wrap">{ overrideLength }</span>
								</span>
							}
						/>
					) }

					<Button
						isLink
						onClick={ () => {
							setOverriding( ! overriding );
						} }
					>
						{ overriding ? __( 'Hide', 'autotweet' ) : __( 'Edit', 'autotweet' ) }
					</Button>
				</div>
			) }

			<div>{ errorMessage }</div>
		</>
	);
}

export default compose(
	withSelect( ( select ) => ( {
		autotweetEnabled: select( STORE ).getAutotweetEnabled(),
		errorMessage: select( STORE ).getErrorMessage(),
		overriding: select( STORE ).getOverriding(),
		overrideLength: select( STORE ).getOverrideLength(),
		saving: select( STORE ).getSaving(),
		tweetText: select( STORE ).getTweetText(),
	} ) ),
	withDispatch( ( dispatch ) => ( {
		setAutotweetEnabled: dispatch( STORE ).setAutotweetEnabled,
		setErrorMessage: dispatch( STORE ).setErrorMessage,
		setOverriding: dispatch( STORE ).setOverriding,
		setOverrideLength: dispatch( STORE ).setOverrideLength,
		setSaving: dispatch( STORE ).setSaving,
		setTweetText: dispatch( STORE ).setTweetText,
	} ) ),
)( AutotweetPrePublishPanel );
