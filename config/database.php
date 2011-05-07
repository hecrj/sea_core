<?php

# Database configuration
$db = array(
	
	// Connections to database
	'connections'	=>	array(
		
		// Development connection
		'development'	=>	array(
			// Database type
			'type'		=>	'mysql',
			// Server of your database
			'server'	=>	'localhost',
			// Name of your database
			'name'		=>	'hectamvc',
			// User of your database
			'user'		=>	'root',
			// User password
			'password'	=>	''
		)
		
	),
	
	// Default connection
	'default'	=>	'development'
	
);

?>
