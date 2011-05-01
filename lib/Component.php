<?php

# Component class
interface Component
{	
	public static function init();	
}

// Require components
require(DIR . 'lib/components/Router.php');
require(DIR . 'lib/components/Request.php');
require(DIR . 'lib/components/Cookie.php');
require(DIR . 'lib/components/Session.php');
require(DIR . 'lib/components/Security.php');
require(DIR . 'lib/components/Auth.php');

?>
