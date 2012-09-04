<?php

namespace Sea\Helpers;

class Javascript
{
	private $local;
	private $external;
	private $path;
	
	public function __construct()
	{
		$this->local = array();
		$this->external = array();
		$this->path = 'http://www'. WEB_DOMAIN;
	}
	
	public function setPath($http)
	{
		$this->path = $http;
		
		return $this;
	}
	
	public function local()
	{
		foreach(func_get_args() as $jsPath)
			$this->local[] = $jsPath;
		
		return $this;
	}
	
	public function external()
	{
		foreach(func_get_args() as $jsExt)
			$this->external[] = $jsExt;
		
		return $this;
	}
	
	public function render()
	{
		foreach($this->local as $jsPath)
			echo '<script type="text/javascript" src="'. $this->path .'/js/'. $jsPath .'.js"></script>'."\n";
		
		foreach($this->external as $jsExt)
			echo '<script type="text/javascript" src="'. $jsExt .'"></script>'."\n";
	}
}
