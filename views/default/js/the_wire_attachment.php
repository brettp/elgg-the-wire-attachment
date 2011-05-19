<?php
/**
 * JS to alter the encoding type for the wire post forms.
 */
?>

//<script>
elgg.provide('elgg.the_wire_attachment');

/**
 * Finds the wire post forms and changes the enc type
 */
elgg.the_wire_attachment.init = function() {
	$('input.elgg-the-wire-attachment').parents('form').attr('enctype', 'multipart/form-data');
}

elgg.register_hook_handler('init', 'system', elgg.the_wire_attachment.init);