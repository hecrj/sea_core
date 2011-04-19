<?php

### Session component
class Session implements Component
{
	private static $data = array();
	
	public static function init(){
		// Configure sessions
		session_set_cookie_params(0, '/', '.'.WEB_DOMAIN);
		
		// Initialize sessions
		session_start();
				
		// Get session data
		self::$data = $_SESSION;
		
		// If a CSRF token doesn't exist
		if(!self::exists('csrf_token'))
			// Generate and save one
			self::write('csrf_token', md5(uniqid(rand(), TRUE)));
	}

	public static function destroy($destroy_cookie = false){
		// Get flash message
		$flash = self::read('flash', false);
		
		// Delete global data
		$_SESSION = array();
		
		// If destroy cookie is needed
		if($destroy_cookie)
		{
			// If session use cookies
			if (ini_get("session.use_cookies")) {
				// Get params of session cookie
				$params = session_get_cookie_params();
				// Destroy session cookie
				setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
			}
		}
		
		// Destroy session info
		session_destroy();
		
		// Restart component
		self::init();
		
		// Rewrite flash message
		self::write('flash', $flash, false);
	}
	
	public static function write($name, $value, $serialize = true){
		// Adding value to session and data
		$_SESSION[$name] = self::$data[$name] = ($serialize) ? serialize($value) : $value;
	}
	
	public static function flash($flash_message){
		// Flash shortcut
		self::write('flash', $flash_message);
	}
	
	public static function read($name, $unserialize = true){
		// Unserialize and return session data
		return ($unserialize) ? unserialize(self::$data[$name]) : self::$data[$name];
	}
	
	public static function delete($name){
		if(self::exists($name))
		{
			// Delete session data
			unset($_SESSION[$name]);
			
			// Delete data
			unset(self::$data[$name]);
		}
	}
	
	public static function exists($name){
		// Check if exists session data
		return array_key_exists($name, self::$data);
	}
}

?>