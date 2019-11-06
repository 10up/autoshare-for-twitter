import apiFetch from '@wordpress/api-fetch';
import { Button, CheckboxControl, Dashicon, TextareaControl } from '@wordpress/components';
import { withDispatch, withSelect } from '@wordpress/data';
import { compose } from '@wordpress/compose';
import { Component } from '@wordpress/element';
import { debounce } from 'lodash';
import { enableAutoshareKey, errorText, restUrl, siteUrl, tweetBodyKey } from 'admin-autoshare-for-twitter';
import { __ } from '@wordpress/i18n';

import { STORE } from './store';

class AutoshareForTwitterPrePublishPanel extends Component {
	constructor( props ) {
		super( props );

		// Although these values are delivered as props, we copy them into state so that we can check for changes
		// and save data when they update.
		this.state = { autoshareEnabled: null, tweetText: null };

		this.saveData = debounce( this.saveData.bind( this ), 250 );
	}

	componentDidMount() {
		const { autoshareEnabled, tweetText } = this.props;

		this.setState( { autoshareEnabled, tweetText } );
	}

	componentDidUpdate() {
		const { autoshareEnabled, tweetText } = this.props;

		// Update if either of these values has changed in the data store.
		if ( autoshareEnabled !== this.state.autoshareEnabled || tweetText !== this.state.tweetText ) {
			this.setState( { autoshareEnabled, tweetText }, this.saveData );
		}
	}

	async saveData() {
		const { autoshareEnabled, setErrorMessage, setSaving, tweetText } = this.props;

		const body = {};
		body[ enableAutoshareKey ] = autoshareEnabled;
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
				e.statusText ? `${ errorText } ${ e.status }: ${ e.statusText }` : __( 'An error occurred.', 'auto-share-for-twitter' ),
			);
		}
	}

	render() {
		const {
			autoshareEnabled,
			errorMessage,
			overriding,
			permalinkLength,
			saving,
			setAutoshareEnabled,
			setOverriding,
			setTweetText,
			tweetText,
		} = this.props;

		const twitterIconClass = () => {
			const iconClass = autoshareEnabled ? 'enabled' : 'disabled';
			return `${ iconClass } ${ saving ? 'pending' : '' }`;
		};

		const overrideLengthClass = () => {
			if ( 280 <= permalinkLength + tweetText.length ) {
				return 'over-limit';
			}

			if ( 240 <= permalinkLength + tweetText.length ) {
				return 'near-limit';
			}

			return null;
		};

		return (
			<>
				<div className="autoshare-for-twitter-prepublish__checkbox-row">
					<CheckboxControl
						className="autoshare-for-twitter-prepublish__checkbox"
						label={
							<span className="autoshare-for-twitter-prepublish__checkbox-label">
								<Dashicon icon="twitter" className={ twitterIconClass() } />
								{ __( 'Tweet this post?', 'auto-share-for-twitter' ) }
							</span>
						}
						checked={ autoshareEnabled }
						onChange={ ( checked ) => {
							setAutoshareEnabled( checked );
						} }
					/>
				</div>

				{ autoshareEnabled && (
					<div className="autoshare-for-twitter-prepublish__override-row">
						{ overriding && (
							<TextareaControl
								value={ tweetText }
								onChange={ ( value ) => {
									setTweetText( value );
								} }
								label={
									<span className="autoshare-for-twitter-prepublish__message-label">
										<span>{ __( 'Custom message:', 'auto-share-for-twitter' ) }&nbsp;</span>
										<span id="autoshare-for-twitter-counter-wrap" className={ overrideLengthClass() }>
											{ tweetText.length }
										</span>
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
							{ overriding ? __( 'Hide', 'auto-share-for-twitter' ) : __( 'Edit', 'auto-share-for-twitter' ) }
						</Button>
					</div>
				) }

				<div>{ errorMessage }</div>
			</>
		);
	}
}

const permalinkLength = ( select ) => {
	const permalink = select( 'core/editor' ).getPermalink();
	if ( permalink ) {
		return permalink.length;
	}

	const title = select( 'core/editor' ).getEditedPostAttribute( 'title' );
	if ( title && 'rendered' in title ) {
		return ( siteUrl + title.rendered ).length;
	}

	return siteUrl.length;
};

export default compose(
	withSelect( ( select ) => ( {
		autoshareEnabled: select( STORE ).getAutoshareEnabled(),
		errorMessage: select( STORE ).getErrorMessage(),
		overriding: select( STORE ).getOverriding(),
		permalinkLength: permalinkLength( select ),
		saving: select( STORE ).getSaving(),
		tweetText: select( STORE ).getTweetText(),
	} ) ),
	withDispatch( ( dispatch ) => ( {
		setAutoshareEnabled: dispatch( STORE ).setAutoshareEnabled,
		setErrorMessage: dispatch( STORE ).setErrorMessage,
		setOverriding: dispatch( STORE ).setOverriding,
		setSaving: dispatch( STORE ).setSaving,
		setTweetText: dispatch( STORE ).setTweetText,
	} ) ),
)( AutoshareForTwitterPrePublishPanel );
