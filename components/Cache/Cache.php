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
	
	public function clean() {
		$filenames = func_get_args();
		
		if(empty($filenames))
			$this->cleanDirectory();
		
		else
			$this->cleanFiles($filenames);
	}
	
	private function cleanDirectory($dir = null) {
		if($dir === null)
			$dir = $this->getDir();
		
		$elements = scandir($dir);
		
		foreach($elements as $element) {
			if($element == '.' or $element == '..')
				continue;
			
			$path = $dir .'/'. $element;
			
			if(is_file($path))
				$this->cleanFile($path);
			else
				$this->cleanDirectory($path);
		}
	}
	
	private function cleanFiles($filenames) {
		foreach($filenames as $filename)
			$this->cleanFile($this->getPath($filename));
	}
	
	private function cleanFile($path) {	
		if(! is_file($path))
			return true;
		
		if(! @unlink($path))
			throw new \RuntimeException('Impossible to delete cache file: <strong>'. $path .'</strong>');
		
		return true;
	}
}
