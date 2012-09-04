<?php

namespace Sea\Components\Routing\Generators;

interface URLGeneratorInterface
{
	public function generate($name, $arguments, $module);
}