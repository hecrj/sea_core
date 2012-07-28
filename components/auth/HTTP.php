<?php

namespace Sea\Core\Components\Auth;

class HTTP extends ProtocolAbstract
{	
	protected function init()
	{
		if(!$this->sessionPersist())
		{
			$this->session->delete('user_data');
			
			if(!$this->cookiePersist())
				$this->cookie->delete('user_data');
		}
		
		return true;
	}
	
	private function cookiePersist()
	{
		if(! $this->cookie->exists('user_data'))
			return false;
		
		$cookie_data = $this->cookie->read('user_data');
		
		if(! $user = $this->cookieUser($cookie_data))
			return false;
		
		return $this->persist($user);
	}
	
	private function sessionPersist()
	{
		if(! $this->session->exists('user_data'))
			return false;
		
		$session_data = $this->session->read('user_data');

		if(! $user = $this->sessionUser($session_data))
			return false;
		
		$this->sessionUpdate($session_data);
		$this->user = $user;
		
		return true;
	}
	
	public function persist($user)
	{
		$this->sessionCreate($user);
		
		if($user->remember)
			$this->cookieCreate($user, $this->cookie_time);

		$this->user = $user;
		
		return true;
	}
	
}
