<?php

namespace Core\Components\Routing\Resolvers;
use Core\Components\Routing\RequestInterface;
use Core\Components\Routing\Routes\RouteCollectionInterface;

abstract class ResolverAbstract
{
	private $contextClass = 'Core\\Context';
	
	abstract public function getContext(RequestInterface $request, RouteCollectionInterface $routes);	
}
