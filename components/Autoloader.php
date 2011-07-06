<?php

namespace Core\Components;

# Autoloader class
class Autoloader
{
	private $namespace = array();
	
	public function __construct()
	{
		spl_autoload_register(array($this, 'load'));
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
		$this->namespace[$namespace] = $path;
	}
	
	public function vendor($path, $main, $config = null)
	{
		if(! is_file(DIR . 'vendor/'. $path .'/'. $main))
			throw new \InvalidArgumentException('Vendor main file not found in: vendor/'. $path .'/'. $main);
		
		require(DIR . 'vendor/'. $path .'/'. $main);
		
		if(! empty($config))
		{
			if(! is_file(DIR . 'config/'. $config))
				throw new \InvalidArgumentException('Config vendor file not found in: config/'. $config);
			
			require(DIR . 'config/' . $config);
		}
		
		return true;
	}
}

?>
