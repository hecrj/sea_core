<?php

namespace Core;
use Core\Components\Request;
use Core\Components\DynamicInjector;
use Core\Components\Cache;

### View class
class View
{	
	/**
	 * Contains controller data as var_name => value
	 *
	 * @var array
	 */
	private $request;
	private $controller;
	private $injector;
	
	/**
	 * Constructs a view.
	 *
	 * When controller action, called by FrontController, has been finished, a View is instantiated.
	 * Then @param $data, which contains all data necessary for View, is passed to the constructor by
	 * Controller and it's assigned to @var $data in View object.
	 *
	 * @param object $request       Core\Components\Request    A request object
	 * @param object $controller    Core\Controller            A controller object
	 * @return View
	 */
	public function __construct(Request $request, Controller $controller, DynamicInjector $injector)
	{
		// Set view request
		$this->request = $request;
		
		// Set view controller
		$this->controller = $controller;
		
		// Set helper injector
		$this->injector = $injector;
	}
	
	public function init()
	{
		// If Request is an AJAX request
		if($this->request->isAjax())
			// Render only the view, without layout
			$this->render();

		// If Request is not an AJAX request
		else
			// Load layout associated to controller
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
		$view = $this;
		
		// Set layout path
		$layout_file = DIR_VIEWS . 'layouts/' . $this->controller->getLayout() . '.html.php';
		
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
	 * $this->render('users/login_form'); --> Will load 'users/_login_form.php.html' partial file
	 *
	 * @param string $partial Name of the partial file to load. If it's null, the view is loaded.
	 * @param array $options Hash of options, like collection set and cache directory and file in an array.
	 */
	public function render($partial = null, Array $options = null)
	{
		$view = $this;
		
		// Normal view if it's empty
		if(empty($partial))
			$file = DIR_VIEWS . $this->controller->getView() .'.html.php';
	
		// Partial if it isn't empty
		else
		{
			// Get partial name with preg_split
			list(/* EMPTY */, $path, $partial_name) = preg_split('/(.*\/)?([^\/]+)/', $partial, 0, PREG_SPLIT_DELIM_CAPTURE);
		
			// Set file path of partial: _partial_name
			$file = DIR_VIEWS . $path . '_'.$partial_name . '.html.php';
		}
				
		// If cache is set
		if($options['cache'] instanceof Cache)
		{
			if(!$options['cache']->load())
				$options['cache']->flush()->generate($file);
		}
		
		// If cache is not set
		else
		{
			// ERROR 404 if the file isn't found
			if(!is_file($file))
				throw new \RuntimeException('The requested partial or view file doesn\'t exist in: <strong>'.$file.'</strong>');
			
			// If collection is not defined
			if(!isset($options['collection']))
				// Iterate over $data and set variables for layout
				foreach($this->controller->getData() as $key => $value)
					$$key = $value;
			
			// If collection is defined
			else
				// Set collection shortcut
				$collection = $options['collection'];
					
			// Render file
			require($file);
		}
	}
	
	public function css_tag($path_file, $media = 'screen')
	{
		return '<link href="/css/' . $path_file . '.css" rel="stylesheet" type="text/css" media="'.$media.'" />
	';
	}
	
	public function js_tag($path_file)
	{
		return '<script src="/js/' . $path_file . '.js" type="text/javascript"></script>
	';
	}
	
}

?>
