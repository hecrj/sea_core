<?php

namespace Core\Components;

### Request component
class Request
{
	public $get = array();
	public $post = array();
	public $files = array();
	private $subdomain = 'www';
	private $hostname;
	private $route;
	private $ssl;
	private $method;
	private $ajax;
	
	public function __construct($host = 'localhost', $route = '/', $ssl = null, $method = 'GET', $requester = null, Array $get = null, Array $post = null, Array $files = null)
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
		$this->ssl    = !empty($ssl);
		$this->ajax   = (!is_null($requester) && $requester == 'XMLHttpRequest');
		$this->get    = (array)$get;
		$this->post   = (array)$post;
		$this->files  = (array)$files;
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
	
	public function isSSL()
	{
		return $this->ssl;
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
