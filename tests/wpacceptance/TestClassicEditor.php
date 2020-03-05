<?php
/**
 * Example test class
 *
 * @package wpacceptance
 */

/**
 * PHPUnit test class
 */
class ExampleTest extends \TestCaseBase {

	/**
	 * Example test
	 */
	public function test_save_post_with_autoshare_off() {
		global $wp_version;

		if ( 5 <= intval( $wp_version ) ) {
			return;
		}

		$actor = $this->openBrowserPage();
		$actor->loginAs( 'admin' );

		$actor->moveTo( 'wp-admin/post-new.php' );
	}
}
