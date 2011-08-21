<?php

namespace Core\Helpers;

class Cache
{
	private $path;
	private $started = false;
	private $content;
	
	public function __construct(){}
	
	public function setPath($path)
	{
		$this->path = $path;
		
		return $this;
	}
	
	public function getPath()
	{
		return $this->path;
	}
	
	public function load()
	{	
		$path = $this->getCachePath();
		
		if(! is_file($path))
			return false;
		
		include($path);
		
		return true;
	}
	
	public function getCachePath()
	{
		return DIR .'cache/'. $this->path .'.cache';
	}
	
	public function start()
	{
		if($this->isStarted())
			return false;
		
		ob_start();
		$this->started = true;
	}
	
	public function isStarted()
	{
		return $this->started;
	}
	
	public function stop()
	{
		$this->content .= ob_get_flush();
		$this->started = false;
	}
	
	public function save()
	{
		if($this->isStarted())
			$this->stop();
		
		$path = $this->getCachePath();
		
		list(/* JUMP */, $dir, $filename) = preg_split('/(.*\/)?([^\/]+)/', $path, 0, PREG_SPLIT_DELIM_CAPTURE);
		
		if(! $this->makeDirectory($dir))
			throw new \RuntimeException('Impossible to create directories for cache files: '. $dir);
		
		if(! $this->saveCacheFile())
			throw new \RuntimeException('Impossible to save cache file: '. $path);
		
		$this->content = null;
	}
	
	private function makeDirectory($dir)
	{
		if(file_exists($dir))
			return true;
		
		return @mkdir($dir, 0777, true);
	}
	
	private function saveCacheFile()
	{
		return @file_put_contents($this->path, $this->content, LOCK_EX) !== false;
	}
}

?>
