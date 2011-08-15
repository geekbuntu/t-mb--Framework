<?php
// Start sessions
session_start();
// Turn on error reporting
ini_set('display_errors', true);
ini_set('error_reporting', E_ALL);
// Define our base path
if (!defined('BASEPATH')) {
    define('BASEPATH', dirname(__FILE__));
}
// Define our application path
if (!defined('APPLICATIONPATH')) {
    define('APPLICATIONPATH', dirname(__FILE__).'/../application');
}

/**
 * This function is responsible for
 * seamlessly loading our classes
 * into memory for use
 *
 * @param string $sClass
 * @return boolean
 */
function classLoader($sClass) {
	// Correct the class name, replace
	// spaces with / to indicate subdirectories
	$sClass = str_replace('_','/', $sClass);
	// Set the full file path
	$sFile  = APPLICATIONPATH."/library/{$sClass}.php";
	// Double check that the
	// file actually exists
	if (file_exists($sFile)) {
		// If so, load it
		require_once($sFile);
		// All is well,
		// return true
		return true;
	}
	// Check for controllers to load
	$sFile = APPLICATIONPATH."/controllers/{$sClass}.php";
	// Double check that the
	// file actually exists
	if (file_exists($sFile)) {
		// If so, load it
		require_once($sFile);
		// All is well,
		// return true
		return true;
	}
	// Check for Models to load
	$sFile = APPLICATIONPATH."/models/{$sClass}.php";
	// Double check that the
	// file actually exists
	if (file_exists($sFile)) {
		// If so, load it
		require_once($sFile);
		// All is well,
		// return true
		return true;
	}
	// There was an error,
	// return false
	return false;
}
function redirect($sUri) {
	// Do the redirect
	return Html::getInstance()->generateScript('text/javascript', null, "self.location='{$sUri}';")->getHtml(true);
}
// Register our autoloader
spl_autoload_register('classLoader');
// If you do not wish to use the singleton
// pattern, which is used throughout the
// system, simply uncomment the following
// line and comment out the line below it
// $oFw = new Framework();
$oFw = Framework::getInstance();
    // Check to see if we
    // need to force SSL
    if (($oFw->loadConfigVar('systemSettings', 'forceSsl') == true) && ($_SERVER['SERVER_PORT'] != 443)) {
        // Do the redirect
		redirect("https://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}");
    }
    // Execute the route
    $oFw->executeRoute();
    // Log the visit
    $oFw->logVisit();
