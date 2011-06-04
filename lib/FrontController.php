<?php

### FrontController class
class FrontController
{
	
	static function init()
	{
		// Start otuput buffering
		ob_start();
		
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
			
			// Get and initialize controller to request
			Router::getControllerFor($_SERVER['HTTP_HOST'], $_SERVER['PATH_INFO'])->init(Router::getAction(), Router::getArguments());
		}
		
		catch (Exception $e)
		{
			// Clean output buffering
			ob_clean();
			
			// Require error page
			if(! @include(DIR .'public/'. $e->getCode() .'.php'))
				echo 'The requested '. $e->getCode() .' error page does not exist!<br />'.$e->getMessage();
		}
		
		// End and flush output buffering
		ob_end_flush();
 	}
}

?>
