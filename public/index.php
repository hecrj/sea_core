<?php

// Define absolute path to include files
define('DIR', dirname(__DIR__).'/');

// FrontController class
require(DIR . 'lib/FrontController.php');

// Start magic!
FrontController::init();

?>