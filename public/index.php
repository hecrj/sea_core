<?php

$time = microtime(true);

// Define absolute path to include files
define('DIR', dirname(__DIR__).'/');

// Boot configuration
require(DIR . 'config/boot.php');
// Constants needed first
require(DIR . 'lib/Constants.php');
// Core functions
require(DIR . 'lib/Functions.php');
// FrontController class
require(DIR . 'lib/FrontController.php');
// View class
require(DIR . 'lib/View.php');
// Controller class
require(DIR . 'lib/Controller.php');
// Autoload class
require(DIR . 'lib/Autoload.php');
// Component class
require(DIR . 'lib/Component.php');

// If database has to be active
if(DB_ACTIVE)
	// ActiveRecord initialize
	require(DIR . 'lib/ActiveRecord.php');

// Start magic!
FrontController::init();

?>