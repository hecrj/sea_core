<?php

namespace Core\Components\Router;

abstract class Analyzer
{
	protected $controllerName;
	protected $controllerAction;
	protected $controllerArguments;
	
	public function __construct(){}
	
	abstract public function analyze(Route $route, Array $rules);
	
	final public function getControllerData()
	{
		return array($this->controllerName, $this->controllerAction, $this->controllerArguments);
	}
}

?>
