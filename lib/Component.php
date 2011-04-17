<?php

# Component class
abstract class Component
{
	private static $components = array('Cookie', 'Session', 'Request');
	
	abstract public static function init();
	
	public static function load($name)
	{
		if(in_array($name, self::$components))
		{
			require(DIR . 'lib/components/' . $name. '.php');
			$name::init();
			return true;
		}
			
		return false;
	}
}

// Add an autoload for the components
spl_autoload_register('Component::load');

?>