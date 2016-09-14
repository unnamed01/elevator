<?php

namespace Elevator;

use Elevator\Storage\StorageAdapterFilesystem;

class ElevatorManagerFactory
{
    public function create()
    {
        $elevatorFactory = new ElevatorFactory;

        $config = new Config;
        $storageAdapter = new StorageAdapterFilesystem($config);

        return new ElevatorManager($elevatorFactory, $storageAdapter);
    }
}