<?php

namespace SSSS\FormInvalidation;

add_action( 'wpcf7_init', __NAMESPACE__ . '\add_ssss_tag', 10 );
add_filter( 'wpcf7_skip_spam_check', __NAMESPACE__ . '\maybe_check_form', 10, 2 );

/**
 * Add support for an [ssss] tag to be used in a form that this plugin
 * should attempt to protect.
 */
function add_ssss_tag() {
	wpcf7_add_form_tag(
		'ssss',
		__NAMESPACE__ . '\tag_handler',
		array(
			'name'         => 'ssss',
			'name-attr'    => true,
			'do-not-store' => true,
			'not-for-mail' => true,
		)
	);
}

/**
 * Provide the input markup to inject in place of the [ssss] tag in the
 * contact form.
 *
 * @param \WPCF7_FormTag $tag The current tag being processed.
 * @return string The HTML markup to display in place of the tag.
 */
function tag_handler( $tag ) {
	if ( 'ssss' !== $tag->type ) {
		return '';
	}

	return \SSSS\Common\get_input_markup();
}

/**
 * Determine if this form uses the custom [ssss] tag and if it should then
 * be processed for possible spam by this plugin.
 *
 * @param bool              $should_check Untouched.
 * @param \WPCF7_Submission $submission   The form submission.
 */
function maybe_check_form( $should_check, $submission ) {

	$form_tags = wp_list_pluck( $submission->get_contact_form()->scan_form_tags(), 'type' );

	if ( in_array( 'ssss', $form_tags, true ) ) {
		add_filter( 'wpcf7_spam', __NAMESPACE__ . '\validate_ssss_tag', 10, 2 );
	}

	return $should_check;
}

/**
 * Determine if a submitted message is likely to be spam based on the
 * contents of the empty and important tags.
 *
 * @param bool              $spam       Whether a spam submission has been detected.
 * @param \WPCF7_Submission $submission The form submission.
 * @return bool True if spam. False if not.
 */
function validate_ssss_tag( $spam, $submission ) {
	if ( $spam ) {
		return $spam;
	}

	if ( ! isset( $_POST['extremely_empty'] ) || ! isset( $_POST['extremely_important'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return true;
	}

	if ( \SSSS\Common\get_valid_message() !== $_POST['extremely_important'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return true;
	}

	if ( '' !== $_POST['extremely_empty'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return true;
	}

	if ( isset( $_POST['ssss_form_loaded'] ) && '' !== $_POST['ssss_form_loaded'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$time_difference = time() - (int) $_POST['ssss_form_loaded']; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		update_post_meta( $submission->get_contact_form()->id(), '_ssss_form_time_elapsed', sanitize_text_field( $time_difference ) );
	} elseif ( ! isset( $_POST['ssss_form_loaded'] ) || '' === $_POST['ssss_form_loaded'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return true;
	}

	return false;
}
