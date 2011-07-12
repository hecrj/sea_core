<?php

namespace Core\Components\Auth;

class HTTPS extends ProtocolAbstract
{
	protected $session_time_active = 3600;
	
	public function init()
	{
		if($this->secure)
		{
			if($this->session->exists('user_secure') and !$this->sessionPersist())
				$this->session->destroy(true);
		}
		elseif($this->cookie->exists('user_data') and !$this->cookiePersist())
			$this->cookie->delete('user_data');
		
		return true;	
	}
	
	public function persist($user)
	{	
		$this->sessionCreate($user, 'user_secure');
		
		$cookie_time = $user->getRemember() ? $this->cookie_time : 0;
		
		$this->cookieCreate($user, $cookie_time);
			
		$this->user = $user;
		
		return true;
	}
	
	private function cookiePersist()
	{
		$cookie_data = $this->cookie->read('user_data');
		
		if(! $user = $this->cookieUser($cookie_data))
			return false;
		
		$this->user = $user;
		
		return true;
	}
	
	private function sessionPersist()
	{
		$session_data = $this->session->read('user_secure');
		
		if(! $user = $this->sessionUser($session_data))
			return false;
		
		$this->sessionUpdate($session_data);
		
		if(! $this->cookie->exists('user_data'))
			$this->cookieCreate($user, 0);
		
		$this->user = $user;
		
		return true;
	}
	
}

?>