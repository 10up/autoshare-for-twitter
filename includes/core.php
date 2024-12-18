<?php
/**
 * Core plugin setup.
 *
 * @package TenUp\AutoshareForTwitter\Core
 */

namespace TenUp\AutoshareForTwitter\Core;

use TenUp\AutoshareForTwitter\Utils;
use TenUp\AutoshareForTwitter\Core\AST_Staging\AST_Staging;
use TenUp\AutoshareForTwitter\Core\Twitter_Accounts;
use const TenUp\AutoshareForTwitter\Core\Post_Meta\TWITTER_STATUS_KEY;
use function TenUp\AutoshareForTwitter\Utils\autoshare_enabled;

const POST_TYPE_SUPPORT_FEATURE = 'autoshare-for-twitter';

/**
 * The main setup action.
 */
function setup() {
	require_once plugin_dir_path( AUTOSHARE_FOR_TWITTER ) . 'includes/admin/assets.php';
	require_once plugin_dir_path( AUTOSHARE_FOR_TWITTER ) . 'includes/admin/settings.php';
	require_once plugin_dir_path( AUTOSHARE_FOR_TWITTER ) . 'includes/admin/post-meta.php';
	require_once plugin_dir_path( AUTOSHARE_FOR_TWITTER ) . 'includes/admin/post-transition.php';
	require_once plugin_dir_path( AUTOSHARE_FOR_TWITTER ) . 'includes/class-ast-staging.php';
	require_once plugin_dir_path( AUTOSHARE_FOR_TWITTER ) . 'includes/class-publish-tweet.php';
	require_once plugin_dir_path( AUTOSHARE_FOR_TWITTER ) . 'includes/rest.php';
	require_once plugin_dir_path( AUTOSHARE_FOR_TWITTER ) . 'includes/class-twitter-accounts-list-table.php';
	require_once plugin_dir_path( AUTOSHARE_FOR_TWITTER ) . 'includes/class-twitter-api.php';
	require_once plugin_dir_path( AUTOSHARE_FOR_TWITTER ) . 'includes/class-twitter-accounts.php';

	\TenUp\AutoshareForTwitter\Admin\Assets\add_hook_callbacks();
	\TenUp\AutoshareForTwitter\REST\add_hook_callbacks();

	// Initiate staging class.
	AST_Staging::init();

	// Initialize the Twitter Account class.
	$twitter_accounts = new Twitter_Accounts();
	$twitter_accounts->init();

	/**
	 * Allow others to hook into the core setup action
	 */
	do_action( 'autoshare_for_twitter_setup' );

	// Setup hooks to add post type support and tweet status columns for supported / enabled post types.
	add_action( 'init', __NAMESPACE__ . '\set_post_type_supports_with_custom_columns' );
	add_filter( 'autoshare_for_twitter_enabled_default', __NAMESPACE__ . '\maybe_enable_autoshare_by_default' );
	add_filter( 'autoshare_for_twitter_attached_image', __NAMESPACE__ . '\maybe_disable_upload_image', 10, 2 );
	add_action( 'admin_init', __NAMESPACE__ . '\handle_notice_dismiss' );
	add_action( 'admin_notices', __NAMESPACE__ . '\migrate_to_twitter_v2_api' );
	add_action( 'autoshare_for_twitter_after_status_update', __NAMESPACE__ . '\update_account_rate_limits', 10, 5 );
	add_action( 'wp_dashboard_setup', __NAMESPACE__ . '\register_rate_monitor_dashboard_widget' );
}

/**
 * Fire up the module.
 *
 * @uses autoshare_for_twitter_loaded
 */
add_action( 'autoshare_for_twitter_loaded', __NAMESPACE__ . '\setup' );

/**
 * Adds autoshare support for enabled post types, and add Autopost status column.
 *
 * @since 1.0.0
 */
function set_post_type_supports_with_custom_columns() {
	// Loop through all the supported post types and add tweet status column.
	$post_types = Utils\get_enabled_post_types();
	foreach ( (array) $post_types as $post_type ) {
		add_post_type_support( $post_type, POST_TYPE_SUPPORT_FEATURE );
		add_filter( "manage_{$post_type}_posts_columns", __NAMESPACE__ . '\modify_post_type_add_tweet_status_column' );
		add_action( 'manage_' . $post_type . '_posts_custom_column', __NAMESPACE__ . '\modify_post_type_add_tweet_status', 10, 2 );
	}
}

/**
 * Enable autoshare by default.
 *
 * @since 1.0.0
 */
function maybe_enable_autoshare_by_default() {
	return (bool) Utils\get_autoshare_for_twitter_settings( 'enable_default' );
}

/**
 * Maybe disable uploading image to Twitter. We upload attached image to Twitter
 * by default, so we disable it if needed here.
 *
 * @since 1.0.0
 *
 * @param null|int $attachment_id ID of attachment being uploaded.
 * @param \WP_Post $post          Post being tweeted.
 *
 * @return null|int|bool
 */
function maybe_disable_upload_image( $attachment_id, $post ) {
	if ( ! Utils\tweet_image_allowed( $post->ID ) ) {
		return false;
	}

	return $attachment_id;
}

/**
 * Add 'Tweeted' column for supported post types.
 *
 * @param array $columns Supported columns for a post type.
 */
function modify_post_type_add_tweet_status_column( $columns ) {
	// Do this so our custom column doesn't end up being the last one, messing up UI.
	unset( $columns['date'] );

	// Add tweet status column header.
	$columns['is_tweeted'] = sprintf(
		'<span class="autoshare-for-twitter-status-logo" title="%s"><span class="screen-reader-text">%s</span></span>',
		esc_attr__( 'Autopost status', 'autoshare-for-twitter' ),
		esc_html__( 'Posted to X/Twitter status', 'autoshare-for-twitter' )
	);

	// Add the date column back.
	$columns['date'] = esc_html__( 'Date', 'autoshare-for-twitter' );

	return $columns;
}

/**
 * Add tweet status data to each row.
 *
 * @param  string $column_name Column name.
 * @param  int    $post_id Post ID.
 */
function modify_post_type_add_tweet_status( $column_name, $post_id ) {
	if ( 'is_tweeted' !== $column_name ) {
		return;
	}

	$post_status = get_post_status( $post_id );
	$tweet_meta  = Utils\get_autoshare_for_twitter_meta( $post_id, TWITTER_STATUS_KEY );

	$tweet_status = array();
	if ( isset( $tweet_meta['status'] ) ) {
		$tweet_status = $tweet_meta;
	} elseif ( ! empty( $tweet_meta ) ) {
		$tweet_status = end( $tweet_meta );
	}

	$status = isset( $tweet_status['status'] ) ? $tweet_status['status'] : '';

	if ( 'publish' === $post_status && 'published' === $status ) {
		$date        = Utils\date_from_twitter( $tweet_status['created_at'] );
		$twitter_url = Utils\link_from_twitter( $tweet_status );
		$tweet_title = sprintf(
			'%s %s',
			__( 'Posted to X/Twitter on', 'autoshare-for-twitter' ),
			$date
		);

		printf(
			'<a href="' . esc_url( $twitter_url ) . '" target="_blank" title="' . esc_attr( $tweet_title ) . '">
				<span class="autoshare-for-twitter-status-logo autoshare-for-twitter-status-logo--published"></span>
			</a>'
		);
	} elseif ( 'publish' === $post_status && 'error' === $status ) {
		printf(
			'<span class="autoshare-for-twitter-status-logo autoshare-for-twitter-status-logo--error"></span>'
		);
	} elseif ( 'future' === $post_status && autoshare_enabled( $post_id ) ) {
		printf(
			'<span class="autoshare-for-twitter-status-logo autoshare-for-twitter-status-logo--enabled"></span>'
		);
	} else {
		printf(
			'<span class="autoshare-for-twitter-status-logo autoshare-for-twitter-status-logo--disabled" title="' . esc_attr( __( 'Has not been Posted to X/Twitter', 'autoshare-for-twitter' ) ) . '"></span>'
		);
	}
}

/**
 * Display admin notice to migrate to Twitter v2 API.
 *
 * @since 2.0.0
 */
function migrate_to_twitter_v2_api() {
	$show_notice = get_option( 'autoshare_migrate_to_v2_api_notice_dismissed', false );
	if ( $show_notice ) {
		return;
	}
	$dismiss_url = wp_nonce_url( add_query_arg( 'autoshare_dismiss_notice', '1' ), 'ast_dismiss_migrate_notice', '_ast_dismiss_nonce' );
	?>
	<div class="ast_notice notice notice-warning is-dismissible" data-dismiss-url="<?php echo esc_url( $dismiss_url ); ?>">
		<p>
			<?php
			printf(
				// translators: 1$-2$: Opening and closing <a> tags for Twitter V2 API, 3$-4$: Opening and closing <a> tags for migrate app, 5$-6$: Opening and closing <a> tags for learn more.
				wp_kses_post( __( 'Autopost for X/Twitter now utilizes the %1$sX/Twitter v2 API%2$s. If you have not already done so, please %3$smigrate your app%4$s to X/Twitter v2 API to continue using Autopost for X. %5$sLearn more about migrating here%6$s.', 'autoshare-for-twitter' ) ),
				'<a href="https://developer.twitter.com/en/products/twitter-api" target="_blank">',
				'</a>',
				'<a href="https://developer.twitter.com/en/portal/projects-and-apps" target="_blank">',
				'</a>',
				'<a href="https://developer.twitter.com/en/docs/twitter-api/migrate/ready-to-migrate" target="_blank">',
				'</a>'
			);
			?>
		</p>
	</div>
	<?php
}

/**
 * Handle notice dismissal.
 *
 * @since 2.0.0
 */
function handle_notice_dismiss() {
	if (
		! empty( $_GET['_ast_dismiss_nonce'] ) &&
		wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_ast_dismiss_nonce'] ) ), 'ast_dismiss_migrate_notice' ) &&
		isset( $_GET['autoshare_dismiss_notice'] )
	) {
		update_option( 'autoshare_migrate_to_v2_api_notice_dismissed', true );
	}
}

/**
 * Update the account rate limits from the last X/Twitter API request.
 *
 * @param  object     $response     The response from the X/Twitter endpoint.
 * @param  array      $update_data  Data to send to the X/Twitter endpoint.
 * @param  \WP_Post   $post         The post associated with the tweet.
 * @param  string     $account_id   The account ID associated with the tweet.
 * @param  array|null $last_headers The headers from the last request.
 * @return void
 */
function update_account_rate_limits( $response, $update_data, $post, $account_id, $last_headers ) {

	if ( empty( $account_id ) ) {
		return;
	}

	$accounts = get_option( 'autoshare_for_twitter_accounts', array() );

	if ( empty( $accounts[ $account_id ] ) ) {
		return;
	}

	$rate_limits = parse_last_headers(
		$last_headers,
		array(
			'rate_limit_limit'     => 'x_rate_limit_limit',
			'rate_limit_reset'     => 'x_rate_limit_reset',
			'rate_limit_remaining' => 'x_rate_limit_remaining',
		)
	);

	$app_rate_limits = parse_last_headers(
		$last_headers,
		array(
			'app_limit_24hour_limit'     => 'x_app_limit_24hour_limit',
			'app_limit_24hour_reset'     => 'x_app_limit_24hour_reset',
			'app_limit_24hour_remaining' => 'x_app_limit_24hour_remaining',
		)
	);

	$user_rate_limits = parse_last_headers(
		$last_headers,
		array(
			'user_limit_24hour_limit'     => 'x_user_limit_24hour_limit',
			'user_limit_24hour_reset'     => 'x_user_limit_24hour_reset',
			'user_limit_24hour_remaining' => 'x_user_limit_24hour_remaining',
		)
	);

	foreach ( $accounts as $key => $account ) {
		$current_rate_limits = ( isset( $account['rate_limits'] ) && is_array( $account['rate_limits'] ) ) ? $account['rate_limits'] : array();

		// Update the "global" and app rate limits on all accounts.
		$account_rate_limits = array_merge( $rate_limits, $app_rate_limits );

		// Update the user rate limits on the account that made the request.
		if ( $account['id'] === $account_id ) {
			$account_rate_limits = array_merge( $user_rate_limits, $account_rate_limits );
		}

		// Merge the current rate limits with the new rate limits.
		$account_rate_limits = array_merge( $current_rate_limits, $account_rate_limits );

		$accounts[ $key ]['rate_limits'] = $account_rate_limits;
	}

	update_option( 'autoshare_for_twitter_accounts', $accounts );
}

/**
 * Register the Rate Monitor dashboard widget.
 *
 * @return void
 */
function register_rate_monitor_dashboard_widget() {

	wp_add_dashboard_widget(
		'autopost_for_x_rate_monitor_dashboard_widget',
		esc_html__( 'Autopost for X — Rate Monitor', 'autoshare-for-twitter' ),
		__NAMESPACE__ . '\display_rate_monitor_dashboard_widget'
	);
}

/**
 * Display the Rate Monitor dashboard widget.
 *
 * @return void
 */
function display_rate_monitor_dashboard_widget() {
	$accounts = get_option( 'autoshare_for_twitter_accounts', array() );

	if ( empty( $accounts ) ) {
		printf(
			'<p class="autoshare-for-twitter-no-accounts">%s</p>',
			esc_html__( 'No X/Twitter accounts are connected. Please connect at least one X/Twitter account to continue using Autopost for X.', 'autoshare-for-twitter' )
		);
		return;
	}

	$app_rate_limits_markup   = '';
	$users_rate_limits_markup = '';

	foreach ( $accounts as $account ) {

		$account_markup = '';

		if ( ! empty( $account['rate_limits'] ) ) {
			$account_markup = get_user_rate_limits_markup( $account['rate_limits'] );

			if ( empty( $app_rate_limits_markup ) ) { // We only need to display the app rate limits once.
				$app_rate_limits_markup = get_app_rate_limits_markup( $account['rate_limits'] );
			}
		} else {
			$account_markup = sprintf(
				'<p>%s</p>',
				esc_html__( 'No X/Twitter rate limit available yet. Make a post to X/Twitter first.', 'autoshare-for-twitter' )
			);
		}

		$users_rate_limits_markup .= sprintf(
			'<div class="autoshare-for-twitter-rate-monitor__user">
				<img src="%1$s" alt="%2$s" class="twitter-account-profile-image">
				<div class="autoshare-for-twitter-rate-monitor__user-info">
					<h3>@%3$s</h3>
					%4$s
				</div>
			</div>',
			esc_url( $account['profile_image_url'] ),
			esc_attr( $account['name'] ),
			esc_html( $account['username'] ),
			$account_markup // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
	}

	$footnotes = array(
		__( 'User 24-Hour Limit: The maximum number of requests a single user can make across all API endpoints within a 24-hour period.', 'autoshare-for-twitter' ),
		__( 'App 24-Hour Limit: The total number of API calls your app can make across all users within a 24-hour period.', 'autoshare-for-twitter' ),
	);

	$footnotes = array_map(
		function ( $footnote ) {
			return sprintf(
				'<li>%1$s</li>',
				esc_html( $footnote )
			);
		},
		$footnotes
	);

	printf(
		'<div class="autoshare-for-twitter-rate-monitor">
			<div class="autoshare-for-twitter-rate-monitor__users">
				%1$s
			</div>
			<div class="autoshare-for-twitter-rate-monitor__app">
				%2$s
			</div>
			<div class="autoshare-for-twitter-rate-monitor__disclaimer">
				<p><strong>%3$s</strong> %4$s</p>
				<ul>%5$s</ul>
			</div>
		</div>',
		$users_rate_limits_markup, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		$app_rate_limits_markup, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		esc_html__( 'Note:', 'autoshare-for-twitter' ),
		esc_html__( 'The displayed API rate limits are updated only when a tweet is posted. Since there is no dedicated endpoint for real-time usage data, the information provided may not fully reflect the current API usage, especially if other tweets are made through the same app.', 'autoshare-for-twitter' ),
		implode( ' ', $footnotes ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	);
}

/**
 * Parse the last headers from the X/Twitter API response.
 *
 * @param  array $last_headers The headers from the last request.
 * @param  array $map          The map of X/Twitter headers to internal WP keys.
 * @return array
 */
function parse_last_headers( $last_headers, $map ) {

	$parsed = array();

	foreach ( $map as $key => $header ) {

		if ( ! isset( $last_headers[ $header ] ) ) {
			continue;
		}

		$parsed[ $key ] = sanitize_text_field( $last_headers[ $header ] );
	}

	return $parsed;
}

/**
 * Get human readable time.
 *
 * @param  int    $timestamp   Timestamp.
 * @param  string $date_format Date format.
 * @return string
 */
function human_readable_time( $timestamp, $date_format = '' ) {

	$timestamp = (int) $timestamp;

	$datetime = new \DateTime( '@' . $timestamp, new \DateTimeZone( 'UTC' ) );

	if ( empty( $date_format ) ) {
		$date_format = sprintf(
			'%s %s',
			esc_html( get_option( 'date_format' ) ),
			esc_html( get_option( 'time_format' ) )
		);
	}

	$human_readable_time = $datetime->format( $date_format );
	$human_readable_time = sprintf( '%s (UTC)', $human_readable_time );

	return $human_readable_time;
}

/**
 * Get user rate limits markup.
 *
 * @param  array $rate_limits Rate limits.
 * @return string
 */
function get_user_rate_limits_markup( $rate_limits ) {

	$remaining = isset( $rate_limits['user_limit_24hour_remaining'] ) ? $rate_limits['user_limit_24hour_remaining'] : '';
	$limit     = isset( $rate_limits['user_limit_24hour_limit'] ) ? $rate_limits['user_limit_24hour_limit'] : '';
	$reset     = isset( $rate_limits['user_limit_24hour_reset'] ) ? $rate_limits['user_limit_24hour_reset'] : '';

	if ( empty( $remaining ) && empty( $limit ) && empty( $reset ) ) {
		return sprintf(
			'<p>%s</p>',
			esc_html__( 'No X/Twitter rate limit available yet. Make a post to X/Twitter first.', 'autoshare-for-twitter' )
		);
	}

	return get_rate_limits_markup(
		__( 'User 24-Hour Limit:', 'autoshare-for-twitter' ),
		$remaining,
		$limit,
		$reset
	);
}

/**
 * Get app rate limits markup.
 *
 * @param  array $rate_limits Rate limits.
 * @return string
 */
function get_app_rate_limits_markup( $rate_limits ) {

	$remaining = isset( $rate_limits['app_limit_24hour_remaining'] ) ? $rate_limits['app_limit_24hour_remaining'] : '';
	$limit     = isset( $rate_limits['app_limit_24hour_limit'] ) ? $rate_limits['app_limit_24hour_limit'] : '';
	$reset     = isset( $rate_limits['app_limit_24hour_reset'] ) ? $rate_limits['app_limit_24hour_reset'] : '';

	return get_rate_limits_markup(
		__( 'App 24-Hour Limit:', 'autoshare-for-twitter' ),
		$remaining,
		$limit,
		$reset
	);
}

/**
 * Get rate limits markup.
 *
 * @param  string $title      Rate limit title.
 * @param  int    $remaining  Remaining rate limit.
 * @param  int    $limit      Total rate limit.
 * @param  int    $reset      Rate limit reset time.
 * @return string
 */
function get_rate_limits_markup( $title, $remaining, $limit, $reset ) {

	$remaining = isset( $remaining ) ? (int) $remaining : esc_html__( 'N/A', 'autoshare-for-twitter' );
	$limit     = isset( $limit ) ? (int) $limit : esc_html__( 'N/A', 'autoshare-for-twitter' );
	$reset     = isset( $reset ) ? human_readable_time( $reset ) : esc_html__( 'N/A', 'autoshare-for-twitter' );

	return sprintf(
		'<div class="autoshare-for-twitter-rate-monitor__rate">
			<p class="autoshare-for-twitter-rate-monitor__rate-limit"><strong>%1$s</strong> %2$s</p>
			<p class="autoshare-for-twitter-rate-monitor__rate-reset">%3$s</p>
		</div>',
		esc_html( $title ),
		sprintf(
			/* translators: %1$s: Remaining, %2$s: Limit */
			esc_html__( '%1$s of %2$s', 'autoshare-for-twitter' ),
			esc_html( $remaining ),
			esc_html( $limit )
		),
		sprintf(
			/* translators: %1$s: Reset time */
			esc_html__( 'Resets on %1$s', 'autoshare-for-twitter' ),
			esc_html( $reset )
		)
	);
}
