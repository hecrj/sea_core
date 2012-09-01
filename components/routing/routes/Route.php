<?php

namespace Sea\Core\Components\Routing\Routes;

class Route implements RouteInterface
{
	private $name;
	private $pattern;
	private $controller;
	private $defaults = array('page' => 1);
	private $requirements = array('page' => '\d+');
	
	public function __construct($name, $pattern, $controller)
	{
		$this->name = $name;
		$this->pattern = $pattern;
		$this->controller = $controller;
	}

	public function getName()
	{
		return $this->name;
	}
	
	public function getPattern() {
		return $this->pattern;
	}

	public function getController()
	{
		return $this->controller;
	}
	
	public function defaults(Array $defaults) {
		$this->defaults[] = $defaults;
		
		return $this;
	}

	public function hasDefault($name)
	{
		return array_key_exists($name, $this->defaults);
	}

	public function getDefault($name) {
		return $this->defaults[$name];
	}
	
	public function constraints(Array $constraints) {
		$this->requirements[] = $constraints;
		
		return $this;
	}

	public function hasConstraint($name)
	{
		return array_key_exists($name, $this->requirements);
	}

	public function getConstraint($name) {
		return $this->requirements[$name];
	}
}
