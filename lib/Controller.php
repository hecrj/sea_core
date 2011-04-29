<?php

### Controller abstract class
abstract class Controller {
	
	protected $layout = 'application';
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
		
		if(!Request::isAjax())
			// Load layout associated to controller
			$this->view->load($this->layout);
		else
			$this->view->render();
	}
					    
	private function call($action)
	{
		// 404 if the action method doesn't exist --> Exception
		To404Unless(method_exists($this, $action), 'The called action: <strong>'. $action .'</strong> does not exist in '. get_class($this) .'!');
			
		// Reflection to check type of method
		$Reflection = new ReflectionMethod($this, $action);
		
		//  404 if the method isn't public --> Exception
		To404Unless($Reflection->isPublic(), 'The called action: <strong>'. $action .'</strong> is not public!');
			
		// Call the action method in the controller
		call_user_func_array(array($this, Router::$action), Router::$arguments);
	}
	
	protected function afterCall(){
		// Default method after call the action
	}
	
}

?>
