<?php
/**
 * Tests rest.php
 *
 * @since 1.0.0
 * @package TenUp\AutoshareForTwitter
 */

namespace TenUp\AutoshareForTwitter\Tests;

use WP_REST_Request;
use WP_UnitTestCase;
use function TenUp\AutoshareForTwitter\REST\post_autoshare_for_twitter_meta_rest_route;
use function TenUp\AutoshareForTwitter\REST\update_post_autoshare_for_twitter_meta;
use function TenUp\AutoshareForTwitter\Utils\get_autoshare_for_twitter_meta;
use function TenUp\AutoshareForTwitter\Utils\update_autoshare_for_twitter_meta;
use const TenUp\AutoshareForTwitter\Core\Post_Meta\ENABLE_AUTOSHARE_FOR_TWITTER_KEY;
use const TenUp\AutoshareForTwitter\Core\Post_Meta\TWEET_BODY_KEY;
use const TenUp\AutoshareForTwitter\Core\Post_Meta\TWEET_ACCOUNTS_KEY;
use const TenUp\AutoshareForTwitter\Core\Post_Meta\TWEET_ALLOW_IMAGE;
use const TenUp\AutoshareForTwitter\Core\Post_Meta\TWITTER_STATUS_KEY;
use const TenUp\AutoshareForTwitter\Core\Post_Meta\META_PREFIX;

/**
 * TestRest class.
 *
 * @sincd 1.0.0
 */
class RestTest extends WP_UnitTestCase {

	/**
	 * Provides a valid request for testing the autoshare endpoint.
	 *
	 * @param int $post Post ID.
	 *
	 * @return WP_REST_Requst $requst;
	 */
	private function get_valid_request( $post = null ) {
		if ( empty( $post ) ) {
			$post = $this->factory->post->create();
		}

		$request = WP_REST_Request::from_url( rest_url( post_autoshare_for_twitter_meta_rest_route( $post ) ) );
		$request->set_method( 'POST' );
		$request->set_body_params(
			[
				ENABLE_AUTOSHARE_FOR_TWITTER_KEY => true,
				TWEET_BODY_KEY                   => 'tweet override',
				'id'                             => $post,
			]
		);
		$request->set_attributes( [ 'id' => $post ] );

		return $request;
	}

	/**
	 * Sends the block editor's meta request through the REST server.
	 *
	 * @param int   $post_id Post ID.
	 * @param array $params  Body params to override.
	 *
	 * @return \WP_REST_Response
	 */
	private function post_meta_request( $post_id, $params = array() ) {
		$request = new WP_REST_Request( 'POST', '/' . post_autoshare_for_twitter_meta_rest_route( $post_id ) );
		$request->set_body_params(
			array_merge(
				array(
					ENABLE_AUTOSHARE_FOR_TWITTER_KEY => true,
					TWEET_BODY_KEY                   => 'tweet override',
					TWEET_ALLOW_IMAGE                => true,
					TWEET_ACCOUNTS_KEY               => array( 'ACCOUNT_ID' ),
				),
				$params
			)
		);

		return rest_do_request( $request );
	}

	/**
	 * A user who can't edit the post can't change its Autopost settings.
	 */
	public function test_contributor_cannot_update_meta_on_someone_elses_post() {
		$post = $this->factory->post->create( array( 'post_author' => $this->factory->user->create( array( 'role' => 'author' ) ) ) );
		wp_set_current_user( $this->factory->user->create( array( 'role' => 'contributor' ) ) );

		$response = $this->post_meta_request( $post );

		$this->assertSame( 403, $response->get_status() );
		$this->assertFalse( metadata_exists( 'post', $post, META_PREFIX . '_' . TWEET_BODY_KEY ) );
	}

	/**
	 * A user who can edit the post saves its Autopost settings, sanitized.
	 */
	public function test_author_updates_meta_on_own_post_with_sanitized_values() {
		$author = $this->factory->user->create( array( 'role' => 'author' ) );
		$post   = $this->factory->post->create( array( 'post_author' => $author ) );
		wp_set_current_user( $author );

		$response = $this->post_meta_request(
			$post,
			array(
				TWEET_BODY_KEY     => '<b>Read</b> this<script>alert(1)</script>',
				TWEET_ACCOUNTS_KEY => array( '<i>ACCOUNT_ID</i>' ),
			)
		);

		$this->assertSame( 200, $response->get_status() );
		$this->assertSame( 'Read this', get_autoshare_for_twitter_meta( $post, TWEET_BODY_KEY ) );
		$this->assertSame( array( 'ACCOUNT_ID' ), get_autoshare_for_twitter_meta( $post, TWEET_ACCOUNTS_KEY ) );
	}

	/**
	 * Tests the update_post_autoshare_for_twitter_meta function.
	 *
	 * @since 1.0.0
	 */
	public function test_update_post_autoshare_for_twitter_meta() {
		$response = update_post_autoshare_for_twitter_meta( $this->get_valid_request() );
		$this->assertEquals(
			[
				'enabled'       => true,
				'message'       => 'Autopost to X/Twitter enabled.',
				'override'      => true,
				'allowImage'    => true,
				'tweetAccounts' => [],
			],
			$response->get_data()
		);
	}

	/**
	 * The post's tweet log is in the posts endpoint for editors (edit context)
	 * but not in the public view context.
	 */
	public function test_tweet_status_field_is_only_in_edit_context() {
		$post = $this->factory->post->create( array( 'post_status' => 'publish' ) );
		update_autoshare_for_twitter_meta(
			$post,
			TWITTER_STATUS_KEY,
			array(
				'status'     => 'published',
				'twitter_id' => 444,
				'handle'     => 'handle',
				'created_at' => '2017-01-01',
			)
		);
		wp_set_current_user( $this->factory->user->create( array( 'role' => 'administrator' ) ) );

		$edit = new WP_REST_Request( 'GET', '/wp/v2/posts/' . $post );
		$edit->set_param( 'context', 'edit' );
		$edit_data = rest_do_request( $edit )->get_data();

		$view_data = rest_do_request( new WP_REST_Request( 'GET', '/wp/v2/posts/' . $post ) )->get_data();

		$this->assertSame( 'published', $edit_data['autoshare_for_twitter_status']['message'][0]['status'] );
		$this->assertArrayNotHasKey( 'autoshare_for_twitter_status', $view_data );
	}
}
