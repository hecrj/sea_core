<?php

### View class
class View
{	
	private $data;
	
	public function __construct(Array $data)
	{
		// Set data given from controller
		$this->data = $data;
	}
	
	public function load($file)
	{	
		// Set layout path
		$layout_file = DIR_VIEWS . 'layouts/' . $file . '.html.php';
		
		// ERROR 404 if the layout doesn't exist
		To404Unless(is_file($layout_file), 'The requested layout file doesn\'t exist in: <strong>'.$layout_file.'</strong>');
		
		// Iterate over $data and set variables for layout
		foreach($this->data as $key => $value)
			$$key = $value;
			
		// Render layout
		require($layout_file);
	}
	
	public function render($partial = null)
	{
		// Normal view if it's empty
		if(empty($partial))
			$file = DIR_VIEWS . Router::$controller . '/' . Router::$action . '.html.php';
		// Partial if it isn't empty
		else
		{
			// Get partial name with preg_split
			list($path, $partial_name) = preg_split('/[.*\/]?([^\/]+)/', $partial, 0, PREG_SPLIT_DELIM_CAPTURE);
			// Set file path of partial: _partial_name
			$file = DIR_VIEWS . $path . '_'.$partial_name . '.html.php';
		}
		
		// ERROR 404 if the file isn't found
		To404Unless(is_file($file), 'The requested partial or view file doesn\'t exist in: <strong>'.$file.'</strong>');
		
		// Iterate over $data and set variables for layout
		foreach($this->data as $key => $value)
			$$key = $value;
		
		// Render file
		require($file);
	}
	
}

?>
