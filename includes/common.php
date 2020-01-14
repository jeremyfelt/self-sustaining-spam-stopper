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
		// Drugs, pharmaceutical
		'viagra',
		'cialis',
		'albendazole',
		'butalbital',

		// Crypto, not cryptography.
		'mining crypto',

		// Other forum patterns.
		'[url=', // WP doesn't expect this style.

		// Long misives on religion.
		'the false prophet',
		'number of the beast',
		'mark of the beast',

		// Maybe take this out if your site is about dating?
		'casual dating site',
		'adult dating site',
		'senior dating site',
		'adultdating',

		// Was I targetted by wig spam because I'm bald?
		'cheap wigs',

		// Of course...
		'hardcore galleries',
		'hardcore gallery',
		'hardcore photo',   // Catches plural as well.
		'hardcore picture', // Catches plural as well.
		'online sex',
		'want sex in',
		'want sex right',
		'for sex in',
		'for sex right',
		' sex online', // Add a space just in case someone is talking about Sussex?
		' sex toy',
		' sex shop',
		'sexy photo galleries',
		'sexy photo gallery',
		'sexy galleries',
		'sexy gallery',
		'sexy porn',
		'free porn',
		'porn clip',
		'porn stream',
		'porn tube',
		'porn torrent',
		'porn video',
		'porn picture',
		'porn galleries',
		'porn gallery',

		// Gambling!
		'online sports betting',
		'online casino',

		// Uninteresting.
		'bеst 100 freе',

		// Some URLs.
		'sellaccs.net',      // Selling "aged" Twitter accounts.
		'voda-da.by',        // A bunch of cryllic characters and URLs.
		'cravefreebies.com', // Free Stuff!
		'goo-gl.su',         // Imagine where that might go...
		'tiny.cc',
		'jackpotbetonline.com',
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
		'заработке',   // income
		'развлекушки', // fun

		// Russian forums?
		'.ru/member',
		'.ru/forum',
		'.ru/users',
		'.ru/blog.php',
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
