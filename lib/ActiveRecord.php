<?php

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

?>
