<?php

namespace Sea\Core\Components\Routing;

class Context implements ContextInterface
{
	private $moduleName;
	private $controller;
	private $controllerName;
	private $actionName;
	private $arguments;
	private $routeName;

	public function __construct($controllerName, $actionName, Array $arguments, $moduleName = null, $routeName = null)
	{
		$this->moduleName = $moduleName;
		$this->controller = null;
		$this->controllerName = $controllerName;
		$this->actionName = $actionName;
		$this->arguments = $arguments;
		$this->routeName = $routeName;
	}

	public function getModuleName()
	{
		return $this->moduleName;
	}

	public function getController()
	{
		if($this->controller === null)
			$this->controller = $this->createController();

		return $this->controller;
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

	private function createController()
	{
		$controllerClass = 'Sea\\App\\Controllers\\';

		if(null !== $this->moduleName)
			$controllerClass .= ucfirst($this->moduleName) .'\\';
		
		$controllerClass .= $this->controllerName .'Controller';

		return new $controllerClass($this->controllerName, $this->moduleName);
	}
}