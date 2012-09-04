<?php

namespace Sea\Components\Routing;
use Sea\Components\Routing\Routes\RouteCollectionInterface;

interface RouterInterface
{
	public function addRoutes($subdomain, RouteCollectionInterface $routes);
	public function setRoutes(Array $routes);
	public function getContext(RequestInterface $request);
}
