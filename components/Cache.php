<?php

namespace Core\Components;

# Cache component
class Cache 
{
	private $data = array();
	private $path;
	private $filename;
	private $fullPath;
	private $exists = true;
	private $broken = false;
	
	public function __construct($path, $filename)
	{
		$this->path = $path;
		$this->filename = $filename;
		$this->fullPath = DIR . 'cache/' . $path .'/'. $filename .'.cache';
	}
	
	public function set($key, $value)
	{
		$this->data[$key] = $value;
		
		return $this;
	}
	
	public function get($key)
	{
		if(! isset($this->data[$key]))
			return null;
		
		return $this->data[$key];
	}
	
	public function setData(Array $data)
	{
		if(empty($this->data))
			$this->data = $data;
		
		return $this;
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
		
		$this->exists = true;
		
		return $this;
	}
	
	public function dynamic($varName)
	{
		return '<?php echo $'. $varName .' ?>';
	}
	
	public function load()
	{
		// If cache file does not exist
		if(!$this->exists or $this->filename == null or !is_file($this->fullPath))
			return $this->exists = false;
		

		require($this->fullPath);
		
		echo "\n".'<!-- Loaded '. (microtime(true) - $GLOBALS['time']) .' -->'."\n";
		
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