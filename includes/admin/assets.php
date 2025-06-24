<?php
/**
 * Handles loading of JS and CSS.
 *
 * @since 1.0.0
 * @package TenUp\AutoshareForTwitter
 */

namespace TenUp\AutoshareForTwitter\Admin\Assets;

use TenUp\AutoshareForTwitter\Core\Twitter_Accounts as Twitter_Accounts;

use function TenUp\AutoshareForTwitter\Utils\get_autoshare_for_twitter_meta;
use function TenUp\AutoshareForTwitter\Utils\opted_into_autoshare_for_twitter;
use function TenUp\AutoshareForTwitter\REST\post_autoshare_for_twitter_meta_rest_route;
use function TenUp\AutoshareForTwitter\Utils\autoshare_enabled;
use function TenUp\AutoshareForTwitter\Utils\tweet_image_allowed;
use function TenUp\AutoshareForTwitter\Utils\get_tweet_accounts;
use function TenUp\AutoshareForTwitter\Utils\is_local;

use const TenUp\AutoshareForTwitter\Core\Post_Meta\ENABLE_AUTOSHARE_FOR_TWITTER_KEY;
use const TenUp\AutoshareForTwitter\Core\Post_Meta\TWEET_ACCOUNTS_KEY;
use const TenUp\AutoshareForTwitter\Core\Post_Meta\TWEET_BODY_KEY;
use const TenUp\AutoshareForTwitter\Core\Post_Meta\TWITTER_STATUS_KEY;
use const TenUp\AutoshareForTwitter\Core\Post_Meta\TWEET_ALLOW_IMAGE;

/**
 * The handle used in registering plugin assets.
 */
const SCRIPT_HANDLE = 'autoshare_for_twitter';

/**
 * Adds WP hook callbacks.
 *
 * @since 1.0.0
 */
function add_hook_callbacks() {
	add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\enqueue_shared_assets' );
	add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\enqueue_settings_assets' );
	add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\maybe_enqueue_classic_editor_assets' );
	add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\enqueue_editor_assets' );
}

/**
 * Enqueues assets shared by WP5.0 and classic editors.
 *
 * @since 1.0.0
 */
function enqueue_shared_assets() {
	wp_enqueue_style(
		'admin_autoshare_for_twitter',
		trailingslashit( AUTOSHARE_FOR_TWITTER_URL ) . 'assets/css/admin-autoshare-for-twitter.css',
		[],
		AUTOSHARE_FOR_TWITTER_VERSION
	);

	wp_enqueue_script(
		'admin_autoshare_for_twitter',
		trailingslashit( AUTOSHARE_FOR_TWITTER_URL ) . 'assets/js/admin-autoshare-for-twitter.js',
		[ 'jquery' ],
		AUTOSHARE_FOR_TWITTER_VERSION,
		true
	);
}

/**
 * Enqueues assets shared by WP5.0 and classic editors.
 *
 * @since 1.0.2
 */
function enqueue_settings_assets() {
	$current_screen = get_current_screen();

	if ( ! $current_screen || 'settings_page_autoshare-for-twitter' !== $current_screen->id ) {
		return;
	}

	wp_enqueue_style(
		'admin_autoshare_for_twitter_settings',
		trailingslashit( AUTOSHARE_FOR_TWITTER_URL ) . 'assets/css/admin-autoshare-for-twitter-settings.css',
		[],
		AUTOSHARE_FOR_TWITTER_VERSION
	);

	wp_enqueue_script(
		'admin_autoshare_for_twitter_settings',
		trailingslashit( AUTOSHARE_FOR_TWITTER_URL ) . 'assets/js/admin-autoshare-for-twitter-settings.js',
		[],
		AUTOSHARE_FOR_TWITTER_VERSION,
		true
	);
}

/**
 * Enqueues assets for supported post type editors where the block editor is not active.
 *
 * @since 1.0.0
 * @param string $hook The current admin page.
 */
function maybe_enqueue_classic_editor_assets( $hook ) {
	if ( ! in_array( $hook, [ 'post-new.php', 'post.php' ], true ) ) {
		return;
	}

	if ( ! opted_into_autoshare_for_twitter( get_the_ID() ) ) {
		return;
	}

	$current_screen = get_current_screen();
	if ( method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {
		return;
	}

	$handle = 'admin_autoshare_for_twitter_classic_editor';
	wp_enqueue_script(
		$handle,
		trailingslashit( AUTOSHARE_FOR_TWITTER_URL ) . 'assets/js/admin-autoshare-for-twitter-classic-editor.js',
		[ 'jquery', 'wp-api-fetch' ],
		AUTOSHARE_FOR_TWITTER_VERSION,
		true
	);

	localize_data( $handle );
}

/**
 * Enqueues block editor assets.
 *
 * @since 1.0.0
 */
function enqueue_editor_assets() {
	if ( ! opted_into_autoshare_for_twitter( get_the_ID() ) ) {
		return;
	}

	// Don't load if no Twitter accounts are configured.
	$accounts = ( new Twitter_Accounts() )->get_twitter_accounts( true );
	if ( empty( $accounts ) ) {
		return;
	}

	$asset_file = AUTOSHARE_FOR_TWITTER_PATH . '/dist/autoshare-for-twitter.asset.php';
	// Fallback asset data.
	$asset_data = array(
		'version'      => AUTOSHARE_FOR_TWITTER_VERSION,
		'dependencies' => array(
			'lodash',
			'wp-components',
			'wp-compose',
			'wp-data',
			'wp-edit-post',
			'wp-element',
			'wp-i18n',
			'wp-plugins',
			'wp-primitives',
		),
	);
	if ( file_exists( $asset_file ) ) {
		$asset_data = require $asset_file;
	}

	wp_enqueue_script(
		SCRIPT_HANDLE,
		trailingslashit( AUTOSHARE_FOR_TWITTER_URL ) . 'dist/autoshare-for-twitter.js',
		$asset_data['dependencies'],
		$asset_data['version'],
		true
	);

	localize_data();
}

/**
 * Passes data to Javascript.
 *
 * @since 1.0.0
 * @param string $handle Handle of the JS script intended to consume the data.
 */
function localize_data( $handle = SCRIPT_HANDLE ) {
	$post_id = intval( get_the_ID() );

	if ( empty( $post_id ) ) {
		$post_id = intval(
			filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT )  // Filter removes all characters except digits.
		);
	}

	$status_meta    = get_autoshare_for_twitter_meta( $post_id, TWITTER_STATUS_KEY );
	$accounts       = ( new Twitter_Accounts() )->get_twitter_accounts( true );
	$tweet_accounts = get_tweet_accounts( $post_id );
	$tweet_body     = trim( get_autoshare_for_twitter_meta( $post_id, TWEET_BODY_KEY ) );

	$localization = [
		'enabled'            => autoshare_enabled( $post_id ),
		'enableAutoshareKey' => ENABLE_AUTOSHARE_FOR_TWITTER_KEY,
		'errorText'          => __( 'Error', 'autoshare-for-twitter' ),
		'nonce'              => wp_create_nonce( 'wp_rest' ),
		'restUrl'            => rest_url( post_autoshare_for_twitter_meta_rest_route( $post_id ) ),
		'tweetBodyKey'       => TWEET_BODY_KEY,
		'customTweetBody'    => $tweet_body,
		'status'             => $status_meta && is_array( $status_meta ) ? $status_meta : null,
		'unknownErrorText'   => __( 'An unknown error occurred', 'autoshare-for-twitter' ),
		'siteUrl'            => home_url(),
		'allowTweetImage'    => tweet_image_allowed( $post_id ),
		'allowTweetImageKey' => TWEET_ALLOW_IMAGE,
		'retweetAction'      => 'tenup_autoshare_retweet',
		'connectAccountUrl'  => admin_url( 'options-general.php?page=autoshare-for-twitter' ),
		'tweetAccounts'      => $tweet_accounts,
		'tweetAccountsKey'   => TWEET_ACCOUNTS_KEY,
		'connectedAccounts'  => $accounts ?? [],
		'isLocalSite'        => is_local(),
		'twitterURLLength'   => AUTOSHARE_FOR_TWITTER_URL_LENGTH,
	];

	wp_localize_script( $handle, 'adminAutoshareForTwitter', $localization );
}
