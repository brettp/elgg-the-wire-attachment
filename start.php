<?php
/**
 * The Wire Attachment.
 *
 * Attach files to the wire!
 */

elgg_register_event_handler('init', 'system', 'the_wire_attachment');

/**
 * Inits the plugin
 *
 * @return void
 */
function the_wire_attachment() {
	$plugin_root = dirname(__FILE__);
	elgg_register_library('the_wire_attachment', "$plugin_root/lib/the_wire_attachment.php");
	
	// we're prepending the form view. Note the -1 priority.
	elgg_extend_view('forms/thewire/add', 'the_wire_attachment/form_extension', -1);

	elgg_extend_view('object/thewire', 'the_wire_attachment/attachment');
	elgg_extend_view('js/elgg', 'js/the_wire_attachment');

	elgg_register_event_handler('create', 'object', 'the_wire_attachment_check_attachments');
	elgg_register_event_handler('delete', 'object', 'the_wire_attachment_delete_attached_files');

	// downloads are served through pages instead of actions so the download link can be shared.
	// action tokens prevent sharing action links.
	// this means we need to implement our own security in the page handler using gatekeeper().
	elgg_register_page_handler('the_wire_attachment', 'the_wire_attachment_page_handler');
}

/**
 * Check for attachments when wire posts are created.
 *
 * @param type $event
 * @param type $type
 * @param type $object
 * @return type mixed
 */
function the_wire_attachment_check_attachments($event, $type, $object) {
	if (!elgg_instanceof($object, 'object', 'thewire')) {
		return null;
	}

	$file = elgg_extract('the_wire_attachment_file', $_FILES, null);

	if ($file) {
		$file_obj = new TheWireAttachment();

		$file_obj->setFilename('the_wire_attachment/' . rand());
		$file_obj->setMimeType($file['type']);
		$file_obj->original_filename = $file['name'];
		$file_obj->simpletype = file_get_simple_type($file['type']);

		$file_obj->open("write");
		$file_obj->write(get_uploaded_file('the_wire_attachment_file'));
		$file_obj->close();

		if ($file_obj->save()) {
			$file_obj->addRelationship($object->getGuid(), 'is_attachment');
		} else {
			register_error(elgg_echo('the_wire_attachment:could_not_save_attachment'));
		}
	}

	return null;
}

/**
 * The wire attachment page handler
 *
 * Supports:
 *	Download an attachment: the_wire_attachment/download/<guid>/<title>
 *
 * @param array $page From the page_handler function
 * @return bool
 */
function the_wire_attachment_page_handler($page) {
	gatekeeper();
	$pages = dirname(__FILE__) . '/pages/the_wire_attachment';
	$section = elgg_extract(0, $page);

	switch($section) {
		case 'download':
			$guid = elgg_extract(1, $page);
			set_input('guid', $guid);
			require "$pages/download.php";
			break;

		default:
			// in the future we'll be able to register this as a 404
			// for now, act like an action and forward away.
			register_error(elgg_echo('the_wire_attachment:invalid_section'));
			forward(REFERRER);
	}
}

/**
 * Deletes any attachments when wire posts are deleted.
 *
 * @param type $event
 * @param type $type
 * @param type $object
 * @return null
 */
function the_wire_attachment_delete_attached_files($event, $type, $object) {

	if (!elgg_instanceof($object, 'object', 'thewire')) {
		return null;
	}
	
	// we want to use the the_wire_attachment_get_attachments() function,
	// so load the library.
	elgg_load_library('the_wire_attachment');

	$attachment = the_wire_attachment_get_attachments($object->getGUID());

	if ($attachment && !$attachment->delete()) {
		register_error(elgg_echo('the_wire_attachment:could_not_delete'));
	}
}