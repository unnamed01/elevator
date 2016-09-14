<?php

namespace Elevator\Handler;

use Elevator\Config;
use Elevator\ElevatorManagerFactory;
use Elevator\Handler\LevelReachedHandler\BackendFactory;
use Elevator\MovementManagerFactory;

class DoorClosedHandlerFactory
{
    public function create()
    {
        $config = new Config;

        $backendFactory = new BackendFactory;
        $backend = $backendFactory->create();

        $movementManagerFactory = new MovementManagerFactory();
        $movementManager = $movementManagerFactory->create();

        $elevatorManagerFactory = new ElevatorManagerFactory();
        $elevatorManager = $elevatorManagerFactory->create();

        return new DoorClosedHandler($config, $backend, $movementManager, $elevatorManager);
    }
}