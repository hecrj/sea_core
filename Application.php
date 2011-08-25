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
			$this->initAutoloader();
			$this->initBasicComponents();
			$this->initConstants();
			$this->initRouter();
			$this->initController();
			$this->initTemplate();
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
		$requestClass = $this->classes['Request'];
		$this->request = $requestClass::createFromGlobals();
		
		$this->componentInjector = new $this->classes['ComponentInjector'];
		$this->componentInjector->set('request', $this->request);
	}
	
	private function initConstants()
	{
		if($this->request->isSecure())
			$httpUrl = 'http://www.'. WEB_DOMAIN;
		else
			$httpsUrl = 'https://www.'. WEB_DOMAIN;
		
		define('HTTP_URL', $httpUrl);
		define('HTTPS_URL', $httpsUrl);
	}
	
	private function initRouter()
	{	
		$this->router = $this->componentInjector->get('router');
		
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
	}
	
	private function initTemplate()
	{
		$helperInjector = new $this->classes['HelperInjector']($this->componentInjector);
		$this->templating = new $this->classes['Templating']($helperInjector, new $this->classes['TemplateFinder']);
		$this->templating->render($this->controller->getView(), $this->controller->getData());
	}
	
}

?>
