<?php

namespace Sea\Core\Components\Routing;

/**
 * Request class represents an HTTP/S request.
 * 
 * @author Héctor Ramón Jiménez
 */
class Request implements RequestInterface
{
	public $get = array();
	public $post = array();
	public $files = array();
	private $hostname;
	private $subdomain;
	private $path;
	private $secure;
	private $method = 'GET';
	private $ajax = false;

	public function __construct()
	{
		
	}
	
	public static function createFromGlobals()
	{
		$ajax = false;

		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']))
			$ajax = ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');

		$secure = !empty($_SERVER['HTTPS']);
		
		$request = new self;
		$request->setHost($_SERVER['HTTP_HOST'])
				->setPath($_SERVER[PROTOCOL])
				->setSecure($secure)
				->setMethod($_SERVER['REQUEST_METHOD'])
				->setAjax($ajax)
				->setGet($_GET)
				->setPost($_POST)
				->setFiles($_FILES);
		
		return $request;
	}
	
	public function setHost($host)
	{
		$host_parts = explode('.', $host);
		
		if(count($host_parts) > 2)
		{
			$this->subdomain = $host_parts[0];	
			$this->hostname  = $host_parts[1];
		}
		else
		{
			$this->subdomain = 'www';
			$this->hostname  = $host;
		}
		
		return $this;
	}
	
	public function getSubdomain()
	{
		return $this->subdomain;
	}
	
	public function getHostname()
	{
		return $this->hostname;
	}
	
	public function setPath($path)
	{
		if(empty($path))
			$path = '/';
		else
		{
			// Add first / to the route
			if($path[0] != '/')
				$path = '/'. $path;
			
			// Remove last / from the route
			if(substr($path, -1) == '/')
				$path = substr($path, 0, -1);
		}
		
		$this->path = $path;
		
		return $this;
	}
	
	public function getPath()
	{
		return $this->path;
	}
	
	public function setGet(Array $get)
	{
		$this->get = $get;
		
		return $this;
	}
	
	public function setPost(Array $post)
	{
		$this->post = $post;
		
		return $this;
	}
	
	public function setFiles(Array $files)
	{
		$this->files = $files;
		
		return $files;
	}
	
	public function setSecure($secure)
	{
		$this->secure = (bool)$secure;
		
		return $this;
	}
	
	public function isSecure()
	{
		return $this->secure;
	}
	
	public function getProtocol()
	{
		return $secure ? 'https' : 'http';
	}
	
	public function setMethod($method)
	{
		$this->method = $method;
		
		return $this;
	}
	
	public function getMethod()
	{
		return $this->method;
	}
	
	public function setAjax($ajax)
	{
		$this->ajax = (bool)$ajax;
		
		return $this;
	}
	
	public function isAjax()
	{
		return $this->ajax;
	}
	
	public function redirectTo($path)
	{
		header('Location: '. $path);
		exit();
	}
}
