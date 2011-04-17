<?php

# Component class
abstract class Component
{
	private static $components = array('Cookie', 'Session');
	
	public static function init();
}

?>