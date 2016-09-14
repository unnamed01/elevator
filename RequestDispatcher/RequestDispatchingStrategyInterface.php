<?php

namespace Elevator\RequestDispatcher;

use Elevator\Http\HttpRequestInterface;
use Elevator\SystemState\ElevatorState\ElevatorStateInterface;
use Elevator\SystemState\SystemStateInterface;

interface RequestDispatchingStrategyInterface
{
    /**
     * @param HttpRequestInterface $request
     * @param SystemStateInterface $systemState
     * @return ElevatorStateInterface
     */
    public function dispatch(HttpRequestInterface $request, SystemStateInterface $systemState);
}