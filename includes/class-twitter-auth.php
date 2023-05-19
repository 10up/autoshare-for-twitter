<?php
/**
 * Class to handle Tweeter account authorization.
 *
 * @package TenUp\AutoshareForTwitter\Core
 */

namespace TenUp\AutoshareForTwitter\Core\Twitter_Auth;

use TenUp\AutoshareForTwitter\Utils as Utils;
use Abraham\TwitterOAuth\TwitterOAuth as TwitterOAuth;

/**
 * Authorization for twitter.
 */
class Twitter_Auth {

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
	 * The TwitterOAuth client.
	 *
	 * @var TwitterOAuth The TwitterOAuth client.
	 */
	protected $client;

	/**
	 * Construct the PublishTweet class.
	 */
	public function __construct() {
		$at_settings = Utils\get_autoshare_for_twitter_settings();

		$this->consumer_key    = $at_settings['api_key'];
		$this->consumer_secret = $at_settings['api_secret'];

		$this->client = new TwitterOAuth(
			$this->consumer_key,
			$this->consumer_secret
		);

		add_action( 'admin_notices', array( $this, 'twitter_connection_notices' ) );
		add_action( 'admin_post_autoshare_twitter_authorize_action', array( $this, 'twitter_authorize' ) );
		add_action( 'admin_post_autoshare_twitter_disconnect_action', array( $this, 'twitter_disconnect' ) );
		add_action( 'admin_post_authoshare_authorize_callback', array( $this, 'authoshare_authorize_user_callback' ) );
	}

	/**
	 * Authorize the user with Twitter.
	 */
	public function twitter_authorize() {
		// Check if the user has the correct permissions.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'autoshare-for-twitter' ) );
		}

		// Check if the nonce is valid.
		if ( ! isset( $_GET['autoshare_twitter_authorize_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['autoshare_twitter_authorize_nonce'] ) ), 'autoshare_twitter_authorize_action' ) ) {
			wp_die( esc_html__( 'You have not access to doing this operations.', 'autoshare-for-twitter' ) );
		}

		// Get the request token.
		$callback_url = admin_url( 'admin-post.php?action=authoshare_authorize_callback' );

		try {
			$request_token = $this->client->oauth( 'oauth/request_token', array( 'oauth_callback' => $callback_url ) );

			// Save temporary credentials to cookies for later use.
			setcookie( 'autoshare_oauth_token', $request_token['oauth_token'], time() + 3600, '/' );
			setcookie( 'autoshare_oauth_token_secret', $request_token['oauth_token_secret'], time() + 3600, '/' );

			// Initiate authorization.
			$url = $this->client->url( 'oauth/authorize', array( 'oauth_token' => $request_token['oauth_token'] ) );
			if ( ! empty( $url ) ) {
				wp_redirect( $url ); // phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect
				exit();
			}
		} catch ( \Exception $e ) {
			$error   = $e->getMessage();
			$decoded = json_decode( $e->getMessage() );
			if ( json_last_error() === JSON_ERROR_NONE ) {
				$error = $decoded;
			}

			if ( ! empty( $error->errors ) ) {
				$error         = current( $error->errors );
				$error_message = $error->message;
			} elseif ( ! empty( $error ) ) {
				$error_message = $error;
			} else {
				$error_message = __( 'Something went wrong. Please try again.', 'autoshare-for-twitter' );
			}

			$this->set_connection_notice( 'error', $error_message );
		}

		// Redirect back to AutoShare settings page.
		wp_safe_redirect( admin_url( 'options-general.php?page=autoshare-for-twitter' ) );
		exit();
	}

	/**
	 * Callback for Twitter authorization process.
	 *
	 * @throws \Exception If the request token is not valid.
	 */
	public function authoshare_authorize_user_callback() {
		// Check if the user has the correct permissions.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'autoshare-for-twitter' ) );
		}

		try {
			// Check if users has denied the authorization.
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$is_deniend = isset( $_GET['denied'] ) ? sanitize_text_field( wp_unslash( $_GET['denied'] ) ) : false;
			if ( $is_deniend ) {
				throw new \Exception( __( 'You have denied the authorization.', 'autoshare-for-twitter' ) );
			}

			$oauth_token        = isset( $_COOKIE['autoshare_oauth_token'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['autoshare_oauth_token'] ) ) : '';
			$oauth_token_secret = isset( $_COOKIE['autoshare_oauth_token_secret'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['autoshare_oauth_token_secret'] ) ) : '';

			// Check if the request token is valid.
			if ( ! isset( $_REQUEST['oauth_token'] ) || sanitize_text_field( wp_unslash( $_REQUEST['oauth_token'] ) !== $oauth_token ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				wp_die( 'Something went wrong. Please try again.' );
			}

			$connection = new TwitterOAuth(
				$this->consumer_key,
				$this->consumer_secret,
				$oauth_token,
				$oauth_token_secret
			);

			// Get the access token.
			$oauth_verifier = isset( $_REQUEST['oauth_verifier'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['oauth_verifier'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$access_token   = $connection->oauth( 'oauth/access_token', array( 'oauth_verifier' => $oauth_verifier ) );

			if ( ! $access_token || ! isset( $access_token['oauth_token'] ) || ! isset( $access_token['oauth_token_secret'] ) ) {
				throw new \Exception( 'autoshare_twitter_authorize_error', __( 'Something went wrong during getting access token. Please try again', 'autoshare-for-twitter' ) );
			}

			// Remove temporary credentials from cookies.
			setcookie( 'autoshare_oauth_token', '', time() - 3600, '/' );
			setcookie( 'autoshare_oauth_token_secret', '', time() - 3600, '/' );

			$connection = new TwitterOAuth(
				$this->consumer_key,
				$this->consumer_secret,
				$access_token['oauth_token'],
				$access_token['oauth_token_secret']
			);

			$connection->setApiVersion( '2' );
			$user = $connection->get(
				'users/me',
				array(
					'user.fields' => 'id,name,username,profile_image_url',
				)
			);

			if ( ! $user || ! isset( $user->data ) || ! isset( $user->data->id ) ) {
				throw new \Exception( 'autoshare_twitter_authorize_error', __( 'Something went wrong during getting user details. Please try again', 'autoshare-for-twitter' ) );
			}

			$user_data = $user->data;
			$account   = array(
				'id'                 => $user_data->id,
				'name'               => $user_data->name,
				'username'           => $user_data->username,
				'profile_image_url'  => $user_data->profile_image_url,
				'oauth_token'        => $access_token['oauth_token'],
				'oauth_token_secret' => $access_token['oauth_token_secret'],
			);

			// Save account details.
			Utils\save_twitter_account( $account );
			$this->set_connection_notice( 'success', __( 'Twitter account authenticated successfully' ) );
		} catch ( \Exception $e ) {
			$error_message = $e->getMessage();
			$this->set_connection_notice( 'error', $error_message );
		}

		// Redirect back to AutoShare settings page.
		wp_safe_redirect( admin_url( 'options-general.php?page=autoshare-for-twitter' ) );
		exit();
	}

	/**
	 * Show notices for Twitter connection errors/success.
	 *
	 * @return void
	 */
	public function twitter_connection_notices() {
		$notice = get_transient( 'autoshare_for_twitter_connection_notice' );
		if ( ! $notice ) {
			return;
		}

		if ( ! empty( $notice['message'] ) ) {
			?>
			<div class="notice notice-<?php echo esc_attr( $notice['type'] ); ?> is-dismissible">
				<p><?php echo esc_html( $notice['message'] ); ?></p>
			</div>
			<?php
		}
	}

	/**
	 * Set connection notice.
	 *
	 * @param string $type    Notice type.
	 * @param string $message Notice message.
	 */
	public function set_connection_notice( $type, $message ) {
		set_transient(
			'autoshare_for_twitter_connection_notice',
			array(
				'type'    => $type,
				'message' => $message,
			),
			30
		);
	}

	/**
	 * Get connection notice.
	 *
	 * @param string $type    Notice type.
	 * @param string $message Notice message.
	 */
	public function get_connection_notice( $type, $message ) {
		$notices = get_transient( 'autoshare_for_twitter_connection_notice' );
		delete_transient( 'autoshare_for_twitter_connection_notice' );

		return $notices;
	}
}

new Twitter_Auth();
