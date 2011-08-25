<?php

namespace Core\Components\Router;

class Route
{
	private $protocol;
	private $subdomain = 'www';
	private $hostname;
	private $path;

	public function __construct($host = 'localhost', $path = null, $https = false)
	{
		$this->protocol = $https ? 'https' : 'http';
		
		$this->setHostData($host);
		
		$this->setPath($path);
	}
	
	private function setHostData($host)
	{
		$host_parts = explode('.', $host);
		
		if(count($host_parts) > 2)
		{
			$this->subdomain = $host_parts[0];	
			$this->hostname  = $host_parts[1];
		}
		else
			$this->hostname  = $host;
	}
	
	public function getProtocol()
	{
		return $this->protocol;
	}
	
	public function getSubdomain()
	{
		return $this->subdomain;
	}
	
	public function getHostname()
	{
		return $this->hostname;
	}
	
	public function getPath()
	{
		return $this->path;
	}
	
	public function setPath($path)
	{
		// Remove first / from the route
		if($path[0] == '/')
			$path = substr($path, 1);
		
		// Remove last / from the route
		if(substr($path, -1) == '/')
			$path = substr($path, 0, -1);
		
		$this->path = $path;
	}
	
	public function __toString()
	{
		//		http://					subdomain		  .		example.com		/	home
		return $this->protocol .'://'. $this->subdomain .'.'. $this->hostname .'/'. $this->path;
	}

}

?>
