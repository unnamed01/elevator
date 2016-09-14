<?php

use Elevator\EntryPointFactory;
use Elevator\EntryPointInterface;

//TODO use guzzle

error_reporting(E_ALL);
ini_set('display_errors', 1);
define('BASE_PATH', realpath(dirname(__FILE__)));

require __DIR__ . '/vendor/autoload.php';

/*spl_autoload_register(function($class){
    $parts = explode('\\', $class);

    if (array_shift($parts) !== 'Elevator') {
        throw new Exception(sprintf('Unable to load class %s', $class));
    }

    $filename = BASE_PATH . DIRECTORY_SEPARATOR . join(DIRECTORY_SEPARATOR, $parts) . '.php';

    require $filename;
});*/





/*$_REQUEST = [
    'data' => json_encode([
        'type' => \Elevator\Dispatcher::REQUEST_TYPE_REQUEST_BUTTON,
        'direction' => ElevatorStateInterface::DIRECTION_UP,
        'floor' => 1,
    ]),
];*/


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