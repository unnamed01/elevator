<?php

namespace Elevator;

use Elevator\Direction\DirectionHelper;
use Elevator\Handler\LevelReachedHandler\BackendFactory;
use Elevator\Motor\MotorClientFactory;
use Elevator\Waypoint\WaypointResolver;

class MovementManagerFactory
{
    public function create()
    {
        $elevatorManagerFactory = new ElevatorManagerFactory();
        $elevatorManager = $elevatorManagerFactory->create();
        
        $backendFactory = new BackendFactory;

        $motorClientFactory = new MotorClientFactory;
        $motorClient = $motorClientFactory->create();

        return new MovementManager($elevatorManager, $backendFactory, $motorClient);
    }
}