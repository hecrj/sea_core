<?php

namespace Core\Components\Routing;
use Core\Components\Routing\Routes\RouteCollectionInterface;

interface RouterInterface
{
	public function addRoutes($subdomain, RouteCollectionInterface $routes);
	public function loadRoutes($routesPath);
	public function getContext(RequestInterface $request);
}
