<?php

namespace Sea\Core\Components\Routing\Resolvers;
use Sea\Core\Components\Routing\RequestInterface;
use Sea\Core\Components\Routing\Routes\RouteCollectionInterface;

abstract class ResolverAbstract
{
	private $contextClass = 'Sea\\Core\\Routing\\Context';
	
	abstract public function getContext(RequestInterface $request, RouteCollectionInterface $routes);	
}
