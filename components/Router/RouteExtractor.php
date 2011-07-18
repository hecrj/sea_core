<?php

namespace Core\Components\Router;

class RouteExtractor extends Analyzer
{
	private $controllerNameDefault;
	private $controllerActionDefault = 'index';
	
	public function analyze(Route $route)
	{
		$rules = $this->getRulesFor($route);
		
		$this->setControllerArgumentsAsRouteParts($route);
		$this->extractControllerNameFromArgumentsUsing($rules);
		$this->extractFromArguments('controllerAction');
	}
	
	private function setControllerArgumentsAsRouteParts($route)
	{
		$this->controllerArguments = explode('/', $route->getPath());
	}
	
	private function extractControllerNameFromArgumentsUsing($rules)
	{
		if(isset($rules['controller']))
		{
			$this->controllerNameDefault = $rules['controller'];
			$this->extractFromArguments('controllerName');
		}
		elseif(isset($rules['static_controller']))
			$this->controllerName = $rules['static_controller'];
		
		else
			throw new \RuntimeException('Undefined default controller. Check
				your routing configuration.');
	}
	
	private function extractFromArguments($controllerAttr)
	{
		$argument = array_shift($this->controllerArguments);
		
		if(empty($argument))
		{
			$defaultProperty = $controllerAttr . 'Default';
			$argument = $this->$defaultProperty;
		}
		
		$this->$controllerAttr = $argument;
	}
	
}

?>
