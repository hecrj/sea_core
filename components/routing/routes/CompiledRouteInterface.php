<?php

namespace Sea\Components\Routing\Routes;

interface CompiledRouteInterface
{
	public function addRegexp($regexp);
	public function getRegexp();
	public function addText($text);
	public function addArgument($argument);
	public function setArgument($argument, $value);
	public function getArguments();
}
