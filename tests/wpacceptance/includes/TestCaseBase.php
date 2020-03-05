<?php
/**
 * Test case class that provides us with some helper functionality.
 *
 * @package TenUp\AutoshareForTwitter
 */

/**
 * Class extends \WPAcceptance\PHPUnit\TestCase
 */
class TestCaseBase extends \WPAcceptance\PHPUnit\TestCase {
	/**
	 * Activate the plugin.
	 *
	 * @param \WPAcceptance\PHPUnit\Actor $actor The actor.
	 */
	protected function activatePlugin( $actor ) {
		// Activate the plugin.
		$actor->moveTo( '/wp-admin/plugins.php' );
		try {
			$element = $actor->getElement( '[data-slug="autoshare-for-twitter"] .deactivate a' );
			if ( $element ) {
				$actor->click( $element );
				$actor->waitUntilElementVisible( '#message' );
			}
		} catch ( \Exception $e ) { // phpcs:disable Generic.CodeAnalysis.EmptyStatement.DetectedCatch
			// Do nothing.
		}
		$actor->click( '[data-slug="autoshare-for-twitter"] .activate a' );
		$actor->waitUntilElementVisible( '#message' );
	}
}
