import { TextareaControl, Tooltip } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { __, sprintf } from '@wordpress/i18n';
import { useTweetText } from '../hooks';
import { useEffect, useState } from '@wordpress/element';
const { siteUrl } = adminAutoshareForTwitter;

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

	const { permalinkLength, maxLength } = useSelect( ( select ) => {
		return {
			permalinkLength: getPermalinkLength( select ),
			maxLength: 275 - getPermalinkLength( select ),
		};
	} );

	const getTweetLength = () => {
		// +5 because of the space between body and URL and the ellipsis.
		const length = permalinkLength + tweetText.length + 5;
		if ( 280 <= length ) {
			return {
				tweetLength: sprintf(
					/* translators: %d is tweet message character count */
					__( '%d - Too Long!', 'autoshare-for-twitter' ),
					length
				),
				overrideLengthClass: 'over-limit',
			};
		}

		if ( 240 <= length ) {
			return {
				tweetLength: sprintf(
					/* translators: %d is tweet message character count */
					__( '%d - Getting Long!', 'autoshare-for-twitter' ),
					length
				),
				overrideLengthClass: 'near-limit',
			};
		}

		return { tweetLength: `${ length }`, overrideLengthClass: '' };
	};

	const [ tweetText, setTweetText ] = useTweetText();
	const { tweetLength, overrideLengthClass } = getTweetLength();

	const status = useSelect( ( __select ) =>
		__select( 'core/editor' ).getEditedPostAttribute( 'status' )
	);
	const [ isPublished, setIsPublished ] = useState( status === 'publish' );

	useEffect( () => {
		if ( 'publish' !== status || isPublished ) {
			return;
		}

		setTweetText( '' );
		setIsPublished( true );
	}, [ status, isPublished ] );

	const CounterTooltip = () => (
		<Tooltip
			text={ __(
				'Count is inclusive of the post permalink which will be included in the final tweet.',
				'autoshare-for-twitter'
			) }
		>
			<div>{ tweetLength }</div>
		</Tooltip>
	);

	return (
		<TextareaControl
			value={ tweetText }
			onChange={ ( value ) => {
				setTweetText( value );
			} }
			className="autoshare-for-twitter-tweet-text"
			maxLength={ maxLength }
			label={
				<span
					style={ { marginTop: '0.5rem', display: 'block' } }
					className="autoshare-for-twitter-prepublish__message-label"
				>
					<span>
						{ __( 'Custom message:', 'autoshare-for-twitter' ) }
						&nbsp;
					</span>
					<span
						id="autoshare-for-twitter-counter-wrap"
						className={ `alignright ${ overrideLengthClass }` }
					>
						<CounterTooltip />
					</span>
				</span>
			}
		/>
	);
}
