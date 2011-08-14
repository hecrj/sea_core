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
		$action = $reflection->name;
		
		$this->data['view'] = $this->name .'/'. $action;
		
		if($this->before_filter)
			$this->callbacksFor($action, $this->before_filter);
		
		// if($this->access_filter)
			// ...
		
		$actionData = $this->callAction($reflection, $arguments);
		
		if(is_array($actionData))
			$this->data = $actionData;
		
		if($this->after_filter)
			$this->callbacksFor($action, $this->after_filter);
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
		return $this->data['view'];
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
	
	public static function getControllerClassName($controllerName)
	{
		return 'App\\Controllers\\' . ucfirst($controllerName) . 'Controller';
	}
	
}
