import { __ } from '@wordpress/i18n';
import { compose } from '@wordpress/compose';
import { withSelect, useSelect } from '@wordpress/data';
import { Dashicon, Button } from '@wordpress/components';
import { TweetTextField } from './components/TweetTextField';

export function AutoshareForTwitterPostStatusInfo() {
	const { statusMessage } = useSelect( ( select ) => {
		return {
			statusMessage: select( 'core/editor' ).getCurrentPostAttribute( 'autoshare_for_twitter_status' ),
		};
	} );

	return (
		statusMessage.message && (
			<div className="autoshare-for-twitter-post-status">
				<Dashicon icon="twitter" />
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
				<div>
					<Button
						variant="link"
						text={ __( 'Tweet now', 'autoshare-for-twitter' ) }
					/>
					<TweetTextField />
				</div>
			</div>
		)
	);
}

export default compose(
	withSelect( ( select ) => ( {
		statusMessage: select( 'core/editor' ).getCurrentPostAttribute( 'autoshare_for_twitter_status' ),
	} ) ),
)( AutoshareForTwitterPostStatusInfo );
