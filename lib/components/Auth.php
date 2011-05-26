<?php

# Auth component
class Auth implements Component
{
	public static $user;
	
	public static function init()
	{
		// If cookie user_data exists but session does not exist
		if(Cookie::exists('user_data') && !Session::exists('user_data'))
			// Write user_data in session
			Session::write('user_data', Cookie::read('user_data'), false);
				
		if(Session::exists('user_data'))
		{
			$user_data = Session::read('user_data');
			self::$user = User::keep_logged($user_data['username'], $user_data['hash']);
		
			if(!self::$user->isLogged())	
			{
				Cookie::delete('user_data');
				Session::destroy();
			}
		}
		else
			self::$user = new User;
			
	}	
}

?>
