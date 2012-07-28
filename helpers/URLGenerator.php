<?php

namespace Sea\Core\Helpers;
use Sea\Core\Components\Routing\Generators\URLGeneratorInterface;

class URLGenerator implements URLGeneratorInterface
{
	private $generator;

	public function __construct(URLGeneratorInterface $generator)
	{
		$this->generator = $generator;
	}

	public function generate($name, $arguments = array(), $module = null)
	{
		return $this->generator->generate($name, $arguments, $module);
	}
}
