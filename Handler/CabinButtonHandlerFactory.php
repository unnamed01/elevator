<?php

namespace Elevator\Handler;

use Elevator\Config;
use Elevator\ElevatorManagerFactory;

class CabinButtonHandlerFactory
{
    public function create()
    {
        $config = new Config;

        $elevatorManagerFactory = new ElevatorManagerFactory();
        $elevatorManager = $elevatorManagerFactory->create();

        return new CabinButtonHandler($config, $elevatorManager);
    }
}