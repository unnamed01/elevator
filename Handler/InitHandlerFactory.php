<?php

namespace Elevator\Handler;

use Elevator\Config;
use Elevator\ElevatorFactory;
use Elevator\ElevatorManagerFactory;
use Elevator\SystemState\SystemStateFactory;

class InitHandlerFactory
{
    /**
     * @return InitHandler
     */
    public function create()
    {
        $config = new Config;
        $elevatorFactory = new ElevatorFactory;
        
        $elevatorManagerFactory = new ElevatorManagerFactory;
        $elevatorManager = $elevatorManagerFactory->create();

        $systemStateFactory = new SystemStateFactory;
        $systemState = $systemStateFactory->create();

        return new InitHandler($config, $elevatorFactory, $elevatorManager, $systemState);
    }
}