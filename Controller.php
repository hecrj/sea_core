<?php

namespace Core;
use Core\Components\Router;
use Core\Components\DynamicInjector;

### Controller abstract class
abstract class Controller {
	
	protected $router;
	protected $layout = 'application';
	protected $access_filter = false;
	protected $before_filter;
	protected $after_filter;
	private $name;
	private $injector;
	private $view;
	private $data = array();
    
	public function __construct(DynamicInjector $injector)
	{	
		$class_name = get_class($this);
		$this->name = strtolower(substr($class_name, strrpos($class_name, '\\')+1, -10));
		$this->injector = $injector;
	}
	
	public function get($name)
	{
		return $this->injector->get($name);
	}

	public function __set($key, $value)
	{
		$this->data[$key] = $value;
	}
	
	public function __get($key)
	{
		return $this->data[$key];
	}
			
	public function init($action, $arguments)
	{	
		// Set view path
		$this->view = array($this->name, $action);
		
		// Call before filter function
		if(isset($this->before_filter))
			$this->callbacksFor($action, $this->before_filter);
		
		// Exception if the action method doesn't exist --> Exception
		if(! method_exists($this, $action))
			throw new \RuntimeException('The called action: <strong>'. $action .'</strong> does not exist in '. get_class($this) .'!');
			
		// Reflection to check type of method
		$r = new \ReflectionMethod($this, $action);
		
		//  Exception if the method isn't public --> Exception
		if(! $r->isPublic())
			throw new \RuntimeException('The called action: <strong>'. $action .'</strong> is not public!');
		
		/*/ If access filter is defined
		if($this->access_filter)
		{
			// Exception unless Auth class exists
			ExceptionUnless(is_a(Auth::$user, 'UserBase'), 'Native access filtering needs an Auth component class with static $user property having an UserBase based model as content.');
			
			// Set group or role to check. If is not defined, use '*' as key for default access filtering
			$group_role = (isset($this->access_filter[$action])) ? $this->access_filter[$action] : $this->access_filter['*'];
			
			// If group role is not false
			if($group_role)
				// 403 if current user cannot access to current action
				ExceptionUnless(Auth::$user->is($group_role), 'Sorry, but you don\'t have enough privilegies to access this page.', 403);
		}*/
		
		// Get number of required parameters
		$num_params	= $r->getNumberOfRequiredParameters();
		
		// Get number of args
		$num_args	= count($arguments);
		
		// If there are more required parameters than args
		if($num_params > $num_args)
			$num_args = $num_params;
		
		// Switch to avoid call_user_func_array depending the number of args
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
		
		// Call after filter action
		if($this->after_filter)
			$this->callbacksFor($action, $this->after_filter);
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function getLayout()
	{
		return $this->layout;
	}
	
	public function getView()
	{
		return $this->view;
	}
	
	public function getData()
	{
		return $this->data;
	}
	
	protected function setView($dir, $view)
	{
		$this->view = array($dir, $view);
	}
	
	private function callbacksFor($action, Array $callbacks){
		foreach($callbacks as $key => $value)
		{
			if(is_int($key))
				$this->$value();
			
			elseif(in_array($action, $value))
				$this->$key();
		}
	}
	
}

?>
