<?php
/**
 * Class to handle Tweet publishing.
 *
 * @package TenUp\Auto_Tweet\Core
 */

namespace TenUp\Auto_Tweet\Core\Publish_Tweet;

use TenUp\Auto_Tweet\Utils as Utils;
use Abraham\TwitterOAuth\TwitterOAuth as TwitterOAuth;

/**
 * Publish tweets to twitter.
 */
class Publish_Tweet {

	/**
	 * The consumer key.
	 *
	 * @var string The consumer key.
	 */
	protected $consumer_key;

	/**
	 * The consumer secret.
	 *
	 * @var string The consumer secret.
	 */
	protected $consumer_secret;

	/**
	 * The access token.
	 *
	 * @var string The access token.
	 */
	protected $access_token;

	/**
	 * The access secret.
	 *
	 * @var string The access secret.
	 */
	protected $access_token_secret;

	/**
	 * The twitter handle.
	 *
	 * @var string The twitter handle.
	 */
	protected $twitter_handle;

	/**
	 * The TwitterOAuth client.
	 *
	 * @var object The TwitterOAuth client.
	 */
	protected $client;

	/**
	 * Construct the PublishTweet class.
	 */
	public function __construct() {

		$at_settings = Utils\get_auto_tweet_settings();

		$this->consumer_key        = $at_settings['api_key'];
		$this->consumer_secret     = $at_settings['api_secret'];
		$this->access_token        = $at_settings['access_token'];
		$this->access_token_secret = $at_settings['access_secret'];
		$this->twitter_handle      = $at_settings['twitter_handle'];

		// @todo add empty check error handler here
		$this->client = new TwitterOAuth(
			$this->consumer_key,
			$this->consumer_secret,
			$this->access_token,
			$this->access_token_secret
		);
	}

	/**
	 *  Maybe account/verify_credentials?
	 */
	public function connection_test() {}

	/**
	 * POST a status update.
	 *
	 * @param string  $body The tweet body.
	 * @param WP_Post $post The post object.
	 *
	 * @return object
	 */
	public function status_update( $body, $post ) {

		// Bail early if the body text is empty.
		if ( empty( $body ) ) {
			return;
		}
		$update_data = array(
			'status' => $body, // URL encoding handled by buildHttpQuery vai TwitterOAuth.
		);
		$update_data = apply_filters( 'tenup_auto_tweet_tweet', $update_data, $post );
		$this->client->setTimeouts( 10, 30 );
		$response = $this->client->post(
			'statuses/update',
			$update_data
		);

		return $response;

	}

	/**
	 * Upload an image
	 *
	 * @param Object $image The image object.
	 *
	 * @return Object The upload result.
	 */
	public function upload( $image ) {
		$response = $this->client->upload( 'media/upload', array( 'media' => $image ) );

		return $response;

	}

}
