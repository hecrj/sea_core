<?php

namespace Core\Components;

abstract class DynamicInjector
{
	protected $injectorClass;
	protected $classes;
	protected $dependencies;
	protected $shared = array();
	private   $instances = array();
	
	public function __construct(DynamicInjector $injector = null)
	{
		if(null !== $this->injectorClass and !($injector instanceof $this->injectorClass))
			throw new \Exception('The injector in '. get_class($this) .' must be a '. $this->injectorClass .
				' instance.');
		
		if(null !== $injector)
		{
			$this->instances['external_injector'] = $injector;
			$this->shared[] = 'external_injector';
		}
		
		$this->instances['injector'] = $this;
		$this->shared[] = 'injector';
	}
	
	public function set($name, $instance)
	{
		$this->instances[$name] = $instance;
	}
	
	public function has($name)
	{
		return isset($this->classes[$name]);
	}
    
    public function getClassName($name)
    {
        return $this->classes[$name];
    }
	
	public function get($name)
	{
		if(isset($this->instances[$name]))
			return $this->instances[$name];
					
		if(!isset($this->classes[$name]))
			throw new \RuntimeException('Unsetted class name: '. $name .'. Check your class: '. get_class($this));
		
		return $this->saveInstance($name, $this->inject($this->classes[$name], $this->dependencies[$name]));
	}
	
	private function saveInstance($name, $instance)
	{
		if(in_array($name, $this->shared))
			$this->instances[$name] = $instance;
		
		return $instance;
	}
	
	public function inject($class_name, $dependencies = null)
	{
		$injections = array();
		
		if(is_array($dependencies))
			foreach($dependencies as $dependency)
				$injections[] = $this->getDependency($dependency);
		
		$inject_num = count($injections);
		
		switch($inject_num)
		{
			case 0: $instance = new $class_name(); break;
		    case 1: $instance = new $class_name($injections[0]); break;
		    case 2: $instance = new $class_name($injections[0], $injections[1]); break;
		    case 3: $instance = new $class_name($injections[0], $injections[1], $injections[2]); break;
		    case 4: $instance = new $class_name($injections[0], $injections[1], $injections[2], $injections[3]); break;
		    case 5: $instance = new $class_name($injections[0], $injections[1], $injections[2], $injections[3], $injections[4]); break;
		    case 6: $instance = new $class_name($injections[0], $injections[1], $injections[2], $injections[3], $injections[4], $injections[5]); break;
		    case 7: $instance = new $class_name($injections[0], $injections[1], $injections[2], $injections[3], $injections[4], $injections[5], $injections[6]); break;
		    case 8: $instance = new $class_name($injections[0], $injections[1], $injections[2], $injections[3], $injections[4], $injections[5], $injections[6], $injections[7]); break;
		    case 9: $instance = new $class_name($injections[0], $injections[1], $injections[2], $injections[3], $injections[4], $injections[5], $injections[6], $injections[7], $injections[8]); break;
		    
			default:
				$r = new ReflectionClass($class_name);
				$instance = $r->newInstanceArgs($injections);
		}
		
		return $instance;
	}
	
	private function getDependency($name)
	{
		if(isset($this->instances[$name]))
			return $this->instances[$name];
		
		if(!isset($this->classes[$name]))
		{
			if(isset($this->instances['external_injector']))
				return $this->instances['external_injector']->get($name);
			else
				throw new \RuntimeException('Dependency not present in classes list or in instances of the injector: '. $name .'. Check your class: '. get_class($this));
		}
		
		return $this->saveInstance($name, $this->inject($this->classes[$name], $this->dependencies[$name]));
	}
}
