<?php

### Controller abstract class
abstract class Controller {
	
	protected $layout = 'application';
	protected $access_filter = false;
	private $view;
	private $data = array();
    
	public function __construct()
	{		
		// Launch configuration of the controller
		$this->configure();
    }

	public function __set($key, $value)
	{
		$this->data[$key] = $value;
	}
	
	public function __get($key)
	{
		return $this->data[$key];
	}
	
	protected function configure()
	{
		// Default configuration method
	}
			
	public function init($action)
	{	
		// Call to the action
		$this->call($action);
		
		// Method after the action
		$this->afterCall();
		
		// New View for controller
		$this->view = new View($this->data);
		
		// Start output buffering
		ob_start();
		
		// If Request isn't an AJAX request
		if(!Request::isAjax())
			// Load layout associated to controller
			$this->view->load($this->layout);
		
		// If Request is an AJAX request
		else
			// Render only the view, without layout
			$this->view->render();
		
		// End and flush output buffering
		ob_end_flush();	

	}
					    
	private function call($action)
	{
		// Exception if the action method doesn't exist --> Exception
		ExceptionUnless(method_exists($this, $action), 'The called action: <strong>'. $action .'</strong> does not exist in '. get_class($this) .'!');
			
		// Reflection to check type of method
		$Reflection = new ReflectionMethod($this, $action);
		
		//  Exception if the method isn't public --> Exception
		ExceptionUnless($Reflection->isPublic(), 'The called action: <strong>'. $action .'</strong> is not public!');
		
		// If access filter is defined
		if($this->access_filter)
			// 403 if current user cannot access to current action
			ExceptionUnless(Auth::$user->is($this->access_filter[$action]), 'Sorry, but you don\'t have enough privilegies to access this page.', 403);
			
		// Call the action method in the controller
		call_user_func_array(array($this, Router::$action), Router::$arguments);
	}
	
	protected function afterCall(){
		// Default method after call the action
	}
	
}

?>
