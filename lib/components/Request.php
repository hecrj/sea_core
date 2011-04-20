<?php

### Request component
class Request implements Component
{
	private static $params;
	private static $method;
	private static $isAjax;
	
	public static function init(){		
		self::$params	=	array_merge($_GET, $_POST, $_FILES);
		self::$method	=	$_SERVER['REQUEST_METHOD'];
		self::$isAjax	=	(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
	}
	
	public static function params($key = null)
	{
		if(empty($key))
			return self::$params;
		elseif(array_key_exists($key, self::$params))
			return self::$params[$key];
		
		return array();
	}
	
	public static function IP(){
		return $_SERVER['REMOTE_ADDR'];
	}
	
	public static function isPost(){
		return (self::$method == 'POST');
	}
	
	public static function isGet(){
		return (self::$method == 'GET');
	}
	
	public static function isAjax(){
		return self::$isAjax;
	}
	
	public static function redirect($path, $flash = null){
		if(!empty($flash))
			Session::write('flash', $flash);
		
		header('Location: ' . $path);
		exit();
	}
}

?>