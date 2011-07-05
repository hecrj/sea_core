<?php

namespace Core\Component;

### Cookie component
class Cookie
{
	private $data = array();
	
	public function init(){		
		// Get cookie data
		$this->data = $_COOKIE;
	}
	
	public function create($name, $value, $time = 2592000, $path = '/'){
		// Encode value
		$value = base64_encode($value);
		
		// Creaate cookie
		setcookie($name, $value, time()+$time, $path, '.'.WEB_DOMAIN);
		
		// Add value to data
		$this->data[$name] = $value;
	}
	
	public function delete($name){
		// If cookie exists
		if($this->exists($name))
		{
			// Delete cookie
			setcookie($name, '', 0, '/', '.'.WEB_DOMAIN);
			
			// Unset the data
			unset($this->data[$name]);
		}
	}
	
	public function read($name){
		// Decode and return Cookie data
		return base64_decode($this->data[$name]);
	}
	
	public function exists($name){
		// Return if requested data exists
		return array_key_exists($name, $this->data);
	}
}

?>
