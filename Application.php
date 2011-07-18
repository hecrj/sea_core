<?php

namespace Core;

/**
 * FrontController class handles every request
 * 
 * @author Héctor Ramón Jiménez
 */
class Application
{
	private $classes;
	private $autoloader;
	private $request;
	private $route;
	private $router;
	private $componentInjector;
	private $controller;
	private $view;
	
	public function __construct(Array $classes)
	{
		$this->classes = $classes;
	}
	
	/**
	 * Creates basic application objects and starts application logic
	 * 
	 * @param array $classes Collection of classes
	 */
	public function init()
	{
		ob_start();
		
		try
		{
			$this->initAutoloader();
			$this->initBasicComponents();
			$this->initRouter();
			$this->initController();
			$this->initView();
		}
		
		catch (\Exception $e)
		{
			ob_clean();
			
			if(! @include(DIR .'app/views/exceptions/'. ($e->getCode() ? : '404') .'.html.php'))
				echo 'The requested '. $e->getCode() .' error page does not exist!<br />'.$e->getMessage();
		}
		
		ob_end_flush();
 	}
	
	private function initAutoloader()
	{
		require(DIR . 'core/components/Autoloader.php');
		$this->autoloader = new $this->classes['Autoloader'];
		
		require(DIR . 'config/application.php');
		
		foreach($vendors as $file => $path)
			$this->autoloader->vendor($file, $path);
		
		require(DIR . 'config/boot.php');
		$this->autoloader->register();
	}
	
	private function initBasicComponents()
	{
		$this->componentInjector = new $this->classes['ComponentInjector'];
		$this->request = new $this->classes['Request']($_SERVER['HTTPS'], $_SERVER['REQUEST_METHOD'], $_SERVER['HTTP_X_REQUESTED_WITH'], $_GET, $_POST, $_FILES);
		
		$this->componentInjector->set('request', $request);
	}
	
	private function initRouter()
	{
		$this->route = new $this->classes['Route']($_SERVER['HTTPS'],
				$_SERVER['HTTP_HOST'], $_SERVER['PATH_INFO']);
		
		require(DIR . 'config/routes.php');
		$matcher = new $this->classes['RouteMatcher']($routes);
		$extractor = new $this->classes['RouteExtractor']($routes);
		
		$this->router = new $this->classes['Router']($matcher, $extractor);
	}
	
	private function initController()
	{
		list($controllerName, $controllerAction,
			$controllerArguments) = $this->router->getControllerDataFrom($this->route);
		
		$controllerClassName = $this->getControllerClassName($controllerName);
		$this->controller = new $controllerClassName($this->componentInjector);
		
		$this->controller->init($controllerAction, $controllerArguments);
	}
	
	private function initView()
	{
		$this->view = new $this->classes['View']($request, $controller,
				new $this->classes['HelperInjector']($this->componentInjector));
		
		$this->view->init();
	}
	
	private function getControllerClassName($controllerName)
	{
		return 'App\\Controllers\\' . ucwords($controllerName) . 'Controller';
	}
	
}

?>
