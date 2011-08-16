<?php

namespace Core;
use Core\Components\Request;
use Core\Components\DynamicInjector;
use Core\Components\Cache;

class View
{	
	private $controller;
	private $injector;
	private $path;
	private $partials = array();
	
	public function __construct(Controller $controller, DynamicInjector $injector)
	{	
		$this->controller = $controller;
		$this->injector = $injector;
		$this->path = $this->controller->getView();
	}
	
	public function init($isAjax = false)
	{
		if($isAjax)
			$this->render();

		else
			$this->load();
	}
	
	protected function get($name)
	{
		return $this->injector->get($name);
	}
	
	/**
	 * Loads layout passed.
	 *
	 * After instantiate a View, the Controller calls load function to render its related layout.
	 *
	 * @param string $file Name of the layout file to load
	 */
	private function load()
	{	
		// Set layout path
		$layout_file = DIR . 'app/views/layouts/' . $this->controller->getLayout() . '.html.php';
		
		// Exception if the layout doesn't exist
		if(! is_file($layout_file))
			throw new \RuntimeException('The requested layout file doesn\'t exist in: <strong>'.$layout_file.'</strong>', 404);
		
		// Iterate over $data and set variables for layout
		foreach($this->controller->getData() as $key => $value)
			$$key = $value;
			
		// Render layout
		require($layout_file);
	}
	
	/**
	 * Renders a view or a partial.
	 *
	 * In the layouts is very usual to call this method. If is needed to load the view related with Controller,
	 * then this method must be called without @param. If is needed to load a partial, then this method must be
	 * called with the file name of the partial.
	 * 
	 * EXAMPLE: If we access this URL http://www.domain.com/pages/contact
	 * // Load a view
	 * $this->render(); --> Will load 'pages/contact.html.php' view file
	 * 
	 * // Load a partial
	 * $this->render('users/login_form'); --> Will load 'users/_login_form.html.php' partial file
	 *
	 * @param string $partial Name of the partial file to load. If it's null, the view is loaded.
	 * @param mixed $dataCache Additional data in an array or a Cache instance.
	 */
	public function render($partial = null, $dataCache = null)
	{
		$view = $this;
		
		// Normal view if it's empty
		if(empty($partial))
			$file = DIR . 'app/views/' . $this->path .'.html.php';
	
		// Partial if it isn't empty
		else
		{
			// Get partial name with preg_split
			list(/* EMPTY */, $path, $partial_name) = preg_split('/(.*\/)?([^\/]+)/', $partial, 0, PREG_SPLIT_DELIM_CAPTURE);
		
			// Set file path of partial: _partial_name
			$file = DIR . 'app/views/' . $path . '_'.$partial_name . '.html.php';
		}
				
		// If cache is set
		if($dataCache instanceof Cache)
		{
			$cache = $dataCache;
			
			if(!$cache->load())
				$cache->generate($file)->load();
		}
		
		// If cache is not set
		else
		{
			$data = $dataCache;
			
			// ERROR 404 if the file isn't found
			if(!is_file($file))
				throw new \RuntimeException('The requested partial or view file doesn\'t exist in: <strong>'.$file.'</strong>');
			
			// If collection is not defined
			if($data == null)
				$data = $this->controller->getData();
			
			// Iterate over $data and set variables for layout
			foreach($data as $key => $value)
				$$key = $value;
					
			// Render file
			require($file);
		}
	}
	
	public function block($controllerName, $action, Array $arguments = null)
	{
		try
		{
			$controller = $this->controller;
			$blockControllerClassName = $controller::getControllerClassName($controllerName);
			
			$blockController = new $blockControllerClassName($this->injector->get('componentInjector'));
			$blockController->initBlock($action, (array)$arguments);
			
			$view = new self($blockController, $this->injector);
			$view->render();
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
	
	public function css_tag($path_file, $media = 'screen')
	{
		return '<link href="/css/' . $path_file . '.css" rel="stylesheet" type="text/css" media="'.$media.'" />
	';
	}
	
}

?>
