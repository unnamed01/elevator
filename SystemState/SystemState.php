<?php

namespace Elevator\SystemState;

use Elevator\Storage\StorageAdapterInterface;
use Elevator\SystemState\ElevatorState\ElevatorStateCollection;

class SystemState implements SystemStateInterface
{
    /**
     * @var array
     */
    private $elevatorStatesCollection;

    /**
     * @var StorageAdapterInterface
     */
    private $storageAdapter;

    public function __construct(StorageAdapterInterface $storageAdapter)
    {
        $this->storageAdapter = $storageAdapter;
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
        $elevatorStatesData = $this->storageAdapter->getElevatorStates();

        $elevatorStates = $elevatorStatesData;
        /*$elevatorStateFactory = new ElevatorStateFactory;
        $elevatorStates = [];
        foreach ($elevatorStatesData as $elevatorStateData) {
            $elevatorState = $elevatorStateFactory->create($elevatorStateData);
            $elevatorStates[] = $elevatorState;
        }*/

        $elevatorStatesCollection =  new ElevatorStateCollection($elevatorStates);

        return $elevatorStatesCollection;
    }
}