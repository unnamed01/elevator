<?php

namespace Elevator\RequestDispatcher;

use Elevator\Config;
use Elevator\SystemState\SystemStateManagerFactory;

class RequestDispatcherFactory
{
    public function create()
    {
        $systemStateManagerFactory = new SystemStateManagerFactory;
        $systemStateManager = $systemStateManagerFactory->create();

        $dispatchingStrategyFactory = new RequestDispatcherStrategyFactory;
        $dispatchingStrategy = $dispatchingStrategyFactory->create(RequestDispatcherStrategyFactory::STRATEGY_SIMPLE);

        $dispatcher = new RequestDispatcher($systemStateManager, $dispatchingStrategy);

        return $dispatcher;
    }
}