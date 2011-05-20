<?php

# Routes File
## Insert here your Regular Expressions to match routes
## Priority = First declared > Second declared ...

self::$routes = array(
	
	/**
	 * Here you can configure the enrouting internal redirection.
	 * You can use different configurations for every subdomain.
	 * The default subdomain is 'www', but you can add more at your will.
	 * 
	 * See the above example to learn how it works:
	 *
	 * EXAMPLE
	 * 
	 * // Configuration for 'www' default domain
	 * 'www' => array(
	 * 
	 *		// Set the default controller
	 * 		'default'	=>	'index',
	 *
	 *		// Set route match redirection, use regular expressions in array keys
	 *		'match'		=>	array(
	 *
	 *			// Redirect: 'http://(www.)example.com/signup' to 'UsersController->add()'
	 *			'^signup$'	=>	array('users', 'add'),
	 *			
	 *			// Content between parantheses is caught and passed to the controller action as parameter.
	 *			// For example:
	 *
	 *			// Redirect: 'http://(www.)example.com/read/(:title)' to 'NewsController->read(:title)'
	 *			'^read\/([a-z-_]+)$'	=>	array('news', 'read')
	 * 
	 *		)
	 *
	 * )
	 */
	'www' => array(
		
		'default'	=>	'index' // Default controller --> IndexController
		
	)

);

?>
