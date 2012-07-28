<?php

namespace Sea\Core\Components\Routing\Resolvers;
use Sea\Core\Components\Routing\RequestInterface;
use Sea\Core\Components\Routing\Routes\RouteCollectionInterface;

interface ResolverCollectionInterface
{
	public function addResolver(ResolverAbstract $resolver);
	public function getContext(RequestInterface $request, RouteCollectionInterface $routes);
}