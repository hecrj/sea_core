<?php

namespace Core\Components\Router;

class Route
{
	private $protocol = 'http://';
	private $subdomain = 'www';
	private $hostname;
	private $path;
	private $page = 1;
	private $pageFormat = 'page-';

	public function __construct($ssl = null, $host = 'localhost', $path = '/')
	{
		$this->protocol = empty($ssl) ? 'http://' : 'https://';
		$this->setHostData($host);
		
		// Remove first / from the route
		if(strpos($path, '/') === 0)
			$path = substr($path, 1);
		
		$this->path = $path;
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
			$this->hostname  = $host_parts[0];
	}
	
	public function setPath($path)
	{
		$this->path = $path;
	}
	
	public function setPage($page)
	{
		$this->page = (int)$page;
	}
	
	public function setPageFormat($format)
	{
		$this->pageFormat = $format;
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
	
	public function getPage()
	{
		return $this->page;
	}
	
	public function getPageFormat()
	{
		return $this->pageFormat;
	}
	
	public function __toString()
	{
		//		http://			www				.	example.com		/	contact
		return $this->protocol.$this->subdomain.'.'.$this->hostname.'/'.$this->path;
	}

}

?>
