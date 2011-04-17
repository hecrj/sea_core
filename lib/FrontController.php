<?php

### FrontController class
class FrontController {
	
	static function init()
	{
		// Uncomment to make index.php unaccessible (Recommended for production)
		# To404Unless(strpos($_SERVER['REQUEST_URI'], 'index.php') === FALSE);
		
		// Analyze route and set request data
		Router::analyze($_SERVER['PATH_INFO']);
		
		// Getting controller name
		$controller			=	ucwords(Router::$controller) . 'Controller';
		// Setting controller path
		$controller_file	=	DIR_CONTROLLERS . $controller . '.php';
		
		try
		{
			// 404 if controller file doesn't exist
			To404Unless(is_file($controller_file), 'Controller: <strong>'. $controller_file .'</strong> not found!');

			// Require the controller
			require_once($controller_file);
				
			// Instance the controller
			$Controller = new $controller();
			
			// Initialize the controller
			$Controller->init(Router::$action);
		}
		catch (Exception $e)
		{
			// If Exception is caught --> Show errors
			echo '<p>Some errors have ocurred during your request:</p>';
			echo '<p>'. $e->getMessage() .'</p>';
		}
		
 	}
}

?>
