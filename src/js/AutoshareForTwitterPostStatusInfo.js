import { __ } from '@wordpress/i18n';
import { compose } from '@wordpress/compose';
import { useState } from '@wordpress/element';
import { withSelect, useSelect } from '@wordpress/data';
import { Button, ToggleControl } from '@wordpress/components';
import { TweetTextField } from './components/TweetTextField';
import { useHasFeaturedImage, useAllowTweetImage, useSaveTwitterData } from './hooks';

export function AutoshareForTwitterPostStatusInfo() {
	const hasFeaturedImage = useHasFeaturedImage();
	const [ allowTweetImage, setAllowTweetImage ] = useAllowTweetImage();
	const [ reTweet, setReTweet ] = useState( false );
	const { messages } = useSelect( ( select ) => {
		return {
			messages: select( 'core/editor' ).getCurrentPostAttribute( 'autoshare_for_twitter_status' ),
		};
	} );

	const [ statusMessages, setStatusMessages ] = useState( messages );

	useSaveTwitterData();

	const tweetNow = async () => {
		setReTweet( true );

		const postId = await wp.data.select("core/editor").getCurrentPostId();
		const body = new FormData();

		body.append( 'action', adminAutoshareForTwitter.retweetAction );
		body.append( 'nonce', adminAutoshareForTwitter.nonce );
		body.append( 'post_id', postId );

		const apiResponse = await fetch( ajaxurl, {
			method: 'POST',
			body,
		} );

		const { success, data } = await apiResponse.json();

		if ( success ) {
			setStatusMessages( data );
		}

		setReTweet( false );
	};

	if ( statusMessages && ! statusMessages.message.length ) {
		return null;
	}

	return (
		<div className="autoshare-for-twitter-post-status">
			{ statusMessages.message.map( ( statusMessage, index ) => {
				return (
					<div key={ index }>
						{ statusMessage.message }
						{ statusMessage.url && (
							<>
								{ ' (' }
								<a target="_blank" rel="noopener noreferrer" href={ statusMessage.url }>
									{ __( 'View', 'autoshare-for-twitter' ) }
								</a>
								{ ')' }
							</>
						) }
					</div>
				)
			} ) }
			<div>
				<Button
					variant="link"
					text={ __( 'Tweet now', 'autoshare-for-twitter' ) }
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
				<TweetTextField />
				<Button
					variant='primary'
					text={ reTweet ? __( 'Tweeting...', 'autoshare-for-twitter' ) : __( 'Tweet again', 'autoshare-for-twitter' ) }
					onClick={ () => {
						tweetNow();
					} }
				/>
			</div>
		</div>
	);
}

export default compose(
	withSelect( ( select ) => ( {
		statusMessage: select( 'core/editor' ).getCurrentPostAttribute( 'autoshare_for_twitter_status' ),
	} ) ),
)( AutoshareForTwitterPostStatusInfo );
