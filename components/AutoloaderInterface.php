<?php

namespace Sea\Components;

interface AutoloaderInterface
{
	public function register();
	public function set($namespace, $path);
	public function vendors(Array $vendors);
}
