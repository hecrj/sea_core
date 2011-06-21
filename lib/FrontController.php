<?php

### FrontController class
class FrontController
{
	
	static function init()
	{	
		// Constants needed first
		require(DIR . 'lib/Constants.php');
		
		// Core functions
		require(DIR . 'lib/Functions.php');

		// View class
		require(DIR . 'lib/View.php');
		
		// Controller class
		require(DIR . 'lib/Controller.php');
		
		// Autoload class
		require(DIR . 'lib/Autoload.php');
		
		// Component class
		require(DIR . 'lib/Component.php');
		
		// Application configuration
		require(DIR . 'config/application.php');

		// If database has to be active
		if(DB_ACTIVE)
		{
			// ActiveRecord library
			require(DIR . 'vendor/php-activerecord/ActiveRecord.php');
			
			// Model class
			require(DIR . 'lib/Model.php');

			// ActiveRecord configuration
			ActiveRecord\Config::initialize(
			function($cfg)
			{
				// Require database configuration
				require(DIR . 'config/database.php');

				// Set path to models directory
				$cfg->set_model_directory(DIR_MODELS);

				// Define connections as protocol url
				foreach($db['connections'] as $connection => $options)
					$connections[$connection] = $options['type'] . '://' . $options['user'] . ':' . $options['password'] . '@' . $options['server'] . '/' . $options['name'] .'?charset=utf8';

				// Set connections
				$cfg->set_connections($connections);

				// Set default connection
				$cfg->set_default_connection($db['default']);
			}
			);
		}
		
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
			
			// Boot initializing
			require(DIR . 'config/boot.php');
			
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
