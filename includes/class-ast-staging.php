<?php
/**
 * Class to handle staging site Autoshare.
 *
 * @package TenUp\AutoshareForTwitter\Core
 */

namespace TenUp\AutoshareForTwitter\Core\AST_Staging;

/**
 * Autopost for X staging mode handler.
 *
 * @package TenUp\AutoshareForTwitter\Core
 *
 * @since   1.2.0
 */
class AST_Staging {

	/**
	 * Add actions
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'maybe_add_autoshare_live_url' ) );
		add_action( 'admin_init', array( __CLASS__, 'handle_site_change_notice_actions' ) );
		add_action( 'admin_notices', array( __CLASS__, 'handle_site_change_notice' ) );
	}

	/**
	 * Handle site change notice actions
	 *
	 * @since 1.2.0
	 */
	public static function handle_site_change_notice_actions() {
		if ( ! self::is_production_site() && current_user_can( 'manage_options' ) ) {

			if (
				! empty( $_GET['_astnonce'] ) &&
				wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_astnonce'] ) ), 'ast_duplicate_site' ) &&
				isset( $_GET['autoshare_duplicate_site'] )
			) {
				$duplicate_site = sanitize_text_field( wp_unslash( $_GET['autoshare_duplicate_site'] ) );
				if ( 'update' === $duplicate_site ) {
					self::set_autoshare_live_url_lock();
				} elseif ( 'ignore' === $duplicate_site ) {
					update_option( 'autoshare_ignore_duplicate_site_notice', self::get_autoshare_live_url_lock_key() );
				}
				wp_safe_redirect( remove_query_arg( array( 'autoshare_duplicate_site', '_astnonce' ) ) );
			}
		}
	}

	/**
	 * Displays a notice when plugin is being run on a different site, like a staging or testing site.
	 *
	 * @since 1.2.0
	 */
	public static function handle_site_change_notice() {
		if (
			! self::is_production_site() &&
			current_user_can( 'manage_options' ) &&
			self::get_autoshare_live_url_lock_key() !== get_option( 'autoshare_ignore_duplicate_site_notice' )
		) {
			$ignore_url = wp_nonce_url( add_query_arg( 'autoshare_duplicate_site', 'ignore' ), 'ast_duplicate_site', '_astnonce' );
			$update_url = wp_nonce_url( add_query_arg( 'autoshare_duplicate_site', 'update' ), 'ast_duplicate_site', '_astnonce' );
			?>
			<div class="notice notice-error">
				<p>
					<?php
					printf(
						// translators: 1$-2$: opening and closing <strong> tags. 3$-4$: Opening and closing link to production URL. 5$: Production URL.
						esc_html__( 'It looks like this site has moved or is a duplicate site. %1$sAutopost for X%2$s has disabled publish tweets on this site to prevent tweets from a staging or test environment. %1$sAutopost for X%2$s considers %3$s%5$s%4$s to be the site\'s URL. ', 'autoshare-for-twitter' ),
						'<strong>',
						'</strong>',
						'<a href="' . esc_url( self::get_url_from_source( 'autoshare' ) ) . '" target="_blank">',
						'</a>',
						esc_url( self::get_url_from_source( 'autoshare' ) )
					)
					?>
				</p>
				<p>
					<a class="button button-primary" href="<?php echo esc_url( $ignore_url ); ?>">
						<?php esc_html_e( 'Dismiss this (but don\'t enable publish tweets)', 'autoshare-for-twitter' ); ?>
					</a>
					<a class="button" href="<?php echo esc_url( $update_url ); ?>">
						<?php esc_html_e( 'Enable publish tweets', 'autoshare-for-twitter' ); ?>
					</a>
				</p>
			</div>
			<?php
		}
	}

	/**
	 * Determines if this is a production site.
	 *
	 * Checks if the current WordPress URL is the same as the URL Autoshare considers the live URL.
	 *
	 * @since 1.2.0
	 *
	 * @return bool Whether the site is a production URL or not.
	 */
	public static function is_production_site() {
		$wp_url_parts  = wp_parse_url( self::get_url_from_source( 'current_wp_site' ) );
		$ast_url_parts = wp_parse_url( self::get_url_from_source( 'autoshare' ) );

		if ( ! isset( $wp_url_parts['path'] ) && ! isset( $ast_url_parts['path'] ) ) {
			$paths_match = true;
		} elseif ( isset( $wp_url_parts['path'] ) && isset( $ast_url_parts['path'] ) && $wp_url_parts['path'] === $ast_url_parts['path'] ) {
			$paths_match = true;
		} else {
			$paths_match = false;
		}

		if ( isset( $wp_url_parts['host'] ) && isset( $ast_url_parts['host'] ) && $wp_url_parts['host'] === $ast_url_parts['host'] ) {
			$hosts_match = true;
		} else {
			$hosts_match = false;
		}

		// Check the host and path, do not check the protocol/scheme to avoid issues.
		if ( $paths_match && $hosts_match ) {
			$is_production = true;
		} else {
			$is_production = false;
		}

		/**
		 * Filters value of "Is production site?".
		 *
		 * @since 1.2.0
		 *
		 * @param boolean $is_production Whether the site is a production URL or not.
		 */
		return apply_filters( 'autoshare_for_twitter_is_production_site', $is_production );
	}

	/**
	 * Save autoshare live site URL in database if it is not saved.
	 *
	 * @since 1.2.0
	 */
	public static function maybe_add_autoshare_live_url() {
		$autoshare_liveurl = get_option( 'autoshare_liveurl', false );

		// Check if autoshare live url is stored in options, save option if not stored.
		if ( false === $autoshare_liveurl ) {
			self::set_autoshare_live_url_lock();
		}
	}

	/**
	 * Sets the autoshare site lock key to record the site's "live" url.
	 *
	 * This key is checked to determine if this database has moved to a different URL.
	 *
	 * @since 1.2.0
	 */
	public static function set_autoshare_live_url_lock() {
		update_option( 'autoshare_liveurl', self::get_autoshare_live_url_lock_key() );
	}

	/**
	 * Generates a unique key based on the sites URL used to determine duplicate/staging sites.
	 *
	 * The key can not simply be the site URL, e.g. http://example.com, because some hosts replaces all
	 * instances of the site URL in the database when creating a staging site. As a result, we obfuscate
	 * the URL by inserting '_[autoshare_liveurl]_' into the middle of it.
	 *
	 * @since 1.2.0
	 *
	 * @return string The autoshare live URL lock key.
	 */
	public static function get_autoshare_live_url_lock_key() {
		$url    = self::get_url_from_source( 'current_wp_site' );
		$scheme = wp_parse_url( $url, PHP_URL_SCHEME ) . '://';
		$url    = str_replace( $scheme, '', $url );

		return $scheme . substr_replace( $url, '_[autoshare_liveurl]_', intval( strlen( $url ) / 2 ), 0 );
	}

	/**
	 * Gets the URL Autoshare considers as the live URL.
	 *
	 * This URL is set by `self::set_autoshare_live_url_lock()`. This function removes the obfuscation to get a raw URL.
	 *
	 * @since 1.2.0
	 */
	public static function get_authoshare_live_url() {
		$url = get_option( 'autoshare_liveurl', '' );

		// Remove the prefix used to prevent the site URL being updated by search & replace.
		$url = str_replace( '_[autoshare_liveurl]_', '', $url );

		return $url;
	}

	/**
	 * Gets the sites WordPress or Autoshare URL.
	 *
	 * WordPress - This is typically the URL the current site is accessible via.
	 * Autoshare URL is the URL Autoshare considers to be the URL to publish/autoshare tweet on. It may differ to the WP URL if the site has moved.
	 *
	 * @since 1.2.0
	 *
	 * @param string $source The URL source to get. Optional. Takes values 'current_wp_site' or 'autoshare'. Default is 'current_wp_site'.
	 * @return string The URL.
	 */
	public static function get_url_from_source( $source = 'current_wp_site' ) {
		if ( 'autoshare' === $source ) {
			$url = self::get_authoshare_live_url();
		} else {
			$url = get_home_url();
		}

		return $url;
	}
}
