<?php
/**
 * The wire attachment object
 */
class TheWireAttachment extends ElggFile {

	/**
	 * Override the subtype
	 */
	public function initializeAttributes() {
		parent::initializeAttributes();

		$this->attributes['subtype'] = 'the_wire_attachment';
	}
}