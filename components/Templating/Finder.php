<?php

namespace Core\Components\Templating;

/**
 * A very simple template finder.
 *
 * @author Héctor Ramón Jiménez
 */
class Finder
{	
	public function __construct()
	{
		
	}
	
	public function getPath($template)
	{
		return DIR . 'app/views/'. $template .'.html.php';
	}
}
