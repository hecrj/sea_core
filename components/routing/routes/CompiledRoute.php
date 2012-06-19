<?php

namespace Core\Components\Routing\Routes;

class CompiledRoute implements CompiledRouteInterface
{
	const ARGUMENT_CHAR = ':';
	private $regexp;
	private $names = array();
	private $tokens = array();
	private $arguments = array();

	public function __construct()
	{
		
	}

	public function addRegexp($regexp)
	{
		$this->regexp .= $regexp;
	}

	public function getRegexp()
	{
		return '/^'. $this->regexp .'$/';
	}

	public function addText($text)
	{
		$this->tokens[] = '/'. $text;
	}

	public function addArgument($argument)
	{
		$this->tokens[] = $argument;
		$this->arguments[$argument] = null;
	}

	public function setArgument($argument, $value)
	{
		$this->arguments[$argument] = $value;
	}

	public function getArguments()
	{
		return $this->arguments;
	}

	public function getTokens()
	{
		return $this->tokens;
	}
}
