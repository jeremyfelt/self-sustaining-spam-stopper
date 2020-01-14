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
		'butalbital',
		'mining crypto',
		'[url=', // Nobody is really trying to use this when commenting.
		'dating site', // I mean, maybe, but very unlikely to be real.
		'the false prophet',   // Long missives on religion
		'number of the beast', // See above.
		'mark of the beast',   // See above.
		'cheap wigs',
		'hardcore galleries',
		'hardcore photos',
		'sexy photo galleries',
		'sexy porn',
		'free porn',
		'porn clips',
		'porn stream',
		'porn tube',
		'porn torrent',
		'porn pictures',
		'porn galleries',
		'online sports betting',
		'online casino',

		// Some URLs.
		'sellaccs.net', // Selling "aged" Twitter accounts.
		'voda-da.by', // A bunch of cryllic characters and URLs.
		'cravefreebies.com', // Free Stuff!
		'goo-gl.su', // Imagine where that might go...
		'tiny.cc',
		'jackpotbetonline.com',

		'.ru/member', // Russian forums?
		'.ru/forum',
		'.ru/users',
		'.ru/blog.php',

		'doodlekit.com',
		'mystrikingly.com',
		'dvddiscountshop.com',
		'vip-voyeur.com',

		// Where do they come up with these?
		'hairstylesvip',
		'hairstyleslook',
		'hairstylescool',

		'SaveTheOA', // This was a heavy spam campaign for a while.

		// S E Oh no.
		'check here for the best seo services',
		'seowebsitetrafficnet',

		// Russian
		'казино',      // casino
		'займ',        // loan
		'порно',       // porn
		'проститутки', // prostitute
		'деньги',      // money
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
