<?php

namespace Core\Components;

class Router
{
	private $request;
	private $routes_file;
	private $controller_class_name;
	private $controller_name;
	private $controller_action;
	private $controller_arguments;
	
	public function __construct(Request $request, $routes_file = 'routes')
	{
		$this->request     = $request;
		$this->routes_file = DIR . 'config/' . $routes_file . '.php';
		
		$this->processRoutes();
	}
	
	public function getControllerClassName($namespace = 'App\\Controllers\\')
	{
		if(isset($this->controller_class_name))
			return $this->controller_class_name;
		
		// Return controller class name
		return $this->controller_class_name = $namespace . ucwords($this->controller_name) . 'Controller';;
	}
	
	# Analyze route and extract request info
	private function processRoutes()
	{
		if(! is_file($this->routes_file))
			throw new \RuntimeException('Routes file not found in: <strong>'. $this->routes_file .'</strong>', 404);
		
		// Require routes to match
		require($this->routes_file);
		
		// Get route
		$route = $this->request->getRoute();
			
		// Remove / at the beginning
		if(strpos($route, '/') === 0)
			$route = substr($route, 1);
		
		// Get subdomain
		$subdomain = $this->request->getSubdomain();
		
		// Get subdomain info
		$sub_info = $routes[$subdomain];
		
		// Exception if subdomain info is not defined
		if(! isset($sub_info))
			throw new \RuntimeException('Enrouting information is not defined for subdomain: <strong>'. $subdomain .'</strong>', 404);
		
		// If isset subdomain matches
		if(isset($sub_info['match']))
			// If one route has matched...
			if($this->checkMatches($route, $sub_info['match']))
				// Return true and stop analyzing
				return true;
		
		// Explode /  route
		$route_parts = explode('/', $route);
		
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
					if(in_array($controller, $sub_info['exclude']))
						throw new RuntimeException('The <strong>'. $controller .'</strong> controller is an excluded controller for this
						subdomain.<br />Check your <strong>'. $this->routes_file .'.php</strong> configuration file.', 404);
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
		$this->controller_name      = $controller;
		$this->controller_action    = $action;
		$this->controller_arguments = $route_parts;
	}
	
	public function getRequest()
	{
		return $this->request;
	}
	
	public function getRoutesFile()
	{
		return $this->routes_file;
	}
	
	public function getControllerName()
	{
		return $this->controller_name;
	}
	
	public function getControllerAction()
	{
		return $this->controller_action;
	}
	
	public function getControllerArguments()
	{
		return $this->controller_arguments;
	}
	
	# Search for route matches declared in config/routes.php
	private function checkMatches($route, $match)
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
					$this->controller_name = $matches[$route_data[0]];
					
					// Unset the controller match
					unset($matches[$route_data[0]]);
				}
				// If route controller is not an integer
				else
					// Set controller as route controller
					$this->controller_name = $route_data[0];
				
				// If route action is integer
				if(is_int($route_data[1]))
				{
					// Select match by integer and set action
					$this->controller_action = $matches[$route_data[1]];
					
					// Unset the action match
					unset($matches[$route_data[1]]);
				}
				
				// If route action is not an integer
				else
					// Set action as route action
					$this->controller_action		=	$route_data[1];
				
				// Throw first match (complete string)
				array_shift($matches);
					
				// Set matches caught as arguments
				$this->controller_arguments = $matches;
				
				// Return true and stop searching
				return true;
			}
		}
		
		// No matches found
		return false;
	}

}

?>
