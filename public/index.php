<?php

// Define absolute path to include files
define('DIR', dirname(__DIR__).'/');

// Website configuration
require(DIR . 'config/website.php'); // <-- Revision pending
// Constants needed first
require(DIR . 'lib/Constants.php');
// Core functions
require(DIR . 'lib/Functions.php');
// Router class
require(DIR . 'lib/Router.php');
// FrontController class
require(DIR . 'lib/FrontController.php');
// View class
require(DIR . 'lib/View.php');
// Controller class
require(DIR . 'lib/Controller.php');
// Component class
require(DIR . 'lib/Component.php');

// Start magic!
FrontController::init();

?>