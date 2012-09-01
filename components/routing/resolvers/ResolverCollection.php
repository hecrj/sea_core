<?php

namespace Sea\Core\Components\Routing\Resolvers;
use Sea\Core\Components\Routing\RequestInterface;
use Sea\Core\Components\Routing\Routes\RouteCollectionInterface;

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

		throw new \RuntimeException('Impossible to find a Context for the current Request.');
	}
}
