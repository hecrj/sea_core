<?php

namespace Core\Components;
use Core\Components\DynamicInjector;

class ComponentInjector extends DynamicInjector
{
	
	protected $classes = array(
		'auth'			=>	'Core\\Components\\Auth\\HTTPS',
		'cache'			=>	'Core\\Components\\Cache',
		'cookie'		=>	'Core\\Components\\Cookie',
		'request'		=>	'Core\\Components\\Request',
		'route'			=>	'Core\\Components\\Router\\Route',
		'security'		=>	'Core\\Components\\Security',
		'session'		=>	'Core\\Components\\Session',
		'pagination'	=>	'Core\\Components\\Pagination'
	);

	protected $dependencies = array(
		'auth'			=>	array('session', 'cookie'),
		'security'		=>	array('session', 'request'),
		'session'		=>	array('cookie', 'request'),
		'pagination'	=>	array('route')
	);
	
	protected $shared = array('auth', 'cookie', 'request', 'route', 'session', 'security');
}

?>