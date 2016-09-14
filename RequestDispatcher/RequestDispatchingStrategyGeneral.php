<?php

namespace Elevator\RequestDispatcher;

use Elevator\Config;
use Elevator\Http\HttpRequestInterface;
use Elevator\SystemState\ElevatorState\ElevatorStateInterface;
use Elevator\SystemState\SystemStateInterface;

class RequestDispatchingStrategyGeneral implements RequestDispatchingStrategyInterface
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
        $elevatorByDistance = $this->getElevatorsByDistance($request, $systemState);
        
        foreach ($elevatorByDistance as $distance => $elevators) {
            /** @var ElevatorStateInterface $elevator */
            foreach ($elevators as $elevator) {
                if (count($elevators)==1) {
                    return $elevator;
                } else {
                    if ($elevator->isFree()) {
                        return $elevator;
                    }
                }
            }

            return $elevator;
        }

        throw new \Exception('Unable to dispatch floor button request');
    }

    private function getElevatorsByDistance(
        HttpRequestInterface $request,
        SystemStateInterface $systemState
    ) {
        $elevatorByDistance = [];
        foreach ($systemState->getElevatorStates() as $item) {
            $distance = $this->getElevatorEffectiveDistance($request, $item);
            $elevatorByDistance[$distance][] = $item;
        }

        ksort($elevatorByDistance);

        return $elevatorByDistance;
    }

    private function getElevatorEffectiveDistance(
        HttpRequestInterface $request,
        ElevatorStateInterface $elevatorState
    ) {
        $passengerDirection = $request->getDataItem('direction');
        $passengerFloorId = $request->getDataItem('floorId');

        $floorData = $this->config->getFloorById($passengerFloorId);
        $passengerLevel = $floorData['level'];

        if ($elevatorState->isFree()) {
            $distance = $this->getDistanceFree($elevatorState->getLevel(), $passengerLevel);
        } elseif ($elevatorState->getDirection() == ElevatorStateInterface::DIRECTION_UP) {
            if ($passengerDirection == ElevatorStateInterface::DIRECTION_UP) {
                if ($elevatorState->getLevel() < $passengerLevel) {
                    $distance = $this->getDistanceWhenElevatorApproaching($elevatorState->getLevel(), $passengerLevel);
                } else {
                    $distance = $this->getDistanceWhenPassengerBehind($elevatorState->getLevel(), $passengerLevel);
                }
            } else {
                $distance = $this->getDistanceWhenDirectionsDiverge($elevatorState->getLevel(), $passengerLevel);
            }
        } else {
            if ($passengerDirection == ElevatorStateInterface::DIRECTION_DOWN) {
                if ($elevatorState->getLevel() > $passengerLevel) {
                    $distance = $this->getDistanceWhenElevatorApproaching($elevatorState->getLevel(), $passengerLevel);
                } else {
                    $distance = $this->getDistanceWhenPassengerBehind($elevatorState->getLevel(), $passengerLevel);
                }
            } else {
                $distance = $this->getDistanceWhenDirectionsDiverge($elevatorState->getLevel(), $passengerLevel);
            }
        }

        return $distance;
    }

    /**
     * @param int $elevatorFloor
     * @param int $passengerLevel
     * @return int
     */
    private function getDistanceFree($elevatorFloor, $passengerLevel)
    {
        $distance = abs($elevatorFloor - $passengerLevel);

        return $distance;
    }

    /**
     * @param int $elevatorFloor
     * @param int $passengerLevel
     * @return int
     */
    private function getDistanceWhenElevatorApproaching($elevatorFloor, $passengerLevel)
    {
        $distance = abs($passengerLevel - $elevatorFloor) * self::BUSY_ELEVATOR_DISTANCE_MODIFIER;
        return $distance;
    }

    /**
     * @param int $elevatorFloor
     * @param int $passengerLevel
     * @return int
     */
    private function getDistanceWhenPassengerBehind($elevatorFloor, $passengerLevel)
    {
        $distance = self::DISTANCE_TOO_LONG;
        return $distance;
    }

    /**
     * @param int $elevatorFloor
     * @param int $passengerLevel
     * @return int
     */
    private function getDistanceWhenDirectionsDiverge($elevatorFloor, $passengerLevel)
    {
        $distance = self::DISTANCE_TOO_LONG;
        return $distance;
    }

    private function pickElevatorFromGroupOfSameDistance()
    {

    }
}