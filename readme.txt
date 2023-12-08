=== Self-Sustaining Spam Stopper ===
Contributors: jeremyfelt
Tags: comments, spam
Requires at least: 6.3
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 2.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Stop spam without relying on an external service.

## Description

This plugin attempts to stop spam from the standard WordPress comment field and custom Contact 7 form submissions.

It does not send any data to an external service to determine if submitted content is spam. No cookies or other personal user information are captured or stored.

## Installation

### Comments

In most cases, no configuration should be necessary when using this plugin to protect against comment spam. Most WordPress themes will use WordPress core to output comment fields and most of the time the proper hook (`comment_form_top`) will be fired so that the fields from this plugin are added.

### Contact Form 7

To use this plugin with Contact Form 7, add `[ssss ssss]` to any contact form to add spam checking by this plugin to the form.

### Caveats

* JavaScript is required for this to work.

## Changelog

### 2.0.0

* Change WordPress requirement to 6.3.
* Change PHP requirement to 7.4 to match WordPress 6.3+.

### 1.1.0

* Start logging the elapsed time comment submissions.
* Start logging elapsed time for CF7 form submissions.
* Confirm WP 5.9 support.

### 1.0.1

* Housekeeping, cruft removal.
* Confirm WP 5.8 support.

### 1.0.0

* Initial plugin version.
