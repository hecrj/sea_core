<?php

namespace Sea\Components;

### Cookie component
class Cookie
{
	private $data = array();
	
	public function __construct()
	{		
		// Get cookie data
		$this->data = $_COOKIE;
	}
	
	public function create($name, $value, $time = 604800, $path = '/', $secure = false, $httponly = false)
	{
		// Encode value
		$value = base64_encode(serialize($value));
		
		// Create cookie
		setcookie($name, $value, $time ? time()+$time : 0, $path, '.'. Cookie\DOMAIN, $secure, $httponly);
		
		// Add value to data
		$this->data[$name] = $value;
	}
	
	public function delete($name)
	{
		// If cookie exists
		if($this->exists($name))
		{
			// Delete cookie
			setcookie($name, '', 0, '/', '.'. Cookie\DOMAIN);
			
			// Unset the data
			unset($this->data[$name]);
		}
	}
	
	public function read($name)
	{
		// Decode and return Cookie data
		return unserialize(base64_decode($this->data[$name]));
	}
	
	public function exists($name)
	{
		// Return if requested data exists
		return array_key_exists($name, $this->data);
	}
}
