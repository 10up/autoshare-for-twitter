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
	const [ firstAccount ] = accounts;

	const [ tweetAccounts ] = useTweetAccounts();

	return (
		<div className="autoshare-for-twitter-accounts-wrapper">
			{ accounts.map( ( account ) => (
				<TwitterAccount key={ account.id } { ...account } />
			) ) }
			{ firstAccount && tweetAccounts?.length > 0 && (
				<TwitterAppRateLimits { ...firstAccount } />
			) }
			{ tweetAccounts?.length > 0 && (
				<div className="autoshare-for-twitter-rate-monitor__disclaimer">
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
			) }

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
			{ tweetAccounts && tweetAccounts.includes( id ) && (
				<TwitterUserRateLimits { ...props } />
			) }
		</>
	);
}

/**
 * Display user rate limits.
 *
 * @param {Object} props
 * @param {Object} props.rate_limits - Rate limit data from the API.
 * @return {JSX.Element} The account rate limits.
 */
function TwitterUserRateLimits( { rate_limits: rateLimits } ) {
	if (
		! rateLimits ||
		! rateLimits.user_limit_24hour_limit ||
		rateLimits?.user_limit_24hour_reset < Math.floor( Date.now() / 1000 )
	) {
		return (
			<p>
				{ __(
					'No X/Twitter rate limit available yet. Make a post to X/Twitter first.',
					'autoshare-for-twitter'
				) }
			</p>
		);
	}

	return (
		<div className="autoshare-for-twitter-rate-monitor__user">
			<TwitterRateLimits
				title={ __( 'User 24-Hour Limit:', 'autoshare-for-twitter' ) }
				remaining={ rateLimits?.user_limit_24hour_remaining }
				limit={ rateLimits?.user_limit_24hour_limit }
				reset={ rateLimits?.user_limit_24hour_reset }
				tooltip={ __(
					'The maximum number of requests a single user can make across all API endpoints within a 24-hour period.',
					'autoshare-for-twitter'
				) }
			/>
		</div>
	);
}

/**
 * Display app rate limits.
 *
 * @param {Object} props
 * @param {Object} props.rate_limits - Rate limit data from the API.
 * @return {JSX.Element} The account rate limits.
 */
function TwitterAppRateLimits( { rate_limits: rateLimits } ) {
	if (
		! rateLimits ||
		! rateLimits.app_limit_24hour_limit ||
		rateLimits?.app_limit_24hour_reset < Math.floor( Date.now() / 1000 )
	) {
		return null;
	}

	return (
		<div className="autoshare-for-twitter-rate-monitor__app">
			<TwitterRateLimits
				title={ __( 'App 24-Hour Limit:', 'autoshare-for-twitter' ) }
				remaining={ rateLimits?.app_limit_24hour_remaining }
				limit={ rateLimits?.app_limit_24hour_limit }
				reset={ rateLimits?.app_limit_24hour_reset }
				tooltip={ __(
					'The total number of API calls your app can make across all users within a 24-hour period.',
					'autoshare-for-twitter'
				) }
			/>
		</div>
	);
}

/**
 * Display rate limit details.
 *
 * @param {Object} props
 * @param {string} props.title     - The title of the rate limit (e.g., "Rate Limit").
 * @param {number} props.remaining - The remaining requests for this limit.
 * @param {number} props.limit     - The total limit for this type.
 * @param {number} props.reset     - The UNIX timestamp for when the limit resets.
 * @param {string} props.tooltip   - The tooltip for the rate limit.
 * @return {JSX.Element} The rate limit details.
 */
function TwitterRateLimits( { title, remaining, limit, reset, tooltip } ) {
	let formattedResetTime = __( 'N/A', 'autoshare-for-twitter' );
	if ( reset && settings?.formats?.datetime ) {
		formattedResetTime = dateI18n(
			settings.formats.datetime,
			reset * 1000
		);
		formattedResetTime = sprintf( 'Resets on %1$s', formattedResetTime );
	}

	return (
		<div className="autoshare-for-twitter-rate-monitor__rate">
			<p className="autoshare-for-twitter-rate-monitor__rate-limit">
				<Tooltip text={ tooltip }>
					<strong>{ title }</strong>
				</Tooltip>{ ' ' }
				{ sprintf(
					/* translators: %1$s: Remaining, %2$s: Limit */
					__(
						'%1$s of %2$s requests remaining',
						'autoshare-for-twitter'
					),
					remaining ?? __( 'N/A', 'autoshare-for-twitter' ),
					limit ?? __( 'N/A', 'autoshare-for-twitter' )
				) }
			</p>
			<p className="autoshare-for-twitter-rate-monitor__rate-reset">
				{ formattedResetTime }
			</p>
		</div>
	);
}
