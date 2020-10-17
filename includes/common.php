<?php

namespace SSSS\Common;

/**
 * Return the message expected in the comment validator input box.
 */
function get_valid_message() {
	return 'Hey. Ignore me while I try to mess with bots. Thanks for commenting!';
}

/**
 * Return the markup used to represent the hidden inputs that act as honey pots.
 *
 * @return string HTML.
 */
function get_input_markup() {
	ob_start();
	?>
	<input name="extremely_important" type="text" style="display:none;" value="<?php echo esc_attr( get_valid_message() ); ?>" />
	<input id="extremely-empty" name="extremely_empty" type="text" style="display: none;" value="This should arrive empty to get a perfect score!" />
	<script type="text/Javascript">
		{
			// Clear the input that we expect to be empty when the comment is submitted, which
			// I hope takes actual people longer than 1.5 seconds. If not, then uh... think
			// before you type just a tiny bit more?
			setTimeout( function() {
				document.getElementById( 'extremely-empty' ).setAttribute( 'value', '' );
			}, 1500 );
		}
	</script>
	<?php
	$markup = ob_get_contents();
	ob_end_clean();

	return $markup;
}
