<?php

namespace Sea\Components\Routing\Routes;

interface RouteCompilerInterface
{
	/**
	 * Compiles a route. A compiled route is a redefined route for performance
	 * purposes.
	 * 
	 * @param Route $route The route to compile
	 * @return CompiledRoute The compiled route
	 */
	public function compile(RouteInterface $route);
}
