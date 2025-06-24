<?php
/**
 * Plugin name: Autopost for X Cypress Test plugin
 * Description: Mock the Twitter API requests.
 *
 * @package autoshare-for-twitter
 */

/**
 * Mock the Twitter media upload.
 */
add_filter(
	'autoshare_for_twitter_pre_media_upload',
	function( $media_id ) {
		return 'dummy_media_id';
	}
);

/**
 * Mock the Twitter status update.
 */
add_filter(
	'autoshare_for_twitter_pre_status_update',
	function( $update_data ) {
		return (object) array(
			'id' => 'dummy_tweet_id',
		);
	}
);

/**
 * Mock the Twitter invalidate token API.
 */
add_filter( 'autoshare_for_twitter_pre_disconnect_account', '__return_true' );
