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

	/**
	 * When a post is saved as a draft with "Tweet this post" checked, then published
	 * with "Tweet this post" unchecked, it should not be tweeted.
	 *
	 * @see https://github.com/10up/autoshare-for-twitter/issues/80
	 */
	public function test_save_draft_with_autoshare_on_then_publish_with_autoshare_off() {
		$we = $this->openBrowserPage();
		$we->loginAs( 'editor' ); // User with Classic Editor enabled.

		$we->moveTo( 'wp-admin/post-new.php' );
		$we->seeText( 'Tweet this post' );
		$we->fillField( '#title', 'New draft post' );
		$we->click( '#autoshare-for-twitter-enable' ); // Check the box.
		$we->click( '#save-post' );

		// Pageload 2.
		$we->waitUntilElementVisible( '#wpadminbar' );
		$we->seeText( 'Tweet this post' );
		$we->click( '#autoshare-for-twitter-enable' ); // Uncheck the box.

		$we->waitUntilElementEnabled( '#publish' );
		$we->click( '#publish' );

		$we->waitUntilNavigation();

		// Pageload 3.
		$we->waitUntilElementVisible( '#wpadminbar' );
		$we->seeText( 'This post was not tweeted.', '#autoshare_for_twitter_metabox' );
	}

	/**
	 * When a post is saved as a draft with "Tweet this post" unchecked, then published
	 * with "Tweet this post" checked, it should be tweeted.
	 */
	public function test_save_draft_with_autoshare_off_then_publish_with_autoshare_on() {
		$we = $this->openBrowserPage();
		$we->loginAs( 'editor' ); // User with Classic Editor enabled.

		$we->moveTo( 'wp-admin/post-new.php' );
		$we->seeText( 'Tweet this post' );
		$we->fillField( '#title', $this->get_random_post_title() );
		$we->click( '#save-post' );

		// Pageload 2.
		$we->waitUntilElementVisible( '#wpadminbar' );
		$we->seeText( 'Tweet this post' );
		$we->click( '#autoshare-for-twitter-enable' ); // Check the box.

		$we->waitUntilElementEnabled( '#publish' );
		$we->click( '#publish' );

		$we->waitUntilNavigation();

		// Pageload 3.
		$we->waitUntilElementVisible( '#wpadminbar' );
		$we->seeText( 'Tweeted on', '#autoshare_for_twitter_metabox' );
	}
}
