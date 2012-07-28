<?php

namespace Sea\Core\Components\Routing\Routes;

interface RouteInterface
{
	public function getName();
	public function getPattern();
	public function getController();
	public function addDefault($name, $value);
	public function hasDefault($name);
	public function getDefault($name);
	public function addRequirement($name, $value);
	public function hasRequirement($name);
	public function getRequirement($name);
}
