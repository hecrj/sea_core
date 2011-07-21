<?php

namespace Core\Components;

/**
 * Request class represents an HTTP/S request.
 * 
 * @author Héctor Ramón Jiménez
 */
class Request
{

	public $get = array();
	public $post = array();
	public $files = array();
	private $method;
	private $ssl;
	private $ajax;
	
    /**
     * Request constructor.
     *
     * @param string $host
     * @param string $route
     * @param string $ssl
     * @param string $method
     * @param string $type
     * @param array $get
     * @param array $post
     * @param array $files 
     */
	public function __construct($method = 'GET', $ssl = null, $type = null, Array $get = null, Array $post = null, Array $files = null)
	{
		$this->method = $method;
		$this->ssl    = !empty($ssl);
		$this->ajax   = (bool)($type == 'XMLHttpRequest');
		$this->get    = (array)$get;
		$this->post   = (array)$post;
		$this->files  = (array)$files;
	}
	
	public function getMethod()
	{
		return $this->method;
	}
	
	public function isSSL()
	{
		return $this->ssl;
	}
	
	public function isAjax()
	{
		return $this->ajax;
	}
	
}

?>
