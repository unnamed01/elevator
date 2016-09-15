<?php

namespace Elevator\Handler;

use Elevator\Config;
use Elevator\ElevatorInterface;
use Elevator\ElevatorManagerInterface;
use Elevator\Handler\LevelReachedHandler\Backend;
use Elevator\Http\HttpRequestInterface;
use Elevator\Http\HttpResponseInterface;
use Elevator\MovementManagerInterface;
use Elevator\SystemState\ElevatorState\ElevatorStateInterface;

class DoorClosedHandler implements HandlerInterface
{
    private $backend;

    private $config;

    /**
     * @var MovementManagerInterface
     */
    private $movementManager;

    /**
     * @var ElevatorManagerInterface
     */
    private $elevatorManager;

    public function __construct(
        Config $config,
        Backend $backend,
        MovementManagerInterface $movementManager,
        ElevatorManagerInterface $elevatorManager
    ) {
        $this->config = $config;
        $this->backend = $backend;
        $this->movementManager = $movementManager;
        $this->elevatorManager = $elevatorManager;
    }

    /**
     * @param HttpRequestInterface $request
     * @param HttpResponseInterface $response
     * @return $this
     */
    public function handle(HttpRequestInterface $request, HttpResponseInterface $response)
    {
        $elevatorId = $request->getDataItem('elevatorId');

        $elevator = $this->elevatorManager->getElevatorById($this->config->getElevatorByHtmlId($elevatorId)['id']);

        if($elevator->getWaypoints()->isEmpty()) {
            $elevator->goIdle();
            $this->elevatorManager->saveElevator($elevator);

            $response->setData([
                'idle'       => true,
                'elevatorId' => $elevatorId,
            ]);

            return $this;
        }

        /*if($elevator->getState()->getDirection()===ElevatorStateInterface::DIRECTION_NONE) {
            $elevator->setDirection($this->getDirection($elevator));
            $this->elevatorManager->saveElevator($elevator);
        }
        $this->movementManager->moveForward($elevator);*/

        if ($elevator->getState()->isFree()) {
            //If elevator is free and we just added a waypoint means we have only one now
            //So we can skip resolver
            //But we can face concurrency issues if someone added a waypoint in parallel, but in such case we just process random waypoint
            //TODO What if new waypoint got deleted?
            $this->movementManager->moveToTheOnlyWaypoint($elevator);
        } else {
            $this->movementManager->moveForward($elevator);
        }



        $response->setData([
            'idle'       => false,
            'elevatorId' => $elevatorId,
            'direction'  => $elevator->getState()->getDirection(),
            'level'      => $elevator->getState()->getLevel(),
        ]);

        return $this;
    }

    private function getDirection(ElevatorInterface $elevator)
    {
        $elevatorLevel = $elevator->getState()->getLevel();
        $waypointLevel = $elevator->getWaypoints()->getTheOnlyWaypoint()->getLevel();

        if ($elevatorLevel > $waypointLevel) {
            $direction = ElevatorStateInterface::DIRECTION_DOWN;
        } elseif ($elevatorLevel < $waypointLevel) {
            $direction = ElevatorStateInterface::DIRECTION_UP;
        } else {
            throw new \Exception('Impossible case when the only waypoint level matches elevator level');
        }

        return $direction;
    }
}