<?php

# Cache helper
class Cache 
{
	public static function generate(Array $data, $cache_file, $directory, $file, $flush = false)
	{
		// Iterate over data array
		foreach($data as $key => $value)
			// Assign key named variables to array values
			$$key = $value;
		
		// Exception if cache template does not exist
		ExceptionUnless(is_file($cache_file), 'The cache file you requested does not exist in: <strong>'. $cache_path .'</strong>');
		
		// Start output buffering
		ob_start();

		// Load file
		require($cache_file);

		// Get contents
		$content = ob_get_contents();

		if($flush)
			// End and flush output buffering
			ob_end_flush();
		else
			// End and clean output buffering
			ob_end_clean();
		
		if(!file_exists(DIR_CACHE . $directory))
			// Make directories for cache files --> Recursive
			mkdir(DIR_CACHE . $directory, 0777, true);

		// Generate cache file
		file_put_contents(DIR_CACHE . $directory .'/'. $file .'.cache', $content);
	}
	
	public static function load($directory, $file)
	{
		// Set cache file path
		$path = DIR_CACHE . $directory .'/'. $file .'.cache';
		
		// If cache file does not exist
		if(! is_file($path))
			return false;
		
		// Load cache file
		require($path);
		
		return true;
	}
	
	public static function clean($directory = null, $file = null)
	{
		// Set directory path
		$dir_path = DIR_CACHE . $directory;
		
		// If directory path has / final
		if(substr($dir_path, -1) == '/')
			// Remove / final
			$dir_path = substr($dir_path, 0, strlen($dir_path) - 1);
		
		// If we need to clean a cache file
		if(!empty($file))
		{
			// Set file path
			$file_path = $dir_path .'/'. $file .'.cache';
			
			// Exception if delete cache file fails
			ExceptionUnless(@unlink($file_path), 'Impossible delete file: <strong>'. $file_path .'</strong>');
		}
		
		// If we need to clean a directory
		else
			// Clean directory recursively
			self::cleanDir($dir_path);
	}
	
	private static function cleanDir($dir_path, $delete = false)
	{
		// If directory does not exist
		if(!file_exists($dir_path))
			return false;

		// Exception if dir path is not a valid path to a directory
		ExceptionUnless(is_dir($dir_path), 'Invalid directory path given: <strong>'. $dir_path .'</strong>');
		
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
				self::cleanDir($new_path, true);
				
			// If is not a directory
			else
				// Exception if delete cache file fails
				ExceptionUnless(@unlink($new_path), 'Impossible delete file: <strong>'. $new_path .'</strong>');
		}
		
		// If we need to delete directory
		if($delete)
			// Exception if delete directory fails
			ExceptionUnless(@rmdir($dir_path), 'Impossible delete directory: <strong>'. $dir_path .'</strong>');
	}
}

?>