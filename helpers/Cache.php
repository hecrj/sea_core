<?php

namespace Core\Helpers;
use Core\Components\Templating\Engine;

class Cache
{
	private $templating;
	private $dir;
	private $started = false;
	
	public function __construct(Engine $templating)
	{
		$this->templating = $templating;
	}
	
	public function setDir($path)
	{
		$this->dir = $path;
		
		return $this;
	}
	
	public function getDir()
	{
		return $this->dir;
	}
	
	public function getCacheDir()
	{
		return DIR .'cache/'. $this->dir .'/';
	}
	
	public function load($cacheFile)
	{
		$path = $this->getCacheDir() . $cacheFile .'.cache';
		
		if(! is_file($path))
			return false;
		
		include($path);

		return true;
	}
	
	public function get($cacheFile)
	{
		$path = $this->getCacheDir() . $cacheFile .'.cache';
		
		if(! is_file($path))
			return false;
		
		return file_get_contents($path);
	}
	
	public function render($cacheFile, $template, Array $arguments = null)
	{	
		if($this->load($cacheFile))
			return true;
		
		$contents = $this->generate($cacheFile, $template, $arguments);
		
		echo $contents;
	}
	
	public function generate($cacheFile, $template, Array $arguments = null)
	{
		$this->start();
		$this->renderTemplate($template, $arguments);
		
		return $this->save($cacheFile);
	}
	
	private function renderTemplate($template, $arguments)
	{
		try
		{
			$this->templating->render($template, $arguments);
		}
		catch(\Exception $e)
		{
			ob_end_clean();
			
			throw $e;
		}
	}
	
	public function start()
	{
		if($this->isStarted())
			throw new \RuntimeException('Cache helper is already started!');
		
		ob_start();
		$this->started = true;
		
		return $this;
	}
	
	public function isStarted()
	{
		return $this->started;
	}
	
	public function getContents()
	{
		return ob_get_contents();
	}
	
	public function flush()
	{
		ob_flush();
		
		return $this;
	}
	
	public function end()
	{
		ob_end_clean();
		$this->started = false;
	}
	
	public function save($filename)
	{
		if(! $this->isStarted())
			throw new \RuntimeException('Cache must be started before saving!');
		
		$content = $this->getContents();
		
		$dir = $this->getCacheDir();
		
		if(! $this->makeDirectory($dir))
			throw new \RuntimeException('Impossible to create directories for cache files: '. $dir);
		
		$path = $dir . $filename;
		
		if(! $this->writeCacheFile($path, $content))
			throw new \RuntimeException('Impossible to save cache file: '. $path);
		
		$this->end();
		
		return $content;
	}
	
	private function makeDirectory($dir)
	{
		if(file_exists($dir))
			return true;
		
		return @mkdir($dir, 0777, true);
	}
	
	private function writeCacheFile($path, $content)
	{
		return @file_put_contents($path .'.cache', $content, LOCK_EX) !== false;
	}
}

?>
