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
		'drug prices',
		'buy pharmacy',
		'online pharmacy',

		// Vaping!
		'e-liquid',
		'eliquid',
		'cbdoil',

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
		'dating for sex',
		'sex dating',

		// Was I targetted by wig spam because I'm bald?
		'cheap wigs',

		'parbriz', // I don't get it, but there's a lot of "windscreen" spam.

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
		'hot photo galleries',
		'hot photo gallery',
		'hot galleries',
		'hot gallery',
		'sexy photo galleries',
		'sexy photo gallery',
		'sexy picture',
		'sexy galleries',
		'sexy gallery',
		'sexy porn',
		'free porn',
		'porn clip',
		'porn stream',
		'porn tube',
		'porn torrent',
		'porn mpegs',
		'porn video',
		'porn xvideo',
		'porn picture',
		'porn pics',
		'porn galleries',
		'porn gallery',
		'porn website',
		'russian escort',
		'russianescort',

		// Gambling!
		'online sports betting',
		'online casino',
		'yesbet88',
		'88bet',

		// Uninteresting.
		'bеst 100 freе',
		'affiliatelab',
		'affiliate marketing',

		// Air quality.
		'iqair',

		// Some email address partials.
		'davidduke', // No time for that.

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
		'allvapebrands.com',
		'allvapestores.com',
		'supremesearch.net',
		'cbdlifemag.com',
		'biblefreedom.com',
		'creativebeartech.com',
		'roulettekr.com',
		'plugmycode.com',
		'newproxylists.com',
		'4gproxies.net',
		'agahidan.ir',
		'networkcity.info',
		'dreamproxies.com',
		'proxyti.com',
		'studybay.com',
		'edvesting.com',
		'gotwebsite1.com',

		// Don't hide your links.
		'bit.ly',
		'ow.ly',
		'tinyurl.com',

		// Where do they come up with these?
		'hairstylesvip',
		'hairstyleslook',
		'hairstylescool',

		'SaveTheOA', // This was a heavy spam campaign for a while.

		// S E Oh no.
		'check here for the best seo services',
		'seowebsitetrafficnet',
		'seo-services',

		// Some spam is self identifying.
		'you have a spam problem',

		// Russian
		'казино',      // casino
		'займ',        // loan
		'порно',       // porn
		'проститутки', // prostitute
		'деньги',      // money
		'заработке',   // income
		'развлекушки', // fun
		'молодая',     // young
		'медицинские', // medical
		'водопады',    // don't go chasing decorative waterfalls.
		'удобрения',   // fertilizers

		// Russian forums?
		'.ru/member',
		'.ru/forum',
		'.ru/user',
		'.ru/cms',
		'.ru/blog.php',
	);
}

/**
 * Provide a list of MD5 hashes for previously flagged spam content.
 *
 * @return array A list of hashes.
 */
function get_spam_hash_list() {
	return array(
		'1060217a23ba44f83a41d409688af505', // hermit crab
		'fed35afde5eccc1ed028a6583f34cb2c', // is anyone else having problems with the pictures
	);
}

/**
 * Determine if content contains a word or string classifed as
 * spammy.
 *
 * @param string $content Content to check.
 * @return bool True if spam. False if not.
 */
function contains_spam_word( $content ) {

	// Remove confusing unicode characters.
	$content = transliterate_content( $content );

	// There are a few words that can always be considered spam.
	foreach ( get_spam_word_list() as $word ) {

		// Anything containing a blacklisted word is marked as spam.
		if ( false !== mb_strpos( $content, $word ) ) {
			return true;
		}
	}

	return false;
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
 * Determine if the comment text, when stripped of whitespace, matches
 * a previously recorded MD5 hash of a spam comment.
 *
 * @param string $content The comment text.
 * @return bool True if spammy. False if not.
 */
function matches_comment_hash( $content ) {
	$content = preg_replace( '/\s+/', '', $content );
	$content = md5( $content );

	if ( in_array( $content, get_spam_hash_list(), true ) ) {
		return true;
	}

	return false;
}

/**
 * Replace confusing unicode characters with their less confusing or
 * more expected equivalents.
 *
 * @param string $content The content to transliterate.
 * @return string The transliterated content.
 */
function transliterate_content( $content ) {
    $transliteration_data = array(
		'о' => 'o', // 1086
		'е' => 'e', // 1077
		'х' => 'x', // 1093
	);

    return str_replace(
		array_keys( $transliteration_data ),
		array_values( $transliteration_data ),
		$content
	);
}

/**
 * Count the number of bare URLs used in content with no markup surrounding
 * them to indicate that context is given.
 *
 * @param string $comment_content The comment content.
 * @return int The number of raw URLs, with no markup.
 */
function count_raw_urls( $comment_content ) {

	// Break apart content into an array on any whitespace.
	$contents = preg_split('/\s+/', $comment_content, -1, PREG_SPLIT_NO_EMPTY);

	$urls = 0;

	// Count how many lines at the end of content start with http.
	while ( $content = array_pop( $contents ) ) {
		if ( 0 === mb_strpos( $content, 'http' ) ) {
			$urls++;
		}
	}

	return $urls;
}
