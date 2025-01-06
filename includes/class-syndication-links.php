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

/**
 * Class Syndication_Links
 */
class Syndication_Links {

	/**
	 * Initialize the hook.
	 */
	public static function init() {
		// Hook for when tweet is posted and saved into post metadata.
		add_action( 'autoshare_for_twitter_post_tweet_status_updated', [ __CLASS__, 'handle_syndication_after_tweet_status_updated' ], 10, 2 );
	}

	/**
	 * Handle syndication after tweet status meta is updated.
	 *
	 * @param int   $post_id    The post ID.
	 * @param array $tweet_meta The tweet meta array containing tweet status data.
	 */
	public static function handle_syndication_after_tweet_status_updated( $post_id, $tweet_meta ) {
		if ( empty( $tweet_meta ) ) {
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
	private static function is_valid_tweet_url( $url ) {
		return (
			! empty( $url ) &&
			false !== strpos( $url, '/status/' ) &&
			strlen( $url ) > 30 &&
			wp_http_validate_url( $url )
		);
	}
}
