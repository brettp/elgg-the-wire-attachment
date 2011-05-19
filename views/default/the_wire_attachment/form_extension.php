<?php
/**
 * Adds a file input to the wire post form.
 */

$input = elgg_view('input/file', array(
	'name' => 'the_wire_attachment_file',
	'class' => 'elgg-the-wire-attachment'
));

echo $input;