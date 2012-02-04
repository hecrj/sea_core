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
	private $router;
	
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
			
			try
			{
				$templating = $this->componentInjector->get('templating');
				$templating->clean();
				$templating->render('exceptions/'. ($e->getCode() ? : '404'), array('e' => $e));
			}
			
			catch (\Exception $e)
			{
				echo '<h1>A critical error has occurred:</h1>';
				echo '<p>'. $e->getMessage() .'</p>';
				echo $e->getTraceAsString();
			}

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
		$this->router->enroute($this->request);
		
		$moduleName = $this->request->get('moduleName');
		$controllerName = $this->request->get('controllerName');
		$controllerAction = $this->request->get('controllerAction');
		
		$controllerBaseClass = $this->classes['Controller'];
		$controllerClass = $controllerBaseClass::getControllerClass($moduleName, $controllerName);
		
		$this->controller = new $controllerClass($this->componentInjector);
		$this->controller->setView($moduleName .'/'. $controllerName . '/' . $controllerAction);
		$this->controller->init($controllerAction, $this->request->get('controllerArguments'));
		
		$this->componentInjector->get('templating')->render($this->controller->getView(), $this->controller->getData());
	}
	
}
