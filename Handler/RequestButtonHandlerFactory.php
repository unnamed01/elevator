<?php

namespace Elevator\Handler;

use Elevator\ElevatorManagerFactory;
use Elevator\MovementManagerFactory;
use Elevator\RequestDispatcher\RequestDispatcherFactory;

class RequestButtonHandlerFactory
{
    public function create()
    {
        $requestDispatcherFactory = new RequestDispatcherFactory;
        $requestDispatcher = $requestDispatcherFactory->create();

        $elevatorManagerFactory = new ElevatorManagerFactory();
        $elevatorManager = $elevatorManagerFactory->create();

        $movementManagerFactory = new MovementManagerFactory();
        $movementManager = $movementManagerFactory->create();

        return new RequestButtonHandler($requestDispatcher, $elevatorManager, $movementManager);
    }
}