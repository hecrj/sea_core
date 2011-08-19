<?php

namespace Core;
use Core\Components\DynamicInjector;
use Core\Components\Cache;

class View
{	
	private $injector;
	private $data = array();
	private $partials = array();
	private $extend;
	
	public function __construct(DynamicInjector $injector)
	{	
		$this->injector = $injector;
	}
	
	public function extend($extend)
	{
		$this->extend = $extend;
		
		return $this;
	}
	
	protected function get($name)
	{
		return $this->injector->get($name);
	}
	
	protected function set($key, $value)
	{
		$this->data[$key] = $value;
		
		return $this;
	}
	
	protected function output($key)
	{
		if(! isset($this->data[$key]))
			return null;
		
		return $this->data[$key];
	}
	
	public function partial($name, $path = null, $dataCache = null)
	{
		if($path == null)
			$this->loadPartial($name);
		
		$this->partials[$name] = array($path, $dataCache);
		
		return $this;
	}
	
	private function loadPartial($name)
	{
		if(! isset($this->partials[$name]))
			return false;
		
		$this->render($this->partials[$name][0], $this->partials[$name][1]);
	}
	
	public function load($viewPath, Array $viewData = null)
	{	
		// Set layout path
		$viewPath = DIR . 'app/views/'. $viewPath .'.html.php';
		
		// Exception if the layout doesn't exist
		if(! is_file($viewPath))
			throw new \RuntimeException('The requested view file doesn\'t exist in: <strong>'.$viewPath.'</strong>', 404);
		
		$this->extend = null;
		
		ob_start();
		
		$this->contextRequire($viewPath, (array)$viewData);
		
		if($this->extend == null)
			ob_end_flush();
		
		else
		{
			$this->data['content'] = ob_get_contents();
		
			ob_end_clean();
			
			$this->load($this->extend);
		}
		
	}
	
	public function render($partial = null, $dataCache = null)
	{	

		list(/* EMPTY */, $path, $partial_name) = preg_split('/(.*\/)?([^\/]+)/', $partial, 0, PREG_SPLIT_DELIM_CAPTURE);
		$file = DIR . 'app/views/' . $path . '_'.$partial_name . '.html.php';
				
		if($dataCache instanceof Cache)
		{
			$cache = $dataCache;
			
			if(!$cache->load())
				$cache->generate($file)->load();
		}
		else
		{
			$data = $dataCache;

			if(!is_file($file))
				throw new \RuntimeException('The requested partial or view file doesn\'t exist in: <strong>'.$file.'</strong>');
			
			$this->contextRequire($file, (array)$data);
		}
	}
	
	private function contextRequire($file, $data)
	{	
		extract($data);	
		require($file);
	}
	
	public function block($controllerName, $action, Array $arguments = null)
	{
		try
		{
			$blockControllerClassName = Controller::getControllerClassName($controllerName);
			
			$blockController = new $blockControllerClassName($this->injector->get('componentInjector'));
			$blockController->initBlock($action, (array)$arguments);
			
			$this->render($blockController->getView(), $blockController->getData());
		}
		catch(\Exception $e)
		{
			$exceptionView = DIR . 'app/views/exceptions/block.html.php';
			
			if(is_file($exceptionView))
				require($exceptionView);
			else
				throw $e;
		}
	}
	
}

?>
