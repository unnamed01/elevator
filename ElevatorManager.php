<?php

namespace Elevator;

use Elevator\Storage\StorageAdapterInterface;

class ElevatorManager implements ElevatorManagerInterface
{
    /**
     * @var ElevatorFactory
     */
    private $elevatorFactory;

    /**
     * @var StorageAdapterInterface
     */
    private $storageAdapter;

    public function __construct(
        ElevatorFactory $elevatorFactory,
        StorageAdapterInterface $storageAdapter
    ) {
        $this->elevatorFactory = $elevatorFactory;
        $this->storageAdapter = $storageAdapter;
    }
    
    /**
     * @param int $elevatorId
     * @return ElevatorInterface
     */
    public function getElevatorById($elevatorId)
    {
        $stateData = $this->storageAdapter->getElevatorStateById($elevatorId);
        $waypointData = $this->storageAdapter->getElevatorWaypointsById($elevatorId);
        
        $elevator = $this->elevatorFactory->create($stateData, $waypointData);
        
        return $elevator;
    }

    /**
     * @return ElevatorInterface[]
     */
    public function getElevators()
    {
        $statesData = $this->storageAdapter->getElevatorStates();
        $waypointsById = $this->storageAdapter->getElevatorWaypoints();

        $elevators = [];
        foreach ($statesData as $stateData) {
            $elevatorId = $stateData['id'];
            $waypoints = $waypointsById[$elevatorId];

            $elevators[] = $this->elevatorFactory->create($stateData, $waypoints);
        }

        return $elevators;
    }

    public function saveElevator(ElevatorInterface $elevator)
    {
        $this->storageAdapter->saveWaypoints($elevator->getId(), $elevator->getWaypoints());
        $this->storageAdapter->saveElevatorState($elevator->getId(), $elevator->getState());

        return $this;
    }
}