<?php

class Router implements Component
{	
	private static $controller;
	private static $action;
	private static $arguments;
	private static $hostname;
	private static $subdomain = 'www';
	private static $route;
	private static $routes = array();
	
	public static function init()
	{
		// Require routes to match
		require(DIR_CONFIG . 'routes.php');
		
		self::$routes = $routes;
	}
	
	public static function getControllerFor($host, $route)
	{
		// Analyze server host and path info
		self::analyze($host, $route);
		
		// Set controller name
		$controller_name = ucwords(self::$controller) . 'Controller';
		
		// Setting controller path
		$controller_file	=	DIR_CONTROLLERS . $controller_name . '.php';
	
		// Exception if controller file doesn't exist
		ExceptionUnless(is_file($controller_file), 'Controller: <strong>'. $controller_file .'</strong> not found!');

		// Require the controller
		require_once($controller_file);
		
		// Return controller object
		return new $controller_name;
	}
	
	public static function getController()
	{
		return self::$controller;
	}
	
	public static function getAction()
	{
		return self::$action;
	}
	
	public static function getArguments()
	{
		return self::$arguments;
	}
	
	public static function getRoute()
	{
		return self::$route;
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
		
		// If route doesn't have / at the end
		if(substr($route, -1) != '/')
			self::$route = $route . '/';
		else
		{
			self::$route = $route;
			$route = substr($route, 0, strlen($route) - 1);
		}
			
		// Remove / at the beginning
		if(strpos($route, '/') === 0)
			$route = substr($route, 1);
		
		// Get subdomain info
		$sub_info = self::$routes[self::$subdomain];
		
		// Exception if subdomain info is not defined
		ExceptionUnless(isset($sub_info), 'Enrouting information is not defined for subdomain: <strong>'. self::$subdomain .'</strong>');
		
		// If isset subdomain matches
		if(isset($sub_info['match']))
			// If one route has matched...
			if(self::route_matches($route, $sub_info['match']))
				// Return true and stop analyzing
				return true;
		
		// Explode /  route
		$route_parts = explode('/', $route, 8);
		
		// If has an static controller
		if(isset($sub_info['static_controller']))
			// Set controller as an static controller
			$controller = $sub_info['static_controller'];
		
		// If subdomain does not have an static controller
		else
		{
			// Get controller from the route
			$controller = array_shift($route_parts);
			
			// If controller isn't defined
			if(empty($controller))
				$controller = $sub_info['controller'];

			// If controller is defined
			else
			{
				// Controller name string to lower
				$controller = strtolower($controller);
				
				// If subdomain has info about excluded controllers
				if(isset($sub_info['exclude']))
					// Exception if the current controller is an excluded controller
					ExceptionIf(in_array($controller, $sub_info['exclude']), 'The <strong>'. $controller .'</strong> controller is an excluded controller for this subdomain.<br />Check your <strong>routes.php</strong> configuration file.');
			}
		}
				
		// Get action from the route
		$action = array_shift($route_parts);
			
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
	private static function route_matches($route, $match)
	{	
		// For each route match
		foreach($match as $preg => $route_data)
		{
			// If regular expression matches
			if(preg_match('/'.$preg.'/', $route, $matches))
			{	
				// If route controller is integer
				if(is_int($route_data[0]))
				{
					// Select match by integer and set controller
					self::$controller = $matches[$route_data[0]];
					
					// Unset the controller match
					unset($matches[$route_data[0]]);
				}
				// If route controller is not an integer
				else
					// Set controller as route controller
					self::$controller = $route_data[0];
				
				// If route action is integer
				if(is_int($route_data[1]))
				{
					// Select match by integer and set action
					self::$action = $matches[$route_data[1]];
					
					// Unset the action match
					unset($matches[$route_data[1]]);
				}
				
				// If route action is not an integer
				else
					// Set action as route action
					self::$action		=	$route_data[1];
				
				// Throw first match (complete string)
				array_shift($matches);
					
				// Set matches caught as arguments
				self::$arguments = $matches;
				
				// Return true and stop searching
				return true;
			}
		}
		
		// No matches found
		return false;
	}

}

?>
