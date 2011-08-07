<?php

namespace Core\Components\Router;

class RouteExtractor extends Analyzer
{
	private $controllerNameDefault = 'index';
	private $controllerActionDefault = 'index';
	
	public function analyze(Route $route, Array $rules)
	{	
		$this->cleanRoutePath($route, $rules);
		$this->setControllerArgumentsAsRouteParts($route);
		$this->extractControllerNameFromArgumentsUsing($rules);
		$this->extractFromArguments('controllerAction');
		
		return true;
	}
	
	public function cleanRoutePath($route, $rules)
	{
		$path = $route->getPath();
		$format = $rules['page_format'] ?: $route->getPageFormat();
		$pattern = '/'. $format .'([0-9]+)\/?/';
		
		if(preg_match($pattern, $path, $matches))
		{
			$route->setPage($matches[1]);
			$route->setPageFormat($format);
			
			$cleanPath = preg_replace($pattern, '', $path);
			$route->setPath($cleanPath);
		}
	}
	
	private function setControllerArgumentsAsRouteParts($route)
	{
		$path = $route->getPath();
		
		if(substr($path, -1) == '/')
			$path = substr($path, 0, -1);
		
		$this->controllerArguments = explode('/', $path);
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
