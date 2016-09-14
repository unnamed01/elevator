<?php

use Elevator\Config;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\Wamp\WampServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory;
use React\Socket\Server;
use Elevator\Socket\Context;
use Elevator\Socket\SocketApp;

error_reporting(E_ALL);
ini_set('display_errors', 1);

require dirname(__DIR__) . '/vendor/autoload.php';


$config = new Config();

$loop   = Factory::create();
$socketApp = new SocketApp;

// Listen for the web server to make a ZeroMQ push after an ajax request
$context = new Context($loop);

/** @var React\ZMQ\SocketWrapper $pull */
$pull = $context->getSocket(ZMQ::SOCKET_PULL);
$pull->bind($config->getPullSocketDsn());
$pull->on('message', array($socketApp, 'onMessage'));



// Set up our WebSocket server for clients wanting real-time updates
$webSock = new Server($loop);
$webSock->listen($config->getWebSocketPort(), $config->getWebSocketHost());
$webServer = new IoServer(
    new HttpServer(
        new WsServer(
            new WampServer(
                $socketApp
            )
        )
    ),
    $webSock
);

$loop->run();