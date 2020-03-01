<?php
/**
 * Plugin Name:     Self-Sustaining Spam Stopper
 * Plugin URI:      https://github.com/jeremyfelt/self-sustaining-spam-stopper/
 * Description:     Stop spam without relying on an external service.
 * Author:          Jeremy Felt
 * Author URI:      https://jeremyfelt.com
 * Text Domain:     self-sustaining-spam-stopper
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Self_Sustaining_Spam_Stopper
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// This plugin, like WordPress, requires PHP 5.6 and higher.
if ( version_compare( PHP_VERSION, '5.6', '<' ) ) {
	add_action( 'admin_notices', 'ssss_admin_notice' );
	/**
	 * Display an admin notice if PHP is not 5.6.
	 */
	function ssss_admin_notice() {
		echo '<div class=\"error\"><p>';
		echo __( 'Self Sustaining Spam Stopper requires PHP 5.6 to function properly. Please upgrade PHP or deactivate the plugin.', 'self-sustaining-spam-stopper' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</p></div>';
	}

	return;
}

require_once __DIR__ . '/includes/common.php';
require_once __DIR__ . '/includes/comment-filters.php';
require_once __DIR__ . '/includes/contact-form-7-filters.php';
require_once __DIR__ . '/includes/data/drug-names.php';
