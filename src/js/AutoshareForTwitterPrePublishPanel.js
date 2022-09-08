import apiFetch from '@wordpress/api-fetch';
import { Button, TextareaControl, ToggleControl } from '@wordpress/components';
import { withDispatch, withSelect } from '@wordpress/data';
import { compose } from '@wordpress/compose';
import { Component } from '@wordpress/element';
import { debounce } from 'lodash';
import { enableAutoshareKey, errorText, restUrl, siteUrl, tweetBodyKey, allowTweetImageKey } from 'admin-autoshare-for-twitter';
import { __ } from '@wordpress/i18n';

import { STORE } from './store';

class AutoshareForTwitterPrePublishPanel extends Component {
	constructor( props ) {
		super( props );

		// Although these values are delivered as props, we copy them into state so that we can check for changes
		// and save data when they update.
		this.state = { autoshareEnabled: null, tweetText: null, hasFeaturedImage: null, allowTweetImage: true };

		this.saveData = debounce( this.saveData.bind( this ), 250 );
	}

	componentDidMount() {
		const { autoshareEnabled, tweetText, allowTweetImage } = this.props;

		this.setState( { autoshareEnabled, tweetText, allowTweetImage } );
	}

	componentDidUpdate() {
		const { autoshareEnabled, tweetText, hasFeaturedImage, allowTweetImage } = this.props;

		// Update if either of these values has changed in the data store.
		if (
			autoshareEnabled !== this.state.autoshareEnabled ||
			tweetText !== this.state.tweetText ||
			allowTweetImage !== this.state.allowTweetImage
		) {
			this.setState( { autoshareEnabled, tweetText, hasFeaturedImage, allowTweetImage }, () => {
				this.props.setSaving( true );
				this.saveData();
			} );
		}
	}

	async saveData() {
		const { autoshareEnabled, setErrorMessage, setSaving, tweetText, allowTweetImage } = this.props;

		const body = {};
		body[ enableAutoshareKey ] = autoshareEnabled;
		body[ tweetBodyKey ] = tweetText;
		body[ allowTweetImageKey ] = allowTweetImage;

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

	render() {
		const {
			autoshareEnabled,
			errorMessage,
			overriding,
			permalinkLength,
			allowTweetImage,
			setAutoshareEnabled,
			setOverriding,
			setTweetText,
			setAllowTweetImage,
			tweetText,
			hasFeaturedImage,
		} = this.props;

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
				<ToggleControl
					label={ autoshareEnabled ? __( 'Tweet when published', 'autoshare-for-twitter' ) : __( 'Don\'t Tweet', 'autoshare-for-twitter' )
					}
					checked={ autoshareEnabled }
					onChange={ ( checked ) => {
						setAutoshareEnabled( checked );
					} }
					className="autoshare-for-twitter-toggle-control"
				/>

				{ hasFeaturedImage && (
					<ToggleControl
						label={ __( 'Use featured image in Tweet', 'autoshare-for-twitter' ) }
						checked={ allowTweetImage }
						onChange={ () => {
							setAllowTweetImage( ! allowTweetImage );
						} }
						className="autoshare-for-twitter-toggle-control"
					/>
				) }

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
										<span>{ __( 'Custom message:', 'autoshare-for-twitter' ) }&nbsp;</span>
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
							{ overriding ? __( 'Hide', 'autoshare-for-twitter' ) : __( 'Edit', 'autoshare-for-twitter' ) }
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

/**
 * Returns true if the post has a featured image, false otherwise.
 * @param {Function} select Data store selector function.
 * @returns {boolean}
 */
const hasFeaturedImage = ( select ) => {
	const imageId = select( 'core/editor' ).getEditedPostAttribute( 'featured_media' );

	return imageId > 0;
};

export default compose(
	withSelect( ( select ) => ( {
		autoshareEnabled: select( STORE ).getAutoshareEnabled(),
		errorMessage: select( STORE ).getErrorMessage(),
		overriding: select( STORE ).getOverriding(),
		permalinkLength: permalinkLength( select ),
		saving: select( STORE ).getSaving(),
		tweetText: select( STORE ).getTweetText(),
		hasFeaturedImage: hasFeaturedImage( select ),
		allowTweetImage: select( STORE ).getAllowTweetImage(),
	} ) ),
	withDispatch( ( dispatch ) => ( {
		setAutoshareEnabled: dispatch( STORE ).setAutoshareEnabled,
		setErrorMessage: dispatch( STORE ).setErrorMessage,
		setOverriding: dispatch( STORE ).setOverriding,
		setSaving: ( saving ) => {
			dispatch( STORE ).setSaving( saving );

			if ( saving ) {
				dispatch( 'core/editor' ).lockPostSaving();
			} else {
				dispatch( 'core/editor' ).unlockPostSaving();
			}
		},
		setTweetText: dispatch( STORE ).setTweetText,
		setAllowTweetImage: dispatch( STORE ).setAllowTweetImage,
	} ) ),
)( AutoshareForTwitterPrePublishPanel );
