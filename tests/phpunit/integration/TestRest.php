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
use function TenUp\AutoshareForTwitter\REST\update_post_autoshare_for_twitter_meta_permission_check;
use function TenUp\AutoshareForTwitter\REST\update_post_autoshare_for_twitter_meta;
use const TenUp\AutoshareForTwitter\Core\Post_Meta\ENABLE_AUTOSHARE_FOR_TWITTER_KEY;
use const TenUp\AutoshareForTwitter\Core\Post_Meta\TWEET_BODY_KEY;

/**
 * TestRest class.
 *
 * @sincd 1.0.0
 */
class TestRest extends WP_UnitTestCase {
	/**
	 * Provides a valid request for testing the autoshare endpoint.
	 *
	 * @param int $post Post ID.
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
	 * Tests the post_autoshare_for_twitter_meta_rest_route function.
	 *
	 * @since 1.0.0
	 */
	public function test_post_autoshare_for_twitter_meta_rest_route() {
		$this->assertEquals(
			'autoshare/v1/post-autoshare-for-twitter-meta/999',
			post_autoshare_for_twitter_meta_rest_route( 999 )
		);

		$this->assertEquals(
			'autoshare/v1/post-autoshare-for-twitter-meta/9999',
			post_autoshare_for_twitter_meta_rest_route( '9999' )
		);
	}

	/**
	 * Tests the update_post_autoshare_for_twitter_meta_permission_check function.
	 *
	 * @since 1.0.0
	 */
	public function test_update_post_autoshare_for_twitter_meta_permission_check() {
		wp_set_current_user( $this->factory->user->create() );
		$this->assertFalse( update_post_autoshare_for_twitter_meta_permission_check( $this->get_valid_request() ) );

		$user = $this->factory->user->create(
			[
				'role' => 'administrator',
			]
		);

		wp_set_current_user( $user );
		$request = $this->get_valid_request();
		$this->assertTrue( update_post_autoshare_for_twitter_meta_permission_check( $request ) );
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
				'enabled'  => true,
				'message'  => 'Autoshare enabled.',
				'override' => true,
			],
			$response->get_data()
		);

	}
}
