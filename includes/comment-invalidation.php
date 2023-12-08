<?php
/**
 * Manage comment invalidation.
 *
 * @package self-sustaining-spam-stopper
 */

namespace SSSS\CommentInvalidation;

use SSSS\Common;

add_action( 'comment_form_top', __NAMESPACE__ . '\add_comment_fields' );
add_filter( 'pre_comment_approved', __NAMESPACE__ . '\get_comment_status', 15, 2 );
add_action( 'comment_post', __NAMESPACE__ . '\log_reasoning', 15 );
add_filter( 'manage_edit-comments_columns', __NAMESPACE__ . '\add_list_table_columns' );
add_action( 'manage_comments_custom_column', __NAMESPACE__ . '\populate_list_table_columns', 10, 2 );


/**
 * Add comment fields in an attempt to mess with bot traffic.
 */
function add_comment_fields() {
	echo \SSSS\Common\get_input_markup(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Determine if a comment submission is spam.
 *
 * @param int|string $approved    1 if approved. 0 if pending. spam if spam.
 * @param array      $commentdata {
 *     Comment data.
 *
 *     @type string $comment_author       The name of the comment author.
 *     @type string $comment_author_email The comment author email address.
 *     @type string $comment_author_url   The comment author URL.
 *     @type string $comment_content      The content of the comment.
 *     @type string $comment_date         The date the comment was submitted. Default is the current time.
 *     @type string $comment_date_gmt     The date the comment was submitted in the GMT timezone.
 *                                        Default is `$comment_date` in the GMT timezone.
 *     @type int    $comment_parent       The ID of this comment's parent, if any. Default 0.
 *     @type int    $comment_post_ID      The ID of the post that relates to the comment.
 *     @type int    $user_id              The ID of the user who submitted the comment. Default 0.
 *     @type int    $user_ID              Kept for backward-compatibility. Use `$user_id` instead.
 *     @type string $comment_agent        Comment author user agent. Default is the value of 'HTTP_USER_AGENT'
 *                                        in the `$_SERVER` superglobal sent in the original request.
 *     @type string $comment_author_IP    Comment author IP address in IPv4 format. Default is the value of
 *                                        'REMOTE_ADDR' in the `$_SERVER` superglobal sent in the original request.
 * }
 */
function get_comment_status( $approved, $commentdata ) {
	if ( 'spam' === $approved ) {
		return $approved;
	}

	if ( is_user_logged_in() ) {
		return $approved;
	}

	// Only check if this is a comment form submission.
	if ( ! isset( $_POST['comment'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return $approved;
	}

	// Immediately trash obvious bots.
	if ( in_array( get_spam_reason(), array( 'bot_missing_inputs', 'bot_confused_js', 'bot_mismatched_empty' ), true ) ) {
		return 'trash';
	}

	return $approved;
}

/**
 * Determine why a comment should be marked as spam.
 *
 * @return string The reason for marking the comment as spam.
 */
function get_spam_reason(): string {
	$extremely_empty     = isset( $_POST['extremely_empty'] ) ? $_POST['extremely_empty'] : false; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$extremely_important = isset( $_POST['extremely_important'] ) ? $_POST['extremely_important'] : false; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$form_loaded_time    = isset( $_POST['ssss_form_loaded'] ) ? $_POST['ssss_form_loaded'] : false; // phpcs:ignore WordPress.Security.NonceVerification.Missing

	// If no inputs are set, assume the comment was submitted by a bot.
	if ( false === $extremely_empty && false === $extremely_important && false === $form_loaded_time ) {
		return 'bot_missing_inputs';
	}

	/**
	 * If a comment is submitted from a browser that does not support JavaScript, then extremely_empty
	 * and extremely_important will be set to default values.
	 *
	 * If extremely_empty is still the default and extremely_important is not the default, then we can
	 * assume that the comment was submitted by a bot that supports JavaScript.
	 */
	if ( Common\get_empty_message() === $extremely_empty && Common\get_valid_message() !== $extremely_important ) {
		return 'bot_confused_js';
	}

	/**
	 * If extremely_empty is not the default nor empty, then it is not this plugin's JavaScript that
	 * has manipulated the value. Assume the comment was submitted by a bot.
	 */
	if ( Common\get_empty_message() !== $extremely_empty && '' !== $extremely_empty ) {
		return 'bot_mismatched_empty';
	}

	return '';
}

/**
 * Capture information about invalid commments to determine how the bot
 * reacted to our tricks.
 *
 * @param int $comment_id The ID of the comment.
 */
function log_reasoning( $comment_id ): void {
	// Only check if this is a comment form submission.
	if ( ! isset( $_POST['comment'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return;
	}

	$extremely_empty     = isset( $_POST['extremely_empty'] ) ? $_POST['extremely_empty'] : false; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$extremely_important = isset( $_POST['extremely_important'] ) ? $_POST['extremely_important'] : false; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$form_loaded_time    = isset( $_POST['ssss_form_loaded'] ) ? $_POST['ssss_form_loaded'] : false; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$reason              = get_spam_reason();

	update_comment_meta( $comment_id, '_ssss_extremely_important_value', sanitize_text_field( $extremely_important ) );
	update_comment_meta( $comment_id, '_ssss_extremely_empty_value', sanitize_text_field( $extremely_empty ) );
	update_comment_meta( $comment_id, '_ssss_form_loaded_value', sanitize_text_field( $form_loaded_time ) );
	update_comment_meta( $comment_id, '_ssss_spam_reason', sanitize_text_field( $reason ) );
}

/**
 * Add the contents of the empty and important inputs to the comment list table.
 *
 * @param array $columns The list of columns being output.
 * @return array The modified list of columns.
 */
function add_list_table_columns( $columns ) {
	$columns['ssss_reason']  = __( 'Reasoning', 'self-sustaining-spam-stopper' );
	$columns['ssss_elapsed'] = __( 'Time Elapsed', 'self-sustaining-spam-stopper' );

	return $columns;
}

/**
 * Display the contents of the empty and important inputs in the comments list table.
 *
 * @param string $column     The column being output.
 * @param int    $comment_id The comment ID.
 */
function populate_list_table_columns( $column, $comment_id ) {
	if ( 'ssss_reason' === $column ) {
		$reason = get_comment_meta( $comment_id, '_ssss_spam_reason', true );
		$reason = $reason ? $reason : 'none';

		echo esc_html( $reason );
	}

	if ( 'ssss_elapsed' === $column ) {
		$comment       = get_comment( $comment_id );
		$submited_time = strtotime( $comment->comment_date_gmt );
		$loaded_time   = get_comment_meta( $comment_id, '_ssss_form_loaded_value', true );

		if ( ! $loaded_time ) {
			$elapsed = get_comment_meta( $comment_id, '_ssss_form_time_elapsed', true );
			$elapsed = $elapsed ? $elapsed : 0;
		} else {
			$elapsed = $submited_time - $loaded_time;
		}

		echo esc_html( $elapsed ) . esc_html__( ' seconds', 'self-sustaining-spam-stopper' );
	}
}
