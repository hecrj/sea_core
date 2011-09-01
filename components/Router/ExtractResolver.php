<?php

namespace Core\Components\Router;

class ExtractResolver extends ResolverAbstract
{	
	protected function resolve(Request $request, Array $rules)
	{	
		$this->controllerArguments = $this->getArgumentsAsRouteParts($request);
		$this->controllerName = $this->extractControllerNameFromArgumentsUsing($rules);
		$this->controllerAction = $this->extractFromArguments();
		
		return true;
	}
	
	private function getArgumentsAsRouteParts(Request $request)
	{
		$path = $request->getPath();
		
		return explode('/', $path);
	}
	
	private function extractControllerNameFromArgumentsUsing($rules)
	{
		if(isset($rules['controller']))
			return $this->extractFromArguments($rules['controller']);
		
		elseif(isset($rules['static_controller']))
			return $rules['static_controller'];
		
		else
			return $this->extractFromArguments();
	}
	
	private function extractFromArguments($default = 'index')
	{
		$argument = array_shift($this->controllerArguments);
		
		if(empty($argument))
			$argument = $default;
		
		return $argument;
	}
	
}
