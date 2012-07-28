<?php

namespace Sea\Core;
use Sea\Core\Components\AutoloaderInterface;
use Sea\App\Components\ComponentInjector;
use Sea\Core\Components\Routing\RouterInterface;

/**
 * Application class handles every request
 */
class Application
{	
	public function __construct()
	{}
	
	public function init(AutoloaderInterface $autoloader)
	{
		ob_start();
		
		try
		{
			require(\Sea\DIR . 'config/application.php');
			require(\Sea\DIR . 'config/boot.php');
			
			$this->registerAutoloader($autoloader);
			
			$injector = $this->createComponentInjector();

			$request = $this->createRequest($injector->getClassName('request'));
			$injector->set('request', $request);

			$router = $injector->get('router');
			$router->loadRoutes('config/routes.php');

			$context = $router->getContext($request);
			$injector->set('context', $context);

			$controller = $context->getController();
			$controller->setInjector($injector);
			$controller->init($context->getActionName(), $context->getArguments());

			$templating = $injector->get('templating');
			$templating->render($controller->getView(), $controller->getData());
		}
		
		catch (\Exception $e)
		{
			ob_clean();

			$this->handleException($e, $injector);
		}
		
		ob_end_flush();
 	}

	private function registerAutoloader($autoloader)
	{
		$vendors = require(\Sea\DIR . 'config/vendors.php');
		$autoloader->vendors($vendors);
		
		$autoloader->register();
	}

	private function createComponentInjector()
	{
		return new ComponentInjector();
	}

	private function createRequest($requestClass)
	{	
		return $requestClass::createFromGlobals();
	}

	private function handleException($e, $injector = null)
	{
		try
		{
			if($injector === null)
				throw $e;

			$templating = $injector->get('templating');
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
