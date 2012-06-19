<?php

namespace Core\Components\Routing\Resolvers;
use Core\Components\Routing\RequestInterface;
use Core\Components\Routing\Routes\RouteCollectionInterface;

abstract class ResolverAbstract
{
	protected $contextClass = 'Core\\Components\\Routing\\Context';
	
	abstract public function getContext(RequestInterface $request, RouteCollectionInterface $routes);	
}
