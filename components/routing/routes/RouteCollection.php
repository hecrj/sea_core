<?php

namespace Sea\Core\Components\Routing\Routes;

class RouteCollection implements RouteCollectionInterface
{
	private $moduleName;
	private $routes = array();
	
	public function __construct($moduleName = null)
	{
		$this->moduleName = $moduleName;
	}
	
	public function getModuleName()
	{
		return $this->moduleName;
	}
	
	public function match($name, $pattern = '/', $controller)
	{
		$route = new Route($this->moduleName .'/'. $name, $pattern, $controller);
		$this->routes[$name] = $route;
		
		return $route;
	}
	
	public function hasRoute($name)
	{
		return array_key_exists($name, $this->routes);
	}
	
	public function getRoute($name)
	{
		if(! $this->hasRoute($name))
			throw new \RuntimeException('The route: '. $name .' is not declared.');

		return $this->routes[$name];
	}
	
	public function getRoutes()
	{
		return $this->routes;
	}
	
	public function isEmpty()
	{
		return empty($this->routes);
	}
}
