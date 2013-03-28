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

 	public function respond(Request $request, $routesPath)
 	{
 		ob_start();

 		try
		{
			$components = new ComponentInjector();
			$components->set('request', $request);

			$router = $components->get('router');
			$router->setRoutesFrom($routesPath, $components);

			$context = $router->getContext($request);
			$components->set('context', $context);

			$controller = $context->getController();
			$controller->setInjector($components);
			$controller->init($context->getActionName(), $context->getArguments());

			$templating = $components->get('templating');
			$templating->render($controller->getView(), $controller->getData());
		}
		
		catch (\Exception $e)
		{
			ob_clean();

			$this->handleException($e, $components);
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
