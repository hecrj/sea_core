<?php

namespace Core\Components;
use Core\Components\DynamicInjector;

class ComponentInjector extends DynamicInjector
{
	
	protected $classes = array(
		'cache'			=>	'Core\\Components\\Cache',
		'cookie'		=>	'Core\\Components\\Cookie',
		'request'		=>	'Core\\Components\\Request',
		'security'		=>	'Core\\Components\\Security',
		'session'		=>	'Core\\Components\\Session',
		'pagination'	=>	'Core\\Components\\Pagination'
	);

	protected $dependencies = array(	
		'security'		=>	array('session', 'request'),
		'pagination'	=>	array('request')
	);
	
	protected $shared = array('cookie', 'request', 'session', 'security');
}

?>