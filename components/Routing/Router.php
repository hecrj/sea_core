<?php

namespace Core\Components\Routing;
use Core\Components\Routing\Resolvers\ResolverCollectionInterface;
use Core\Components\Routing\Routes\RouteCollectionInterface;

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
		if($module === null)
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

	public function getController(ContextInterface $context)
	{
		$controllerClass = 'App\\Controllers\\';

		$moduleName = $context->getModuleName();

		if(null !== $moduleName)
			$controllerClass .= $moduleName .'\\';
		
		$controllerName = $context->getControllerName();
		$controllerClass .= $controllerName .'Controller';

		return new $controllerClass($controllerName, $moduleName);
	}

}
