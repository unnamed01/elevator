<?php

namespace Elevator\Motor;

use Elevator\Config;
use Elevator\Zmq\ZmqResponseFactory;

class MotorClientFactory
{
    public function create()
    {
        $config = new Config;
        $responseFactory = new ZmqResponseFactory;

        return new MotorClient($config, $responseFactory);
    }
}