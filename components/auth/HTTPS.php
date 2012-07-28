<?php

namespace Sea\Core\Components\Auth;

class HTTPS extends ProtocolAbstract
{
	protected $session_time_active = 3600;
	
	public function init()
	{
		if(! $this->sessionPersist())
		{
			// If session persistance fails, the authenthication is not secure
			$this->secure = false;
			$this->session->delete('user_secure');
			
			if(! $this->cookiePersist())
				$this->cookie->delete('user_data');
		}
		
		return true;	
	}
	
	public function persist($user)
	{	
		$this->sessionCreate($user, 'user_secure');
		
		$cookie_time = $user->remember ? $this->cookie_time : 0;
		
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
		if(!$this->secure or !$this->session->exists('user_secure'))
			return false;
		
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
