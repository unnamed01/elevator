<?php

namespace Elevator\RequestDispatcher;

use Elevator\SystemState\SystemStateManagerInterface;
use Elevator\Http\HttpRequestInterface;

class RequestDispatcher implements RequestDispatcherInterface
{
    private $systemStateManager;
    private $dispatchingStrategy;

    public function __construct(
        SystemStateManagerInterface $systemStateManager,
        RequestDispatchingStrategyInterface $dispatchingStrategy
    ) {
        $this->systemStateManager = $systemStateManager;
        $this->dispatchingStrategy = $dispatchingStrategy;
    }

    public function dispatch(HttpRequestInterface $request)
    {
        $elevatorState = $this->dispatchingStrategy->dispatch($request, $this->systemStateManager->getSystemState());

        return $elevatorState;
    }
}