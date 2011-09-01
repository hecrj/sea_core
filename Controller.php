<?php

namespace Core;
use Core\Components\Router;
use Core\Components\DynamicInjector;

/**
 * Abstract controller class to use as base for all application controllers.
 * 
 * A controller performs an action and stores the resulting data to use in
 * the view.
 *
 * @author Héctor Ramón Jiménez
 */
abstract class Controller {
	
	protected $view;
	protected $accessFilter = false;
	protected $before_filter;
	protected $after_filter;
	private $name;
	private $action;
	private $injector;
	private $data = array();
    
	public function __construct(DynamicInjector $injector)
	{	
		$class_name = get_class($this);
		$this->name = strtolower(substr($class_name, strrpos($class_name, '\\')+1, -10));
		$this->injector = $injector;
	}
	
	public function __set($key, $value)
	{
		$this->data[$key] = $value;
	}
	
	public function __get($key)
	{
		return $this->data[$key];
	}
	
	public function init($action, Array $arguments)
	{	
		$reflection = new \ReflectionMethod($this, $action);
		
		if($reflection->isPublic())
			$this->call($reflection, $arguments);
		else
			throw new \RuntimeException('The called action: <strong>'. $action .'</strong> is not public!');
		
	}
	
	public function initBlock($action, Array $arguments)
	{
		$reflection = new \ReflectionMethod($this, $action);
		
		if($reflection->isPublic() or $reflection->isProtected())
			$this->call($reflection, $arguments);
		else
			throw new \RuntimeException('The called block action: <strong>'. $action .'</strong> is not public or protected!');
	}
	
	private function call($reflection, $arguments)
	{	
		$this->action = $reflection->name;
		$this->view = $this->name .'/'. $this->action;
		
		if($this->before_filter)
			$this->callbacksFor($this->before_filter);
		
		if($this->injector->has('auth'))
			$this->checkAccessFilter();
		
		$actionData = $this->callAction($reflection, $arguments);
		
		if(is_array($actionData))
			$this->data = array_merge($this->data, $actionData);
		
		if($this->after_filter)
			$this->callbacksFor($this->after_filter);
	}
	
	private function checkAccessFilter()
	{
		$user = $this->get('auth')->getUser();
		$this->__set('user', $user);
		
		if(! $this->accessFilter)
			return;
		
		$groupRole = (isset($this->accessFilter[$this->action])) ? $this->accessFilter[$this->action] : $this->accessFilter['*'];

		if($groupRole and !$user->is($groupRole))
			throw new \RuntimeException('Sorry, but you don\'t have enough privilegies to access this page.<br />'.
					'Your current role is: <strong>'. $user->role .'</strong>', 403);
	}
	
	private function callAction($reflection, $arguments)
	{
		$action = $reflection->name;
		$num_params = $reflection->getNumberOfParameters();
		$num_req_params	= $reflection->getNumberOfRequiredParameters();
		
		$num_args = count($arguments);
		
		if($num_req_params > $num_args)
			$num_args = $num_req_params;
		
		elseif($num_params < $num_args)
			throw new \RuntimeException('There are too much arguments in the route for the action <strong>'. $action .
					'</strong> in <strong>'. get_class($this) . '</strong>');
		
		// Switch to avoid call_user_func_array
		switch ($num_args)
		{
		    case 0: return $this->$action(); break;
		    case 1: return $this->$action($arguments[0]); break;
		    case 2: return $this->$action($arguments[0], $arguments[1]); break;
		    case 3: return $this->$action($arguments[0], $arguments[1], $arguments[2]); break;
		    case 4: return $this->$action($arguments[0], $arguments[1], $arguments[2], $arguments[3]); break;
		    case 5: return $this->$action($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4]); break;
		    case 6: return $this->$action($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4], $arguments[5]); break;
		    case 7: return $this->$action($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4], $arguments[5], $arguments[6]); break;
		    case 8: return $this->$action($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4], $arguments[5], $arguments[6], $arguments[7]); break;
		    case 9: return $this->$action($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4], $arguments[5], $arguments[6], $arguments[7], $arguments[8]); break;
		    default: return call_user_func_array(array($this, $action), $arguments);
		}
	}
	
		
	public function get($name)
	{
		return $this->injector->get($name);
	}

	public function getName()
	{
		return $this->name;
	}
	
	public function getView()
	{
		return $this->view;
	}
	
	public function getData()
	{
		return $this->data;
	}
	
	private function callbacksFor(Array $callbacks){
		foreach($callbacks as $key => $value)
		{
			if(is_int($key))
				$this->$value();
			
			elseif(in_array($this->action, $value))
				$this->$key();
		}
	}
	
	public static function getControllerClassName($controllerName)
	{
		return 'App\\Controllers\\' . ucfirst($controllerName) . 'Controller';
	}
	
}
