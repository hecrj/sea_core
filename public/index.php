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
// Autoload class
require(DIR . 'lib/Autoload.php');
// Component class
require(DIR . 'lib/Component.php');

// If database has to be active
if(DB_ACTIVE)
{
	// ActiveRecord library
	require(DIR . 'vendor/php-activerecord/ActiveRecord.php');
	// Model class
	require(DIR . 'lib/Model.php');

	// ActiveRecord configuration
	ActiveRecord\Config::initialize(
		function($cfg)
		{
			// Require database configuration
			require(DIR . 'config/database.php');
			
			// Set path to models directory
			$cfg->set_model_directory(DIR_MODELS);
			
			// Define connections as protocol url
			foreach($db['connections'] as $connection => $options)
				$connections[$connection] = $options['type'] . '://' . $options['user'] . ':' . $options['password'] . '@' . $options['server'] . '/' . $options['name'] .'?charset=utf8';
			
			// Set connections
			$cfg->set_connections($connections);
			
			// Set default connection
			$cfg->set_default_connection($db['default']);
		}
	);
}

// Start magic!
FrontController::init();

?>