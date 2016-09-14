<?php

namespace Elevator\Door;

use Elevator\Config;
use Elevator\Zmq\ZmqResponseFactory;

class DoorClientFactory
{
    public function create()
    {
        $config = new Config;
        $responseFactory = new ZmqResponseFactory;

        return new DoorClient($config, $responseFactory);
    }
}