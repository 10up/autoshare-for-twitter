import {__ } from '@wordpress/i18n';
import {compose } from '@wordpress/compose';
import {withSelect } from '@wordpress/data';
import {Dashicon } from '@wordpress/components';

export function AutoshareForTwitterPostStatusInfo( { statusMessage } ) {
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
			</div>
		)
	);
}

export default compose(
	withSelect( ( select ) => ( {
		statusMessage: select( 'core/editor' ).getCurrentPostAttribute( 'autoshare_for_twitter_status' ),
	} ) ),
)( AutoshareForTwitterPostStatusInfo );
