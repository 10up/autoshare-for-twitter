<?php
/**
 * Tests rest.php
 *
 * @since 1.0.0
 * @package TenUp\AutoTweet
 */

namespace TenUp\AutoTweet\Tests;

use WP_REST_Request;
use WP_UnitTestCase;
use function TenUp\AutoTweet\REST\{
	post_autotweet_meta_rest_route,
	update_post_autotweet_meta_permission_check,
	update_post_autotweet_meta
};
use const TenUp\AutoTweet\Core\Post_Meta\ENABLE_AUTOTWEET_KEY;
use const TenUp\AutoTweet\Core\Post_Meta\TWEET_BODY_KEY;

/**
 * TestRest class.
 *
 * @sincd 1.0.0
 */
class TestRest extends WP_UnitTestCase {
	/**
	 * Provides a valid request for testing the autotweet endpoint.
	 *
	 * @param int $post Post ID.
	 * @return WP_REST_Requst $requst;
	 */
	private function get_valid_request( $post = null ) {
		if ( empty( $post ) ) {
			$post = $this->factory->post->create();
		}

		$request = WP_REST_Request::from_url( rest_url( post_autotweet_meta_rest_route( $post ) ) );
		$request->set_method( 'POST' );
		$request->set_body_params(
			[
				ENABLE_AUTOTWEET_KEY => true,
				TWEET_BODY_KEY       => 'tweet override',
				'id'                 => $post,
			]
		);
		$request->set_attributes( [ 'id' => $post ] );

		return $request;
	}

	/**
	 * Tests the post_autotweet_meta_rest_route function.
	 *
	 * @since 1.0.0
	 */
	public function test_post_autotweet_meta_rest_route() {
		$this->assertEquals(
			'autotweet/v1/post-autotweet-meta/999',
			post_autotweet_meta_rest_route( 999 )
		);

		$this->assertEquals(
			'autotweet/v1/post-autotweet-meta/9999',
			post_autotweet_meta_rest_route( '9999' )
		);
	}

	/**
	 * Tests the update_post_autotweet_meta_permission_check function.
	 *
	 * @since 1.0.0
	 */
	public function test_update_post_autotweet_meta_permission_check() {
		wp_set_current_user( $this->factory->user->create() );
		$this->assertFalse( update_post_autotweet_meta_permission_check( $this->get_valid_request() ) );

		$user = $this->factory->user->create(
			[
				'role' => 'administrator',
			]
		);

		wp_set_current_user( $user );
		$request = $this->get_valid_request();
		$this->assertTrue( update_post_autotweet_meta_permission_check( $request ) );
	}

	/**
	 * Tests the update_post_autotweet_meta function.
	 *
	 * @since 1.0.0
	 */
	public function test_update_post_autotweet_meta() {
		$response = update_post_autotweet_meta( $this->get_valid_request() );
		$this->assertEquals(
			[
				'enabled'  => true,
				'message'  => 'Autotweet disabled.',
				'override' => true,
			],
			$response->get_data()
		);

	}
}
