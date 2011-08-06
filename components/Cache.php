<?php

namespace Core\Components;

# Cache component
class Cache 
{
	private $data = array();
	private $path;
	private $filename;
	private $flush = false;
	
	public function __construct()
	{}
	
	public function set(Array $data)
	{
		if(empty($this->data))
			$this->data = $data;
		
		return $this;
	}
	
	public function path($path, $filename)
	{
		if(!isset($this->path))
		{
			$this->path = $path;
			$this->filename = $filename;
		}
		
		return $this;
	}
	
	public function flush($flush = true)
	{
		$this->flush = (bool)$flush;
		
		return $this;
	}
	
	public function generate($template)
	{
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

		if($this->flush)
			// End and flush output buffering
			ob_end_flush();
		else
			// End and clean output buffering
			ob_end_clean();
		
		// Make directories for cache files --> Recursive
		if(!file_exists(DIR . 'cache/' . $this->path) and ! @mkdir(DIR . 'cache/' . $this->path, 0777, true))
			throw new \RuntimeException('Impossible to create directories for cache files: ' . $this->path, 404);

		// Generate cache file
		file_put_contents(DIR . 'cache/' . $this->path .'/'. $this->filename .'.cache', $content);
		
		return $this;
	}
	
	public function load()
	{
		// Set cache file path
		$full_path = DIR . 'cache/' . $this->path .'/'. $this->filename .'.cache';
		
		// If cache file does not exist
		if(! is_file($full_path))
			return false;
		
		// Load cache file
		require($full_path);
		
		return true;
	}
	
	public function clean($directory, $file = null)
	{
		// Set directory path
		$dir_path = DIR . 'cache/' . $directory;
		
		// If directory path has / final
		if(substr($dir_path, -1) == '/')
			// Remove / final
			$dir_path = substr($dir_path, 0, strlen($dir_path) - 1);
		
		// If we need to clean a cache file
		if(!empty($file))
		{
			// Set file path
			$file_path = $dir_path .'/'. $file .'.cache';
			
			if(! is_file($file_path))
				return false;
			
			// Exception if delete cache file fails
			if(! @unlink($file_path))
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