import { __ } from '@wordpress/i18n';
import { ExternalLink, CardDivider } from '@wordpress/components';
import { getIconByStatus } from '../utils';

// Error message component.
const ErrorMessage = ( { errorMessage } ) => {
	return (
		<span>
			{ errorMessage }{ ' ' }
			{ errorMessage?.includes(
				'When authenticating requests to the Twitter API v2 endpoints, you must use keys and tokens from a Twitter developer App that is attached to a Project. You can create a project via the developer portal.'
			) && (
				<ExternalLink
					href={
						'https://developer.twitter.com/en/docs/twitter-api/migrate/ready-to-migrate'
					}
				>
					{ __( 'Learn more here.', 'autoshare-for-twitter' ) }
				</ExternalLink>
			) }
		</span>
	);
};

export function StatusLogs( { messages } ) {
	if ( ! messages || ! messages.message.length ) {
		return null;
	}

	return (
		<div className="autoshare-for-twitter-post-status">
			{ messages.message.map( ( statusMessage, index ) => {
				const TweetIcon = getIconByStatus( statusMessage.status );
				return (
					<div className="autoshare-for-twitter-log" key={ index }>
						{ TweetIcon }
						<span>
							{ statusMessage.url ? (
								<ExternalLink href={ statusMessage.url }>
									{ statusMessage.message }
								</ExternalLink>
							) : (
								<ErrorMessage
									errorMessage={ statusMessage.message }
								/>
							) }
							{ !! statusMessage.handle && (
								<strong>
									{ ` - @` + statusMessage.handle }
								</strong>
							) }
						</span>
					</div>
				);
			} ) }
			<CardDivider />
		</div>
	);
}
