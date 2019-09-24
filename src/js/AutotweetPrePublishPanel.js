import apiFetch from '@wordpress/api-fetch';
import { Button, CheckboxControl, Dashicon, TextareaControl } from '@wordpress/components';
import { withDispatch, withSelect } from '@wordpress/data';
import { compose } from '@wordpress/compose';
import { Component } from '@wordpress/element';
import { debounce } from 'lodash';
import { enableAutotweetKey, errorText, restUrl, tweetBodyKey } from 'admin-autotweet';
import { __ } from '@wordpress/i18n';

import { STORE } from './store';

class AutotweetPrePublishPanel extends Component {
	constructor( props ) {
		super( props );

		this.state = { autotweetEnabled: null, tweetText: null };

		this.saveData = debounce( this.saveData.bind( this ), 1000 );
	}

	componentDidUpdate() {
		const { autotweetEnabled, tweetText } = this.props;

		if ( autotweetEnabled !== this.state.autotweetEnabled || tweetText !== this.state.tweetText ) {
			this.setState( { autotweetEnabled, tweetText }, this.saveData );
		}
	}

	async saveData() {
		const { autotweetEnabled, setErrorMessage, setSaving, tweetText } = this.props;

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
			setErrorMessage(
				e.statusText ? `${ errorText } ${ e.status }: ${ e.statusText }` : __( 'An error occurred.', 'autotweet' ),
			);
		}
	}

	render() {
		const {
			autotweetEnabled,
			errorMessage,
			overriding,
			overrideLength,
			saving,
			setAutotweetEnabled,
			setOverriding,
			setOverrideLength,
			setTweetText,
			tweetText,
		} = this.props;

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
