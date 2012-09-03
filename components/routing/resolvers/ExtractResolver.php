<?php

namespace Sea\Core\Components\Routing\Resolvers;
use Sea\Core\Components\Routing\RequestInterface;
use Sea\Core\Components\Routing\Routes\RouteCollectionInterface;

class ExtractResolver extends ResolverAbstract
{	
	public function getContext(RequestInterface $request, RouteCollectionInterface $routes)
	{	
		$path = $request->getPath();

		if($path == '/index')
			$request->redirectTo('/');

		$arguments = explode('/', substr($path, 1));

		$controller = array_shift($arguments);

		$controllerName = $controller ? ucfirst($controller) : 'Index';
		
		$actionName = array_shift($arguments);

		if($actionName == 'index')
			$request->redirectTo("/$controller");
		
		if(empty($actionName))
			$actionName = 'index';

		$contextClass = $this->contextClass;
		return new $contextClass($controllerName, $actionName, $arguments, $routes->getModuleName());
	}
}
