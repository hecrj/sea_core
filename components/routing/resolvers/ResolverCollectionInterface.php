<?php

namespace Sea\Components\Routing\Resolvers;
use Sea\Components\Routing\RequestInterface;
use Sea\Components\Routing\Routes\RouteCollectionInterface;

interface ResolverCollectionInterface
{
	public function addResolver(ResolverAbstract $resolver);
	public function getContext(RequestInterface $request, RouteCollectionInterface $routes);
}