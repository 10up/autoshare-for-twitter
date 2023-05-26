<?php
/**
 * Class to handle Tweet API operations.
 *
 * @package TenUp\AutoshareForTwitter\Core
 */

namespace TenUp\AutoshareForTwitter\Core;

use TenUp\AutoshareForTwitter\Utils as Utils;
use Abraham\TwitterOAuth\TwitterOAuth as TwitterOAuth;
/**
 * Publish tweets to twitter.
 */
class Twitter_API {

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
	 * @var TwitterOAuth The TwitterOAuth client.
	 */
	protected $client;

	/**
	 * Construct the Twitter_API class.
	 *
	 * @param string $twitter_id The Twitter account ID.
	 */
	public function __construct( $twitter_id = null ) {
		$at_settings = Utils\get_autoshare_for_twitter_settings();

		$this->consumer_key    = $at_settings['api_key'];
		$this->consumer_secret = $at_settings['api_secret'];

		$this->client = new TwitterOAuth(
			$this->consumer_key,
			$this->consumer_secret
		);
	}

	/**
	 *  Initialize the Twitter client.
	 */
	public function init_client() {
		$this->client = new TwitterOAuth(
			$this->consumer_key,
			$this->consumer_secret,
			$this->access_token,
			$this->access_token_secret
		);
	}

	/**
	 * Request the Twitter token for initite authorization.
	 *
	 * @param string $callback_url The callback URL.
	 * @return array
	 */
	public function request_token( $callback_url ) {
		return $this->client->oauth( 'oauth/request_token', array( 'oauth_callback' => $callback_url ) );
	}

	/**
	 * Get the Twitter authorize URL.
	 *
	 * @param string $oauth_token The OAuth token.
	 * @return string
	 */
	public function get_authorize_url( $oauth_token ) {
		return $this->client->url( 'oauth/authorize', array( 'oauth_token' => $oauth_token ) );
	}

	/**
	 * Get the Twitter access token. This is the final step in the authorization process.
	 *
	 * @param string $oauth_token         The OAuth token returned from the authorization step.
	 * @param string $oauth_token_secret  The OAuth token secret returned from the authorization step.
	 * @param string $oauth_verifier      The OAuth verifier returned from the authorization step.
	 * @return array
	 */
	public function get_access_token( $oauth_token, $oauth_token_secret, $oauth_verifier ) {
		$this->access_token        = $oauth_token;
		$this->access_token_secret = $oauth_token_secret;

		$this->init_client();

		return $this->client->oauth( 'oauth/access_token', array( 'oauth_verifier' => $oauth_verifier ) );
	}

	/**
	 * Get Twitter account by access token and access token secret.
	 *
	 * @param string $access_token         The access token.
	 * @param string $access_token_secret  The access token secret.
	 * @return array|\WP_Error
	 */
	public function get_twitter_account_by_token( $access_token, $access_token_secret ) {
		$this->access_token        = $access_token;
		$this->access_token_secret = $access_token_secret;

		// Init Twitter client.
		$this->init_client();

		return $this->get_current_account();
	}

	/**
	 * Get the Twitter current account.
	 *
	 * @return array|\WP_Error
	 */
	public function get_current_account() {
		$this->client->setApiVersion( '2' );
		$user = $this->client->get(
			'users/me',
			array(
				'user.fields' => 'id,name,username,profile_image_url',
			)
		);

		if ( ! $user || ! isset( $user->data ) || ! isset( $user->data->id ) ) {
			return new \WP_Error( 'error_get_twitter_user', __( 'Something went wrong during getting user details', 'autoshare-for-twitter' ) );
		}

		$user_data = $user->data;
		return array(
			'id'                 => $user_data->id,
			'name'               => $user_data->name,
			'username'           => $user_data->username,
			'profile_image_url'  => $user_data->profile_image_url,
			'oauth_token'        => $this->access_token,
			'oauth_token_secret' => $this->access_token_secret,
		);
	}
}
