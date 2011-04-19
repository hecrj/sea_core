<?php

# Constants needed by core system framework to work correctly
# Models directory
define('DIR_MODELS', DIR . 'app/models/');
# Controllers directory
define('DIR_CONTROLLERS', DIR . 'app/controllers/');
# Views directory
define('DIR_VIEWS', DIR . 'app/views/');
# Config directory
define('DIR_CONFIG', DIR . 'config/');
# Is an ajax request?
define('IS_AJAX', (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'));
?>