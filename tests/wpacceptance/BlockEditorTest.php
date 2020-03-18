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
class BlockEditorTest extends \TestCaseBase {

	/**
	 * Tests that new post is not tweeted when box is unchecked.
	 */
	public function test_save_new_post_with_autoshare_off() {
		$we = $this->openBrowserPage();
		$we->login();

		$we->moveTo( 'wp-admin/post-new.php' );

		$we->typeInField( '#post-title-0', 'Test' );
		$we->waitUntilElementVisible( '[aria-disabled="false"].editor-post-publish-panel__toggle' );
		$we->click( '.editor-post-publish-panel__toggle' );

		usleep( 100000 );

		// Pre-publish.
		$we->waitUntilElementVisible( '[aria-disabled="false"].editor-post-publish-button' );
		$we->click( '.editor-post-publish-button' );

		// Post-publish.
		$we->waitUntilElementVisible( '.autoshare-for-twitter-post-status' );
		$we->seeText( 'This post was not tweeted.', '.autoshare-for-twitter-post-status' );
	}

	/**
	 * Tests that new post is tweeted when box is checked.
	 */
	public function test_save_new_post_with_autoshare_on() {
		$we = $this->openBrowserPage();
		$we->login();

		$we->moveTo( 'wp-admin/post-new.php' );

		$we->typeInField( '#post-title-0', $this->get_random_post_title() );
		$we->waitUntilElementEnabled( '[aria-disabled="false"].editor-post-publish-panel__toggle' );
		$we->click( '.editor-post-publish-panel__toggle' );

		usleep( 100000 );

		$drawer = $we->getElementContaining( 'Autoshare:' )[0];
		$we->click( $drawer );

		$we->waitUntilElementVisible( '.autoshare-for-twitter-prepublish__checkbox-label' );
		$we->click( '.autoshare-for-twitter-prepublish__checkbox-label' );

		usleep( 100000 );

		$we->waitUntilElementVisible( '[aria-disabled="true"].editor-post-publish-button' );
		$we->waitUntilElementVisible( '[aria-disabled="false"].editor-post-publish-button' );

		$we->click( '.editor-post-publish-button' );

		// Post-publish.
		$we->waitUntilElementVisible( '.autoshare-for-twitter-post-status' );
		$we->dontSeeText( 'This post was not tweeted', '.autoshare-for-twitter-post-status' );
		$we->seeText( 'Tweeted on', '.autoshare-for-twitter-post-status' );
	}
}
