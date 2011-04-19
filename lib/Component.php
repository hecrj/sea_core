<?php

# Component class
interface Component
{	
	public static function init();	
}

// Load Components
require(DIR . 'lib/components/Request.php');
require(DIR . 'lib/components/Cookie.php');
require(DIR . 'lib/components/Session.php');

// Initialize components
Request::init();
Cookie::init();
Session::init();

?>