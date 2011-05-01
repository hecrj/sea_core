<?php

class Router implements Component
{	
	static $controller;
	static $controller_name;
	static $action;
	static $arguments;
	static $hostname;
	static $subdomain;
	static $route;
	static $routes = array();
	
	public static function init()
	{
		// Require routes to match
		require(DIR_CONFIG . 'routes.php');
		
		// Analyze server host and path info
		self::analyze($_SERVER['HTTP_HOST'], $_SERVER['PATH_INFO']);
		
		// Set controller name
		self::$controller_name = ucwords(self::$controller) . 'Controller';
		
		// Execute after filter function
		$afterFilter();
	}
	
	# Analyze route and extract request info
	private static function analyze($host, $route){
		// Explode host
		$host_parts = explode('.', $host);
		
		// If host has 3 or more parts has a subdomain
		if(count($host_parts) > 2)
		{
			// Set subdomain
			self::$subdomain = $host_parts[0];
			
			// Set hostname
			self::$hostname  = $host_parts[1];
		}
		else
			// Set hostname
			self::$hostname  = $host_parts[0];
		
		// Get pagination page
		if(strpos($route, '/page-') !== FALSE)
		{
			// Split route and set pagination page to use in Request component
			list(/* EMPTY */, $route, $_GET['page']) = preg_split('/^(.*)page-([0-9]+)?$/', $route, 0, PREG_SPLIT_DELIM_CAPTURE);
		}
		
		// Set the route
		self::$route = $route . ((substr($route, -1) != '/') ? '/' : '');
			
		// Remove / at the beginning
		if(strpos($route, '/') === 0)
			$route = substr($route, 1);
		
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
		self::$arguments = array_pad((array)$route_parts, 5, 0);
	}
	
	# Search for route matches declared in config/routes.php
	private static function route_matches($route)
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
