<?php
namespace TenUp\Auto_Tweet\Core\Publish_Tweet;

use TenUp\Auto_Tweet\Utils as Utils;
use Abraham\TwitterOAuth\TwitterOAuth as TwitterOAuth;

class Publish_Tweet {

	/**
	 * @var string
	 */
	protected $consumer_key;
	protected $consumer_secret;
	protected $access_token;
	protected $access_token_secret;
	protected $twitter_handle;
	protected $client;

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

	// @todo maybe account/verify_credentials?
	public function connection_test() {}

	/**
	 * POST a status update.
	 *
	 * @param string $body
	 *
	 * @return object
	 */
	public function status_update( $body ) {

		// Bail early if the body text is empty.
		if ( empty( $body ) ) {
			return;
		}

		$response = $this->client->post(
			'statuses/update',
			array(
				'status' => $body // URL encoding handled by buildHttpQuery vai TwitterOAuth
			)
		);

		return $response;

	}

}
