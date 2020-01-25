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
	 * Test for a case where confusing unicode characters look like a phrase, but
	 * do not match for the phrase unless transliterated.
	 */
	public function test_comment_form_submission_contains_confusing_unicode_in_spam_word() {

		$comment_text = file_get_contents( __DIR__ . '/spam-form-submissions/has-confusing-unicode-oex.txt' );

		$this->assertTrue( \SSSS\Common\contains_spam_word( $comment_text ) );
	}
}
