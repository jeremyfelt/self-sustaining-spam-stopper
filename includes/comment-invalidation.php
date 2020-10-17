<?php

namespace SSSS\CommentInvalidation;

add_action( 'comment_form_top', __NAMESPACE__ . '\add_comment_fields' );
add_filter( 'pre_comment_approved', __NAMESPACE__ . '\get_comment_status', 15, 2 );
add_action( 'comment_post', __NAMESPACE__ . '\log_invalid_reasons', 15, 2 );
add_filter( 'manage_edit-comments_columns', __NAMESPACE__ . '\add_list_table_columns' );
add_action( 'manage_comments_custom_column', __NAMESPACE__ . '\populate_list_table_columns', 10, 2 );


/**
 * Add comment fields in an attempt to mess with bot traffic.
 */
function add_comment_fields() {
	echo \SSSS\Common\get_input_markup();
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
	if ( ! isset( $_POST['comment'] ) ) {
		return $approved;
	}

	if ( ! isset( $_POST['extremely_empty'] ) || ! isset( $_POST['extremely_important'] ) ) {
		return 'spam';
	}

	if ( \SSSS\Common\get_valid_message() !== $_POST['extremely_important'] ) {
		return 'spam';
	}

	if ( '' !== $_POST['extremely_empty'] ) {
		return 'spam';
	}

	return $approved;
}

/**
 * Capture information about invalid commments to determine how the bot
 * reacted to our tricks.
 *
 * @param int $comment_id  The ID of the comment.
 * @param string $approved Whether it was marked as spam before.
 */
function log_invalid_reasons( $comment_id, $approved ) {
	if ( 'spam' !== $approved ) {
		return;
	}

	if ( ! isset( $_POST['extremely_empty'] ) || ! isset( $_POST['extremely_important'] ) ) {
		update_comment_meta( $comment_id, '_ssss_missing_fields', 1 );
	}

	if ( isset( $_POST['extremely_important'] ) && \SSSS\Common\get_valid_message() !== $_POST['extremely_important'] ) {
		update_comment_meta( $comment_id, '_ssss_extremely_important_value', sanitize_text_field( $_POST['extremely_important'] ) );
	}

	if ( isset( $_POST['extremely_empty'] ) && '' !== $_POST['extremely_empty'] ) {
		update_comment_meta( $comment_id, '_ssss_extremely_empty_value', sanitize_text_field( $_POST['extremely_empty'] ) );
	}
}

/**
 * Add the contents of the empty and important inputs to the comment list table.
 *
 * @param array $columns The list of columns being output.
 * @return array The modified list of columns.
 */
function add_list_table_columns( $columns ) {
	$columns['ssss_missing']   = 'Missing Inputs';
	$columns['ssss_empty']     = 'Empty Input';
	$columns['ssss_important'] = 'Important Input';

	return $columns;
}

/**
 * Display the contents of the empty and important inputs in the comments list table.
 *
 * @param string $column     The column being output.
 * @param int    $comment_id The comment ID.
 */
function populate_list_table_columns( $column, $comment_id ) {
	if ( 'ssss_missing' === $column ) {
		$missing = get_comment_meta( $comment_id, '_ssss_missing_fields', true );
		echo absint( $missing );
	}

	if ( 'ssss_empty' === $column ) {
		$empty_value = get_comment_meta( $comment_id, '_ssss_extremely_empty_value', true );
		echo esc_html( $empty_value );
	}

	if ( 'ssss_important' === $column ) {
		$important_value = get_comment_meta( $comment_id, '_ssss_extremely_important_value', true );
		echo esc_html( $important_value );
	}
}
