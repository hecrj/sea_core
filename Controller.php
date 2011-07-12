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
 * @author HŽctor Ram—n JimŽnez
 */
abstract class Controller {
	
	/**
	 * Stores layout file name to load with the view
	 *
	 * @var string
	 */
	protected $layout = 'application';
	
	/**
	 * Stores access filtering conditions using arrays.
	 * If set to FALSE, access filtering will be disabled.
	 *
	 * @var mixed
	 */
	protected $access_filter = false;
	
	/**
	 * Array with functions to call before the action is called.
	 *
	 * @var array
	 */
	protected $before_filter;
	
	/**
	 * Array of functions to call after the action finishes.
	 *
	 * @var array
	 */
	protected $after_filter;
	
	/**
	 * Stores controller name.
	 *
	 * @var string
	 */
	private $name;
	
	/**
	 * Stores a component injector to inject dependencies to components dynamically.
	 *
	 * @var DynamicInjector
	 */
	private $injector;
	
	/**
	 * Stores action resulting data.
	 *
	 * @var array
	 */
	private $data = array();
    
	/**
	 * Creates a new controller.
	 *
	 * @param DynamicInjector $injector Dynamic component injector to use dependency injection.
	 */
	public function __construct(DynamicInjector $injector)
	{	
		$class_name = get_class($this);
		$this->name = strtolower(substr($class_name, strrpos($class_name, '\\')+1, -10));
		$this->injector = $injector;
	}
	
	/**
	 * Method to obtain a component.
	 *
	 * @param string $name Name of the component to obtain.
	 * @return object Requested component.
	 */
	public function get($name)
	{
		return $this->injector->get($name);
	}
	
	/**
	 * Magic method to set data easily:
	 * $this->foo = 'bar';
	 *
	 * @param string $key Name of the key to assign to the data
	 * @param string $value Data value
	 */
	public function __set($key, $value)
	{
		$this->data[$key] = $value;
	}
	
	/**
	 * Magic method to get data easily:
	 * $this->foo // returns 'bar'
	 *
	 * @param string $key Name of the key assigned to the data
	 * @return multitype Data value:
	 */
	public function __get($key)
	{
		return $this->data[$key];
	}
	
	/**
	 * Initializes the controller:
	 * 1. Checks if the action can be called.
	 * 2. Calls before filter functions defined in $before_filter
	 * 3. Checks access authorization.
	 * 4. Calls the action.
	 * 5. Calls after filter functions defined in $after_filter
	 *
	 * @param string $action Name of the action to be called
	 * @param array $arguments Array of arguments to pass to the action
	 * @throws \RuntimeException
	 */
	public function init($action, $arguments)
	{	
		// Set view path
		$this->view = array($this->name, $action);
		
		// Exception if the action method doesn't exist --> Exception
		if(! method_exists($this, $action))
			throw new \RuntimeException('The called action: <strong>'. $action .'</strong> does not exist in '. get_class($this) .'!');
			
		// Reflection to check type of method
		$r = new \ReflectionMethod($this, $action);
		
		//  Exception if the method isn't public --> Exception
		if(! $r->isPublic())
			throw new \RuntimeException('The called action: <strong>'. $action .'</strong> is not public!');
		
		// Call before filter function
		if(isset($this->before_filter))
		$this->callbacksFor($action, $this->before_filter);
		
		if($this->access_filter)
		{
			
			// Get authentication component with alias auth
			$auth = $this->injector->get('auth');
			
			//...
		}
		/*/ REFACTORING PENDING --> SECURITY COMPONENT IMPLEMENTATION
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
	
	/**
	 * Gets the name of the controller.
	 * 
	 * @return string Controller name
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * Gets the layout of the controller.
	 * 
	 * @return string Controller layout
	 */
	public function getLayout()
	{
		return $this->layout;
	}
	
	/**
	 * Gets the view of the controller.
	 * 
	 * @return array View data
	 */
	public function getView()
	{
		return $this->view;
	}
	
	/**
	 * Gets the stored data in the controller.
	 * 
	 * @return array Data stored in the controller
	 */
	public function getData()
	{
		return $this->data;
	}
	
	/**
	 * Sets a view.
	 *
	 * @param string $dir Directory in app/views/ where the view is
	 * @param string $view Name of the view to load
	 */
	protected function setView($dir, $view)
	{
		$this->view = array($dir, $view);
	}
	
	/**
	 * Calls $callbacks functions if key matches with the action or if it
	 * is an integer (non related to an action).
	 *
	 * @param string $action Action to match the callback with
	 * @param array $callbacks Array of callback functions to be called
	 */
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
