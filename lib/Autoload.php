<?php

# Autoload class
class Autoload
{
	private static $helpers = array('Form','Pagination','Cache');
	private static $components = array();
	
	public static function helper($name)
	{
		if(! in_array($name, self::$helpers))
			return false;
		
		require(DIR . 'lib/helpers/' . $name. '.php');
		
		return true;
	}
	
	public static function component($name)
	{
		if(! in_array($name, self::$components))
			return false;
		
		require(DIR . 'lib/components/' . $name. '.php');
		$name::init();
		
		return true;
	}
}

// Add an autload for the helpers
spl_autoload_register('Autoload::helper');
// Add an autoload for the components
spl_autoload_register('Autoload::component');

?>
