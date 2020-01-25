<?php
/**
 * Class ContactForm7FiltersTest
 *
 * @package Self_Sustaining_Spam_Stopper
 */

/**
 * Add tests for various spam comment configurations.
 */
class ContactForm7FiltersTest extends WP_UnitTestCase {

	/**
	 * Test the detection of comments that end in more than two URLs or
	 * exactly two URLs if they are the same.
	 */
	public function test_comment_form_submission_contains_spam_word() {

		$comment_text = file_get_contents( __DIR__ . '/spam-form-submissions/for-sex-in.txt' );

		$this->assertTrue( \SSSS\Common\contains_spam_word( $comment_text ) );
	}
}
