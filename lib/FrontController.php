<?php

### FrontController class
class FrontController
{
	
	static function init()
	{
		try
		{
		
			// Initialize Router
			Router::init();
			
			// Uncomment to make index.php unaccessible (Recommended for production)
			# To404Unless(strpos($_SERVER['REQUEST_URI'], 'index.php') === FALSE);
		
			// Initialize components
			Request::init(); // Request component
			Cookie::init(); // Cookie component
			Session::init(); // Session component
			Security::init(); // Security component
			Auth::init(); // Auth component
		
			// Setting controller path
			$controller_file	=	DIR_CONTROLLERS . Router::$controller_name . '.php';
		
			// Exception if controller file doesn't exist
			ExceptionUnless(is_file($controller_file), 'Controller: <strong>'. $controller_file .'</strong> not found!');

			// Require the controller
			require_once($controller_file);
				
			// Instance the controller
			$Controller = new Router::$controller_name;
			
			// Initialize the controller
			$Controller->init(Router::$action);
		}
		catch (Exception $e)
		{
			// Clean output buffering
			ob_clean();
			
			// Require error page
			if(! @include(DIR .'public/'. $e->getCode() .'.php'))
				echo 'The requested '. $e->getCode() .' error page does not exist!';
			
			// End and flush output buffering
			ob_end_flush();
		}
		
 	}
}

?>
