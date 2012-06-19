<?php

namespace Core\Components\Auth;
use Core\Components\Session;
use Core\Components\Cookie;

abstract class ProtocolAbstract
{
	protected $session;
	protected $cookie;
	protected $secure;
	protected $user;
	protected $user_class = 'User';
	protected $algo = 'sha256';
	protected $session_time = 86400;
	protected $session_time_active = 900;
	protected $cookie_time = 604800;
	
	public function __construct(Session $session, Cookie $cookie)
	{
		$this->session = $session;
		$this->cookie  = $cookie;
		$this->secure  = $session->isSecure();
		$this->user = new $this->user_class;
		
		$this->init();
	}
	
	public function isSecure()
	{
		return $this->secure;
	}
	
	public function logout($cookie = 'user_data')
	{
		if($this->user == null)
			return false;
		
		$this->session->destroy(true);
		$this->cookie->delete($cookie);
		$this->user = new $this->user_class;
		
		return true;
	}
	
	final public function setUserClass($class)
	{
		$this->user_class = $class;
	}
	
	final public function getUser()
	{	
		return $this->user;
	}
	
	protected function cookieUser($cookie_data)
	{
		$time_limit = time() - $this->cookie_time;
		
		if($cookie_data['created'] < $time_limit)
			return false;
		
	 	$user_class = $this->user_class;
		
		if(! $user = $user_class::find(array('username' => $cookie_data['username'])))
			return false;
		
		$key = hash_hmac($this->algo, $cookie_data['username'] . $cookie_data['created'], SERVER_KEY);
		$hash = hash_hmac($this->algo, $cookie_data['username'] . $cookie_data['created'] . $user->salt, $key);
		
		if($cookie_data['hash'] != $hash)
			return false;
			
		return $user;
	}
	
	protected function cookieCreate($user, $cookie_time, $name = 'user_data')
	{
		// Store time
		$time = time();

		// Create a key
		$key = hash_hmac($this->algo, $user->username . $time, SERVER_KEY);
		
		// Set user data
		$user_data = array(
			'username'	=>	$user->username,
			'created'	=>	$time,
			'hash'		=>	hash_hmac($this->algo, $user->username . $time . $user->salt, $key)
		);
		
		// Create cookie
		$this->cookie->create($name, $user_data, $cookie_time);
	}
	
	protected function sessionUser($session_data)
	{
		$time_max = time() - $this->session_time;
		
		if($session_data['created'] < $time_max)
			return false;
		
		$time_active = time() - $this->session_time_active;
		
		if($session_data['active'] < $time_active)
			return false;
		
		$user_class = $this->user_class;
		
		if(! $user = $user_class::find(array('username' => $session_data['username'], 'password' => $session_data['password'])))
			return false;
		
		return $user;
	}
	
	protected function sessionCreate($user, $name = 'user_data')
	{
		// Set user data
		$user_data = array(
			'username'	=>	$user->username,
			'password'	=>	$user->password,
			'created'	=>	time(),
			'active'	=>	time()
		);
		
		// Write user data in current session
		$this->session->write($name, $user_data);
	}
	
	protected function sessionUpdate($session_data)
	{
		$session_data['active'] = time();
		$this->session->write('user_data', $session_data);
	}
	
	abstract protected function init();
	abstract public function persist(/* UserInterface */$user);
}
