<?php

namespace Elevator\RequestDispatcher;

use Elevator\Config;
use Elevator\SystemState\SystemStateManagerFactory;

class RequestDispatcherStrategyFactory
{
    const STRATEGY_GENERAL = 'general';
    const STRATEGY_SIMPLE = 'simple';

    private $classMap = [
        self::STRATEGY_GENERAL => RequestDispatchingStrategyGeneral::class,
        self::STRATEGY_SIMPLE => RequestDispatchingStrategySimple::class
    ];

    /**
     * @param string $type
     * @return RequestDispatchingStrategyInterface
     * @throws \Exception
     */
    public function create($type)
    {
        if (!array_key_exists($type, $this->classMap)) {
            throw new \Exception('Unknown dispatcher type: ' . $type);
        }

        $className = $this->classMap[$type];

        $config = new Config();
        $dispatchingStrategy = new $className($config);

        return $dispatchingStrategy;
    }
}