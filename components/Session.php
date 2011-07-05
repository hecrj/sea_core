<?php

namespace Core\Components;

### Session component
class Session
{
	private $data = array();
	
	public function __construct(){
		$this->init();
	}
	
	private function init()
	{
		// Configure sessions
		session_set_cookie_params(0, '/', '.'.WEB_DOMAIN);
		
		// Initialize sessions
		session_start();
				
		// Get session data
		$this->data = $_SESSION;
	}
	
	public function destroy($destroy_cookie = false){
		// Get flash message
		$flash = $this->read('flash', false);
		
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
		$this->init();
		
		// Rewrite flash message
		$this->write('flash', $flash, false);
	}
	
	public function write($name, $value, $serialize = true){
		// Adding value to session and data
		$_SESSION[$name] = $this->data[$name] = ($serialize) ? serialize($value) : $value;
	}
	
	public function flash($flash_message){
		// Flash shortcut
		$this->write('flash', $flash_message);
	}
	
	public function read($name, $unserialize = true){
		if($name == 'flash')
			unset($_SESSION['flash']);
		
		// Unserialize and return session data
		return ($unserialize) ? unserialize($this->data[$name]) : $this->data[$name];
	}
	
	public function delete($name){
		if($this->exists($name))
		{
			// Delete session data
			unset($_SESSION[$name]);
			
			// Delete data
			unset($this->data[$name]);
		}
	}
	
	public function exists($name){
		// Check if exists session data
		return array_key_exists($name, $this->data);
	}
}

?>
