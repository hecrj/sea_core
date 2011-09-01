<?php

namespace Core\Components\Router;

class MatchResolver extends ResolverAbstract
{	
	protected function resolve(Request $request, Array $rules)
	{	
		if(! is_array($rules['routes']))
			return false;
		
		return $this->setControllerDataIfMatch($request, $rules['routes']);
	}
	
	private function setControllerDataIfMatch($request, $routes)
	{
		$path = $request->getPath();
		
		foreach($routes as $routePreg => $routeControllerData)
		{
			if(preg_match('/' . $routePreg . '/', $path, $this->controllerArguments))
			{
				$controllerName = $routeControllerData[0];
				$controllerAction = $routeControllerData[1];
				
				$this->setControllerData($controllerName, $controllerAction);
				
				return true;
			}
		}

		return false;
	}
	
	private function setControllerData($controllerName, $controllerAction)
	{
		if(is_int($controllerName))
			$controllerName = $this->replaceIntegerByArgument($controllerName);
		
		if(is_int($controllerAction))
			$controllerAction = $this->replaceIntegerByArgument($controllerAction);
		
		// Throw away complete matched string
		array_shift($this->controllerArguments);
		
		$this->controllerName = $controllerName;
		$this->controllerAction = $controllerAction;
	}
	
	private function replaceIntegerByArgument($integer)
	{
		$value = $this->controllerArguments[$integer];
		unset($this->controllerArguments[$integer]);
		
		return $value;
	}
	
}
