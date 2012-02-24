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
	private $injector;
	private $controller;
	private $request;
	private $router;
	
	public function __construct(Array $classes)
	{
		$this->classes = $classes;
	}
	
	/**
	 * Creates basic application objects and starts application logic
	 */
	public function init()
	{
		ob_start();
		
		try
		{
			require(DIR . 'config/application.php');
			require(DIR . 'config/boot.php');
			
			$this->initAutoloader();
			
			$this->injector = new $this->classes['ComponentInjector'];
			
			$this->initRouter();
			$this->initController();
			$this->initTemplating();
		}
		
		catch (\Exception $e)
		{
			ob_clean();

			$this->handleException($e);
		}
		
		ob_end_flush();
 	}

	private function initAutoloader()
	{
		require(DIR . 'core/components/Autoloader.php');
		$vendors = require(DIR . 'config/vendors.php');
		
		$this->autoloader = new $this->classes['Autoloader'];
		$this->autoloader->vendors($vendors);
		$this->autoloader->register();
	}
	
	private function initRouter()
	{
		$router = $this->injector->get('router');
		$this->request = $this->getRequest();
		
		require(DIR . 'config/routes.php');
		
		$this->router = $router;
	}
	
	private function getRequest()
	{
		$requestClass = $this->classes['Request'];
		$request = $requestClass::createFromGlobals();
		$this->injector->set('request', $request);
		
		return $request;
	}
	
	private function initController()
	{
		$context = $this->router->getContext($this->request);
		$this->injector->set('context', $context);

		$controller = $this->router->getController($context);
		$controller->setInjector($this->injector);
		$controller->init($context->getActionName(), $context->getArguments());

		$this->controller = $controller;
	}
	
	private function initTemplating()
	{
		$templating = $this->injector->get('templating');
		$templating->render($this->controller->getView(), $this->controller->getData());
	}

	private function handleException($e)
	{
		try
		{
			$templating = $this->injector->get('templating');
			$templating->clean();
			$templating->render('exceptions/'. ($e->getCode() ? : '404'), array('e' => $e));
		}
			
		catch (\Exception $new)
		{
			echo '<h1>A critical error has occurred:</h1>';
			echo '<p>'. $e->getMessage() .'</p>';
			echo $e->getTraceAsString();
		}
	}
	
}
