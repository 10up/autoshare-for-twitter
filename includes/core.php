<?php
/**
 * Core plugin setup.
 *
 * @package TenUp\AutoshareForTwitter\Core
 */

namespace TenUp\AutoshareForTwitter\Core;

use TenUp\AutoshareForTwitter\Utils;
use const TenUp\AutoshareForTwitter\Core\Post_Meta\TWITTER_STATUS_KEY;

const POST_TYPE_SUPPORT_FEATURE = 'autoshare-for-twitter';

/**
 * The main setup action.
 */
function setup() {
	require_once plugin_dir_path( AUTOSHARE_FOR_TWITTER ) . 'includes/admin/assets.php';
	require_once plugin_dir_path( AUTOSHARE_FOR_TWITTER ) . 'includes/admin/settings.php';
	require_once plugin_dir_path( AUTOSHARE_FOR_TWITTER ) . 'includes/admin/post-meta.php';
	require_once plugin_dir_path( AUTOSHARE_FOR_TWITTER ) . 'includes/admin/post-transition.php';
	require_once plugin_dir_path( AUTOSHARE_FOR_TWITTER ) . 'includes/class-publish-tweet.php';
	require_once plugin_dir_path( AUTOSHARE_FOR_TWITTER ) . 'includes/rest.php';

	\TenUp\AutoshareForTwitter\Admin\Assets\add_hook_callbacks();
	\TenUp\AutoshareForTwitter\REST\add_hook_callbacks();

	/**
	 * Allow others to hook into the core setup action
	 */
	do_action( 'autoshare_for_twitter_setup' );

	// Setup hooks to add post type support and tweet status columns for supported / enabled post types.
	add_action( 'init', __NAMESPACE__ . '\set_post_type_supports_with_custom_columns' );
	add_filter( 'autoshare_for_twitter_enabled_default', __NAMESPACE__ . '\maybe_enable_autoshare_by_default' );
	add_filter( 'autoshare_for_twitter_attached_image', __NAMESPACE__ . '\maybe_disable_upload_image' );
}

/**
 * Fire up the module.
 *
 * @uses autoshare_for_twitter_loaded
 */
add_action( 'autoshare_for_twitter_loaded', __NAMESPACE__ . '\setup' );

/**
 * Adds autoshare support for enabled post types, and add tweeted status column.
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
 *
 * @return null|int|bool
 */
function maybe_disable_upload_image( $attachment_id ) {
	if ( ! Utils\get_autoshare_for_twitter_settings( 'enable_upload' ) ) {
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
		esc_attr__( 'Tweeted status', 'autoshare-for-twitter' ),
		esc_html__( 'Tweeted status', 'autoshare-for-twitter' )
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

	$post_status  = get_post_status( $post_id );
	$tweet_status = Utils\get_autoshare_for_twitter_meta( $post_id, TWITTER_STATUS_KEY );
	$status       = isset( $tweet_status['status'] ) ? $tweet_status['status'] : '';

	if ( 'publish' === $post_status && 'published' === $status ) {
		$date        = Utils\date_from_twitter( $tweet_status['created_at'] );
		$twitter_url = Utils\link_from_twitter( $tweet_status['twitter_id'] );
		$tweet_title = sprintf(
			'%s %s',
			__( 'Tweeted on', 'autoshare-for-twitter' ),
			$date
		);

		printf(
			'<a href="' . esc_url( $twitter_url ) . '" target="_blank" title="' . esc_attr( $tweet_title ) . '">
						<span class="autoshare-for-twitter-status-logo allow-hover"></span>
					</a>',
		);
	}
}
