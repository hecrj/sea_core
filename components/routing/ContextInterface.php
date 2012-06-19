<?php

namespace Core\Components\Routing;

interface ContextInterface
{
	public function getControllerName();
	public function getActionName();
	public function getArguments($merge = array());
	public function getModuleName();
	public function getRouteName();
}