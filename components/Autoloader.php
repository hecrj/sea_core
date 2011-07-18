<?php

namespace Core\Components;

# Autoloader class
class Autoloader
{
	private $namespaces = array();
	
	public function __construct()
	{}
	
	public function register($prepend = true)
	{
		spl_autoload_register(array($this, 'load'), true, false);
	}	
	
	public function load($name)
	{
		if(strpos($name, '\\') !== false)
		{
			$namespaces = explode('\\', $name);
			$class_name = array_pop($namespaces);
			
			if(isset($this->namespaces[$namespaces[0]]))
				$namespaces[0] = $this->namespaces[$namespaces[0]];
			
			$path = strtolower(implode(DIRECTORY_SEPARATOR, $namespaces)) . DIRECTORY_SEPARATOR . $class_name. '.php';
		}
		else
			$path = 'app/models/'.$name.'.php';
		
		if(! is_file(DIR . $path))
			throw new \Exception('Unable to load class: <strong>'. $name .'</strong>', 404);
		
		require(DIR . $path);
		
		return true;
	}
	
	public function set($namespace, $path)
	{
		$this->namespaces[$namespace] = $path;
	}
	
	public function vendor($file, $path)
	{
		if(! is_file(DIR . 'vendor/'. $path .'/'. $file .'.php'))
			throw new \InvalidArgumentException('Vendor main file not found in: vendor/'. $path .'/'. $main.'.php');
		
		require(DIR . 'vendor/'. $path .'/'. $file .'.php');
		
		if(is_file(DIR . 'config/vendor/'. $file .'.php'))
			require(DIR . 'config/vendor/'. $file .'.php');
				
		return true;
	}
}

?>
