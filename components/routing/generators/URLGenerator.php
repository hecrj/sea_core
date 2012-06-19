<?php

namespace Core\Components\Routing\Generators;
use Core\Components\Routing\RouterInterface;
use Core\Components\Routing\Routes\RouteCompilerInterface;
use Core\Components\Routing\ContextInterface;

class URLGenerator implements URLGeneratorInterface
{
	private $router;
	private $compiler;
	private $context;

	public function __construct(RouterInterface $router, RouteCompilerInterface $compiler, ContextInterface $context)
	{
		$this->router = $router;
		$this->compiler = $compiler;
		$this->context = $context;
	}

	public function generate($name, $arguments = array(), $moduleName = null)
	{
		if($moduleName === null)
			$moduleName = $this->context->getModuleName();

		$route = $this->router->getRoute($name, $moduleName);
		$compiled = $this->compiler->compile($route);

		return $this->generateURL($compiled, $arguments);
	}

	private function generateURL($compiled, $arguments)
	{
		$url = '';

		foreach($compiled->getTokens() as $token)
		{
			if($token[0] == '/')
				$url .= $token;
			else
				$url .= '/'. $arguments[$token];
		}

		return $url;
	}
}
