<?php

namespace Sea\Core\Components\Routing;
use Sea\Core\Components\Routing\Routes\RouteCollectionInterface;

interface RouterInterface
{
	public function addRoutes($subdomain, RouteCollectionInterface $routes);
	public function setRoutes(Array $routes);
	public function getContext(RequestInterface $request);
}
