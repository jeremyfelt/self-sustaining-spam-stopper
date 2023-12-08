<?php
/**
 * Plugin Name:       Self-Sustaining Spam Stopper
 * Plugin URI:        https://github.com/jeremyfelt/self-sustaining-spam-stopper/
 * Description:       Stop spam without relying on an external service.
 * Author:            Jeremy Felt
 * Author URI:        https://jeremyfelt.com
 * Version:           2.0.0-beta
 * Requires at least: 6.3
 * Requires PHP:      7.4
 *
 * @package Self_Sustaining_Spam_Stopper
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once __DIR__ . '/includes/common.php';
require_once __DIR__ . '/includes/comment-invalidation.php';
require_once __DIR__ . '/includes/form-invalidation.php';
