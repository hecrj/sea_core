<?php

namespace Core\Components\Router;

abstract class ResolverAbstract
{
	protected $controllerName;
	protected $controllerAction;
	protected $controllerArguments;
	
	public function __construct(){}
	
	/**
	 * @return boolean True means controller information has been set.
	 */
	abstract protected function resolve(Request $request, Array $rules);
	
	public function resolves(Request $request, Array $rules)
	{
		if($this->resolve($request, $rules))
		{
			$request->set('moduleName', $rules['module']);
			$request->set('controllerName', $this->controllerName);
			$request->set('controllerAction', $this->controllerAction);
			$request->set('controllerArguments', $this->controllerArguments);
			
			return true;
		}
		else
			return false;
	}
}
