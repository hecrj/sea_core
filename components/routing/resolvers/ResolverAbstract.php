<?php

namespace Sea\Components\Routing\Resolvers;
use Sea\Components\Routing\RequestInterface;
use Sea\Components\Routing\Routes\RouteCollectionInterface;

abstract class ResolverAbstract
{
	protected $contextClass = 'Sea\\Components\\Routing\\Context';
	
	abstract public function getContext(RequestInterface $request, RouteCollectionInterface $routes);	
}
