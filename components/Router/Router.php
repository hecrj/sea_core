<?php

namespace Core\Components\Router;

class Router
{
	private $rules;
	private $analyzers = array();
	
	public function __construct(Array $rules)
	{
		$this->rules = $rules;
	}
	
	public function addAnalyzer(Analyzer $analyzer)
	{
		$this->analyzers[] = $analyzer;
	}

	public function getControllerDataFrom(Route $route)
	{
		$rules = $this->getRulesFor($route);
		
		foreach($this->analyzers as $analyzer)
			if($analyzer->analyze($route, $rules))
				return $analyzer->getControllerData();
	}
	
	private function getRulesFor($route)
	{
		$subdomain = $route->getSubdomain();
		
		if(!isset($this->rules[$subdomain]))
			throw new \RuntimeException('Enrouting rules are not defined for
				subdomain: <strong>' . $subdomain . '</strong>', 404);
		
		return $this->rules[$subdomain];
	}

}

?>
