<?php

namespace Sea\Helpers;
use Sea\Components\Cache\CacheAbstract;
use Sea\Components\Templating\Engine;

class Cache extends CacheAbstract
{
	private $templating;
	private $started = false;
	
	public function __construct(Engine $templating)
	{
		$this->templating = $templating;
	}
	
	public function load($cacheFile)
	{
		$path = $this->getPath($cacheFile);
		
		if(Cache\EMULATE == TRUE or !is_file($path))
			return false;
		
		include($path);

		return true;
	}
	
	public function get($cacheFile)
	{
		$path = $this->getPath($cacheFile);
		
		if(! is_file($path))
			return false;
		
		return file_get_contents($path);
	}
	
	public function render($cacheFile, $template, Array $arguments = null)
	{
		if($this->load($cacheFile))
			return;
		
		echo $this->generate($cacheFile, $template, $arguments);
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
		
		$dir = $this->getDir();
		
		if(! $this->makeDirectory($dir))
			throw new \RuntimeException('Impossible to create directories for cache files: '. $dir);
		
		if(! $this->writeCacheFile($filename, $content))
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
	
	private function writeCacheFile($filename, $content)
	{
		$path = $this->getPath($filename);

		return @file_put_contents($path, $content, LOCK_EX) !== false;
	}
}
