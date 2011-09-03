<?php

namespace Core\Components\Cache;

/**
 * Cache component to use in controllers.
 *
 * @author Héctor Ramón Jiménez
 */
class Cache extends CacheAbstract
{
	public function __construct()
	{}
	
	public function clean()
	{
		$filenames = func_get_args();
		
		if(empty($filenames))
			$this->cleanCacheDirectory();
		
		else
			foreach($filenames as $filename)
				$this->cleanCacheFile($filename);
	}
	
	private function cleanCacheDirectory()
	{
		
	}
	
	private function cleanCacheFile($filename)
	{
		$path = $this->getCachePath($filename);
		
		if(! is_file($path))
			return true;
		
		if(! @unlink($path))
			throw new \RuntimeException('Impossible to delete cache file: <strong>'. $path .'</strong>');
		
		return true;
	}
}
