<?php
/**
 * Common functions.
 *
 * @package self-sustaining-spam-stopper
 */

namespace SSSS\Common;

/**
 * Get the pre-filled value of the "important" input.
 *
 * @return string The message expected in the "important" input.
 */
function get_valid_message(): string {
	return __( 'Hey. Ignore me while I try to mess with bots. Thanks for commenting!', 'self-sustaining-spam-stopper' );
}

/**
 * Get the pre-filled value of the "empty" input.
 *
 * @return string The message expected in the "empty" input.
 */
function get_empty_message(): string {
	return __( 'This should arrive empty to get a perfect score!', 'self-sustaining-spam-stopper' );
}

/**
 * Return the markup used to represent the hidden inputs that act as honey pots.
 *
 * @return string HTML.
 */
function get_input_markup(): string {
	ob_start();
	?>
	<input name="ssss_form_loaded" type="text" style="display: none;" value="<?php echo esc_attr( time() ); ?>" />
	<input name="extremely_important" type="text" style="display:none;" value="<?php echo esc_attr( get_valid_message() ); ?>" />
	<input id="extremely-empty" name="extremely_empty" type="text" style="display: none;" value="<?php echo esc_attr( get_empty_message() ); ?>" />
	<script type="text/Javascript">
		{
			// Clear the input that we expect to be empty when the comment is submitted, which
			// I hope takes actual people longer than 500 milliseconds. If not, then uh... think
			// before you type just a tiny bit more?
			setTimeout( function() {
				document.getElementById( 'extremely-empty' ).setAttribute( 'value', '' );
			}, 500 );
		}
	</script>
	<?php
	$markup = ob_get_contents();
	ob_end_clean();

	return $markup;
}
