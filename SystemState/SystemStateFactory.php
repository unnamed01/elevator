<?php

namespace Elevator\SystemState;

use Elevator\Config;
use Elevator\Storage\StorageAdapterFilesystem;

class SystemStateFactory
{
    public function create()
    {
        $config = new Config;
        $storageAdapter = new StorageAdapterFilesystem($config);

        return new SystemState($storageAdapter);
    }
}