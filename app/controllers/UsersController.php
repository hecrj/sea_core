<?php

# Users controller
class UsersController extends Controller
{
	
	protected $access_filter = array(
		'add'	=>	array('guest'),
		'show'	=>	'staff'
	);
	
	public function index()
	{
		$this->user = Auth::$user;	
	}
	
	public function show()
	{
		// Make a pagination with one user per page
		list($this->user_pages, $this->users) = Pagination::make('User', array('limit' => 1));
	}
	
	public function add()
	{
		// Set new User with params of request
		$user = new User(Request::params('user'));
		
		// If http header is post
		if(Request::isPost())
			// If user saves
			if($user->save())
				// Redirect to controller homepage and set flash message
				Request::redirect('/users', 'User created successfully!');
		
		// Set user to use in view
		$this->user = $user;
		
		// Set blocks partial for the form
		$this->blocks = 'users/signup_info';
	}
	
	public function login()
	{
		$user_params = Request::params('user');
		$user_data = array('username' => $user_params['username'], 'password' => md5($user_params['password']));
		
		if(User::login($user_data['username'], $user_data['password'])->isLogged())
		{
			Session::write('user_data', $user_data);
			Request::redirect('/users');
		}
		else
			Request::redirect('/users', 'Incorrect username or password! Please, try again.');
	}
	
	public function logout()
	{
		Session::destroy();
		
		Request::redirect('/users');
	}
	
	// Function to check if user attributes are valid in AJAX request
	public function check($attribute)
	{
		// Array of attributes allowed to be checked
		$attributes = array('username','password','confirm_password','email');
		
		// Set new User with params of request
		$user = new User(Request::params('user'));
		
		// Check if the attribute is valid and return a message
		$user->ajax_check($attribute, $attributes);
	}
	
}

?>
