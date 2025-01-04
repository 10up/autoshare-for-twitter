<?php
/**
 * Handles integration with the Syndication Links plugin.
 * This class is loaded only if the Syndication Links plugin is active.
 *
 * @link https://wordpress.org/plugins/syndication-links
 * @package TenUp\AutoshareForTwitter\Core
 */

namespace TenUp\AutoshareForTwitter\Core\Syndication;

use TenUp\AutoshareForTwitter\Utils;
use const TenUp\AutoshareForTwitter\Core\Post_Meta\TWITTER_STATUS_KEY;

/**
 * Class Syndication_Links
 */
class Syndication_Links {

	/**
	 * Initialize the hooks.
	 */
	public static function init() {
		if ( ! class_exists( 'Syn_Meta' ) ) {
			return;
		}

		// Hook for when post is first published.
		add_action( 'transition_post_status', [ __CLASS__, 'handle_syndication_on_publish' ], 20, 3 );

		// Hook for when tweet is posted (covers both initial publish and retweet scenarios).
		add_action( 'autoshare_for_twitter_after_status_update', [ __CLASS__, 'handle_syndication_after_tweet' ], 10, 3 );
	}

	/**
	 * Handle adding syndication links when post is published.
	 *
	 * @param string  $new_status New post status.
	 * @param string  $old_status Old post status.
	 * @param WP_Post $post       Post object.
	 */
	public static function handle_syndication_on_publish( $new_status, $old_status, $post ) {
		if ( 'publish' !== $new_status || 'publish' === $old_status ) {
			return;
		}

		// Wait a moment to ensure tweet is processed.
		add_action(
			'shutdown',
			function() use ( $post ) {
				self::add_tweet_to_syndication_links( $post->ID );
			}
		);
	}

	/**
	 * Handle syndication after tweet is posted.
	 *
	 * @param object  $response    Twitter API response.
	 * @param array   $update_data Tweet data.
	 * @param WP_Post $post        Post object.
	 */
	public static function handle_syndication_after_tweet( $response, $update_data, $post ) {
		self::add_tweet_to_syndication_links( $post->ID );
	}

	/**
	 * Add tweet URL to syndication links.
	 *
	 * @param int $post_id Post ID.
	 */
	public static function add_tweet_to_syndication_links( $post_id ) {
		$tweet_meta = Utils\get_autoshare_for_twitter_meta( $post_id, TWITTER_STATUS_KEY );

		if ( empty( $tweet_meta ) || ! is_array( $tweet_meta ) ) {
			return;
		}

		// Handle both single and multiple tweet formats.
		$tweets = isset( $tweet_meta['twitter_id'] ) ? array( $tweet_meta ) : $tweet_meta;

		foreach ( $tweets as $tweet ) {
			if ( 'published' === $tweet['status'] && ! empty( $tweet['twitter_id'] ) ) {
				$uri = Utils\link_from_twitter( $tweet );

				// Only add valid tweet URLs.
				if ( self::is_valid_tweet_url( $uri ) ) {
					\Syn_Meta::add_syndication_link( 'post', $post_id, $uri );
				}
			}
		}
	}

	/**
	 * Check if URL is a valid tweet URL.
	 *
	 * @param string $url URL to check.
	 * @return bool
	 */
	public static function is_valid_tweet_url( $url ) {
		return (
			false !== strpos( $url, '/status/' ) &&
			strlen( $url ) > 30 &&
			wp_http_validate_url( $url )
		);
	}
}
