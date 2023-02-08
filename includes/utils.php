<?php
/**
 * A place for everything, everything in its place doesn't apply here.
 * This file is for utility and helper functions.
 *
 * @package TenUp\AutoshareForTwitter\Utils
 */

namespace TenUp\AutoshareForTwitter\Utils;

use const TenUp\AutoshareForTwitter\Core\POST_TYPE_SUPPORT_FEATURE;
use const TenUp\AutoshareForTwitter\Core\Post_Meta\ENABLE_AUTOSHARE_FOR_TWITTER_KEY;
use const TenUp\AutoshareForTwitter\Core\Post_Meta\META_PREFIX;
use const TenUp\AutoshareForTwitter\Core\Post_Meta\TWEET_BODY_KEY;
use const TenUp\AutoshareForTwitter\Core\Post_Meta\TWITTER_STATUS_KEY;
use const TenUp\AutoshareForTwitter\Core\Post_Meta\TWEET_ALLOW_IMAGE;

/**
 * Helper/Wrapper function for returning the meta entries for autosharing.
 *
 * @param int    $id  The post ID.
 * @param string $key The meta key to retrieve.
 *
 * @return mixed
 */
function get_autoshare_for_twitter_meta( $id, $key ) {
	$data = get_post_meta( $id, sprintf( '%s_%s', META_PREFIX, $key ), true );

	/**
	 * Filters autoshare metadata.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed  Retrieved metadata.
	 * @param int    Post ID.
	 * @param string The meta key.
	 */
	return apply_filters( 'autoshare_for_twitter_meta', $data, $id, $key );
}

/**
 * Updates autoshare-for-twitter-related post metadata by prefixing the passed key.
 *
 * @param int    $id    Post ID.
 * @param string $key   Autoshare meta key.
 * @param mixed  $value The meta value to save.
 * @return mixed The meta_id if the meta doesn't exist, otherwise returns true on success and false on failure.
 */
function update_autoshare_for_twitter_meta( $id, $key, $value ) {
	return update_post_meta( $id, sprintf( '%s_%s', META_PREFIX, $key ), $value );
}

/**
 * Determines whether an Autoshare for Twitter post meta key exists on the provided post.
 *
 * @param int    $id  A Post ID.
 * @param string $key A meta key.
 * @return boolean
 */
function has_autoshare_for_twitter_meta( $id, $key ) {
	return metadata_exists( 'post', $id, sprintf( '%s_%s', META_PREFIX, $key ) );
}

/**
 * Deletes autoshare-for-twitter-related metadata.
 *
 * @param int    $id  The post ID.
 * @param string $key The key of the meta value to delete.
 * @return boolean False for failure. True for success.
 */
function delete_autoshare_for_twitter_meta( $id, $key ) {
	return delete_post_meta( $id, sprintf( '%s_%s', META_PREFIX, $key ) );
}

/**
 * Returns whether autoshare is enabled for a post.
 *
 * @param int $post_id A post ID.
 * @return boolean
 */
function autoshare_enabled( $post_id ) {
	if ( has_autoshare_for_twitter_meta( $post_id, ENABLE_AUTOSHARE_FOR_TWITTER_KEY ) ) {
		return get_autoshare_for_twitter_meta( $post_id, ENABLE_AUTOSHARE_FOR_TWITTER_KEY );
	}

	/**
	 * Filters whether autoshare is enabled by default on a post type or post.
	 *
	 * @param bool   Whether autoshare is enabled by default. False by default.
	 * @param string Post type.
	 * @param int    The current post ID.
	 */
	return apply_filters( 'autoshare_for_twitter_enabled_default', false, get_post_type( $post_id ), $post_id );
}

/**
 * Returns whether image is allowed in a tweet.
 *
 * @param int $post_id A post ID.
 * @return boolean
 */
function tweet_image_allowed( $post_id ) {
	if ( has_autoshare_for_twitter_meta( $post_id, TWEET_ALLOW_IMAGE ) ) {
		return ( 'yes' === get_autoshare_for_twitter_meta( $post_id, TWEET_ALLOW_IMAGE ) );
	}

	$is_allowed = (bool) get_autoshare_for_twitter_settings( 'enable_upload' );

	/**
	 * Filters whether autoshare is enabled by default on a post type or post.
	 *
	 * @param bool   Whether autoshare is enabled by default. True by default.
	 * @param string Post type.
	 * @param int    The current post ID.
	 */
	return apply_filters( 'autoshare_for_twitter_tweet_image_allowed', $is_allowed, get_post_type( $post_id ), $post_id );
}

/**
 * Helper for returning the Auto Tweet site settings.
 *
 * @param string $key The option key.
 *
 * @return mixed
 */
function get_autoshare_for_twitter_settings( $key = '' ) {
	$defaults = [
		'enable_for'     => 'selected',
		'post_types'     => get_post_types_supported_by_default(),
		'enable_default' => 1,
		'enable_upload'  => 1,
		'access_secret'  => '',
		'access_token'   => '',
		'api_key'        => '',
		'api_secret'     => '',
		'twitter_handle' => '',
		'use_api_v2'     => 0,
	];

	$settings = get_option( \TenUp\AutoshareForTwitter\Core\Admin\AT_SETTINGS );

	if ( empty( $settings ) ) {
		$settings = [];
	}

	$settings = wp_parse_args( $settings, $defaults );

	if ( empty( $key ) ) {
		return $settings;
	}

	if ( isset( $settings[ $key ] ) ) {
		return $settings[ $key ];
	}

	return '';
}

/**
 * Helper for checking if Twitter account is configured.
 *
 * @return bool
 */
function is_twitter_configured() {
	$defaults = [
		'access_secret'  => '',
		'access_token'   => '',
		'api_key'        => '',
		'api_secret'     => '',
		'twitter_handle' => '',
	];

	$settings    = get_autoshare_for_twitter_settings();
	$credentials = array_intersect_key( $settings, $defaults );
	return 5 === count( array_filter( $credentials ) );
}

/**
 * Composes the tweet based off Title and URL.
 *
 * @param \WP_Post $post The post object.
 *
 * @return string
 */
function compose_tweet_body( \WP_Post $post ) {

	/**
	 * Allow filtering of tweet body
	 */
	$tweet_body = apply_filters( 'autoshare_for_twitter_body', get_tweet_body( $post->ID ), $post );

	/**
	 * Allow filtering of post permalink.
	 *
	 * @param $permalink
	 */
	$url = apply_filters( 'autoshare_for_twitter_post_url', get_the_permalink( $post->ID ), $post );

	$url               = esc_url( $url );
	$body_max_length   = 275 - strlen( $url ); // 275 instead of 280 because of the space between body and URL and the ellipsis.
	$tweet_body        = sanitize_text_field( $tweet_body );
	$tweet_body        = html_entity_decode( $tweet_body, ENT_QUOTES, get_bloginfo( 'charset' ) );
	$tweet_body_length = strlen( $tweet_body );
	$ellipsis          = ''; // Initialize as empty. Will be set if the tweet body is too long.

	while ( $body_max_length < $tweet_body_length ) {
		// We can't use `&hellip;` here or it will display encoded when tweeting.
		$ellipsis = ' ...';

		// If there are no spaces in the tweet for whatever reason, truncate regardless of where spaces fall.
		if ( false === strpos( $tweet_body, ' ' ) ) {
			$tweet_body = substr( $tweet_body, 0, $body_max_length );
			break;
		}

		// Otherwise, cut off the last word in the text until the tweet is short enough.
		$tweet_words = explode( ' ', $tweet_body );
		array_pop( $tweet_words );
		$tweet_body        = implode( ' ', $tweet_words );
		$tweet_body_length = strlen( $tweet_body );
	}

	return sprintf( '%s%s %s', $tweet_body, $ellipsis, $url );
}

/**
 * Return the Twitter created_at timestamp into local format.
 *
 * @param string $created_at The date the post was created.
 *
 * @return string
 */
function date_from_twitter( $created_at ) {

	$tz   = get_option( 'timezone_string' );
	$tz   = ( ! empty( $tz ) ) ? $tz : 'UTC';
	$date = new \DateTime( $created_at, new \DateTimeZone( 'UTC' ) );
	$date->setTimezone( new \DateTimeZone( $tz ) );

	return $date->format( 'Y-m-d @ g:iA' );
}

/**
 * Format a URL based on the Twitter ID.
 *
 * @param int $post_id The post id.
 *
 * @return string
 */
function link_from_twitter( $post_id ) {

	$handle = get_autoshare_for_twitter_settings( 'twitter_handle' );

	return esc_url( 'https://twitter.com/' . $handle . '/status/' . $post_id );
}

/**
 * Determine if a post has already been published based on the meta entry alone.
 *
 * @internal does NOT query the Twitter API.
 *
 * @param int $post_id The post id.
 *
 * @return bool
 */
function already_published( $post_id ) {

	$twitter_status = get_autoshare_for_twitter_meta( $post_id, TWITTER_STATUS_KEY );

	if ( ! empty( $twitter_status ) ) {
		return ( 'published' === $twitter_status['status'] ) ? true : false;
	}

}

/**
 * Helper for returning the appropriate tweet text body.
 *
 * @param int $post_id The post id.
 *
 * @return string
 */
function get_tweet_body( $post_id ) {
	// Use $post->post_title instead of get_the_title( $post_id ) because the latter may introduce texturized characters
	// that Twitter won't decode.
	$post = get_post( $post_id );
	$body = sanitize_text_field( $post->post_title );

	// Only if.
	$text_override = get_autoshare_for_twitter_meta( $post_id, TWEET_BODY_KEY );
	if ( ! empty( $text_override ) ) {
		$body = $text_override;
	}

	return $body;
}

/**
 * Wrapper for post_type_supports.
 *
 * @param int $post_id The post id to check.
 *
 * @return bool true if the current post type supports autoshare.
 */
function opted_into_autoshare_for_twitter( $post_id ) {
	return post_type_supports( get_post_type( (int) $post_id ), POST_TYPE_SUPPORT_FEATURE );
}

/**
 * Get all available post types.
 *
 * @return array
 */
function get_available_post_types() {
	return array_keys( get_available_post_types_data() );
}

/**
 * Get all available post types data.
 *
 * @return array
 */
function get_available_post_types_data() {
	$output     = [];
	$post_types = get_post_types(
		[
			'public' => true,
		],
		'object'
	);

	unset( $post_types['attachment'] );

	foreach ( $post_types as $post_type ) {
		$output[ $post_type->name ] = $post_type->label;
	}

	return (array) apply_filters( 'autoshare_available_post_types', $output );
}

/**
 * Get post types that are supported by default.
 *
 * @return array
 */
function get_post_types_supported_by_default() {
	/**
	 * Filters post types supported by default.
	 *
	 * @since 1.0.0
	 * @param array Array of post types.
	 */
	return (array) apply_filters( 'autoshare_for_twitter_default_post_types', [ 'post', 'page' ] );
}

/**
 * Get post types that are supported by code.
 *
 * @return array
 */
function get_hardcoded_supported_post_types() {
	if ( 'all' === get_autoshare_for_twitter_settings( 'enable_for' ) ) {
		return [];
	}

	$available_post_types = get_available_post_types();
	$enabled_post_types   = get_autoshare_for_twitter_settings( 'post_types' );
	$remaining            = array_diff( $available_post_types, $enabled_post_types );
	return array_filter(
		$remaining,
		function( $post_type ) {
			return post_type_supports( $post_type, POST_TYPE_SUPPORT_FEATURE );
		}
	);
}

/**
 * Get enabled post types.
 *
 * @return array
 */
function get_enabled_post_types() {
	$enable_for = get_autoshare_for_twitter_settings( 'enable_for' );
	if ( 'all' === $enable_for ) {
		return get_available_post_types();
	}
	return get_autoshare_for_twitter_settings( 'post_types' );
}
