<?php

namespace Core\Components;

# Cache component
class Cache 
{
	private $data = array();
	private $path;
	private $filename;
	private $fullPath;
	private $break;
	private $until;
	private $broken;
	
	public function __construct()
	{}
	
	public function set($key, $value)
	{
		$this->data[$key] = $value;
		
		return $this;
	}
	
	public function setData(Array $data)
	{
		if(empty($this->data))
			$this->data = $data;
		
		return $this;
	}
	
	public function path($path, $filename = null)
	{
		$this->path = $path;
		$this->filename = $filename;
		$this->fullPath = DIR . 'cache/' . $this->path .'/'. $this->filename .'.cache';
		$this->broken = false;
			
		return $this;
	}
	
	public function until($until)
	{
		$this->break = $until;
		$this->until = true;
		
		return $this;
	}
	
	public function since($since)
	{
		$this->break = $since;
		$this->until = false;
		
		return $this;
	}
	
	public function isBroken()
	{
		return $this->broken;
	}
	
	public function generate($template)
	{
		if($this->filename == null)
			throw new \RuntimeException('Cache filename is not defined. Impossible to generate cache file.');
		
		// Iterate over data array
		foreach($this->data as $key => $value)
			// Assign key named variables to array values
			$$key = $value;
		
		// Exception if cache view does not exist
		if(! is_file($template))
			throw new \InvalidArgumentException('The cache template you requested does not exist in: <strong>'. $template .'</strong>');
		
		// Start output buffering
		ob_start();

		// Load file
		require($template);

		// Get contents
		$content = ob_get_contents();

		// End and clean output buffering
		ob_end_clean();
		
		// Make directories for cache files --> Recursive
		if(!file_exists(DIR . 'cache/' . $this->path) and ! @mkdir(DIR . 'cache/' . $this->path, 0777, true))
			throw new \RuntimeException('Impossible to create directories for cache files: ' . $this->path, 404);

		// Generate cache file
		file_put_contents($this->fullPath, $content);
		
		return $this;
	}
	
	public function load()
	{	
		// If cache file does not exist
		if($this->filename == null or ! is_file($this->fullPath))
			return false;
		
		if(isset($this->break))
		{
			$contents = file_get_contents($this->fullPath);
			$parts = explode($this->break, $contents);
			
			$this->broken = (count($parts) > 1);
			
			if($this->until)
				echo $parts[0];
			else
				echo $parts[1];
		}
		else
			require($this->fullPath);
		
		return true;
	}
	
	public function clean()
	{
		// Set directory path
		$dir_path = DIR . 'cache/' . $this->path;
		
		// If we need to clean a cache file
		if($this->filename != null)
		{	
			if(! is_file($this->fullPath))
				return false;
			
			// Exception if delete cache file fails
			if(! @unlink($this->fullPath))
				throw new \RuntimeException('Impossible delete file: <strong>'. $file_path .'</strong>');
		}
		
		// If we need to clean a directory
		else
			// Clean directory recursively
			$this->cleanDir($dir_path);
			
		return true;
	}
	
	private function cleanDir($dir_path, $delete = false)
	{
		// If directory does not exist
		if(!file_exists($dir_path))
			return false;

		// Exception if dir path is not a valid path to a directory
		if(! is_dir($dir_path))
		 	throw new \InvalidArgumentException('Invalid directory path given: <strong>'. $dir_path .'</strong>');
		
		// For every file or dir in dir path
		foreach(scandir($dir_path) as $dir_file)
		{
			// Continue if (.) or (..) of scan dir
			if($dir_file == '.' or $dir_file == '..')
				continue;
			
			// Set new path to directory or file
			$new_path = $dir_path .'/'. $dir_file;
			
			// If is a directory
			if(is_dir($new_path))
				// Clean directory and delete it
				$this->cleanDir($new_path, true);
				
			// If is not a directory, throw an exception if delete cache file fails
			elseif(! @unlink($new_path))
				throw new \RuntimeException('Impossible delete file: <strong>'. $new_path .'</strong>');
		}
		
		// If we need to delete directory, throw an exception if delete directory fails
		if($delete and !@rmdir($dir_path))
		 	throw new \RuntimeException('Impossible delete directory: <strong>'. $dir_path .'</strong>');
	}
}

?>