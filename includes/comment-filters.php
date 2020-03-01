<?php
/**
 * Apply the filters used to determine if a comment
 * submission is spam.
 *
 * @package Self_Sustaining_Spam_Stopper
 */

namespace SSSS\CommentFilters;

add_filter( 'pre_comment_approved', __NAMESPACE__ . '\get_comment_status', 10, 2 );

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

	$user = wp_get_current_user();

	// Don't filter any comment left by a valid user.
	if ( $user->exists() ) {
		return $approved;
	}

	// A current pattern is spam comments that end in the pipe character. Likely because
	// some bot was written badly.
	if ( rtrim( trim( $commentdata['comment_content'] ), '|' ) !== $commentdata['comment_content'] ) {
		return 'spam';
	}

	$comment_content = implode( ' ', array(
		$commentdata['comment_author'],
		$commentdata['comment_author_email'],
		$commentdata['comment_author_url'],
		$commentdata['comment_content'],
	));
	$comment_content = mb_strtolower( $comment_content );

	// There are a few words that can always be considered spam.
	foreach ( \SSSS\Common\get_spam_word_list() as $word ) {

		// Anything containing a blocklisted word is marked as spam.
		if ( false !== strpos( $comment_content, $word ) ) {
			return 'spam';
		}
	}

	foreach ( \SSSS\Data\get_drug_names() as $word ) {

		// Anything containing a drug name is spam.
		if ( false !== strpos( $comment_content, $word ) ) {
			return 'spam';
		}
	}

	// The comment ends in multiple URLs.
	if ( ends_in_urls( $comment_content ) ) {
		return 'spam';
	}

	// The comment contains at least 6 bare URLs.
	if ( 6 <= \SSSS\Common\count_raw_urls( $comment_content ) ) {
		return 'spam';
	}

	// A hazard guess that most sites don't deal with doses.
	if ( \SSSS\Common\contains_mg( $comment_content ) ) {
		return 'spam';
	}

	// The comment is one that has been seen time and time again.
	if ( \SSSS\Common\matches_comment_hash( $commentdata['comment_content'] ) ) {
		return 'spam';
	}

	return $approved;
}

/**
 * Determine if comment content ends in more than 2 URLs or
 * if it ends in 2 URLs that are exactly the same.
 *
 * @param string $comment_content The comment content.
 * @return bool True if it ends in URLs. False if not.
 */
function ends_in_urls( $comment_content ) {

	// Only check the first 500 characters.
	$comment_content = substr( $comment_content, -500 );

	// Break apart content into an array on any whitespace.
	$contents = preg_split('/\s+/', $comment_content, -1, PREG_SPLIT_NO_EMPTY);

	$urls = 0;

	$last = false;
	$next = false;

	// Count how many lines at the end of content start with http.
	while ( $content = array_pop( $contents ) ) {
		if ( 0 === mb_strpos( $content, 'http' ) ) {
			$urls++;

			if ( false === $last ) {
				$last = $content;
			} else if ( false === $next ) {
				$next = $content;
			}

			continue;
		}

		break;
	}

	// If more than 2 URLs are used to end a comment, treat it as spam.
	if ( 2 < $urls ) {
		return true;
	}

	if ( 2 === $urls && $last === $next ) {
		return true;
	}

	return false;
}
