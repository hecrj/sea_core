<?php

namespace Core\Components\Templating;
use Core\Components\DynamicInjector;
use Core\Controller;

class Engine
{	
	private $helperInjector;
	private $finder;
	private $arguments = array();
	private $data = array();
	private $currentTemplate;
	private $parent = array();
	
	public function __construct(DynamicInjector $injector, Finder $finder)
	{	
		$this->helperInjector = $injector;
		$this->finder = $finder;
	}
	
	protected function extend($parent)
	{
		$this->parent[$this->currentTemplate] = $parent;
		
		return $this;
	}
	
	protected function set($key, $value)
	{
		$this->data[$key] = $value;
		
		return $this;
	}
	
	public function get($key)
	{
		if(!isset($this->data[$key]))
			return null;
		
		return $this->data[$key];
	}
	
	public function output($key)
	{
		echo $this->get($key);
	}
	
	protected function helper($name)
	{
		return $this->helperInjector->get($name);
	}
	
	/**
	 * @todo Think about available arguments in parent templates.
	 */
	public function render($template = null, Array $arguments = null)
	{
		if(null === $template)
			return false;
		
		$parentTemplate = $this->currentTemplate;
		$this->currentTemplate = $template;
		
		$path = $this->finder->getPath($template);
		
		if(! is_file($path))
			throw new \RuntimeException('The requested view file doesn\'t exist in: <strong>'. $path .'</strong>', 404);
		
		if(null === $arguments)
			$arguments = $this->arguments[$parentTemplate];
		
		$this->arguments[$template] = $arguments;
		
		ob_start();
		
		try
		{
			$this->requireInContext($path, $arguments);
		}
		catch(\Exception $e)
		{
			ob_end_clean();
			
			throw $e;
		}
		
		if(isset($this->parent[$template]))
		{
			$this->data['content'] = ob_get_contents();
		
			ob_end_clean();
			
			$this->render($this->parent[$template] /*, array() */);
		}
		else
			ob_end_flush();
		
		$this->currentTemplate = $parentTemplate;
	}
	
	private function requireInContext($file, Array $data)
	{	
		extract($data);	
		require($file);
	}
	
	public function block($controllerName, $action, Array $arguments = null)
	{
		try
		{
			$blockControllerClassName = Controller::getControllerClassName($controllerName);
			
			$blockController = new $blockControllerClassName($this->helperInjector->getInjector());
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
