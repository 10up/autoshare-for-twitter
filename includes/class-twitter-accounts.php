<?php
/**
 * Class to handle Tweeter accounts connections.
 *
 * @package TenUp\AutoshareForTwitter\Core
 */

namespace TenUp\AutoshareForTwitter\Core;

use TenUp\AutoshareForTwitter\Utils as Utils;
use TenUp\AutoshareForTwitter\Core\Twitter_API as Twitter_API;

/**
 * Twitter_Accounts Class
 *
 * @since 2.1.0
 */
class Twitter_Accounts {

	/**
	 * Twitter API.
	 *
	 * @var Twitter_API The Twitter API Class Instance.
	 */
	private $twitter_api;

	/**
	 * Option key to save Twitter accounts in options table.
	 *
	 * @var string
	 */
	private $twitter_accounts_key = 'autoshare_for_twitter_accounts';

	/**
	 * Construct the PublishTweet class.
	 */
	public function __construct() {
		$this->twitter_api = new Twitter_API();
	}

	/**
	 * Inintialize the class and register the actions needed.
	 */
	public function init() {
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
			wp_die( esc_html__( 'You are not authorized to perform this operation.', 'autoshare-for-twitter' ) );
		}

		// Get the request token.
		$callback_url = admin_url( 'admin-post.php?action=authoshare_authorize_callback' );

		try {
			$request_token = $this->twitter_api->request_token( $callback_url );

			// Save temporary credentials to cookies for later use.
			setcookie( 'autoshare_oauth_token', $request_token['oauth_token'], time() + 3600, '/' );
			setcookie( 'autoshare_oauth_token_secret', $request_token['oauth_token_secret'], time() + 3600, '/' );

			// Initiate authorization.
			$url = $this->twitter_api->get_authorize_url( $request_token['oauth_token'] );
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
				throw new \Exception( __( 'Authorization denied for this request.', 'autoshare-for-twitter' ) );
			}

			$oauth_token        = isset( $_COOKIE['autoshare_oauth_token'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['autoshare_oauth_token'] ) ) : '';
			$oauth_token_secret = isset( $_COOKIE['autoshare_oauth_token_secret'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['autoshare_oauth_token_secret'] ) ) : '';

			// Check if the request token is valid.
			if ( ! isset( $_REQUEST['oauth_token'] ) || sanitize_text_field( wp_unslash( $_REQUEST['oauth_token'] ) !== $oauth_token ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				wp_die( 'Something went wrong. Please try again.' );
			}

			// Get the access token.
			$oauth_verifier = isset( $_REQUEST['oauth_verifier'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['oauth_verifier'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$access_token   = $this->twitter_api->get_access_token( $oauth_token, $oauth_token_secret, $oauth_verifier );

			if ( ! $access_token || ! isset( $access_token['oauth_token'] ) || ! isset( $access_token['oauth_token_secret'] ) ) {
				throw new \Exception( 'autoshare_twitter_authorize_error', __( 'Something went wrong while fetching the access token. Please try again', 'autoshare-for-twitter' ) );
			}

			// Remove temporary credentials from cookies.
			setcookie( 'autoshare_oauth_token', '', time() - 3600, '/' );
			setcookie( 'autoshare_oauth_token_secret', '', time() - 3600, '/' );

			// Get Twitter account details by access token.
			$account = $this->twitter_api->get_twitter_account_by_token( $access_token['oauth_token'], $access_token['oauth_token_secret'] );

			if ( is_wp_error( $account ) ) {
				throw new \Exception( $account->get_error_message() );
			}

			// Save account details.
			$this->save_twitter_account( $account );
			$this->set_connection_notice( 'success', __( 'X/Twitter account authenticated successfully' ) );
		} catch ( \Exception $e ) {
			$error_message = $e->getMessage();
			$this->set_connection_notice( 'error', $error_message );
		}

		// Redirect back to AutoShare settings page.
		wp_safe_redirect( admin_url( 'options-general.php?page=autoshare-for-twitter' ) );
		exit();
	}

	/**
	 * Disconnect X account.
	 */
	public function twitter_disconnect() {
		// Check if the user has the correct permissions.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'autoshare-for-twitter' ) );
		}

		// Check if the nonce is valid.
		if ( ! isset( $_GET['autoshare_twitter_disconnect_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['autoshare_twitter_disconnect_nonce'] ) ), 'autoshare_twitter_disconnect_action' ) ) {
			wp_die( esc_html__( 'You are not authorized to perform this operation.', 'autoshare-for-twitter' ) );
		}

		if ( ! isset( $_GET['account_id'] ) ) {
			wp_die( esc_html__( 'X/Twitter account ID is required to perform this operation.', 'autoshare-for-twitter' ) );
		}

		try {
			$account_id  = sanitize_text_field( wp_unslash( $_GET['account_id'] ) );
			$twitter_api = new Twitter_API( $account_id );
			$twitter_api->disconnect_account();

			// Delete account details.
			$this->delete_twitter_account( $account_id );

			$this->set_connection_notice( 'success', __( 'Twitter account disconnected successfully' ) );
		} catch ( \Exception $e ) {
			$this->set_connection_notice( 'error', __( 'Something went wrong. Please try again.', 'autoshare-for-twitter' ) );
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
		$notice = $this->get_connection_notice();
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
	 */
	public function get_connection_notice() {
		$notice = get_transient( 'autoshare_for_twitter_connection_notice' );
		delete_transient( 'autoshare_for_twitter_connection_notice' );

		return $notice;
	}

	/**
	 * Save connected Twitter account details.
	 *
	 * @param array $account Twitter Account Data.
	 * @return void
	 */
	public function save_twitter_account( $account ) {
		$accounts = get_option( $this->twitter_accounts_key, array() );

		$accounts[ $account['id'] ] = $account;
		update_option( $this->twitter_accounts_key, $accounts );
	}

	/**
	 * Delete connected Twitter account details.
	 *
	 * @param string $account_id Twitter Account ID.
	 * @return void
	 */
	public function delete_twitter_account( $account_id ) {
		$accounts = get_option( $this->twitter_accounts_key, array() );
		if ( isset( $accounts[ $account_id ] ) ) {
			unset( $accounts[ $account_id ] );
		}
		update_option( $this->twitter_accounts_key, $accounts );
	}

	/**
	 * Gets the list of Twitter accounts.
	 *
	 * @param bool $info_only Whether to return only the account info.
	 *
	 * @return array
	 */
	public function get_twitter_accounts( $info_only = false ) {
		$accounts = get_option( $this->twitter_accounts_key, array() );

		// Backwards compatibility.
		if ( empty( $accounts ) ) {
			$at_settings = Utils\get_autoshare_for_twitter_settings();

			if ( ! empty( $at_settings['access_token'] ) && ! empty( $at_settings['access_secret'] ) ) {
				$account = $this->twitter_api->get_twitter_account_by_token( $at_settings['access_token'], $at_settings['access_secret'] );
				if ( ! empty( $account ) && ! is_wp_error( $account ) ) {
					$this->save_twitter_account( $account );
					$accounts[ $account['id'] ] = $account;
				}
			}
		}

		// Remove sensitive data.
		if ( $info_only && ! empty( $accounts ) ) {
			$accounts = array_map(
				function( $account ) {
					unset( $account['oauth_token'] );
					unset( $account['oauth_token_secret'] );
					return $account;
				},
				$accounts
			);
		}

		return $accounts;
	}

	/**
	 * Get connected Twitter account details.
	 *
	 * @param string $id Twitter Account ID.
	 * @return array
	 */
	public function get_twitter_account( $id ) {
		$accounts = $this->get_twitter_accounts();

		if ( isset( $accounts[ $id ] ) ) {
			return $accounts[ $id ];
		}

		return array();
	}

}
