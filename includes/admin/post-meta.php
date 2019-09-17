<?php
/**
 * Responsible for the registration and display of the metabox.
 *
 * @package TenUp\Auto_Tweet\Core
 */

namespace TenUp\Auto_Tweet\Core\Post_Meta;

/**
 * Aliases
 */
use TenUp\Auto_Tweet\Utils as Utils;
use function TenUp\Auto_Tweet\Utils\update_autotweet_meta;
use function TenUp\Auto_Tweet\Utils\delete_autotweet_meta;

/**
 * The meta prefix that all meta related keys should have
 */
const META_PREFIX = 'tenup-auto-tweet';

/**
 * Enable auto-tweet checkbox
 */
const ENABLE_AUTOTWEET_KEY = 'auto-tweet';

/**
 * Holds the auto-tweet boddy
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
	add_action( 'tenup_auto_tweet_metabox', __NAMESPACE__ . '\render_tweet_submitbox', 10, 1 );
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

	$form_data = sanitize_autotweet_meta_data(
		// Using FILTER_DEFAULT here as data is being passed to sanitize function.
		filter_input( INPUT_POST, META_PREFIX, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY )
	);

	save_autotweet_meta_data( $post_id, $form_data );
}

/**
 * Sanitizes autotweet-related fields passed while saving a post.
 *
 * @since 1.0.0
 * @param array $data Form data.
 * @return array Filtered form data.
 */
function sanitize_autotweet_meta_data( $data ) {
	if ( empty( $data ) || ! is_array( $data ) ) {
		return [];
	}

	$filtered_data = [];
	foreach ( $data as $key => $value ) {
		switch ( $key ) {
			case ENABLE_AUTOTWEET_KEY:
				$filtered_data[ $key ] = boolval( $value );
				break;

			case TWEET_BODY_KEY:
				$filtered_data[ $key ] = sanitize_text_field( $value );
		}
	}

	return $filtered_data;
}

/**
 * Saves fields in an array of autotweet meta.
 *
 * @since 1.0.0
 * @param int   $post_id WP_Post ID.
 * @param array $data Associative array of data to save.
 */
function save_autotweet_meta_data( $post_id, $data ) {
	if ( empty( $data ) || ! is_array( $data ) ) {
		return;
	}

	foreach ( $data as $key => $value ) {
		switch ( $key ) {
			case ENABLE_AUTOTWEET_KEY:
				update_autotweet_meta( $post_id, ENABLE_AUTOTWEET_KEY, $value );
				break;

			case TWEET_BODY_KEY:
				if ( ! empty( $value ) ) {
					update_autotweet_meta( $post_id, TWEET_BODY_KEY, $value );
				} else {
					delete_autotweet_meta( $post_id, TWEET_BODY_KEY );
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
	 * Don't bother enqueuing assets if the post type hasn't opted into auto-tweeting.
	 */
	if ( ! Utils\opted_into_autotweet( $post->ID ) ) {
		return;
	}

	?>
	<div id="tenup-auto-tweet_metabox" class="misc-pub-section">
		<?php do_action( 'tenup_auto_tweet_metabox', $post ); ?>
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

		$twitter_status = Utils\get_autotweet_meta( get_the_ID(), TWITTER_STATUS_KEY );
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
				$output = __( 'This post was not tweeted.', 'tenup_auto_tweet' );
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

	// If the post is already published the output varies slightly.
	if ( 'publish' === $post_status ) {

		$twitter_status = Utils\get_autotweet_meta( $post->ID, TWITTER_STATUS_KEY );
		$status         = isset( $twitter_status['status'] ) ? $twitter_status['status'] : '';

		switch ( $status ) {
			case 'published':
				$date        = Utils\date_from_twitter( $twitter_status['created_at'] );
				$twitter_url = Utils\link_from_twitter( $twitter_status['twitter_id'] );

				return [
					// Translators: Placeholder is a date.
					'message' => sprintf( __( 'Tweeted on %s', 'tenup_auto_tweet' ), $date ),
					'url'     => $twitter_url,
				];

			case 'error':
				return [
					'message' => __( 'Failed to tweet: ', 'autotweet' ) . $twitter_status['message'],
				];

			case 'unknown':
				return [
					'message' => $twitter_status['message'],
				];

			default:
				return [
					'message' => __( 'This post was not tweeted.', 'tenup_auto_tweet' ),
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
		esc_html__( 'Tweeted on', 'tenup_auto_tweet' ),
		esc_html( $date ),
		esc_url( $twitter_url ),
		esc_html__( 'View', 'tenup_auto_tweet' )
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
		esc_html__( 'Failed to tweet', 'tenup_auto_tweet' ),
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
 * Outputs the <input> markup required to set a post to auto-tweet.
 *
 * @return string
 */
function _safe_markup_default() {

	ob_start();
	?>
	<label for="tenup-auto-tweet-enable">
		<input
			type="checkbox"
			id="tenup-auto-tweet-enable"
			name="<?php echo esc_attr( sprintf( '%s[%s]', META_PREFIX, ENABLE_AUTOTWEET_KEY ) ); ?>"
			value="1"
			<?php checked( Utils\get_autotweet_meta( get_the_ID(), 'auto-tweet' ) ); ?>
		>
		<span id="tenup-auto-tweet-icon" class="dashicons-before dashicons-twitter"></span>
		<?php esc_html_e( 'Tweet this post', 'tenup_auto_tweet' ); ?>
		<a href="#edit_tweet_text" id="tenup-auto-tweet-edit"><?php esc_html_e( 'Edit', 'tenup_auto_tweet' ); ?></a>
	</label>

	<div id="tenup-auto-tweet-override-body" style="display: none;">
		<label for="<?php echo esc_attr( sprintf( '%s[%s]', META_PREFIX, TWEET_BODY_KEY ) ); ?>">
			<?php esc_html_e( 'Custom Message', 'tenup_auto_tweet' ); ?>:
		</label>
		<span id="tenup-auto-tweet-counter-wrap" class="alignright">0</span>
		<textarea
			id="tenup-auto-tweet-text"
			name="<?php echo esc_attr( sprintf( '%s[%s]', META_PREFIX, TWEET_BODY_KEY ) ); ?>"
			rows="3"
		><?php echo esc_textarea( Utils\get_autotweet_meta( get_the_ID(), TWEET_BODY_KEY ) ); ?></textarea>

		<p><a href="#" class="hide-if-no-js cancel-tweet-text">Hide</a></p>
	</div>

	<p id="tenup-autotweet-error-message"></p>

	<?php
	return ob_get_clean();
}

/**
 * Fire up the module.
 *
 * @uses auto_tweet_setup
 */
add_action( 'tenup_auto_tweet_setup', __NAMESPACE__ . '\setup' );
