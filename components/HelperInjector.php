<?php

namespace Core\Components;

class HelperInjector extends DynamicInjector
{
	protected $injectorClass = 'Core\\Components\\ComponentInjector';
	
	protected $classes = array(
		'cache'				=>	'Core\\Helpers\\Cache',
		'form'				=>	'Core\\Helpers\\Form',
		'javascript'		=>	'Core\\Helpers\\Javascript'
	);

	protected $dependencies = array(	
		'form'			=>	array('security', 'request')
	);
	
	protected $shared = array('cache', 'javascript');
	
}

?>