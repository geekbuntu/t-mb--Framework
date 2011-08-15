<?php
class AjaxController {
	public function init(Framework $oFw) {
		// Make sure we have POST data and a methos
		if (empty($_POST) || empty($_POST['sMethod'])) {
			// Return failure
			echo(false);
		} else {
			// Determine what needs to be done
			switch ($_POST['sMethod']) {
				
			}
			// Return response
			echo(json_encode($aResponse));
		}
		// Kill the application
		exit;
	}
}