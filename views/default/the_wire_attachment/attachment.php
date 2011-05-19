<?php
/**
 * Views attachments for a wire post
 */

// we want to use the the_wire_attachment_get_attachments() function, so load the library.
elgg_load_library('the_wire_attachment');

$post = elgg_extract('entity', $vars);
if (!elgg_instanceof($post, 'object', 'thewire')) {
	return true;
}

$attachment = the_wire_attachment_get_attachments($post->getGUID());

if ($attachment) {
	$text = elgg_view_icon('clip') . $attachment->original_filename;
	echo elgg_view('output/url', array(
		'href' => 'the_wire_attachment/download/' . $attachment->getGUID() . '/' . $attachment->original_filename,
		'text' => $text,
		'class' => 'mll mbl'
	));
}