<?php

namespace Elevator\RequestDispatcher;

use Elevator\Config;
use Elevator\Http\HttpRequestInterface;
use Elevator\SystemState\ElevatorState\ElevatorStateInterface;
use Elevator\SystemState\SystemStateInterface;

class RequestDispatchingStrategySimple implements RequestDispatchingStrategyInterface
{
    const DISTANCE_TOO_LONG = PHP_INT_MAX;
    const BUSY_ELEVATOR_DISTANCE_MODIFIER = 2;

    /**
     * @var Config
     */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param HttpRequestInterface $request
     * @param SystemStateInterface $systemState
     * @return ElevatorStateInterface
     * @throws \Exception
     */
    public function dispatch(HttpRequestInterface $request, SystemStateInterface $systemState)
    {
        $elevatorHtmlId = $request->getDataItem('elevatorId');
        //$passengerDirection = $request->getDataItem('direction');
        //$passengerFloorId = $request->getDataItem('floorId');

        /** @var ElevatorStateInterface $item */
        foreach ($systemState->getElevatorStates() as $item) {
            if ($item->getHtmlId() === $elevatorHtmlId) {
                return $item;
            }
        }

        throw new \Exception(sprintf('Elevator not found by htmlId=%s', $elevatorHtmlId));
    }
}