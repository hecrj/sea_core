<?php

namespace Sea\Core\Components;

require_once(\Sea\DIR . 'core/components/AutoloaderInterface.php');

# Autoloader class
class Autoloader implements AutoloaderInterface
{
	private $namespaces = array();
	
	public function __construct()
	{}
	
	public function register()
	{
		spl_autoload_register(array($this, 'load'), true, false);
	}	
	
	public function load($name)
	{
		if(strpos($name, '\\') !== false)
		{
			$namespaces = explode('\\', $name);
			$class_name = array_pop($namespaces);
			
			if($namespaces[0] == 'Sea')
			{
				array_shift($namespaces);
			}
			else if(isset($this->namespaces[$namespaces[0]]))
			{
				$namespaces[0] = $this->namespaces[$namespaces[0]];
			}
			
			$path = strtolower(implode(DIRECTORY_SEPARATOR, $namespaces)) . DIRECTORY_SEPARATOR . $class_name. '.php';
		}
		else
			$path = 'app/models/'.$name.'.php';
		
		if(! is_file(\Sea\DIR . $path))
			throw new \Exception('Unable to load class: <strong>'. $name .'</strong>', 404);
		
		require(\Sea\DIR . $path);
		
		return true;
	}
	
	public function set($namespace, $path)
	{
		$this->namespaces[$namespace] = $path;
	}
	
	public function vendors(Array $vendors) {
		foreach($vendors as $file => $path)
			$this->vendor($file, $path);
	}
	
	public function vendor($file, $path)
	{
		if(! is_file(\Sea\DIR . 'vendor/'. $path .'/'. $file .'.php'))
			throw new \InvalidArgumentException('Vendor main file not found in: vendor/'. $path .'/'. $file.'.php');
		
		require(\Sea\DIR . 'vendor/'. $path .'/'. $file .'.php');
		
		if(is_file(\Sea\DIR . 'config/vendor/'. $file .'.php'))
			require(\Sea\DIR . 'config/vendor/'. $file .'.php');
				
		return true;
	}
}
