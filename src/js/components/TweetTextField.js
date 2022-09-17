import { TextareaControl } from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';
import { siteUrl } from 'admin-autoshare-for-twitter';
import { __ } from '@wordpress/i18n';
import { STORE } from '../store';

export function TweetTextField() {
	const getPermalinkLength = ( select ) => {
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

	const overrideLengthClass = () => {
		if ( 280 <= permalinkLength + tweetText.length ) {
			return 'over-limit';
		}

		if ( 240 <= permalinkLength + tweetText.length ) {
			return 'near-limit';
		}

		return null;
	};

	const { permalinkLength, tweetText } = useSelect( ( select ) => {
		return {
			permalinkLength: getPermalinkLength( select ),
			tweetText: select( STORE ).getTweetText(),
		};
	} );

	const { setTweetText } = useDispatch( STORE );

	return (
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
	);
}
