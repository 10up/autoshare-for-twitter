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
use function TenUp\AutoshareForTwitter\Utils\update_autoshare_for_twitter_meta;
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
 * Holds the autoshare boddy
 */
const TWEET_BODY_KEY = 'tweet-body';

/**
 * Holds the formatted response object from Twitter.
 *
 * @see post-transition.php
 */
const TWITTER_STATUS_KEY = 'twitter-status';

/**
 * The setup function
 *
 * @return void
 */
function setup() {
	add_action( 'post_submitbox_misc_actions', __NAMESPACE__ . '\tweet_submitbox_callback', 15 );
	add_action( 'autoshare_for_twitter_metabox', __NAMESPACE__ . '\render_tweet_submitbox', 10, 1 );
	add_action( 'save_post', __NAMESPACE__ . '\save_tweet_meta', 10, 1 );
}

/**
 * Handles the saving of post_meta to catch the times the ajax save might not run.
 * Like when clicking 'Save Draft' or 'Publish' straight from the tweet body field.
 *
 * @param int $post_id The post id.
 *
 * @return void
 */
function save_tweet_meta( $post_id ) {
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$form_data = sanitize_autoshare_for_twitter_meta_data(
		// Using FILTER_DEFAULT here as data is being passed to sanitize function.
		filter_input( INPUT_POST, META_PREFIX, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY )
	);

	save_autoshare_for_twitter_meta_data( $post_id, $form_data );
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
			case ENABLE_AUTOSHARE_FOR_TWITTER_KEY:
				$filtered_data[ $key ] = boolval( $value );
				break;

			case TWEET_BODY_KEY:
				$filtered_data[ $key ] = sanitize_text_field( $value );
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
	if ( empty( $data ) || ! is_array( $data ) ) {
		return;
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
		}
	}
}

/**
 * Callback for the Auto Tweet box in the Submit meta box.
 *
 * @param \WP_Post $post The post being submitted.
 *
 * @return void
 */
function tweet_submitbox_callback( $post ) {

	/**
	 * Don't bother enqueuing assets if the post type hasn't opted into autoshareing.
	 */
	if ( ! Utils\opted_into_autoshare_for_twitter( $post->ID ) ) {
		return;
	}

	?>
	<div id="autoshare_for_twitter_metabox" class="misc-pub-section">
		<?php do_action( 'autoshare_for_twitter_metabox', $post ); ?>
	</div>
	<?php
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

		$twitter_status = Utils\get_autoshare_for_twitter_meta( get_the_ID(), TWITTER_STATUS_KEY );
		$status         = isset( $twitter_status['status'] ) ? $twitter_status['status'] : '';

		switch ( $status ) {

			case 'published':
				$output = markup_published( $twitter_status );
				break;

			case 'error':
				$output = markup_error( $twitter_status );
				break;

			case 'unknown':
				$output = markup_unknown( $twitter_status );
				break;

			default:
				$output = __( 'This post was not tweeted.', 'auto-share-for-twitter' );
				break;
		}

		echo wp_kses_post( "<p class='dashicons-before dashicons-twitter howto'>$output</p>" );

		// Default output.
	} else {
		echo _safe_markup_default(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

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
	$post        = get_post( $post );
	$post_status = get_post_status( $post );

	if ( 'publish' === $post_status ) {

		$twitter_status = Utils\get_autoshare_for_twitter_meta( $post->ID, TWITTER_STATUS_KEY );
		$status         = isset( $twitter_status['status'] ) ? $twitter_status['status'] : '';

		switch ( $status ) {
			case 'published':
				$date        = Utils\date_from_twitter( $twitter_status['created_at'] );
				$twitter_url = Utils\link_from_twitter( $twitter_status['twitter_id'] );

				return [
					// Translators: Placeholder is a date.
					'message' => sprintf( __( 'Tweeted on %s', 'auto-share-for-twitter' ), $date ),
					'url'     => $twitter_url,
				];

			case 'error':
				return [
					'message' => __( 'Failed to tweet: ', 'auto-share-for-twitter' ) . $twitter_status['message'],
				];

			case 'unknown':
				return [
					'message' => $twitter_status['message'],
				];

			default:
				return [
					'message' => __( 'This post was not tweeted.', 'auto-share-for-twitter' ),
				];
		}
	}

	return [ 'message' => '' ];

}

/**
 * Outputs the markeup and language to be used when a post has been successfully
 * sent to Twitter
 *
 * @param array $status_meta The status meta.
 *
 * @return string
 */
function markup_published( $status_meta ) {

	$date        = Utils\date_from_twitter( $status_meta['created_at'] );
	$twitter_url = Utils\link_from_twitter( $status_meta['twitter_id'] );

	return sprintf(
		'%s <span>%s</span> (<a href="%s" target="_blank">%s</a>)</p>',
		esc_html__( 'Tweeted on', 'auto-share-for-twitter' ),
		esc_html( $date ),
		esc_url( $twitter_url ),
		esc_html__( 'View', 'auto-share-for-twitter' )
	);
}

/**
 * Outputs the markeup and language to be used when a post has had an error
 * when posting to Twitter
 *
 * @param array $status_meta The status meta.
 *
 * @return string
 */
function markup_error( $status_meta ) {

	return sprintf(
		'%s<br><pre>%s</pre></p>',
		esc_html__( 'Failed to tweet', 'auto-share-for-twitter' ),
		esc_html( $status_meta['message'] )
	);
}

/**
 * Outputs the markeup and language to be used when a post NOT been auto-posted to Twitter.
 * Also considered a fallback message of sorts.
 *
 * @param array $status_meta The status meta.
 *
 * @return string
 */
function markup_unknown( $status_meta ) {
	return $status_meta['message'];
}

/**
 * Outputs the <input> markup required to set a post to autoshare.
 *
 * @return string
 */
function _safe_markup_default() {

	ob_start();
	?>
	<label for="autoshare-for-twitter-enable">
		<input
			type="checkbox"
			id="autoshare-for-twitter-enable"
			name="<?php echo esc_attr( sprintf( '%s[%s]', META_PREFIX, ENABLE_AUTOSHARE_FOR_TWITTER_KEY ) ); ?>"
			value="1"
			<?php checked( Utils\get_autoshare_for_twitter_meta( get_the_ID(), ENABLE_AUTOSHARE_FOR_TWITTER_KEY ) ); ?>
		>
		<span id="autoshare-for-twitter-icon" class="dashicons-before dashicons-twitter"></span>
		<?php esc_html_e( 'Tweet this post', 'auto-share-for-twitter' ); ?>
		<a href="#edit_tweet_text" id="autoshare-for-twitter-edit"><?php esc_html_e( 'Edit', 'auto-share-for-twitter' ); ?></a>
	</label>

	<div id="autoshare-for-twitter-override-body" style="display: none;">
		<label for="<?php echo esc_attr( sprintf( '%s[%s]', META_PREFIX, TWEET_BODY_KEY ) ); ?>">
			<?php esc_html_e( 'Custom Message', 'auto-share-for-twitter' ); ?>:
		</label>
		<span id="autoshare-for-twitter-counter-wrap" class="alignright">0</span>
		<textarea
			id="autoshare-for-twitter-text"
			name="<?php echo esc_attr( sprintf( '%s[%s]', META_PREFIX, TWEET_BODY_KEY ) ); ?>"
			rows="3"
		><?php echo esc_textarea( Utils\get_autoshare_for_twitter_meta( get_the_ID(), TWEET_BODY_KEY ) ); ?></textarea>

		<p><a href="#" class="hide-if-no-js cancel-tweet-text">Hide</a></p>
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
