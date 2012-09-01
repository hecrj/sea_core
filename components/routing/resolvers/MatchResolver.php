<?php

namespace Sea\Core\Components\Routing\Resolvers;
use Sea\Core\Components\Routing\RequestInterface;
use Sea\Core\Components\Routing\Routes\RouteCompilerInterface;
use Sea\Core\Components\Routing\Routes\RouteCollectionInterface;

class MatchResolver extends ResolverAbstract
{
	private $compiler;
	
	public function __construct(RouteCompilerInterface $compiler)
	{
		$this->compiler = $compiler;
	}
	
	public function getContext(RequestInterface $request, RouteCollectionInterface $routes)
	{	
		if($routes->isEmpty())
			return null;
		
		$path = $request->getPath();

		foreach($routes->getRoutes() as $routeName => $route)
		{
			$compiled = $this->compiler->compile($route);

			if(! preg_match($compiled->getRegexp(), $path, $matches))
				continue;
			
			list($controllerName, $actionName) = explode('#', $route->getController(), 2);
			$arguments = $this->mergeArguments($matches, $compiled);

			$contextClass = $this->contextClass;
			return new $contextClass($controllerName, $actionName, $arguments, $routes->getModuleName(), $routeName);
		}
		
		return null;
	}
	
	private function mergeArguments($matches, $compiled)
	{
		$arguments = $compiled->getArguments();

		foreach($matches as $key => $value)
		{
			if(! is_int($key))
				$arguments[$key] = $value;
		}

		return $arguments;
	}
	
}
