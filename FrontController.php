<?php

namespace Core;

### FrontController class
class FrontController
{
	
	public static function init(Array $classes)
	{
		// Start otuput buffering
		ob_start();
		
		try
		{
			
			// Constants needed first
			require(DIR . 'core/Constants.php');
		
			// Autoload class
			require(DIR . 'core/components/Autoloader.php');
		
			$loader = new $classes['Autoloader'];
		
			// Application configuration
			require(DIR . 'config/application.php');

			// If orm has to be active
			if(ORM_ACTIVE)
				// Initialize ORM
				$loader->vendor(ORM_PATH, ORM_MAIN_FILE, ORM_CONFIG_FILE);
			
			// Boot initializing
			require(DIR . 'config/boot.php');
			
			// New Request component from globals
			$request = new $classes['Request']($_SERVER['HTTP_HOST'], $_SERVER['PATH_INFO'], $_SERVER['REQUEST_METHOD'], $_SERVER['HTTP_X_REQUESTED_WITH'], $_GET, $_POST, $_FILES);
			
			// New Router component
			$router = new $classes['Router']($request);
			
			// Set a dynamic injector for the components!
			$cinjector = new $classes['ComponentInjector'];
			
			// Set the request component
			$cinjector->set('request', $request);
			
			// Get Controller class name from Router
			$controller_class_name = $router->getControllerClassName();
			
			// Instantiate controller with component injector
			$controller = new $controller_class_name($cinjector);
			
			// Initialize controller
			$controller->init($router->getControllerAction(), $router->getControllerArguments());
			
			// New View for request and controller and pass a HelperInjector with the component injector
			$view = new $classes['View']($request, $controller, new $classes['HelperInjector']($cinjector));
			
			// Initialize view
			$view->init();
		}
		
		catch (\Exception $e)
		{
			// Clean output buffering
			ob_clean();
			
			// Require error page
			if(! @include(DIR .'public/'. ($e->getCode() ? : '404') .'.php'))
				echo 'The requested '. $e->getCode() .' error page does not exist!<br />'.$e->getMessage();
		}
		
		// End and flush output buffering
		ob_end_flush();
 	}
	
}

?>
