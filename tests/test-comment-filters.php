<?php
/**
 * Class CommentFiltersTest
 *
 * @package Self_Sustaining_Spam_Stopper
 */

/**
 * Add tests for various spam comment configurations.
 */
class CommentFiltersTest extends WP_UnitTestCase {

	/**
	 * Test the detection of comments that end in more than two URLs or
	 * exactly two URLs if they are the same.
	 */
	public function test_comment_text_ends_in_urls() {

		$comment_text = file_get_contents( __DIR__ . '/spam-comments/ends-with-double-link.txt' );

		$this->assertTrue( \SSSS\CommentFilters\ends_in_urls( $comment_text ) );
	}

	public function test_comment_text_matches_hash() {
		$comment_text = file_get_contents( ( __DIR__ . '/spam-comments/matches-hash.txt' ) );

		$this->assertTrue( \SSSS\Common\matches_comment_hash( $comment_text ) );
	}
}
