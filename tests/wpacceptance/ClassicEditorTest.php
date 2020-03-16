<?php
/**
 * Tests autoshare in the classic editor.
 *
 * @package TenUp\AutoshareForTwitter
 */

/**
 * ClassicEditorTest
 *
 * @package TenUp\AutoshareForTwitter
 */
class ClassicEditorTest extends \TestCaseBase {

	/**
	 * Tests that new post is not tweeted when box is unchecked.
	 */
	public function test_save_new_post_with_autoshare_off() {
		$we = $this->openBrowserPage();
		$we->loginAs( 'editor' ); // User with Classic Editor enabled.

		$we->moveTo( 'wp-admin/post-new.php' );
		$we->seeText( 'Tweet this post' );
		$we->fillField( '#title', 'Test Title' );
		$we->click( '#publish' );

		// Pageload 2.
		$we->waitUntilElementVisible( '#wpadminbar' );
		$we->seeText( 'This post was not tweeted', '#autoshare_for_twitter_metabox' );
	}

	/**
	 * Tests that new post is tweeted when box is checked.
	 */
	public function test_save_new_post_with_autoshare_on() {
		$we = $this->openBrowserPage();
		$we->loginAs( 'editor' ); // User with Classic Editor enabled.

		$we->moveTo( 'wp-admin/post-new.php' );
		$we->seeText( 'Tweet this post' );
		$we->fillField( '#title', $this->get_random_post_title() );
		$we->click( '#autoshare-for-twitter-enable' );
		$we->click( '#publish' );

		// Pageload 2.
		$we->waitUntilElementVisible( '#wpadminbar' );
		$we->seeText( 'Tweeted on', '#autoshare_for_twitter_metabox' );
	}
}
