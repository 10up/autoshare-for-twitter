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
	 * Provides a random post title for testing.
	 *
	 * @param number $chars The length of the string to return.
	 * @return string
	 */
	public function get_random_post_title( $chars = null ) {
		if ( is_null( $chars ) ) {
			$chars = 25;
		}

		$permitted_chars = str_repeat( ' 012 345 678 9ab cde fgh ijk lmn opq rst uvw xyz ', 10 );

		return substr( str_shuffle( $permitted_chars ), 0, $chars );
	}
}
