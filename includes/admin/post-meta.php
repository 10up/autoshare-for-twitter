<?php
/**
 * Responsible for the registration and display of the metabox.
 */
namespace TenUp\Auto_Tweet\Core\Post_Meta;

/**
 * Aliases
 */
use TenUp\Auto_Tweet\Utils as Utils;

/**
 * The meta prefix that all meta related keys should have
 */
const META_PREFIX = 'tenup-auto-tweet';

/**
 * Enable auto-tweet checkbox
 */
const TWEET_KEY   = 'auto-tweet';

/**
 * Holds the auto-tweet boddy
 */
const TWEET_BODY = 'tweet-body';

/**
 * Holds the formatted response object from Twitter.
 * @see post-transition.php
 */
const STATUS_KEY = 'twitter-status';

/**
 * The setup function
 *
 * @return void
 */
function setup() {

	add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\enqueue_scripts', 10, 1 );
	add_action( 'wp_ajax_tenup_auto_tweet', __NAMESPACE__ . '\ajax_save_tweet_meta' );
	add_action( 'post_submitbox_misc_actions', __NAMESPACE__ . '\tweet_submitbox_callback', 15 );
	add_action( 'tenup_auto_tweet_metabox', __NAMESPACE__ . '\render_tweet_submitbox', 10, 1 );
	add_action( 'save_post', __NAMESPACE__ . '\save_tweet_meta', 10, 1 );
}

/**
 * Enqueue the admin related JS.
 *
 * @param string $hook
 *
 * @return void
 */
function enqueue_scripts( $hook ) {

	// Only enqueue the JS on the edit post pages.
	if ( ! in_array( $hook, array( 'post-new.php', 'post.php' ) ) ) {
		return;
	}

	/**
	 * Don't bother enqueuing assets if the post type hasn't opted into auto-tweeting
	 */
	if ( ! Utils\opted_into_auto_tweet() ) {
		return;
	}

	// Enqueue the styles
	wp_enqueue_style( 'admin_tenup-auto-tweet', TUAT_URL . '/assets/css/admin-auto_tweet.css', TUAT_VERSION );

	// Enqueue the JS
	wp_enqueue_script(
		'admin_tenup-auto-tweet',
		TUAT_URL . '/assets/js/admin-auto_tweet.js',
		['jquery'],
		TUAT_VERSION,
		true
	);

	// Pass some useful info to our script
	$localization = array(
		'nonce'  => wp_create_nonce( 'admin_tenup-auto-tweet' ),
		'postId' => get_the_ID() ? get_the_ID() : ( isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0 ),
		'currentStatus' => get_post_meta( get_the_ID(), META_PREFIX . '_' . TWEET_KEY, true )
	);
	wp_localize_script( 'admin_tenup-auto-tweet', 'adminTUAT', $localization );
}

/**
 * AJAX save and response handler for our auto-tweet checkbox.
 *
 * @return string
 */
function ajax_save_tweet_meta() {

	// Verify nonce.
	if ( ! wp_verify_nonce( $_GET['nonce'], 'admin_tenup-auto-tweet' ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Invalid request.', 'tenup_auto_tweet' ) ) );
	}

	// One more check to see if the user has permission to edit the post.
	$post_id = sanitize_text_field( wp_unslash( $_GET['post_id'] ) );
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Permission denied.', 'tenup_auto_tweet' ) ) );
	}

	// Santize values
	$checked_safe = sanitize_text_field( wp_unslash( $_GET['checked'] ) );
	$text_override_safe = sanitize_text_field( wp_unslash( $_GET['text'] ) );

	// Auto-tweet post
	if ( 'true' === $checked_safe ) {
		// Holds the response array
		$response = array(
			'enabled' => 'true',
			'message' => __( 'Auto-tweet enabled.', 'tenup_auto_tweet' )
		);

		update_post_meta( $post_id, META_PREFIX . '_' . TWEET_KEY, sanitize_text_field( wp_unslash( $_GET['value'] ) ) );

		// If there's a manual tweet text
		if ( ! empty( $text_override_safe ) ) {
			update_post_meta( $post_id, META_PREFIX . '_' . TWEET_BODY, sanitize_text_field( wp_unslash( $_GET['text'] ) ) );
			$response['override'] = 'true';
		} else {
			delete_post_meta( $post_id, META_PREFIX . '_' . TWEET_BODY );
		}

		wp_send_json_success( $response );

		// Delete the value if the checkbox is empty
	} elseif ( 'false' === $checked_safe ) {
		update_post_meta( $post_id, META_PREFIX . '_' . TWEET_KEY, 0 );

		wp_send_json_success(array(
			'enabled' => 'false',
			'message' => __( 'Auto-tweet disabled.', 'tenup_auto_tweet' )
		));

		// Something happened during meta save or delete
	} else {
		wp_send_json_error( array(
				'enabled' => 'false',
				'message' => esc_html__( 'Unable to save auto-tweet status. Please try again.', 'tenup_auto_tweet' ) )
		);
	}

}

/**
 * Handles the saving of post_meta to catch the times the ajax save might not run.
 * Like when clicking 'Save Draft' or 'Publish' straight from the tweet body field.
 *
 * @param int $post_id
 *
 * @return void
 */
function save_tweet_meta( $post_id ) {

	// Check check
	if ( ! isset( $_POST['tenup_auto_tweet_meta_nonce'] ) ||
	     ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tenup_auto_tweet_meta_nonce'] ) ), 'tenup_auto_tweet_meta_fields' ) ||
	     ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ||
	     ! current_user_can( 'edit_post', $post_id )
	) {
		return;
	}

	// Auto-tweet post
	if ( isset( $_POST['tenup-auto-tweet']['auto-tweet'] ) ) {
		update_post_meta( $post_id, META_PREFIX . '_' . TWEET_KEY, (int) $_POST['tenup-auto-tweet']['auto-tweet'] );
	} else {
		update_post_meta( $post_id, META_PREFIX . '_' . TWEET_KEY, 0 );
	}

	// Auto-tweet body
	if ( isset( $_POST['tenup-auto-tweet']['auto-tweet-text'] ) ) {
		update_post_meta( $post_id, META_PREFIX . '_' . TWEET_BODY, sanitize_text_field( wp_unslash( $_POST['tenup-auto-tweet']['auto-tweet-text'] ) ) );
	} else {
		delete_post_meta( $post_id, META_PREFIX . '_' . TWEET_BODY );
	}
}

/**
 * Callback for the Auto Tweet box in the Submit meta box.
 *
 * @return void
 */
function tweet_submitbox_callback( $post ) {

	/**
	 * Don't bother enqueuing assets if the post type hasn't opted into auto-tweeting
	 */
	if ( ! Utils\opted_into_auto_tweet() ) {
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
 * @param \WP_Post $post
 *
 * @return string
 */
function render_tweet_submitbox( $post ) {

	$post_status = get_post_status( $post );

	// If the post is already published the output varies slightly.
	if ( 'publish' === $post_status ) {

		$twitter_status = Utils\get_auto_tweet_meta( get_the_ID(), STATUS_KEY );
		switch ( $twitter_status['status'] ) {

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

		echo "<p class='dashicons-before dashicons-twitter howto'>$output</p>";

		// Default output
	} else {
		echo markup_default();
	}

}

/**
 * Outputs the markeup and language to be used when a post has been successfully
 * sent to Twitter
 *
 * @param array $status_meta
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
 * @param array $status_meta
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
 * @param array $status_meta
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
function markup_default() {

	wp_nonce_field( 'tenup_auto_tweet_meta_fields', 'tenup_auto_tweet_meta_nonce' );
	ob_start();
	?>
	<label for="tenup-auto-tweet-enable">
		<input type="checkbox" id="tenup-auto-tweet-enable"
		       name="tenup-auto-tweet[auto-tweet]" value="1" <?php checked( Utils\get_auto_tweet_meta( get_the_ID(), 'auto-tweet' ) ); ?>>
		<span id="tenup-auto-tweet-icon" class="dashicons-before dashicons-twitter"></span>
		<?php esc_html_e( 'Tweet this post', 'tenup_auto_tweet' ); ?>
		<a href="#edit_tweet_text" id="tenup-auto-tweet-edit"><?php esc_html_e( 'Edit', 'tenup_auto_tweet' ); ?></a>
	</label>

	<div id="tenup-auto-tweet-override-body" style="display: none;">
		<label for="tenup-auto-tweet[auto-tweet-text]">
			<?php esc_html_e( 'Custom Message', 'tenup_auto_tweet' ); ?>:
		</label>
		<span id="tenup-auto-tweet-counter-wrap" class="alignright">0</span>
		<textarea id="tenup-auto-tweet-text" name="tenup-auto-tweet[auto-tweet-text]" rows="3"><?php echo esc_textarea( Utils\get_auto_tweet_meta( get_the_ID(), TWEET_BODY ) ); ?></textarea>

		<p><a href="#" class="hide-if-no-js cancel-tweet-text">Hide</a></p>
	</div>

	<?php
	return ob_get_clean();
}

/**
 * Fire up the module.
 *
 * @uses auto_tweet_setup
 */
add_action( 'tenup_auto_tweet_setup', __NAMESPACE__ . '\setup' );
