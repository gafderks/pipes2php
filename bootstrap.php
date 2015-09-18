<?php // /bootstrap.php

require 'vendor/autoload.php';

// create autoloader
function __autoload($class_name) {
    include __DIR__ . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . str_replace('\\', '.', $class_name) . '.php';   
}
spl_autoload_register('__autoload');

ini_set('allow_url_fopen ','ON');