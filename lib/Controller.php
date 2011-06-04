<?php

### Controller abstract class
abstract class Controller {
	
	protected $layout = 'application';
	protected $access_filter = false;
	protected $view;
	private $name;
	private $data = array();
    
	public function __construct()
	{
		// Getting controller class name
		$class = get_class($this);
		
		// Set controller name deleting "Controller" suffix and lowercasing
		$this->name = strtolower(substr($class, 0, strlen($class) - 10));
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
		if($this->before_filter)
			$this->callbacksFor($action, $this->before_filter);
		
		// Exception if the action method doesn't exist --> Exception
		ExceptionUnless(method_exists($this, $action), 'The called action: <strong>'. $action .'</strong> does not exist in '. get_class($this) .'!');
			
		// Reflection to check type of method
		$Reflection = new ReflectionMethod($this, $action);
		
		//  Exception if the method isn't public --> Exception
		ExceptionUnless($Reflection->isPublic(), 'The called action: <strong>'. $action .'</strong> is not public!');
		
		// If access filter is defined
		if($this->access_filter)
		{
			// Exception unless Auth class exists
			ExceptionUnless(class_exists('Auth') and is_a(Auth::$user, 'UserBase'), 'Native access filtering needs an Auth component class with static $user property having an UserBase based model as content.');
			
			// Set group or role to check. If is not defined, use '*' as key for default access filtering
			$group_role = (isset($this->access_filter[$action])) ? $this->access_filter[$action] : $this->access_filter['*'];
			
			// If group role is not false
			if($group_role)
				// 403 if current user cannot access to current action
				ExceptionUnless(Auth::$user->is($group_role), 'Sorry, but you don\'t have enough privilegies to access this page.', 403);
		}
		
		// Call the action method in the controller
		call_user_func_array(array($this, $action), $arguments);
		
		// Call after filter action
		if($this->after_filter)
			$this->callbacksFor($action, $this->after_filter);
		
		// New View for controller
		$this->view = new View($this->view, $this->data);
		
		// If Request isn't an AJAX request
		if(!Request::isAjax())
			// Load layout associated to controller
			$this->view->load($this->layout);
		
		// If Request is an AJAX request
		else
			// Render only the view, without layout
			$this->view->render();
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
