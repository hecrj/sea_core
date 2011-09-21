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
	private $componentInjector;
	private $controller;
	private $request;
	private $route;
	private $router;
	private $templating;
	
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
			require(DIR . 'config/application.php');
			
			$this->initAutoloader();
			
			$this->componentInjector = new $this->classes['ComponentInjector'];
			$this->initRouter();
			$this->initController();
		}
		
		catch (\Exception $e)
		{
			ob_clean();
			
			if(! @include(DIR .'app/views/exceptions/'. ($e->getCode() ? : '404') .'.html.php'))
				echo 'The requested '. $e->getCode() .' error page does not exist!<br />'.$e->getMessage().'<br />'.
				$e->getTraceAsString();
		}
		
		ob_end_flush();
 	}

	private function initAutoloader()
	{
		require(DIR . 'core/components/Autoloader.php');
		$this->autoloader = new $this->classes['Autoloader'];
		
		require(DIR . 'config/vendors.php');
		foreach($vendors as $file => $path)
			$this->autoloader->vendor($file, $path);
		
		require(DIR . 'config/boot.php');
		$this->autoloader->register();
	}
	
	private function initRouter()
	{
		$this->router = $this->componentInjector->get('router');
		$requestClass = $this->classes['Request'];
		$this->request = $requestClass::createFromGlobals();
		
		$this->componentInjector->set('request', $this->request);
		
		require(DIR . 'config/routes.php');
		$this->router->setRules($routeRules);
	}
	
	private function initController()
	{
		list($controllerName, $controllerAction,
			$controllerArguments) = $this->router->getControllerDataFrom($this->request);
		
		$controllerBaseClass = $this->classes['Controller'];
		$controllerClassName = $controllerBaseClass::getControllerClassName($controllerName);
		
		$this->controller = new $controllerClassName($this->componentInjector);
		$this->controller->init($controllerAction, $controllerArguments);
		
		$this->componentInjector->get('templating')->render($this->controller->getView(), $this->controller->getData());
	}
	
}
