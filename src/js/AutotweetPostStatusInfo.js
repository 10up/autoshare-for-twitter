import { __ } from '@wordpress/i18n';
import { Dashicon } from '@wordpress/components';
import { statusMessage } from 'admin-autotweet';

export default function() {
	return statusMessage.message && (
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
	);
}
