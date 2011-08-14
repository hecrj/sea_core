<?php

namespace Core\Components;
use Core\Components\DynamicInjector;

class HelperInjector extends DynamicInjector
{
	
	protected $classes = array(
		'componentInjector'	=>	'Core\\Components\\ComponentInjector',
		'form'				=>	'Core\\Helpers\\Form',
		'javascript'		=>	'Core\\Helpers\\Javascript'
	);

	protected $dependencies = array(	
		'form'			=>	array('security', 'request')
	);
	
	protected $shared = array('javascript');
	
}

?>