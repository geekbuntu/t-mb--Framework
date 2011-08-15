<?php
class IndexController {
	public function init(Framework $oFw) {
		// Set the title
		$oFw->getPage()->setTitle('Welcome');
		// Return instance
		return $this;
	}
}