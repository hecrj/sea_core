<?php

namespace Sea\Core\Components\Routing\Resolvers;
use Sea\Core\Components\Routing\RequestInterface;
use Sea\Core\Components\Routing\Routes\RouteCollectionInterface;

class ExtractResolver extends ResolverAbstract
{	
	public function getContext(RequestInterface $request, RouteCollectionInterface $routes)
	{	
		$arguments = explode('/', substr($request->getPath(), 1));
		$controller = array_shift($arguments);

		$controllerName = $controller ? ucfirst($controller) : 'Index';
		$actionName = array_shift($arguments) ?: 'index';

		$contextClass = $this->contextClass;
		return new $contextClass($controllerName, $actionName, $arguments, $routes->getModuleName());
	}
}
