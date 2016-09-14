<?php

namespace Elevator;

use Elevator\SystemState\ElevatorState\ElevatorStateInterface;
use Elevator\Waypoint\WaypointCollectionInterface;
use Elevator\Waypoint\WaypointFactory;

class Elevator implements ElevatorInterface
{
    /**
     * @var ElevatorStateInterface
     */
    private $state;

    /**
     * @var WaypointFactory
     */
    private $waypointFactory;

    /**
     * @var WaypointCollectionInterface
     */
    private $waypointCollection;
    
    public function __construct(
        ElevatorStateInterface $elevatorState,
        WaypointFactory $waypointFactory,
        WaypointCollectionInterface $waypointCollection
    ) {
        $this->state = $elevatorState;
        $this->waypointFactory = $waypointFactory;
        $this->waypointCollection = $waypointCollection;
    }

    public function getId()
    {
        return $this->state->getId();
    }

    /**
     * @return ElevatorStateInterface
     */
    public function getState()
    {
        return $this->state;
    }

    public function addWaypoint($direction, $floorId)
    {
        $waypoint = $this->waypointFactory->create($direction, $floorId);
        $this->waypointCollection->addWaypoint($waypoint);
    }

    public function deleteWaypoint($direction, $level)
    {
        $this->waypointCollection->deleteWaypoint($direction, $level);
    }

    /**
     * @return WaypointCollectionInterface
     */
    public function getWaypoints()
    {
        return $this->waypointCollection;
    }

    public function hasWaypoint($direction, $level)
    {
        return $this->waypointCollection->hasWaypoint($direction, $level);
    }

    public function setDirection($direction)
    {
        $this->state->setDirection($direction);
        return $this;
    }

    public function setLevel($level)
    {
        $this->state->setLevel($level);

        return $this;
        
        //TODO make state immutable?
        /*$stateData = $this->state->getData();
        $stateData['floor'] = $floor;

        $newState = $this->elevatorStateFactory->create($stateData);

        $this->state = $newState;*/
    }

    public function switchDirection()
    {
        $oppositeDirection = $this->getOppositeDirection($this->getState()->getDirection());
        $this->setDirection($oppositeDirection);
    }

    public function goIdle()
    {
        $this->state->setFree(true);
        return $this;
    }

    public function goActive()
    {
        $this->state->setFree(false);
        return $this;
    }

    private function getOppositeDirection($currentDirection)
    {
        $oppositeDirection = $currentDirection===ElevatorStateInterface::DIRECTION_UP
            ? ElevatorStateInterface::DIRECTION_DOWN
            : ElevatorStateInterface::DIRECTION_UP;

        return $oppositeDirection;
    }

    private function setFree($isFree)
    {
        $this->state->setFree($isFree);
        return $this;
    }
}