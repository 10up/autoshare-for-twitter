<?php
/**
 * A place for everything, everything in its place doesn't apply here.
 * This file is for utility and helper functions.
 *
 * @package 10upautotweet
 */

namespace TenUp\Auto_Tweet\Utils;

use TenUp\Auto_Tweet\Core\Post_Meta as Meta;

/**
 * Helper/Wrapper function for returning the meta entries for auto-tweeting.
 *
 * @param int    $id  The post ID.
 * @param string $key The meta key to retrieve.
 *
 * @return mixed
 */
function get_auto_tweet_meta( int $id, string $key ) {

	return get_post_meta( $id, Meta\META_PREFIX . '_' . $key, true );
}

/**
 * Helper for determining if a post should auto-tweet.
 *
 * @param int $post_id The post ID.
 *
 * @return bool
 */
function maybe_auto_tweet( int $post_id ) {

	return ( '1' === get_auto_tweet_meta( $post_id, 'auto-tweet' ) ) ? true : false;
}

/**
 * Helper for returning the Auto Tweet site settings.
 *
 * @param string $key The option key.
 *
 * @return mixed
 */
function get_auto_tweet_settings( $key = '' ) {

	$settings = get_option( \TenUp\Auto_Tweet\Core\Admin\AT_SETTINGS );

	return ( ! empty( $key ) ) ? $settings[ $key ] : $settings;
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
	$tweet_body = apply_filters( 'tenup_auto_tweet_body', get_tweet_body( $post->ID ), $post );

	/**
	 * Allow filtering of post permalink.
	 *
	 * @param $permalink
	 */
	$url = apply_filters( 'tenup_auto_tweet_post_url', get_the_permalink( $post->ID ), $post );

	// Make it safe.
	$array_body = array(
		'title'    => sanitize_text_field( $tweet_body ), // Twitter calls this the Title.
		'url'      => esc_url( $url ),
		'hashtags' => '', // coming soon!
	);

	// Cleaner (ok, easier) way of string concatination.
	$tweet_body = implode( ' ', $array_body );

	return $tweet_body;
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

	$handle = get_auto_tweet_settings( 'twitter_handle' );

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
function already_published( int $post_id ) {

	$twitter_status = get_auto_tweet_meta( $post_id, Meta\STATUS_KEY );

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
function get_tweet_body( int $post_id ) {

	$body = sanitize_text_field( get_the_title( $post_id ) );

	// Only if.
	$text_override = get_auto_tweet_meta( $post_id, Meta\TWEET_BODY );
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
 * @return bool true if the current post type supports auto-tweet.
 */
function opted_into_auto_tweet( $post_id ) {
	return ( true === post_type_supports( get_post_type( (int) $post_id ), 'tenup-auto-tweet' ) ) ? true : false;
}
