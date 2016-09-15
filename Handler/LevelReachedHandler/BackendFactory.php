<?php

namespace Elevator\Handler\LevelReachedHandler;

use Elevator\Config;
use Elevator\Door\DoorClientFactory;
use Elevator\ElevatorManagerFactory;
use Elevator\MovementManagerFactory;
use Elevator\Waypoint\WaypointFilter;
use Elevator\Waypoint\WaypointManagerFactory;

class BackendFactory
{
    public function create()
    {
        $config = new Config;

        $elevatorManagerFactory = new ElevatorManagerFactory;
        $elevatorManager = $elevatorManagerFactory->create();

        $waypointFilter = new WaypointFilter;

        $doorClientFactory = new DoorClientFactory;
        $doorClient = $doorClientFactory->create();

        $movementManagerFactory = new MovementManagerFactory();
        $movementManager = $movementManagerFactory->create();

        return new Backend($config, $elevatorManager, $waypointFilter, $doorClient, $movementManager);
    }
}