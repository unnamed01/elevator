<?php

namespace Elevator\SystemState;

use Elevator\Config;
use Elevator\Storage\StorageAdapterFilesystem;
use Elevator\SystemState\ElevatorState\ElevatorStateCollectionFactory;

class SystemStateFactory
{
    public function create()
    {
        $config = new Config;
        $storageAdapter = new StorageAdapterFilesystem($config);

        $elevatorStateCollectionFactory = new ElevatorStateCollectionFactory;

        return new SystemState($storageAdapter, $elevatorStateCollectionFactory);
    }
}