<?php

namespace Sea\Components\Routing;
use Sea\Components\Routing\Resolvers\ResolverCollectionInterface;
use Sea\Components\Routing\Routes\RouteCollectionInterface;
use Sea\Components\DynamicInjector;

class Router implements RouterInterface
{
	private $resolvers;
	private $routes = array();
	private $modules = array();
	
	public function __construct(ResolverCollectionInterface $resolvers)
	{
		$this->resolvers = $resolvers;
	}
	
	public function addRoutes($subdomain, RouteCollectionInterface $routes)
	{
		$this->routes[$subdomain] = $routes;

		$moduleName = $routes->getModuleName();
		
		if($moduleName !== null)
			$this->modules[$moduleName] = $subdomain;
		
		return $this;
	}

	public function setRoutes(Array $routes)
	{
		foreach($routes as $subdomain => $collection)
			$this->addRoutes($subdomain, $collection);
	}

	public function setRoutesFrom($routesPath, DynamicInjector $components)
	{
		if($components->has('auth'))
			$user = $components->get('auth')->getUser();

		$routes = require(\Sea\DIR . $routesPath);
		$this->setRoutes($routes);
	}
	
	private function getSubdomainRoutes($subdomain)
	{	
		if(! array_key_exists($subdomain, $this->routes))
			throw new \RuntimeException('Routes are not defined for
				subdomain: <strong>' . $subdomain . '</strong>', 404);
		
		return $this->routes[$subdomain];
	}

	public function getRoute($name, $moduleName = null)
	{
		$subdomain = $this->getModuleSubdomain($moduleName);
		$routes = $this->getSubdomainRoutes($subdomain);
		
		return $routes->getRoute($name);
	}

	public function getModuleSubdomain($moduleName)
	{
		if($moduleName === null)
			return 'www';
		
		if(! array_key_exists($moduleName, $this->modules))
			throw new \RuntimeException('The module: <strong>'. $module .'</strong> is not defined.');

		return $this->modules[$moduleName];
	}

	public function getContext(RequestInterface $request)
	{
		$routes = $this->getSubdomainRoutes($request->getSubdomain());
		
		return $this->resolvers->getContext($request, $routes);
	}

}
