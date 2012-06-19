<?php

namespace Core\Components\Routing\Resolvers;
use Core\Components\Routing\RequestInterface;
use Core\Components\Routing\Routes\RouteCollectionInterface;

class ResolverCollection implements ResolverCollectionInterface
{
	private $resolvers = array();
	
	public function __construct()
	{
		foreach(func_get_args() as $resolver)
			$this->addResolver($resolver);
	}
	
	public function addResolver(ResolverAbstract $resolver)
	{
		$this->resolvers[] = $resolver;
		
		return $this;
	}
	
	public function getContext(RequestInterface $request, RouteCollectionInterface $routes)
	{
		foreach($this->resolvers as $resolver)
		{
			if(null !== $context = $resolver->getContext($request, $routes))
				return $context;
		}

		return null;
	}

	private function createController($info, $module)
	{
		$controllerClass = 'App\\Controllers\\';
		
		if(!empty($module))
			$controllerClass .= ucfirst($module).'\\';
		
		$controllerClass .= $info['controller'] .'Controller';
		
		return new $controllerClass($info['controller'], $module);
	}
}
