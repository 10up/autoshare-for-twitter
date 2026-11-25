<?php
/**
 * Tests the staging lock that stops a copied or moved site from tweeting.
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

/**
 * Staging lock tests.
 */
class StagingLockTest extends WP_UnitTestCase {

	/**
	 * Fake X API.
	 *
	 * @var Tweet_Spy
	 */
	private $spy;

	/**
	 * A published post set to autopost to one account.
	 *
	 * @var int
	 */
	private $post_id;

	/**
	 * The site URL home_url() returns.
	 *
	 * @var string
	 */
	private $home = 'https://example.org';

	/**
	 * Locks the plugin to the production URL and prepares a post to tweet.
	 */
	public function set_up() {
		parent::set_up();

		// wp-env defines WP_HOME, so the 'home' option can't move the site; filter the URL instead.
		add_filter(
			'home_url',
			function ( $url, $path ) {
				return $this->home . ( $path ? '/' . ltrim( $path, '/' ) : '' );
			},
			10,
			2
		);
		AST_Staging::set_autoshare_live_url_lock();

		connect_test_accounts();
		$this->spy     = new Tweet_Spy();
		$this->post_id = $this->factory->post->create( array( 'post_status' => 'publish' ) );
		update_autoshare_for_twitter_meta( $this->post_id, ENABLE_AUTOSHARE_FOR_TWITTER_KEY, 1 );
		update_autoshare_for_twitter_meta( $this->post_id, TWEET_ACCOUNTS_KEY, array( 'A1' ) );
	}

	/**
	 * The site the lock was taken on tweets.
	 */
	public function test_production_site_tweets() {
		$this->assertTrue( publish_tweet( $this->post_id ) );
		$this->assertCount( 1, $this->spy->sent );
	}

	/**
	 * Once the site's URL no longer matches the lock (a copy on staging, a
	 * moved site), nothing is sent to X.
	 */
	public function test_site_with_a_different_url_does_not_tweet() {
		$this->home = 'https://staging.example.org';

		$this->assertFalse( publish_tweet( $this->post_id ) );
		$this->assertCount( 0, $this->spy->sent, 'A staging copy must not tweet.' );
	}

	/**
	 * Moving between http and https is the same site.
	 */
	public function test_scheme_change_still_counts_as_production() {
		$this->home = 'http://example.org';

		$this->assertTrue( publish_tweet( $this->post_id ) );
		$this->assertCount( 1, $this->spy->sent );
	}

	/**
	 * A database search-and-replace of the production URL when copying the site
	 * must not rewrite the lock, or the copy would think it's production.
	 */
	public function test_lock_survives_search_and_replace_of_the_site_url() {
		$stored = get_option( 'autoshare_liveurl' );
		update_option( 'autoshare_liveurl', str_replace( 'example.org', 'staging.example.org', $stored ) );
		$this->home = 'https://staging.example.org';

		$this->assertFalse( publish_tweet( $this->post_id ) );
		$this->assertCount( 0, $this->spy->sent );
	}
}
