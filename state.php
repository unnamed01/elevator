<?php

use Elevator\EntryPointState;

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('BASE_PATH', realpath(dirname(__FILE__)));

spl_autoload_register(function($class){
    $parts = explode('\\', $class);

    if (array_shift($parts) !== 'Elevator') {
        throw new Exception(sprintf('Unable to load class %s', $class));
    }

    $filename = BASE_PATH . DIRECTORY_SEPARATOR . join(DIRECTORY_SEPARATOR, $parts) . '.php';

    require $filename;
});

$entryPoint = new EntryPointState();
$response = $entryPoint->handle($_REQUEST);

//$response->getHeaders();

header('Content-Type: application/json');
echo $response->getDataAsJson();






