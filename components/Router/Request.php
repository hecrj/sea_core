<?php

namespace Core\Components\Router;

/**
 * Request class represents an HTTP/S request.
 * 
 * @author Héctor Ramón Jiménez
 */
class Request
{
	public $get = array();
	public $post = array();
	public $files = array();
	private $method = 'GET';
	private $ajax = false;
	private $attributes = array();
	private $route;

	public function __construct(Route $route)
	{
		$this->route = $route;
	}
	
	public static function createFromGlobals()
	{	
		$ajax = ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');
		$secure = !empty($_SERVER['HTTPS']);
		
		$route = new Route($_SERVER['HTTP_HOST'], $_SERVER[ROUTE_PROTOCOL], $secure);
		
		$request = new self($route);
		$request->setMethod($_SERVER['REQUEST_METHOD'])
				->setAjax($ajax)
				->setParams($_GET, $_POST, $_FILES);
		
		return $request;
	}
	
	public function set($key, $value)
	{
		$this->attributes[$key] = $value;
	}
	
	public function get($key)
	{
		if(isset($this->attributes[$key]))
			return $this->attributes[$key];
		else
			return null;
	}
	
	public function getSubdomain()
	{
		return $this->route->getSubdomain();
	}
	
	public function getPath()
	{
		return $this->route->getPath();
	}
	
	public function setPath($path)
	{
		$this->route->setPath($path);
		
		return $this;
	}
	
	public function getMethod()
	{
		return $this->method;
	}
	
	public function setMethod($method)
	{
		$this->method = $method;
		
		return $this;
	}
	
	public function isSecure()
	{
		return ($this->route->getProtocol() == 'https');
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
	
	public function setParams(Array $get = array(), Array $post = array(), Array $files = array())
	{
		$this->get = $get;
		$this->post = $post;
		$this->files = $files;
		
		return $this;
	}
	
	public function redirectTo($path)
	{
		header('Location: '. $path);
		exit();
	}
	
}
