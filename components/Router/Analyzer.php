<?php

namespace Core\Components\Router;

abstract class Analyzer
{
	protected $rules;
	protected $controllerName;
	protected $controllerAction;
	protected $controllerArguments;
	
	public function __construct(Array $rules)
	{
		$this->rules = $rules;
	}
	
	abstract public function analyze(Route $route);
	
	final protected function getRulesFor(Route $route)
	{
		$subdomain = $route->getSubdomain();
		
		if(!isset($this->rules[$subdomain]))
			throw new \RuntimeException('Enrouting rules are not defined for
				subdomain: <strong>' . $subdomain . '</strong>', 404);
		
		return $this->rules[$subdomain];
	}
	
	final public function getControllerData()
	{
		return array($this->controllerName, $this->controllerAction, $this->controllerArguments);
	}
}

?>
