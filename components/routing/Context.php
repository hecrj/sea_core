<?php

namespace Core\Components\Routing;

class Context implements ContextInterface
{
	private $moduleName;
	private $controllerName;
	private $actionName;
	private $arguments;
	private $routeName;

	public function __construct($controllerName, $actionName, Array $arguments, $moduleName = null, $routeName = null)
	{
		$this->moduleName = $moduleName;
		$this->controllerName = $controllerName;
		$this->actionName = $actionName;
		$this->arguments = $arguments;
		$this->routeName = $routeName;
	}

	public function getModuleName()
	{
		return $this->moduleName;
	}

	public function getControllerName()
	{
		return $this->controllerName;
	}

	public function getActionName()
	{
		return $this->actionName;
	}

	public function getArguments($merge = array())
	{
		$arguments = $this->arguments;

		foreach($merge as $key => $value)
			$arguments[$key] = $value;
			
		return $arguments;
	}

	public function getRouteName()
	{
		return $this->routeName;
	}
}