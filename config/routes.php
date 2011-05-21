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
	* // Set the default controller
	* 'controller' => 'index',
	*
	* // You can set a static controller
	* // This means that the controller won't change. The first part of the route will be the action
	* // If you use this configuration, for example:
	*
	* // 'static_controller' => 'index',
	*
	* // And you access to: 'http://(www.)example.com/about'
	* // Router component will redirect that to: 'IndexController->about()'
	*
	* // Set route match redirection, use regular expressions in array keys
	* 'match' => array(
	*
	*	// Redirect: 'http://(www.)example.com/signup' to 'UsersController->add()'
	*	'^signup$' => array('users', 'add'),
	*
	*	// Content between parantheses is caught and passed to the controller action as parameter.
	*	// For example:
	*
	*	// Redirect: 'http://(www.)example.com/read/(:title)' to 'NewsController->read(:title)'
	*	'^read\/([a-z-_]+)$' => array('news', 'read')
	*
	*	// You can set caught parameters as controller or action defining the number of the paramter you want to use.
	*	// For example:
	*
	*	// Redirect: 'http://(www.)example.com/(:post_title)/(:action)' to 'PostsController->:action(:post_title)'
	*	'^([a-z-_]+)/([a-z]+)$' => array('posts', 2) // --> Parameter 2 (:action) will be the action
	*
	*	)
	*
	* )
	*/
	'www' => array(

	'controller' => 'index' // Default controller --> IndexController

	)

);

?>
