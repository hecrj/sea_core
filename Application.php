<?php

namespace Sea;
use App\Components\ComponentInjector;
use Sea\Components\Routing\Request;
/**
 * Application class handles every request
 */
class Application
{
	public function __construct()
	{
	}

 	public function respond(Request $request, Array $routes)
 	{
 		ob_start();

 		try
		{
			$injector = new ComponentInjector();
			$injector->set('request', $request);

			$router = $injector->get('router');
			$router->setRoutes($routes);

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
