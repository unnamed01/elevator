<?php

namespace Elevator;

use Elevator\SystemState\ElevatorState\ElevatorStateFactory;
use Elevator\SystemState\ElevatorState\ElevatorStateInterface;
use Elevator\Waypoint\WaypointCollectionFactory;
use Elevator\Waypoint\WaypointCollectionInterface;
use Elevator\Waypoint\WaypointFactory;

class ElevatorFactory
{
    /**
     * @param ElevatorStateInterface $elevatorState
     * @param WaypointCollectionInterface $waypointCollection
     * @return Elevator
     */
    public function create(ElevatorStateInterface $elevatorState, WaypointCollectionInterface $waypointCollection)
    {
        $waypointFactory = new WaypointFactory;

        return new Elevator(
            $elevatorState,
            $waypointFactory,
            $waypointCollection
        );
    }

    /**
     * @param array $elevatorStateData
     * @param array $waypointData
     * @return Elevator
     */
    public function createFromArray(array $elevatorStateData, array $waypointData)
    {
        $waypointFactory = new WaypointFactory;
        $elevatorStateFactory = new ElevatorStateFactory;

        $elevatorState = $elevatorStateFactory->create($elevatorStateData);

        $waypointCollectionFactory = new WaypointCollectionFactory;
        $waypointCollection = $waypointCollectionFactory->create($waypointData);

        return new Elevator(
            $elevatorState,
            $waypointFactory,
            $waypointCollection
        );
    }
}