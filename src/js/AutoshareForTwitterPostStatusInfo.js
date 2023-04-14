import { __ } from '@wordpress/i18n';
import { compose } from '@wordpress/compose';
import { useState } from '@wordpress/element';
import { withSelect, useSelect } from '@wordpress/data';
import { Button, ToggleControl, CardDivider, Icon, ExternalLink } from '@wordpress/components';
import { TweetTextField } from './components/TweetTextField';
import { useHasFeaturedImage, useAllowTweetImage, useSaveTwitterData } from './hooks';

import { getIconByStatus } from './utils';

export function AutoshareForTwitterPostStatusInfo() {
	const hasFeaturedImage = useHasFeaturedImage();
	const [ allowTweetImage, setAllowTweetImage ] = useAllowTweetImage();
	const [ reTweet, setReTweet ] = useState( false );
	const [ tweetNow, setTweetNow ] = useState( false );
	const { messages } = useSelect( ( select ) => {
		return {
			messages: select( 'core/editor' ).getCurrentPostAttribute( 'autoshare_for_twitter_status' ),
		};
	} );

	const [ statusMessages, setStatusMessages ] = useState( messages );

	useSaveTwitterData();

	const reTweetHandler = async () => {
		setReTweet( true );

		const postId = await wp.data.select( 'core/editor' ).getCurrentPostId();
		const body = new FormData();

		body.append( 'action', adminAutoshareForTwitter.retweetAction );
		body.append( 'nonce', adminAutoshareForTwitter.nonce );
		body.append( 'post_id', postId );

		const apiResponse = await fetch( ajaxurl, {
			method: 'POST',
			body,
		} );

		const { data } = await apiResponse.json();

		setStatusMessages( data );
		setReTweet( false );
	};

	if ( statusMessages && ! statusMessages.message.length ) {
		return null;
	}

	const chevronUp = <Icon icon={ <svg viewBox="0 0 28 28" xmlns="http://www.w3.org/2000/svg" width="28" height="28" aria-hidden="true" focusable="false"><path d="M6.5 12.4L12 8l5.5 4.4-.9 1.2L12 10l-4.5 3.6-1-1.2z"></path></svg> } />;
	const chevronDown = <Icon icon={ <svg viewBox="0 0 28 28" xmlns="http://www.w3.org/2000/svg" width="28" height="28" aria-hidden="true" focusable="false"><path d="M17.5 11.6L12 16l-5.5-4.4.9-1.2L12 14l4.5-3.6 1 1.2z"></path></svg> } />;

	return (
		<div className="autoshare-for-twitter-post-status">
			{ statusMessages.message.map( ( statusMessage, index ) => {
				const TweetIcon = getIconByStatus( statusMessage.status );

				return (
					<div className="autoshare-for-twitter-log" key={ index }>
						{ TweetIcon }{ statusMessage.url ? <ExternalLink href={ statusMessage.url }>{ statusMessage.message }</ExternalLink> : statusMessage.message }
					</div>
				);
			} ) }
			<CardDivider />
			<Button
				className="autoshare-for-twitter-tweet-now"
				variant="link"
				text={ __( 'Tweet now', 'autoshare-for-twitter' ) }
				onClick={ () => setTweetNow( ! tweetNow ) }
				iconPosition="right"
				icon={ tweetNow ? chevronUp : chevronDown }
			/>
			{ tweetNow && (
				<>
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
					<TweetTextField />
					<Button
						variant="primary"
						className="autoshare-for-twitter-re-tweet"
						text={ reTweet ? __( 'Tweeting...', 'autoshare-for-twitter' ) : __( 'Tweet again', 'autoshare-for-twitter' ) }
						onClick={ () => {
							reTweetHandler();
						} }
					/>
				</>
			) }
		</div>
	);
}

export default compose(
	withSelect( ( select ) => ( {
		statusMessage: select( 'core/editor' ).getCurrentPostAttribute( 'autoshare_for_twitter_status' ),
	} ) ),
)( AutoshareForTwitterPostStatusInfo );
