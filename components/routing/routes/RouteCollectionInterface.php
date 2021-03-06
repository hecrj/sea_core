<?php

namespace Sea\Components\Routing\Routes;

interface RouteCollectionInterface
{
	public function __construct($module = null);
	public function getModuleName();
	public function match($name, $pattern, $controller);
	public function hasRoute($name);
	public function getRoute($name);
	public function getRoutes();
	public function isEmpty();
}
