<?php

# Security component
class Security implements Component
{
	public static function init()
	{
		// If a CSRF token doesn't exist
		if(!Session::exists('csrf_token'))
			// Generate and save one
			Session::write('csrf_token', md5(uniqid(rand(), TRUE)));
			
		ExceptionIf((Request::isPost() and Session::read('csrf_token') != Request::params('csrf_token')), 'Cross Site Request Forgery detected!');
	}
}

?>
