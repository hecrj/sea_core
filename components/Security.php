<?php

namespace Sea\Core\Components;
use Sea\Core\Components\Session;
use Sea\Core\Components\Routing\Request;

# Security component
class Security
{
	private $session;
	private $request;
	private $csrf_token;
	
	public function __construct(Session $session, Request $request)
	{
		// Set dependencies
		$this->session = $session;
		$this->request = $request;
		
		// If a CSRF token exists
		if($session->exists('csrf_token'))
			// Read CSRF Token from session
			$this->csrf_token = $session->read('csrf_token');
		
		else
		{
			// Generate a CSRF Token
			$this->csrf_token = md5(uniqid(rand(), TRUE));
			
			// Save token
			$session->write('csrf_token', $this->csrf_token);
		}
	}
	
	public function getCSRFToken()
	{
		return $this->csrf_token;
	}
	
	public function isCSRFToken($csrf_token)
	{
		return ($this->csrf_token == $csrf_token);
	}
}
