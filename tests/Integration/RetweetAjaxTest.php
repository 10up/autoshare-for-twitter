<?php
/**
 * Tests the "Post to X now" admin-ajax handler.
 *
 * @package TenUp\AutoshareForTwitter
 */

namespace TenUp\AutoshareForTwitter\Tests;

use WP_Ajax_UnitTestCase;
use WPAjaxDieContinueException;
use TenUp\AutoshareForTwitter\Core\AST_Staging\AST_Staging;
use function TenUp\AutoshareForTwitter\Utils\update_autoshare_for_twitter_meta;
use const TenUp\AutoshareForTwitter\Core\Post_Meta\ENABLE_AUTOSHARE_FOR_TWITTER_KEY;
use const TenUp\AutoshareForTwitter\Core\Post_Meta\TWEET_ACCOUNTS_KEY;

/**
 * Retweet AJAX tests.
 */
class RetweetAjaxTest extends WP_Ajax_UnitTestCase {

	/**
	 * Fake X API.
	 *
	 * @var Tweet_Spy
	 */
	private $spy;

	/**
	 * Author of the post.
	 *
	 * @var int
	 */
	private $author;

	/**
	 * A published post that opted out of autoposting.
	 *
	 * @var int
	 */
	private $post_id;

	/**
	 * Prepares a published, opted-out post with one account selected.
	 */
	public function set_up() {
		parent::set_up();

		AST_Staging::set_autoshare_live_url_lock();
		connect_test_accounts();
		$this->spy = new Tweet_Spy();

		$this->author  = $this->factory->user->create( array( 'role' => 'author' ) );
		$this->post_id = $this->factory->post->create(
			array(
				'post_status' => 'publish',
				'post_author' => $this->author,
			)
		);
		update_autoshare_for_twitter_meta( $this->post_id, ENABLE_AUTOSHARE_FOR_TWITTER_KEY, 0 );
		update_autoshare_for_twitter_meta( $this->post_id, TWEET_ACCOUNTS_KEY, array( 'A1' ) );
	}

	/**
	 * Calls the handler and returns its JSON response.
	 *
	 * @param string $nonce Value for the nonce field.
	 *
	 * @return array
	 */
	private function retweet( $nonce ) {
		$_POST['nonce']   = $nonce;
		$_POST['post_id'] = $this->post_id;

		try {
			$this->_handleAjax( 'tenup_autoshare_retweet' );
		} catch ( WPAjaxDieContinueException $e ) {
			unset( $e );
		}

		return json_decode( $this->_last_response, true );
	}

	/**
	 * A request without a valid wp_rest nonce sends nothing.
	 */
	public function test_invalid_nonce_is_refused() {
		wp_set_current_user( $this->author );

		$response = $this->retweet( 'not-a-nonce' );

		$this->assertFalse( $response['success'] );
		$this->assertCount( 0, $this->spy->sent );
	}

	/**
	 * A user who can't edit the post can't tweet it, even with a valid nonce.
	 */
	public function test_user_who_cannot_edit_the_post_is_refused() {
		wp_set_current_user( $this->factory->user->create( array( 'role' => 'contributor' ) ) );

		$response = $this->retweet( wp_create_nonce( 'wp_rest' ) );

		$this->assertFalse( $response['success'] );
		$this->assertCount( 0, $this->spy->sent, 'A contributor must not tweet someone else\'s post.' );
	}

	/**
	 * The post's author can post it on demand, even though it opted out of
	 * autoposting on publish.
	 */
	public function test_author_posts_own_published_post_even_when_opted_out() {
		wp_set_current_user( $this->author );

		$response = $this->retweet( wp_create_nonce( 'wp_rest' ) );

		$this->assertTrue( $response['success'] );
		$this->assertTrue( $response['data']['is_retweeted'] );
		$this->assertCount( 1, $this->spy->sent );
	}
}
