<?php

class PagesController extends Controller
{
	
	public function index ()
	{
		$this->csrf_token = Session::read('csrf_token');
		$this->ip = Request::IP();
		
		if(!Cookie::exists('test'))
			Cookie::create('test', 'foobar');
			
		$this->cookie = Cookie::read('test');
	}
	
	public function about ()
	{
		
	}
	
	public function contact ()
	{
		
	}
	
}

?>