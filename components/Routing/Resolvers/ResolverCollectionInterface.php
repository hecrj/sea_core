<?php

namespace Core\Components\Routing\Resolvers;
use Core\Components\Routing\RequestInterface;
use Core\Components\Routing\Routes\RouteCollectionInterface;

interface ResolverCollectionInterface
{
	public function addResolver(ResolverAbstract $resolver);
	public function getContext(RequestInterface $request, RouteCollectionInterface $routes);
}