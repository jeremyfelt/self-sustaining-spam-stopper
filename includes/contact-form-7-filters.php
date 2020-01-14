<?php
/**
 * Apply the filters used to determine if a Contact Form 7
 * submission is spam.
 *
 * @package Self_Sustaining_Spam_Stopper
 */

namespace SSSS\ContactForm7Filters;

add_filter( 'wpcf7_spam', __NAMESPACE__ . '\check_form_submission', 10, 1 );

/**
 * Determine if a Contact Form 7 submission is spam.
 *
 * @param bool $spam True if spam. False if not.
 * @return bool True if spam. False if not.
 */
function check_form_submission( $spam ) {
	if ( $spam ) {
		return $spam;
	}

	$params = get_params();

	if ( ! $params ) {
		return false;
	}

	$form_content = mb_strtolower( $params['content'] );

	// There are a few words that can always be considered spam.
	foreach ( \SSSS\Common\get_spam_word_list() as $word ) {

		// Anything containing a blacklisted word is marked as spam.
		if ( false !== strpos( $form_content, $word ) ) {
			return true;
		}
	}

	// A hazard guess that most sites don't deal with doses.
	if ( \SSSS\Common\contains_mg( $form_content ) ) {
		return true;
	}

	if ( \SSSS\Common\contains_sex_combo( $form_content ) ) {
		return true;
	}

	return $spam;
}

/**
 * Retrieve the list of params that may have been passed to the Contact Form 7
 * plugin during submission.
 *
 * @return array A list of params.
 */
function get_params() {
	$params = array(
		'author'       => '',
		'author_email' => '',
		'author_url'   => '',
		'content'      => '',
	);

	foreach ( (array) $_POST as $key => $val ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( '_wpcf7' === substr( $key, 0, 6 ) || '_wpnonce' === $key ) {
			continue;
		}

		if ( is_array( $val ) ) {
			$val = implode( ', ', array_flatten( $val ) );
		}

		$val = trim( $val );

		if ( 0 === strlen( $val ) ) {
			continue;
		}

		$params['content'] .= "\n\n" . $val;
	}

	$params['content'] = trim( $params['content'] );

	return $params;
}

/**
 * Flatten an array into a string.
 *
 * This was copied from the GPL licensed Contact Form 7 plugin.
 *
 * @param array $input The array used by Contact Form 7.
 * @return string|array $output A string used by this plugin.
 */
function array_flatten( $input ) {
	if ( ! is_array( $input ) ) {
		return array( $input );
	}

	$output = array();

	foreach ( $input as $value ) {
		$output = array_merge( $output, array_flatten( $value ) );
	}

	return $output;
}
