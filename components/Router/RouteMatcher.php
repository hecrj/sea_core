<?php

namespace Core\Components\Router;

class RouteMatcher extends Analyzer
{	
	public function analyze(Route $route)
	{
		$rules = $this->getRulesFor($route);
		
		if(! is_array($rules['routes']))
			return false;
		
		return $this->setControllerDataIfMatch($route, $rules['routes']);
	}
	
	private function setControllerDataIfMatch($route, $routes)
	{
		$routePath = $route->getPath();
		
		foreach($routes as $routePreg => $routeControllerData)
		{
			if(preg_match('/' . $routePreg . '/', $routePath, $pregMatches))
			{
				// Route controller data array does not have named keys
				// Readability is very important!
				$controllerData = array(
					'controllerName'	=>	$routeControllerData[0],
					'controllerAction'	=>	$routeControllerData[1]
				);
				
				$this->controllerArguments = $pregMatches;
				$this->setControllerData($controllerData);
				return true;
			}
		}

		return false;
	}
	
	private function setControllerData($controllerData)
	{	
		$controllerData = $this->replaceIntegersByArguments($controllerData);
		
		$this->controllerName = $controllerData['controllerName'];
		$this->controllerAction = $controllerData['controllerAction'];
		
		// Shift first controller argument (complete matched string)
		array_shift($this->controllerArguments);
	}
	
	private function replaceIntegersByArguments($controllerData)
	{
		foreach($controllerData as $key => $argNum)
		{
			if(is_int($argNum))
			{
				$controllerData[$key] = $this->controllerArguments[$argNum];
				unset($this->controllerArguments[$argNum]);
			}
		}
		
		return $controllerData;
	}
}
?>
