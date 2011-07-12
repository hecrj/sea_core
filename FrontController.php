<?php

namespace Core;

/**
 * FrontController class handles every request
 * 
 * @author HŽctor Ram—n JimŽnez
 */
class FrontController
{
	/**
	 * Creates basic application objects and starts application logic
	 * 
	 * @param array $classes Collection of classes
	 */
	public static function init(Array $classes)
	{
		// Start otuput buffering
		ob_start();
		
		try
		{
			// Some utilities...
			require(DIR . 'core/Functions.php');
			
			// Autoloader component
			require(DIR . 'core/components/Autoloader.php');
		
			$loader = new $classes['Autoloader'];
		
			// Application configuration
			require(DIR . 'config/application.php');
			
			// Load vendors
			foreach($vendors as $file => $path)
				$loader->vendor($file, $path);
			
			// Boot initializing
			require(DIR . 'config/boot.php');
			
			// Register autoloader
			$loader->register();
			
			// New Request component from globals
			$request = new $classes['Request']($_SERVER['HTTP_HOST'], $_SERVER['PATH_INFO'], $_SERVER['HTTPS'],	$_SERVER['REQUEST_METHOD'], $_SERVER['HTTP_X_REQUESTED_WITH'], $_GET, $_POST, $_FILES);
			
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
			if(! @include(DIR .'app/views/exceptions/'. ($e->getCode() ? : '404') .'.php'))
				echo 'The requested '. $e->getCode() .' error page does not exist!<br />'.$e->getMessage();
		}
		
		// End and flush output buffering
		ob_end_flush();
 	}
	
}

?>
