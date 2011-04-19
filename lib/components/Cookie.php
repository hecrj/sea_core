<?php

### Cookie component
class Cookie implements Component
{
	private static $data = array();
	
	public static function init(){		
		// Get cookie data
		self::$data = $_COOKIE;
	}
	
	public static function create($name, $value, $time = 2592000, $path = '/'){
		// Encode value
		$value = base64_encode($value);
		
		// Creaate cookie
		setcookie($name, $value, time()+$time, $path, '.'.WEB_DOMAIN);
		
		// Add value to data
		self::$data[$name] = $value;
	}
	
	public static function delete($name){
		// If cookie exists
		if(self::exists($name))
		{
			// Delete cookie
			setcookie($name, '', 0, '/', '.'.WEB_DOMAIN);
			
			// Unset the data
			unset(self::$data[$name]);
		}
	}
	
	public static function read($name){
		// Decode and return Cookie data
		return base64_decode(self::$data[$name]);
	}
	
	public static function exists($name){
		// Return if requested data exists
		return array_key_exists($name, self::$data);
	}
}

?>