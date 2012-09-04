<?php

namespace Sea\Components\Routing;

interface RequestInterface
{
	public static function createFromGlobals();
	public function setHost($host);
	public function getSubdomain();
	public function getHostname();
	public function setPath($path);
	public function getPath();
	public function setGet(Array $get);
	public function setPost(Array $post);
	public function setFiles(Array $files);
	public function setSecure($secure);
	public function isSecure();
	public function getProtocol();
	public function setMethod($method);
	public function getMethod();
	public function setAjax($ajax);
	public function isAjax();
	public function redirectTo($path);
}
