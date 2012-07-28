<?php

namespace Sea\Core\Components;
use Sea\Core\Components\Routing\Request;

### Session component
class Session
{
	private $name;
	private $secure;
	private $cookie;
	private $data = array();
	
	public function __construct(Cookie $cookie, Request $request)
	{
		$this->cookie = $cookie;
		$this->secure = $request->isSecure();
		$this->name = $this->secure ? SESSION_SECURE : SESSION_NAME;
		
		$this->init();
	}
	
	public function isSecure()
	{
		return $this->secure;
	}
	
	private function init()
	{		
		// Set session name
		session_name($this->name);
		
		// Configure sessions
		session_set_cookie_params(0, '/', '.'.WEB_DOMAIN, $this->secure);
		
		// Initialize sessions
		session_start();
				
		// Get session data
		$this->data = $_SESSION;
	}
	
	public function destroy($destroy_cookie = false)
	{
		// Delete global data
		$_SESSION = array();
		
		// If destroy cookie is needed
		if($destroy_cookie)
		{
			// If session use cookies
			if (ini_get('session.use_cookies')) {
				// Get params of session cookie
				$params = session_get_cookie_params();
				// Destroy session cookie
				setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
			}
		}
		
		// Destroy session info
		session_destroy();
		
		// Restart session
		$this->init();
		
		return $this;
	}
	
	public function write($name, $value, $serialize = true)
	{
		// Adding value to session and data
		$_SESSION[$name] = $this->data[$name] = ($serialize) ? serialize($value) : $value;
	}
	
	public function flash($name)
	{
		if(null !== $flash = $this->read($name))
			$this->delete($name);
		
		return $flash;
	}
	
	public function read($name, $unserialize = true)
	{	
		if(! $this->exists($name))
			return null;
		
		// Unserialize and return session data
		return ($unserialize) ? unserialize($this->data[$name]) : $this->data[$name];
	}
	
	public function delete($name)
	{
		if($this->exists($name))
		{
			// Delete session data
			unset($_SESSION[$name]);
			
			// Delete data
			unset($this->data[$name]);
		}
	}
	
	public function exists($name)
	{
		// Check if session data exists
		return isset($this->data[$name]);
	}
}
