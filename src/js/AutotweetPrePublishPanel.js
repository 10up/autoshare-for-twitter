import apiFetch from '@wordpress/api-fetch';
import { Button, CheckboxControl, Dashicon, TextareaControl } from '@wordpress/components';
import { withDispatch, withSelect } from '@wordpress/data';
import { compose } from '@wordpress/compose';
import { Component } from '@wordpress/element';
import { debounce } from 'lodash';
import { enableAutotweetKey, errorText, restUrl, siteUrl, tweetBodyKey } from 'admin-autotweet';
import { __ } from '@wordpress/i18n';

import { STORE } from './store';

class AutotweetPrePublishPanel extends Component {
	constructor( props ) {
		super( props );

		// Although these values are delivered as props, we copy them into state so that we can check for changes
		// and save data when they update.
		this.state = { autotweetEnabled: null, tweetText: null };

		this.saveData = debounce( this.saveData.bind( this ), 250 );
	}

	componentDidMount() {
		const { autotweetEnabled, tweetText } = this.props;

		this.setState( { autotweetEnabled, tweetText } );
	}

	componentDidUpdate() {
		const { autotweetEnabled, tweetText } = this.props;

		// Update if either of these values has changed in the data store.
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
			permalinkLength,
			saving,
			setAutotweetEnabled,
			setOverriding,
			setTweetText,
			tweetText,
		} = this.props;

		const twitterIconClass = () => {
			const iconClass = autotweetEnabled ? 'enabled' : 'disabled';
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
									setTweetText( value );
								} }
								label={
									<span className="autotweet-prepublish__message-label">
										<span>{ __( 'Custom message:', 'autotweet' ) }&nbsp;</span>
										<span id="tenup-auto-tweet-counter-wrap" className={ overrideLengthClass() }>{ tweetText.length }</span>
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
		autotweetEnabled: select( STORE ).getAutotweetEnabled(),
		errorMessage: select( STORE ).getErrorMessage(),
		overriding: select( STORE ).getOverriding(),
		permalinkLength: permalinkLength( select ),
		saving: select( STORE ).getSaving(),
		tweetText: select( STORE ).getTweetText(),
	} ) ),
	withDispatch( ( dispatch ) => ( {
		setAutotweetEnabled: dispatch( STORE ).setAutotweetEnabled,
		setErrorMessage: dispatch( STORE ).setErrorMessage,
		setOverriding: dispatch( STORE ).setOverriding,
		setSaving: dispatch( STORE ).setSaving,
		setTweetText: dispatch( STORE ).setTweetText,
	} ) ),
)( AutotweetPrePublishPanel );
