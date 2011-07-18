<?php

/**
 * Bootstrap file
 * 
 * @author hector0193
 */

// Define absolute path to include files
define('DIR', dirname(dirname(__DIR__)).'/');

require_once(DIR . 'core/components/Autoloader.php');

$autoloader = new Core\Components\Autoloader;

$autoloader->register(false);
?>
