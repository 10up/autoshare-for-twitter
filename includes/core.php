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
