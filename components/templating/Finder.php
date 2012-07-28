<?php

namespace Sea\Core\Components\Templating;

/**
 * A very simple template finder.
 *
 * @author Héctor Ramón Jiménez
 */
class Finder
{	
	public function __construct() {
		
	}
	
	public function getPath($template)
	{
		return \Sea\DIR . 'app/views/'. $template .'.html.php';
	}
}
