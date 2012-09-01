<?php

namespace Sea\Core\Components\Routing\Routes;

class RouteCompiler implements RouteCompilerInterface
{
	private $compiled = array();

	public function __construct()
	{
		
	}
	
	public function compile(RouteInterface $route)
	{
		$name = $route->getName();

		if(! $this->isCached($name))
			$this->compiled[$name] = $this->doCompile($route);

		return $this->compiled[$name];
	}

	private function isCached($name)
	{
		return array_key_exists($name, $this->compiled);
	}

	private function doCompile($route)
	{
		$pattern = $route->getPattern();

		if($pattern[0] == '/')
			$pattern = substr($pattern, 1);

		$parts = explode('/', $pattern);

		$compiled = new CompiledRoute;

		foreach($parts as $part)
		{
			if($this->isArgument($part))
				$this->addArgument($route, $part, $compiled);
			else
				$this->addText($part, $compiled);
		}

		return $compiled;
	}

	private function addText($part, $compiled)
	{
		$compiled->addText($part);

		$regexp = '\/' . preg_quote($part, '/');
		$compiled->addRegexp($regexp);
	}

	private function isArgument($part)
	{
		return (!empty($part) and $part[0] == ':');
	}

	private function addArgument($route, $part, $compiled)
	{
		$variable = substr($part, 1);
		$compiled->addArgument($variable);

		if($route->hasConstraint($variable))
			$regexp = '(?P<'. $variable .'>'. $route->getConstraint($variable) .')';
		else
			$regexp = '(?P<'. $variable .'>[a-z0-9-]+)';

		if($route->hasDefault($variable))
		{
			$regexp = '(?:\/'. $regexp .'?)?';
			$compiled->setArgument($variable, $route->getDefault($variable));
		}
		else
			$regexp = '\/'. $regexp;
		
		$compiled->addRegexp($regexp);
	}
}
