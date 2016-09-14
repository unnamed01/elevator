<?php

namespace Elevator\Handler;

use Elevator\ElevatorManagerFactory;

class CabinButtonHandlerFactory
{
    public function create()
    {
        $elevatorManagerFactory = new ElevatorManagerFactory();
        $elevatorManager = $elevatorManagerFactory->create();

        return new CabinButtonHandler($elevatorManager);
    }
}