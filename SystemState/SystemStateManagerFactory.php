<?php

namespace Elevator\SystemState;

use Elevator\Config;
use Elevator\Storage\StorageAdapterFilesystem;

class SystemStateManagerFactory
{
    public function create()
    {
        $systemStateFactory = new SystemStateFactory;

        $config = new Config;
        $storage = new StorageAdapterFilesystem($config);

        return new SystemStateManager($systemStateFactory, $storage);
    }
}