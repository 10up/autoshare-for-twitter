<?php
/**
 * Responsible for the registration and display of the metabox.
 *
 * @package TenUp\AutoshareForTwitter\Core
 */

namespace TenUp\AutoshareForTwitter\Core\Post_Meta;

/**
 * Aliases
 */
use TenUp\AutoshareForTwitter\Utils as Utils;
use TenUp\AutoshareForTwitter\Core\Twitter_Accounts as Twitter_Accounts;

use function TenUp\AutoshareForTwitter\Utils\autoshare_enabled;
use function TenUp\AutoshareForTwitter\Utils\update_autoshare_for_twitter_meta;
use function TenUp\AutoshareForTwitter\Utils\tweet_image_allowed;
use function TenUp\AutoshareForTwitter\Utils\delete_autoshare_for_twitter_meta;

/**
 * The meta prefix that all meta related keys should have
 */
const META_PREFIX = 'autoshare';

/**
 * Enable autoshare checkbox
 */
const ENABLE_AUTOSHARE_FOR_TWITTER_KEY = 'autoshare_for_twitter';

/**
 * Holds the autoshare body
 */
const TWEET_BODY_KEY = 'tweet-body';

/**
 * Holds the formatted response object from X.
 *
 * @see post-transition.php
 */
const TWITTER_STATUS_KEY = 'status';

const TWEET_ALLOW_IMAGE = 'tweet-allow-image';

/**
 * Holds the tweet accounts
 */
const TWEET_ACCOUNTS_KEY = 'tweet_accounts';

/**
 * The setup function
 *
 * @return void
 */
function setup() {
	// Add Autopost for X meta box to classic editor.
	add_action( 'add_meta_boxes', __NAMESPACE__ . '\autoshare_for_twitter_metabox', 10, 2 );
	add_action( 'save_post', __NAMESPACE__ . '\save_tweet_meta', 10, 3 );
}

/**
 * Handles the saving of post_meta to catch the times the ajax save might not run.
 * Like when clicking 'Save Draft' or 'Publish' straight from the tweet body field.
 *
 * @param int     $post_id The post id.
 * @param WP_Post $post Post object.
 * @param boolean $update Whether the post already exists.
 *
 * @return void
 */
function save_tweet_meta( $post_id, $post = null, $update = true ) {
	if ( ! $update ) {
		return;
	}

	// Meta is saved in a separate request in the block editor.
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$form_data = sanitize_autoshare_for_twitter_meta_data( get_autoshare_post_form_data() );

	save_autoshare_for_twitter_meta_data( $post_id, $form_data );
}

/**
 * Provides data passed from the post editor form.
 *
 * @return array
 */
function get_autoshare_post_form_data() {
	// Using FILTER_DEFAULT here as data is being passed to sanitize function.
	$data = filter_input( INPUT_POST, META_PREFIX, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

	/**
	 * Filters data received from the post form.
	 *
	 * @param array $data
	 */
	return apply_filters( 'autoshare_post_form_data', $data );
}

/**
 * Sanitizes autoshare-related fields passed while saving a post.
 *
 * @since 1.0.0
 * @param array $data Form data.
 * @return array Filtered form data.
 */
function sanitize_autoshare_for_twitter_meta_data( $data ) {
	if ( empty( $data ) || ! is_array( $data ) ) {
		return [];
	}

	$filtered_data = [];
	foreach ( $data as $key => $value ) {
		switch ( $key ) {
			case TWEET_ALLOW_IMAGE:
			case ENABLE_AUTOSHARE_FOR_TWITTER_KEY:
				$filtered_data[ $key ] = boolval( $value );
				break;

			case TWEET_BODY_KEY:
				$filtered_data[ $key ] = sanitize_text_field( $value );
				break;

			case TWEET_ACCOUNTS_KEY:
				$filtered_data[ $key ] = is_array( $value ) ? array_map( 'sanitize_text_field', $value ) : [];
				break;
		}
	}

	return $filtered_data;
}

/**
 * Saves fields in an array of autoshare meta.
 *
 * @since 1.0.0
 * @param int   $post_id WP_Post ID.
 * @param array $data Associative array of data to save.
 */
function save_autoshare_for_twitter_meta_data( $post_id, $data ) {
	if ( ! is_array( $data ) ) {
		$data = [];
	}

	// If the enable key is not set, set it to the default setting value.
	if ( ! array_key_exists( ENABLE_AUTOSHARE_FOR_TWITTER_KEY, $data ) ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['autoshare-classic-editor'] ) ) {
			// Handle unchecked "Tweet this post" checkbox for classic editor.
			$data[ ENABLE_AUTOSHARE_FOR_TWITTER_KEY ] = 0;
		} else {
			$data[ ENABLE_AUTOSHARE_FOR_TWITTER_KEY ] = autoshare_enabled( $post_id ) ? 1 : 0;
		}
	}

	if ( ! array_key_exists( TWEET_ALLOW_IMAGE, $data ) ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['autoshare-classic-editor'] ) ) {
			// Handle unchecked "Tweet this post" checkbox for classic editor.
			$data[ TWEET_ALLOW_IMAGE ] = 0;
		} else {
			$data[ TWEET_ALLOW_IMAGE ] = tweet_image_allowed( $post_id ) ? 1 : 0;
		}
	}

	foreach ( $data as $key => $value ) {
		switch ( $key ) {
			case ENABLE_AUTOSHARE_FOR_TWITTER_KEY:
				update_autoshare_for_twitter_meta( $post_id, ENABLE_AUTOSHARE_FOR_TWITTER_KEY, $value );
				break;

			case TWEET_BODY_KEY:
				if ( ! empty( $value ) ) {
					update_autoshare_for_twitter_meta( $post_id, TWEET_BODY_KEY, $value );
				} else {
					delete_autoshare_for_twitter_meta( $post_id, TWEET_BODY_KEY );
				}

				break;

			case TWEET_ALLOW_IMAGE:
				update_autoshare_for_twitter_meta( $post_id, TWEET_ALLOW_IMAGE, $value ? 'yes' : 'no' );
				break;

			case TWEET_ACCOUNTS_KEY:
				update_autoshare_for_twitter_meta( $post_id, TWEET_ACCOUNTS_KEY, $value );
				break;

			default:
				break;
		}
	}
}

/**
 * Add Autopost for X metabox on post/post types.
 *
 * @param string  $post_type Post Type.
 * @param WP_Post $post      WP_Post object.
 *
 * @since 1.3.0
 */
function autoshare_for_twitter_metabox( $post_type, $post ) {
	/**
	 * Don't bother enqueuing assets if the post type hasn't opted into autosharing.
	 */
	if ( ! Utils\opted_into_autoshare_for_twitter( $post->ID ) ) {
		return;
	}

	add_meta_box(
		'autoshare_for_twitter_metabox',
		__( 'Autopost for X/Twitter', 'autoshare-for-twitter' ),
		__NAMESPACE__ . '\render_tweet_submitbox',
		null,
		'side',
		'default',
		array( '__back_compat_meta_box' => true )
	);
}

/**
 * Determines which markup should be used inside the metabox.
 *
 * @param \WP_Post $post The post we are rendering on.
 *
 * @return void
 */
function render_tweet_submitbox( $post ) {

	$post_status = get_post_status( $post );

	// If the post is already published the output varies slightly.
	if ( 'publish' === $post_status ) {
		// Display tweet status logs.
		?>
		<div class="autoshare-for-twitter-status-logs-wrapper">
			<?php echo wp_kses_post( get_tweet_status_logs( $post ) ); ?>
		</div>
		<hr/>
		<button class="button button-link tweet-now-button">
		<?php esc_attr_e( 'Post to X/Twitter now', 'autoshare-for-twitter' ); ?><span class="dashicons dashicons-arrow-down-alt2"></span>
		</button>
		<div class="autoshare-for-twitter-tweet-now-wrapper" style="display: none;">
			<?php
			echo _safe_markup_default(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
			<input type="button" name="tweet_now" id="tweet_now" class="button button-primary" value="<?php esc_attr_e( 'Post to X/Twitter', 'autoshare-for-twitter' ); ?>">
			<span class="spinner"></span>
		</div>
		<?php
		// Default output.
	} else {
		$status_logs = get_tweet_status_logs( $post );
		if ( ! empty( $status_logs ) ) {
			// Display tweet status logs.
			?>
			<div class="autoshare-for-twitter-status-logs-wrapper">
				<?php echo wp_kses_post( $status_logs ); ?>
			</div>
			<hr/>
			<?php
		}
		echo _safe_markup_default(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

}


/**
 * Render the X accounts to be used for autoposting.
 *
 * @param int $post_id The post ID.
 */
function render_twitter_accounts( $post_id ) {
	$post_status = get_post_status( $post_id );
	$accounts    = ( new Twitter_Accounts() )->get_twitter_accounts( true );
	if ( empty( $accounts ) ) {
		return;
	}

	$enabled = Utils\get_tweet_accounts( $post_id );

	$display = ( autoshare_enabled( $post_id ) || 'publish' === $post_status ) ? '' : 'display: none;';
	?>
	<div class="autoshare-for-twitter-accounts-wrapper" style="<?php echo esc_attr( $display ); ?>">
		<?php
		foreach ( $accounts as $account ) {
			?>
			<div class="twitter-account-wrapper">
				<img src="<?php echo esc_url( $account['profile_image_url'] ); ?>" alt="<?php echo esc_attr( $account['name'] ); ?>" class="twitter-account-profile-image" />
				<span class="account-details">
					<strong>@<?php echo esc_html( $account['username'] ); ?></strong>
					<br />
					<?php echo esc_html( $account['name'] ); ?>
				</span>
				<input
					type="checkbox"
					class="autoshare-for-twitter-account-checkbox"
					id="autoshare-for-twitter-account-<?php echo esc_attr( $account['id'] ); ?>"
					name="<?php echo esc_attr( sprintf( '%s[%s][]', META_PREFIX, TWEET_ACCOUNTS_KEY ) ); ?>"
					value="<?php echo esc_attr( $account['id'] ); ?>"
					<?php checked( true, in_array( $account['id'], $enabled, true ), true ); ?>
				/>
			</div>
			<?php
		}
		?>
		<span class="connect-account-link">
			<a href="<?php echo esc_url( admin_url( 'options-general.php?page=autoshare-for-twitter' ) ); ?>" target="_blank">
				<?php esc_html_e( 'Connect an account', 'autoshare-for-twitter' ); ?>
			</a>
		</span>
	</div>
	<?php
}

/**
 * Gets info on the post's Tweet status to send to REST.
 *
 * @since 1.0.0
 *
 * @param int|WP_Post $post The post we are rendering on.
 * @return array Associative array containing a message and a URL if the post was tweeted.
 */
function get_tweet_status_message( $post ) {
	$post           = get_post( $post );
	$post_status    = get_post_status( $post );
	$response_array = array();

	if ( empty( $post ) ) {
		return [ 'message' => $response_array ];
	}

	$tweet_metas = Utils\get_autoshare_for_twitter_meta( $post->ID, TWITTER_STATUS_KEY );

	if ( empty( $tweet_metas ) || isset( $tweet_metas['twitter_id'] ) ) {
		$tweet_metas = array(
			array(
				'status'     => isset( $tweet_metas['status'] ) ? $tweet_metas['status'] : '',
				'created_at' => isset( $tweet_metas['created_at'] ) ? $tweet_metas['created_at'] : '',
				'twitter_id' => isset( $tweet_metas['twitter_id'] ) ? $tweet_metas['twitter_id'] : '',
				'handle'     => isset( $tweet_metas['handle'] ) ? $tweet_metas['handle'] : '',
			),
		);
	} elseif ( isset( $tweet_metas['status'] ) && ( 'error' === $tweet_metas['status'] || 'unknown' === $tweet_metas['status'] || 'other' === $tweet_metas['status'] ) ) {
		$tweet_metas = array(
			$tweet_metas,
		);
	}

	foreach ( $tweet_metas as $tweet_meta ) {
		$status         = $tweet_meta['status'];
		$twitter_handle = $tweet_meta['handle'] ?? '';
		if ( 'publish' !== $post_status && empty( $status ) ) {
			continue;
		}

		switch ( $status ) {
			case 'published':
				$date        = Utils\date_from_twitter( $tweet_meta['created_at'] );
				$twitter_url = Utils\link_from_twitter( $tweet_meta );

				$response_array[] = [
					// Translators: Placeholder is a date.
					'message' => sprintf( __( 'Posted to X/Twitter on %s', 'autoshare-for-twitter' ), $date ),
					'url'     => $twitter_url,
					'status'  => $status,
					'handle'  => $twitter_handle,
				];

				break;

			case 'error':
				$response_array[] = [
					'message' => __( 'Failed to post to X/Twitter; ', 'autoshare-for-twitter' ) . $tweet_meta['message'],
					'status'  => $status,
					'handle'  => $twitter_handle,
				];

				break;

			case 'unknown':
				$response_array[] = [
					'message' => $tweet_meta['message'],
					'status'  => $status,
					'handle'  => $twitter_handle,
				];

				break;

			default:
				$response_array[] = [
					'message' => __( 'This post has not been posted to X/Twitter.', 'autoshare-for-twitter' ),
					'status'  => $status,
				];
		}
	}

	return [ 'message' => $response_array ];

}

/**
 * Gets info on the post's Tweet status to display in classic editor metabox.
 *
 * @since 1.3.0
 *
 * @param int|WP_Post $post The post we are rendering on.
 * @return string markup containing the tweet status logs if the post was tweeted.
 */
function get_tweet_status_logs( $post ) {
	$post        = get_post( $post );
	$post_status = get_post_status( $post );
	$status_logs = '';

	$tweet_metas = Utils\get_autoshare_for_twitter_meta( $post->ID, TWITTER_STATUS_KEY );

	if ( empty( $tweet_metas ) || isset( $tweet_metas['twitter_id'] ) ) {
		$tweet_metas = array(
			array(
				'status'     => isset( $tweet_metas['status'] ) ? $tweet_metas['status'] : '',
				'created_at' => isset( $tweet_metas['created_at'] ) ? $tweet_metas['created_at'] : '',
				'twitter_id' => isset( $tweet_metas['twitter_id'] ) ? $tweet_metas['twitter_id'] : '',
				'handle'     => isset( $tweet_metas['handle'] ) ? $tweet_metas['handle'] : '',
			),
		);
	} elseif ( isset( $tweet_metas['status'] ) && ( 'error' === $tweet_metas['status'] || 'unknown' === $tweet_metas['status'] || 'other' === $tweet_metas['status'] ) ) {
		$tweet_metas = array(
			$tweet_metas,
		);
	}

	foreach ( $tweet_metas as $twitter_meta ) {
		$status = isset( $twitter_meta['status'] ) ? $twitter_meta['status'] : '';
		if ( 'publish' !== $post_status && empty( $status ) ) {
			continue;
		}

		switch ( $status ) {

			case 'published':
				$output = markup_published( $twitter_meta );
				break;

			case 'error':
				$output = markup_error( $twitter_meta );
				break;

			case 'unknown':
				$output = markup_unknown( $twitter_meta );
				break;

			default:
				$output = __( 'This post has not been posted to X/Twitter.', 'autoshare-for-twitter' );
				break;
		}

		$status_logs .= "<div class='autoshare-for-twitter-status-wrap'><span class='autoshare-for-twitter-status-icon autoshare-for-twitter-status-icon--$status'></span>$output</div>";
	}

	return wp_kses_post( $status_logs );
}

/**
 * Outputs the markup and language to be used when a post has been successfully
 * sent to X
 *
 * @param array $status_meta The status meta.
 *
 * @return string
 */
function markup_published( $status_meta ) {

	$date        = Utils\date_from_twitter( $status_meta['created_at'] );
	$twitter_url = Utils\link_from_twitter( $status_meta );
	$handle      = ! empty( $status_meta['handle'] ) ? ' - @' . $status_meta['handle'] : '';

	return sprintf(
		'<div class="autoshare-for-twitter-status-log-data"><strong>%s</strong><br/> <span>%s</span> (<a href="%s" target="_blank">%s</a>)<strong>%s</strong></div>',
		esc_html__( 'Posted to X/Twitter on', 'autoshare-for-twitter' ),
		esc_html( $date ),
		esc_url( $twitter_url ),
		esc_html__( 'View', 'autoshare-for-twitter' ),
		esc_html( $handle )
	);
}

/**
 * Outputs the markup and language to be used when a post has had an error
 * when posting to X
 *
 * @param array $status_meta The status meta.
 *
 * @return string
 */
function markup_error( $status_meta ) {
	$handle     = ! empty( $status_meta['handle'] ) ? '<strong> - @' . $status_meta['handle'] . '</strong>' : '';
	$learn_more = '';
	if ( 'When authenticating requests to the Twitter API v2 endpoints, you must use keys and tokens from a Twitter developer App that is attached to a Project. You can create a project via the developer portal.' === $status_meta['message'] ) {
		$learn_more = sprintf(
			' <a href="%s" target="_blank" rel="external noreferrer noopener">%s</a>',
			esc_url( 'https://developer.twitter.com/en/docs/twitter-api/migrate/ready-to-migrate' ),
			esc_html__( 'Learn more here.', 'autoshare-for-twitter' )
		);
	}

	return sprintf(
		'<div class="autoshare-for-twitter-status-log-data"><strong>%s</strong><br/><pre>%s</pre>%s</div>',
		esc_html__( 'Failed to post to X/Twitter', 'autoshare-for-twitter' ),
		esc_html( $status_meta['message'] ) . wp_kses_post( $learn_more ),
		wp_kses_post( $handle )
	);
}

/**
 * Outputs the markup and language to be used when a post NOT been auto-posted to X.
 * Also considered a fallback message of sorts.
 *
 * @param array $status_meta The status meta.
 *
 * @return string
 */
function markup_unknown( $status_meta ) {
	$handle = ! empty( $status_meta['handle'] ) ? '<strong> - @' . $status_meta['handle'] . '</strong>' : '';
	return sprintf(
		'<div class="autoshare-for-twitter-status-log-data">%s</div>',
		esc_html( $status_meta['message'] ) . wp_kses_post( $handle )
	);
}

/**
 * Outputs the <input> markup required to set a post to autoshare.
 *
 * @return string
 */
function _safe_markup_default() {
	$custom_tweet_body = Utils\get_autoshare_for_twitter_meta( get_the_ID(), TWEET_BODY_KEY );

	$count      = ( strlen( get_permalink( get_the_ID() ) ) + 5 );
	$max_length = 280 - $count;
	ob_start();
	?>
	<label class="autoshare-for-twitter-enable-wrap" for="autoshare-for-twitter-enable">
		<input
			type="checkbox"
			id="autoshare-for-twitter-enable"
			name="<?php echo esc_attr( sprintf( '%s[%s]', META_PREFIX, ENABLE_AUTOSHARE_FOR_TWITTER_KEY ) ); ?>"
			value="1"
			<?php checked( autoshare_enabled( get_the_ID() ) ); ?>
		>
		<input type="hidden" name="autoshare-classic-editor" value="1" />
		<?php esc_html_e( 'Post to X/Twitter', 'autoshare-for-twitter' ); ?>
		<a href="#edit_tweet_text" id="autoshare-for-twitter-edit" style="<?php echo ( ( ! empty( $custom_tweet_body ) ) ? 'display: none;' : '' ); ?>">
			<?php esc_attr_e( 'Edit', 'autoshare-for-twitter' ); ?>
		</a>
	</label>

	<div class="autoshare-for-twitter-tweet-allow-image-wrap" style="display: none;">
		<p>
			<label for="autoshare-for-twitter-tweet-allow-image">
				<input
					type="checkbox"
					id="autoshare-for-twitter-tweet-allow-image"
					name="<?php echo esc_attr( sprintf( '%s[%s]', META_PREFIX, TWEET_ALLOW_IMAGE ) ); ?>"
					value="1"
					<?php checked( tweet_image_allowed( get_the_ID() ) ); ?>
				>
				<?php esc_html_e( 'Use featured image in Post to X/Twitter', 'autoshare-for-twitter' ); ?>
			</label>
		</p>
	</div>

	<?php
	// Display twitter accounts.
	render_twitter_accounts( get_the_ID() );
	?>

	<div id="autoshare-for-twitter-override-body" style="<?php echo ( ( empty( $custom_tweet_body ) ) ? 'display: none;' : '' ); ?>">
		<label for="<?php echo esc_attr( sprintf( '%s[%s]', META_PREFIX, TWEET_BODY_KEY ) ); ?>">
			<?php esc_html_e( 'Custom Message', 'autoshare-for-twitter' ); ?>:
		</label>
		<span id="autoshare-for-twitter-counter-wrap" class="alignright"><?php echo esc_attr( $count ); ?></span>
		<textarea
			id="autoshare-for-twitter-text"
			name="<?php echo esc_attr( sprintf( '%s[%s]', META_PREFIX, TWEET_BODY_KEY ) ); ?>"
			rows="3"
			maxlength="<?php echo esc_attr( $max_length ); ?>"
		><?php echo esc_textarea( $custom_tweet_body ); ?></textarea>
		<p class="howto" id="autoshare-for-twitter-text-desc"><?php esc_html_e( 'Character count is inclusive of the post permalink which will be included in the final tweet.', 'autoshare-for-twitter' ); ?></p>

		<p><a href="#" class="hide-if-no-js cancel-tweet-text"><?php esc_html_e( 'Hide', 'autoshare-for-twitter' ); ?></a></p>
	</div>

	<p id="autoshare-for-twitter-error-message"></p>

	<?php
	return ob_get_clean();
}

/**
 * Fire up the module.
 *
 * @uses autoshare_for_twitter_setup
 */
add_action( 'autoshare_for_twitter_setup', __NAMESPACE__ . '\setup' );
