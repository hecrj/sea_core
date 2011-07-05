<?php

namespace Core\Components;

### Request component
class Request
{
	public  $params = array();
	private $subdomain = 'www';
	private $hostname;
	private $route;
	private $method;
	private $ajax;
	
	public function __construct($host = 'localhost', $route = '/', $method = 'GET', $requester = null, Array $get = null, Array $post = null, Array $files = null)
	{	
		// Explode host
		$host_parts = explode('.', $host);
		
		// If host has 3 or more parts has a subdomain
		if(count($host_parts) > 2)
		{
			// Set subdomain
			$this->subdomain = $host_parts[0];
			
			// Set hostname
			$this->hostname  = $host_parts[1];
		}
		else
			// Set hostname
			$this->hostname  = $host_parts[0];
		
		$this->route  = $route;
		$this->method = $method;
		$this->ajax   = (!is_null($requester) && $requester == 'XMLHttpRequest');
		$this->params = array_merge((array)$get, (array)$post, (array)$files);
	}
	
	public function getSubdomain()
	{
		return $this->subdomain;
	}
	
	public function getHostname()
	{
		return $this->hostname;
	}
	
	public function getRoute()
	{
		return $this->route;
	}
	
	public function getMethod()
	{
		return $this->method;
	}
	
	public function isAjax()
	{
		return $this->ajax;
	}
	
	public function redirect($path, $flash = false){
		header('Location: ' . $path);
		exit();
	}
}

?>
