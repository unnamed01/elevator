<?php

use Elevator\EntryPointFactory;
use Elevator\EntryPointInterface;

//TODO use guzzle

error_reporting(E_ALL);
ini_set('display_errors', 1);
define('BASE_PATH', realpath(dirname(__FILE__)));

require __DIR__ . '/vendor/autoload.php';

$entryPointFactory = new EntryPointFactory;
$entryPoint = $entryPointFactory->create();

$response = $entryPoint->handle($_REQUEST);


//$response->sendHeaders();

if ($response->hasBody()) {
    header('Content-Type: text/plain');
    echo $response->getBody();
} else {
    header('Content-Type: application/json');
    echo $response->getDataAsJson();
}