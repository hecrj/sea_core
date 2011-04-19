<?php

# Users controller
class UsersController extends Controller
{
	
	public function index()
	{
		$this->users = User::all();
	}
	
	public function add()
	{
		$user = new User(Request::params('user'));
		
		if(Request::isPost())
			if($user->save())
				Request::redirect('/users', 'User created successfully!');
		
		$this->user = $user;
		$this->blocks = 'users/signup_info';
	}
	
}

?>