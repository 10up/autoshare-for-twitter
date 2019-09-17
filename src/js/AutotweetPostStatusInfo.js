import { __ } from '@wordpress/i18n';
import { compose } from '@wordpress/compose';
import { withSelect } from '@wordpress/data';
import { Dashicon } from '@wordpress/components';

export function AutotweetPostStatusInfo( { statusMessage } ) {
	return (
		statusMessage.message && (
			<div className="autotweet-post-status">
				<Dashicon icon="twitter" />
				{ statusMessage.message }
				{ statusMessage.url && (
					<>
						{ ' (' }
						<a target="_blank" rel="noopener noreferrer" href={ statusMessage.url }>
							{ __( 'View', 'autotweet' ) }
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
		statusMessage: select( 'core/editor' ).getCurrentPostAttribute( 'autotweet_status' ),
	} ) ),
)( AutotweetPostStatusInfo );
