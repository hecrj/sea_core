<?php

namespace Core\Components\Auth;

class HTTP extends ProtocolAbstract
{	
	protected function init()
	{
		if(! $this->session->exists('user_data'))
		{
			if($this->cookie->exists('user_data') and !$this->cookiePersist())
				$this->logout();
		}
		elseif(! $this->sessionPersist())
			$this->logout();
		
		return true;
	}
	
	private function cookiePersist()
	{
		$cookie_data = $this->cookie->read('user_data');
		
		if(! $user = $this->cookieUser($cookie_data))
			return false;
		
		// Persist user
		return $this->persist($user);
	}
	
	private function sessionPersist()
	{
		$session_data = $this->session->read('user_data');

		if(! $user = $this->sessionUser($session_data))
		{
			if($this->cookie->exists('user_data'))
				return $this->cookiePersist();
			else
				return false;
		}
		
		$this->sessionUpdate($session_data);
		$this->user = $user;
		
		return true;
	}
	
	public function persist($user)
	{
		$this->sessionCreate($user);
		
		if($user->getRemember())
			$this->cookieCreate($user, $this->cookie_time);
		
		$this->user = $user;
		
		return true;
	}
	
}

?>