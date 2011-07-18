<?php

namespace Core\Components\Router;

use Core\Components\Request;

class Router
{
	private $matcher;
	private $extractor;
	
	public function __construct(Analyzer $matcher, Analyzer $extractor)
	{
		$this->matcher = $matcher;
		$this->extractor = $extractor;
	}

	public function getControllerDataFrom(Route $route)
	{	
		if($this->matcher->analyze($route))
			return $this->matcher->getControllerData();
		
		$this->extractor->analyze($route);
		return $this->extractor->getControllerData();
	}

}

?>
