<?php

namespace Sea\Core\Components\Routing\Generators;

interface URLGeneratorInterface
{
	public function generate($name, $arguments, $module);
}