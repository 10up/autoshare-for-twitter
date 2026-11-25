<?php
/**
 * Tests the text and media the plugin sends to X.
 *
 * @package TenUp\AutoshareForTwitter
 */

namespace TenUp\AutoshareForTwitter\Tests;

use WP_UnitTestCase;
use TenUp\AutoshareForTwitter\Core\AST_Staging\AST_Staging;
use function TenUp\AutoshareForTwitter\Core\Post_Transition\publish_tweet;
use function TenUp\AutoshareForTwitter\Utils\update_autoshare_for_twitter_meta;
use const TenUp\AutoshareForTwitter\Core\Post_Meta\ENABLE_AUTOSHARE_FOR_TWITTER_KEY;
use const TenUp\AutoshareForTwitter\Core\Post_Meta\TWEET_ACCOUNTS_KEY;
use const TenUp\AutoshareForTwitter\Core\Post_Meta\TWEET_ALLOW_IMAGE;
use const TenUp\AutoshareForTwitter\Core\Post_Meta\TWEET_BODY_KEY;

/**
 * Tweet payload tests.
 */
class TweetPayloadTest extends WP_UnitTestCase {

	/**
	 * Fake X API.
	 *
	 * @var Tweet_Spy
	 */
	private $spy;

	/**
	 * Prepares the fake API. X counts every URL as 23 characters on a live
	 * site; force that count, since wp-env is a local environment.
	 */
	public function set_up() {
		parent::set_up();

		AST_Staging::set_autoshare_live_url_lock();
		connect_test_accounts();
		$this->spy = new Tweet_Spy();
		add_filter( 'autoshare_for_twitter_is_local', '__return_false' );
	}

	/**
	 * Removes uploaded files.
	 */
	public function tear_down() {
		$this->remove_added_uploads();
		parent::tear_down();
	}

	/**
	 * Publishes a post set to autopost to one account and returns what was sent.
	 *
	 * @param int   $post_id Post ID.
	 * @param array $meta    Extra Autopost meta, keyed by meta key.
	 *
	 * @return array
	 */
	private function send( $post_id, $meta = array() ) {
		$meta += array(
			ENABLE_AUTOSHARE_FOR_TWITTER_KEY => 1,
			TWEET_ACCOUNTS_KEY               => array( 'A1' ),
		);
		foreach ( $meta as $key => $value ) {
			update_autoshare_for_twitter_meta( $post_id, $key, $value );
		}

		publish_tweet( $post_id );
		$this->assertCount( 1, $this->spy->sent );

		return $this->spy->sent[0];
	}

	/**
	 * The text is the title followed by the post's URL.
	 */
	public function test_text_is_the_title_and_permalink() {
		$post_id = $this->factory->post->create(
			array(
				'post_status' => 'publish',
				'post_title'  => 'Fish &amp; Chips',
			)
		);

		$sent = $this->send( $post_id );

		$this->assertSame( 'Fish & Chips ' . get_permalink( $post_id ), $sent['text'] );
	}

	/**
	 * A custom message replaces the title.
	 */
	public function test_custom_message_replaces_the_title() {
		$post_id = $this->factory->post->create(
			array(
				'post_status' => 'publish',
				'post_title'  => 'The title',
			)
		);

		$sent = $this->send( $post_id, array( TWEET_BODY_KEY => 'Read our new post' ) );

		$this->assertSame( 'Read our new post ' . get_permalink( $post_id ), $sent['text'] );
	}

	/**
	 * A title too long for X is cut at a word boundary and marked with " ...",
	 * leaving room for the link X counts as 23 characters.
	 */
	public function test_long_title_is_cut_at_a_word_to_fit_the_limit() {
		$words   = array_map(
			static function ( $n ) {
				return sprintf( 'word%02d', $n );
			},
			range( 1, 60 )
		);
		$title   = implode( ' ', $words ); // 419 characters.
		$post_id = $this->factory->post->create(
			array(
				'post_status' => 'publish',
				'post_title'  => $title,
			)
		);
		$url     = get_permalink( $post_id );

		$text = $this->send( $post_id )['text'];

		$this->assertStringEndsWith( ' ... ' . $url, $text );
		$body = substr( $text, 0, -strlen( ' ... ' . $url ) );
		$this->assertStringStartsWith( $body . ' ', $title, 'The title must be cut between words.' );
		$this->assertLessThanOrEqual( 280, strlen( $body . ' ... ' ) + 23, 'The tweet must fit in 280 characters.' );
		$this->assertGreaterThan( 240, strlen( $body . ' ... ' ) + 23, 'The title should not be cut shorter than needed.' );
	}

	/**
	 * The featured image goes with the tweet unless the post turned images off.
	 */
	public function test_featured_image_is_sent_unless_the_post_turns_images_off() {
		$with    = $this->factory->post->create( array( 'post_status' => 'publish' ) );
		$without = $this->factory->post->create( array( 'post_status' => 'publish' ) );
		foreach ( array( $with, $without ) as $post_id ) {
			set_post_thumbnail( $post_id, $this->factory->attachment->create_upload_object( DIR_TESTDATA . '/images/33772.jpg', $post_id ) );
			update_autoshare_for_twitter_meta( $post_id, ENABLE_AUTOSHARE_FOR_TWITTER_KEY, 1 );
			update_autoshare_for_twitter_meta( $post_id, TWEET_ACCOUNTS_KEY, array( 'A1' ) );
		}

		update_autoshare_for_twitter_meta( $with, TWEET_ALLOW_IMAGE, 'yes' );
		update_autoshare_for_twitter_meta( $without, TWEET_ALLOW_IMAGE, 'no' );
		publish_tweet( $with );
		publish_tweet( $without );

		$this->assertSame( array( 'media-1' ), $this->spy->sent[0]['media']['media_ids'] );
		$this->assertArrayNotHasKey( 'media', $this->spy->sent[1] );
	}
}
