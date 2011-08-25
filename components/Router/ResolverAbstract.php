<?php

namespace Core\Components\Router;

abstract class ResolverAbstract
{
	protected $controllerName;
	protected $controllerAction;
	protected $controllerArguments;
	
	public function __construct(){}
	
	/**
	 * @return boolean True means controller information has been set in Route.
	 */
	abstract protected function resolve(Request $request, Array $rules);
	
	public function getControllerDataFrom(Request $request, Array $rules)
	{
		if($this->resolve($request, $rules))
		{
			$request->set('controllerName', $this->controllerName);
			$request->set('controllerAction', $this->controllerAction);
			
			return array($this->controllerName, $this->controllerAction, $this->controllerArguments);
		}
		else
			return null;
	}
}

?>
