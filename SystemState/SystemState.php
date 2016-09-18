<?php

namespace Elevator\SystemState;

use Elevator\Storage\StorageAdapterInterface;
use Elevator\SystemState\ElevatorState\ElevatorStateCollection;
use Elevator\SystemState\ElevatorState\ElevatorStateCollectionFactory;

class SystemState implements SystemStateInterface
{
    /**
     * @var ElevatorStateCollection
     */
    private $elevatorStatesCollection;

    /**
     * @var StorageAdapterInterface
     */
    private $storageAdapter;

    /**
     * @var ElevatorStateCollectionFactory
     */
    private $elevatorStateCollectionFactory;

    public function __construct(
        StorageAdapterInterface $storageAdapter,
        ElevatorStateCollectionFactory $elevatorStateCollectionFactory
    ) {
        $this->storageAdapter = $storageAdapter;
        $this->elevatorStateCollectionFactory = $elevatorStateCollectionFactory;
    }

    public function isSystemInitialized()
    {
        return $this->storageAdapter->isInitFileExists();
    }

    public function setSystemInitialized()
    {
        return $this->storageAdapter->createInitFile();
    }

    public function getElevatorStates()
    {
        if ($this->elevatorStatesCollection === null) {
            $this->elevatorStatesCollection = $this->getElevatorStatesCollection();
        }
        return $this->elevatorStatesCollection;
    }

    //it belongs to state?
    public function getElevatorWaypoints()
    {
        return $this->storageAdapter->getElevatorWaypoints();
    }

    public function clearSystemState()
    {
        return $this->storageAdapter->clearSystemData();
    }

    protected function getElevatorStatesCollection()
    {
        $elevatorStates = $this->storageAdapter->getElevatorStates();
        $elevatorStatesCollection =  $this->elevatorStateCollectionFactory->create($elevatorStates);

        return $elevatorStatesCollection;
    }
}