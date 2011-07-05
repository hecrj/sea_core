<?php

namespace Core\Components;
use Core\Components\DynamicInjector;

class HelperInjector extends DynamicInjector
{
	
	protected $classes = array(
		'form'			=>	'Core\\Helpers\\Form'
	);

	protected $dependencies = array(	
		'form'			=>	array('security', 'request')
	);
	
	protected $shared = false;
	
}

?>