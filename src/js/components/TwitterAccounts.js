import { ToggleControl, ExternalLink, Tooltip } from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';
import { dateI18n, getSettings } from '@wordpress/date';

import { useTweetAccounts } from '../hooks';
const { connectedAccounts, connectAccountUrl } = adminAutoshareForTwitter;
const settings = getSettings();

/**
 * Twitter accounts component.
 *
 * @return {Function} Twitter accounts component.
 */
export function TwitterAccounts() {
	const accounts = connectedAccounts
		? Object.values( connectedAccounts )
		: [];

	return (
		<div className="autoshare-for-twitter-accounts-wrapper">
			{ accounts.map( ( account ) => (
				<TwitterAccount key={ account.id } { ...account } />
			) ) }
			<span className="connect-account-link">
				<ExternalLink href={ connectAccountUrl }>
					{ __( 'Connect an account', 'autoshare-for-twitter' ) }
				</ExternalLink>
			</span>
		</div>
	);
}

/**
 * Twitter account component.
 *
 * @param {Object} props Twitter account props.
 *
 * @return {Function} Twitter account component.
 */
function TwitterAccount( props ) {
	const [ tweetAccounts, setTweetAccounts ] = useTweetAccounts();
	const { id, name, username, profile_image_url: profileUrl } = props;
	return (
		<>
			<div className="twitter-account-wrapper">
				<img
					src={ profileUrl }
					alt={ name }
					className="twitter-account-profile-image"
				/>
				<span className="account-details">
					<strong>@{ username }</strong>
					<br />
					{ name }
				</span>

				<ToggleControl
					checked={ tweetAccounts && tweetAccounts.includes( id ) }
					onChange={ ( checked ) => {
						if ( checked ) {
							setTweetAccounts( [ ...tweetAccounts, id ] );
						} else {
							setTweetAccounts(
								tweetAccounts.filter(
									( account ) => account !== id
								)
							);
						}
					} }
					className="autoshare-for-twitter-account-toggle"
				/>
			</div>
			<div className="autoshare-for-twitter-rate-monitor">
				<TwitterAccountRateLimits { ...props } />
				<div className="autoshare-for-twitter-rate-monitor__footer">
					<p>
						<strong>
							{ __( 'Note:', 'autoshare-for-twitter' ) }
						</strong>{ ' ' }
						{ __(
							'The displayed API rate limits are updated only when a tweet is posted. Since there is no dedicated endpoint for real-time usage data, the information provided may not fully reflect the current API usage, especially if other tweets are made through the same app.',
							'autoshare-for-twitter'
						) }
					</p>
				</div>
			</div>
		</>
	);
}

/**
 * Main component to display Twitter account rate limits.
 *
 * @param {Object} props
 * @param {Object} props.rate_limits - Rate limit data from the API.
 * @return {JSX.Element} The account rate limits.
 */
function TwitterAccountRateLimits( { rate_limits: rateLimits } ) {
	return (
		<div className="autoshare-for-twitter-rate-monitor__rates">
			{ rateLimits ? (
				<>
					<TwitterRateLimit
						title={ __(
							'User 24-Hour Limit:',
							'autoshare-for-twitter'
						) }
						tooltip={ __(
							'The maximum number of requests a single user can make across all API endpoints within a 24-hour period.',
							'autoshare-for-twitter'
						) }
						remaining={ rateLimits.user_limit_24hour_remaining }
						limit={ rateLimits.user_limit_24hour_limit }
						reset={ rateLimits.user_limit_24hour_reset }
					/>
					<TwitterRateLimit
						title={ __(
							'App 24-Hour Limit:',
							'autoshare-for-twitter'
						) }
						tooltip={ __(
							'The total number of API calls your app can make across all users within a 24-hour period.',
							'autoshare-for-twitter'
						) }
						remaining={ rateLimits.app_limit_24hour_remaining }
						limit={ rateLimits.app_limit_24hour_limit }
						reset={ rateLimits.app_limit_24hour_reset }
					/>
				</>
			) : (
				<p className="autoshare-for-twitter-rate-monitor__error">
					{ __(
						'No X/Twitter rate limit available yet. Make a post to X/Twitter first.',
						'autoshare-for-twitter'
					) }
				</p>
			) }
		</div>
	);
}

/**
 * Reusable component to display rate limit details.
 *
 * @param {Object} props
 * @param {string} props.title     - The title of the rate limit (e.g., "Rate Limit").
 * @param {string} props.tooltip   - The tooltip for the rate limit.
 * @param {number} props.remaining - The remaining requests for this limit.
 * @param {number} props.limit     - The total limit for this type.
 * @param {number} props.reset     - The UNIX timestamp for when the limit resets.
 * @return {JSX.Element} The rate limit details.
 */
function TwitterRateLimit( { title, tooltip, remaining, limit, reset } ) {
	let formattedResetTime = __( 'N/A', 'autoshare-for-twitter' );
	if ( reset && settings?.formats?.datetime ) {
		formattedResetTime = dateI18n(
			settings.formats.datetime,
			reset * 1000,
			'UTC'
		);
		formattedResetTime = sprintf( '%1$s (UTC)', formattedResetTime );
	}

	return (
		<div className="autoshare-for-twitter-rate-monitor__rate">
			<p className="autoshare-for-twitter-rate-monitor__limit">
				<Tooltip text={ tooltip }>
					<strong>{ title }</strong>
				</Tooltip>{ ' ' }
				{ sprintf(
					/* translators: %1$s: Remaining, %2$s: Limit */
					__( '%1$s of %2$s', 'autoshare-for-twitter' ),
					remaining ?? __( 'N/A', 'autoshare-for-twitter' ),
					limit ?? __( 'N/A', 'autoshare-for-twitter' )
				) }
			</p>
			<p className="autoshare-for-twitter-rate-monitor__reset">
				{ formattedResetTime }
			</p>
		</div>
	);
}
