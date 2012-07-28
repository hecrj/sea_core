<?php

/**
 * Bootstrap file
 * 
 * @author hector0193
 */

// Define absolute path to include files
define('\Sea\DIR', dirname(dirname(__\Sea\DIR__)).'/');

require_once(\Sea\DIR . 'core/components/Autoloader.php');

$autoloader = new Core\Components\Autoloader;

$autoloader->register(false);
?>
