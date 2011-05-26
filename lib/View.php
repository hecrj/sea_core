<?php

### View class
class View
{	
	/**
	 * Contains controller data as var_name => value
	 *
	 * @var array
	 */
	private $data;
	private $view;
	
	/**
	 * Constructs a view.
	 *
	 * When controller action, called by FrontController, has been finished, a View is instantiated.
	 * Then @param $data, which contains all data necessary for View, is passed to the constructor by
	 * Controller and it's assigned to @var $data in View object.
	 *
	 * @param array $data Hash containing names and values of variables needed to be accessible in View
	 * @return View
	 */
	public function __construct($view, Array $data)
	{
		// Set view
		$this->view = $view;
		// Set data given from controller
		$this->data = $data;
	}
	
	/**
	 * Loads layout passed.
	 *
	 * After instantiate a View, the Controller calls load function to render its related layout.
	 *
	 * @param string $file Name of the layout file to load
	 */
	public function load($file)
	{	
		// Set layout path
		$layout_file = DIR_VIEWS . 'layouts/' . $file . '.html.php';
		
		// Exception if the layout doesn't exist
		ExceptionUnless(is_file($layout_file), 'The requested layout file doesn\'t exist in: <strong>'.$layout_file.'</strong>');
		
		// Iterate over $data and set variables for layout
		foreach($this->data as $key => $value)
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
			// Normal view if it's empty
			if(empty($partial))
				$file = DIR_VIEWS . $this->view . '.html.php';
		
			// Partial if it isn't empty
			else
			{
				// Get partial name with preg_split
				list(/* EMPTY */, $path, $partial_name) = preg_split('/(.*\/)?([^\/]+)/', $partial, 0, PREG_SPLIT_DELIM_CAPTURE);
			
				// Set file path of partial: _partial_name
				$file = DIR_VIEWS . $path . '_'.$partial_name . '.html.php';
			}
					
			// If cache is not needed
			if(!isset($options['cache']))
			{
				// ERROR 404 if the file isn't found
				ExceptionUnless(is_file($file), 'The requested partial or view file doesn\'t exist in: <strong>'.$file.'</strong>');
				
				// If collection is not defined
				if(!isset($options['collection']))
					// Iterate over $data and set variables for layout
					foreach($this->data as $key => $value)
						$$key = $value;
				
				// If collection is defined
				else
					// Set collection shortcut
					$collection = $options['collection'];
						
				// Render file
				require($file);	
			}
			
			// If cache is needed and it does not load
			elseif(! Cache::load($options['cache'][0], $options['cache'][1]))
				// Generate and flush the cache
				Cache::generate($this->data, $file, $options['cache'][0], $options['cache'][1], true);
	}
	
}

?>
