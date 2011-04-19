<?php

class Router
{	
	static $controller;
	static $action;
	static $arguments;
	static $route;
	static $routes = array();
	
	# Analyze route and extract request info
	public static function analyze($route){
		// Require routes to match
		require(DIR_CONFIG . 'routes.php');
		
		// Set the route
		self::$route = $route;
			
		// Remove / at the beginning
		if(strpos($route, '/') === 0)
			$route = substr($route, 1);
		
		#list($route, $page) = preg_split('/\/pagina-([0-9]+)/', $route, 0, PREG_SPLIT_DELIM_CAPTURE);
		
		// If one route has matched...
		if(self::route_matches($route))
			// Return true and stop analyzing
			return true;
		
		// Explode /  route
		$route_parts = explode('/', $route, 8);
				
		// Controller is the first part of the route
		$controller = array_shift($route_parts);
				
		// Action is the second part of the route
		$action = array_shift($route_parts);
		
		// If controller isn't defined, it will be 'PagesController'
		if(empty($controller))
			$controller = 'pages';
		// If controller is defined, convert string to lower
		else
			$controller = strtolower($controller);

		// If action isn't defined, it will be 'index()'
		if(empty($action))
			$action = 'index';
		// If action is defined, convert string to lower
		else
			$action = strtolower($action);
		
		// Defining data
		self::$controller	=	$controller;
		self::$action 		=	$action;
								
		// The arguments must be 5 parameters minimum
		self::$arguments = array_pad((array)$arguments, 5, 0);
	}
	
	# Search for route matches declared in config/routes.php
	public function route_matches($route)
	{	
		// For each route match
		foreach(self::$routes as $preg => $destination)
		{
			// If regular expression matches
			if(preg_match('/'.$preg.'/', $route, $matches))
			{
				// Set the controller
				self::$controller	=	$destination[0];
				
				// Set the action
				self::$action		=	$destination[1];
				
				// Throw first match (complete string)
				array_shift($matches);
				
				// Set matches caught as arguments
				self::$arguments = $matches;
				
				// Return true and stop searching
				return true;
			}
		}
	}

}

?>