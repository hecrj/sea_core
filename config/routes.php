<?php

# Routes File
## Insert here your Regular Expressions to match routes
## Priority = First declared > Second declared ...

self::$routes = array(
	
	/**
	 * Use: 'REGULAR EXPRESSION'	=>	array('controller','action')
	 *
	 * EXAMPLE:
	 * Match index.html to PagesController->index()
	 *
	 * '^index\.html$'		=>	array('pages', 'index')
	 *
	 * The matches between parentheses will be caught and passed to the action as arguments
	 *
	 * EXAMPLE:
	 * To do something like this: UsersController->show($id)
	 * You can use:
	 *
	 * '^user-([0-9]+)\.html$'		=>	array('users','show')
	 *
	 */

);

$afterFilter = function()
{
	/**
	 * Function executed after match a route
	 *
	 * EXAMPLE:
	 * Redirect subdomains to different controller
	 * 
	 * if(strlen(Router::$subdomain) > 3)
	 * Router::$controller_name = 'Blog' . Router::$controller_name;
	 *
	 */
	
}

?>
