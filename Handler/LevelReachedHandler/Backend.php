<?php

namespace Elevator\Handler\LevelReachedHandler;

use Elevator\Door\DoorClientInterface;
use Elevator\ElevatorInterface;
use Elevator\ElevatorManagerInterface;
use Elevator\MovementManagerInterface;
use Elevator\SystemState\ElevatorState\ElevatorStateInterface;
use Elevator\Waypoint\WaypointFilterInterface;

class Backend
{
    /**
     * @var ElevatorManagerInterface
     */
    private $elevatorManager;

    /**
     * @var WaypointFilterInterface
     */
    private $waypointFilter;

    /**
     * @var DoorClientInterface
     */
    private $doorClient;
    
    /**
     * @var MovementManagerInterface
     */
    private $movementManager;

    public function __construct(
        ElevatorManagerInterface $elevatorManager,
        WaypointFilterInterface $waypointFilter,
        DoorClientInterface $doorClient,
        MovementManagerInterface $movementManager
    ) {
        $this->elevatorManager = $elevatorManager;
        $this->waypointFilter = $waypointFilter;
        $this->doorClient = $doorClient;
        $this->movementManager = $movementManager;
    }

    public function handle($elevatorId, $levelReached)
    {
        $elevator = $this->elevatorManager->getElevatorById($elevatorId);

        $elevator->setLevel($levelReached);
        $this->elevatorManager->saveElevator($elevator);
        
        //NOTE btw if level reached so there is some waypoint elevator was moving to, so check below seems redundant
        if ($elevator->getWaypoints()->isEmpty()) {
            $elevator->goIdle();
            $this->elevatorManager->saveElevator($elevator);

            return $this;
        }

        $result = $this->tryLocal($elevator, $levelReached);
        if (!$result) {
            $result = $this->tryForward($elevator, $levelReached);
            if (!$result) {
                $elevator->switchDirection();
                $this->elevatorManager->saveElevator($elevator);

                $result = $this->tryBackward($elevator, $levelReached);
            }
        }

        if (!$result) {
            throw new \Exception('No waypoints or waypoints not found. This case should never happen.');
        }

        return $this;
    }

    public function fakeHandle($elevatorId, $levelReached)
    {
        return $this->handle($elevatorId, $levelReached);
    }

    public function tryLocal(ElevatorInterface $elevator, $levelReached)
    {
        $currentDirection = $elevator->getState()->getDirection();

        $hasLocalPickupWaypoint = $elevator->getWaypoints()->hasWaypoint($currentDirection, $levelReached);
        $hasLocalDropWaypoint = $elevator->getWaypoints()->hasWaypoint(ElevatorStateInterface::DIRECTION_NONE, $levelReached);

        if($hasLocalPickupWaypoint || $hasLocalDropWaypoint) {
            if ($hasLocalPickupWaypoint) {
                $this->deleteWaypoint($elevator, $levelReached, $currentDirection);
            }
            if ($hasLocalDropWaypoint) {
                $this->deleteWaypoint($elevator, $levelReached, ElevatorStateInterface::DIRECTION_NONE);

                if ($currentDirection==ElevatorStateInterface::DIRECTION_UP && !$this->hasWaypointUpFromPosition($elevator, $levelReached)) {
                    $elevator->setDirection(ElevatorStateInterface::DIRECTION_DOWN);
                    $this->elevatorManager->saveElevator($elevator);
                }

                if ($currentDirection==ElevatorStateInterface::DIRECTION_DOWN && !$this->hasWaypointDownFromPosition($elevator, $levelReached)) {
                    $elevator->setDirection(ElevatorStateInterface::DIRECTION_UP);
                    $this->elevatorManager->saveElevator($elevator);
                }
            }
            $this->sendOpenDoorsCommand($elevator, $levelReached);

            return true;
        }

        return false;
    }
    
    private function tryForward(ElevatorInterface $elevator, $levelReached)
    {
        if ($this->hasForwardWaypoint($elevator, $levelReached)) {
            //move to next in forward direction
            $this->movementManager->moveForward($elevator);

            return true;
        }

        return false;
    }

    private function tryBackward(ElevatorInterface $elevator, $levelReached)
    {
        $currentDirection = $elevator->getState()->getDirection();

        $hasLocalPickupWaypoint = $elevator->getWaypoints()->hasWaypoint($currentDirection, $levelReached);
        if($hasLocalPickupWaypoint) {
            $this->deleteWaypoint($elevator, $levelReached, $currentDirection);
            $this->sendOpenDoorsCommand($elevator, $levelReached);

            return true;
        }

        if ($this->hasForwardWaypoint($elevator, $levelReached)) {
            //move to next in forward direction
            $this->movementManager->moveForward($elevator);

            return true;
        }

        return false;
    }

    private function deleteWaypoint(ElevatorInterface $elevator, $levelReached, $currentDirection)
    {
        $elevator->deleteWaypoint($currentDirection, $levelReached);
        $this->elevatorManager->saveElevator($elevator);
    }

    private function sendOpenDoorsCommand(ElevatorInterface $elevator, $levelReached)
    {
        $response = $this->doorClient->sendOpenDoorsCommand($elevator->getId(), $levelReached);

        return $response;
    }

    private function hasForwardWaypoint(ElevatorInterface $elevator, $levelReached)
    {
        $currentDirection = $elevator->getState()->getDirection();

        switch($currentDirection) {
            case ElevatorStateInterface::DIRECTION_UP:
                return $this->hasWaypointUpFromPosition($elevator, $levelReached);
                break;

            case ElevatorStateInterface::DIRECTION_DOWN:
                return $this->hasWaypointDownFromPosition($elevator, $levelReached);
                break;

            default:
                throw new \Exception('Invalid search direction ' . $currentDirection);
        }
    }

    private function hasWaypointUpFromPosition(ElevatorInterface $elevator, $levelReached)
    {
        $waypoints = $elevator->getWaypoints();

        $filters = [];
        $filters[] = [
            'field' => 'level',
            'comparator' => WaypointFilterInterface::GREATER_THAN,
            'context'    => $levelReached
        ];

        return $this->waypointFilter->filter($waypoints, $filters);
    }

    private function hasWaypointDownFromPosition(ElevatorInterface $elevator, $levelReached)
    {
        $waypoints = $elevator->getWaypoints();

        $filters = [];
        $filters[] = [
            'field' => 'level',
            'comparator' => WaypointFilterInterface::LESS_THAN,
            'context'    => $levelReached
        ];

        return $this->waypointFilter->filter($waypoints, $filters);
    }

}