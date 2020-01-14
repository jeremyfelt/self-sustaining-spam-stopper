<?php
/**
 * Provide common functionality shared by other pieces
 * of the plugin.
 *
 * @package Self_Sustaining_Spam_Stopper
 */

namespace SSSS\Common;

/**
 * Provide a list of words included in content that can immediately be considered spam.
 *
 * @return array A list of spam words.
 */
function get_spam_word_list() {
	return array(
		'viagra',
		'cialis',
		'albendazole',
		'mining crypto',
		'[url=', // Nobody is really trying to use this when commenting.
		'dating site', // I mean, maybe, but very unlikely to be real.
		'the false prophet',   // Long missives on religion
		'number of the beast', // See above.
		'mark of the beast',   // See above.
		'cheap wigs',

		// Some URLs.
		'sellaccs.net', // Selling "aged" Twitter accounts.
		'voda-da.by', // A bunch of cryllic characters and URLs.
		'cravefreebies.com', // Free Stuff!

		// Where do they come up with these?
		'hairstylesvip',
		'hairstyleslook',
		'hairstylescool',

		'SaveTheOA', // This was a heavy spam campaign for a while.

		// S E Oh no.
		'check here for the best seo services',
		'seowebsitetrafficnet'
	);
}

/**
 * Determine if text contains "mg" with or without a space in front
 * of it and with digits immediately in front of it.
 *
 * @param string $text The comment text.
 * @return bool True if "mg" exists. False if not.
 */
function contains_mg( $text ) {
	if ( 1 === preg_match( '/(\d+\s?mg)/', $text ) ) {
		return true;
	}

	return false;
}

/**
 * Provide a list of words to look for in combination with "sex"
 * to determine if content is spam.
 *
 * @return array A list of words.
 */
function get_combo_word_list() {
	return array(
		'girl',
		'woman',
		'women',
		'online',
		'local',
		'city',
		'shop',
		'toy',
	);
}

/**
 * Determine if a combination of words with "sex" is present in
 * a block of text.
 *
 * @param string $text The comment text.
 * @return bool True if present. False if not.
 */
function contains_sex_combo( $text ) {
	if ( false === strpos( $text, 'sex' ) ) {
		return false;
	}

	foreach ( get_combo_word_list() as $word ) {
		if ( false !== strpos( $text, $word ) ) {
			return true;
		}
	}

	return false;
}
