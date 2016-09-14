<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/vendor/autoload.php';

echo '12345';

/*$data = array(
    'category' => $_REQUEST['category'],
    'title'    => $_REQUEST['title'],
    'article'  => $_REQUEST['article'],
    'when'     => time()
);*/


$data = [
    'command' => 'doors-elevator0',
    'title'     => 'title',
    'article'   => 'article',
    'when'      => time()
];



$config = new \Elevator\Config;

$context = new ZMQContext();
$socket = $context->getSocket(ZMQ::SOCKET_PUSH, 'my pusher');
$socket->connect($config->getPushSocketDsn());

$socket->send(json_encode($data));

echo __FILE__.'::'.__LINE__.'<pre>';
var_dump($data);
echo '</pre>';
exit('--END--');


echo '67890';
