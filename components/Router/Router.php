<?php

/**
 * @todo REFACTORIZE
 */

namespace Core\Components\Router;

class Router
{
	private $rules;
	private $resolvers = array();
	
	public function __construct()
	{
		foreach(func_get_args() as $resolver)
			$this->addResolver($resolver);
	}
	
	public function addResolver(ResolverAbstract $resolver)
	{
		$this->resolvers[] = $resolver;
	}
	
	public function setRules(Array $rules)
	{
		$this->rules = $rules;
	}

	public function enroute(Request $request)
	{
		$rules = $this->getRulesFor($request->getSubdomain());
		
		foreach($this->resolvers as $resolver)
			if($resolver->resolves($request, $rules))
				return;
	}
	
	private function getRulesFor($subdomain)
	{	
		if(!isset($this->rules[$subdomain]))
			throw new \RuntimeException('Enrouting rules are not defined for
				subdomain: <strong>' . $subdomain . '</strong>', 404);
		
		return $this->rules[$subdomain];
	}

}
