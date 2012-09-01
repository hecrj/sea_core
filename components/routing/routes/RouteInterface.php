<?php

namespace Sea\Core\Components\Routing\Routes;

interface RouteInterface
{
	public function getName();
	public function getPattern();
	public function getController();
	public function defaults(Array $defaults);
	public function hasDefault($name);
	public function getDefault($name);
	public function constraints(Array $constraints);
	public function hasConstraint($name);
	public function getConstraint($name);
}
