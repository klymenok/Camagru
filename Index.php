<?php

use routes\Router;

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
define('DS', DIRECTORY_SEPARATOR);
define('RT', __DIR__. DS);

spl_autoload_extensions('.php');
spl_autoload_register(function ($class) {
    include_once RT . str_replace('\\', '/', $class).'.php';
});

$router = new Router();
?>