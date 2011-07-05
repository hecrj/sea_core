<?php

namespace Core\Components;

abstract class DynamicInjector
{
	
	protected $classes;
	protected $dependencies;
	protected $shared = true;
	private   $instances = array();
	private   $injector;
	
	public function __construct(DynamicInjector $injector = null)
	{
		$this->injector  = $injector;
	}
	
	public function set($name, $instance)
	{
		$this->instances[$name] = $instance;
	}
	
	public function get($name)
	{
		if(isset($this->instances[$name]))
			return $this->instances[$name];
		
		if(!isset($this->classes[$name]))
			throw new \RuntimeException('Invalid dependency relationship name or unsetted class name: '. $name .'. Check your class: '. get_class($this));
		
		return $this->instantiate($name);
	}
	
	private function getDependency($name)
	{
		if(isset($this->instances[$name]))
			return $this->instances[$name];
		
		if(!isset($this->classes[$name]))
		{
			if(null !== $this->injector)
				return $this->injector->get($name);
			else
				throw new \RuntimeException('Dependency not present in classes list of the injector: '. $name .'. Check your class: '. get_class($this));
		}
		
		return $this->instantiate($name);
	}
	
	private function instantiate($name)
	{
		$dependencies = array();
		
		if(is_array($this->dependencies[$name]))
			foreach($this->dependencies[$name] as $dependency)
				$dependencies[] = $this->getDependency($dependency);
		
		$class_name = $this->classes[$name];
		$depend_num = count($dependencies);
		
		switch($depend_num)
		{
			case 0: $instance = new $class_name(); break;
		    case 1: $instance = new $class_name($dependencies[0]); break;
		    case 2: $instance = new $class_name($dependencies[0], $dependencies[1]); break;
		    case 3: $instance = new $class_name($dependencies[0], $dependencies[1], $dependencies[2]); break;
		    case 4: $instance = new $class_name($dependencies[0], $dependencies[1], $dependencies[2], $dependencies[3]); break;
		    case 5: $instance = new $class_name($dependencies[0], $dependencies[1], $dependencies[2], $dependencies[3], $dependencies[4]); break;
		    case 6: $instance = new $class_name($dependencies[0], $dependencies[1], $dependencies[2], $dependencies[3], $dependencies[4], $dependencies[5]); break;
		    case 7: $instance = new $class_name($dependencies[0], $dependencies[1], $dependencies[2], $dependencies[3], $dependencies[4], $dependencies[5], $dependencies[6]); break;
		    case 8: $instance = new $class_name($dependencies[0], $dependencies[1], $dependencies[2], $dependencies[3], $dependencies[4], $dependencies[5], $dependencies[6], $dependencies[7]); break;
		    case 9: $instance = new $class_name($dependencies[0], $dependencies[1], $dependencies[2], $dependencies[3], $dependencies[4], $dependencies[5], $dependencies[6], $dependencies[7], $dependencies[8]); break;
		    
			default:
				$r = new ReflectionClass($class_name);
				$instance = $r->newInstanceArgs($dependencies);
		}
		
		if($this->shared !== FALSE and ($this->shared === TRUE or in_array($name, $this->shared)))
			$this->instances[$name] = $instance;
		
		return $instance;
	}
}

?>