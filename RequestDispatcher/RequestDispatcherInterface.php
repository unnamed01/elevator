<?php

namespace Elevator\RequestDispatcher;

use Elevator\Http\HttpRequestInterface;
use Elevator\SystemState\ElevatorState\ElevatorStateInterface;

interface RequestDispatcherInterface
{
    /**
     * @param HttpRequestInterface $request
     * @return ElevatorStateInterface
     */
    public function dispatch(HttpRequestInterface $request);
}