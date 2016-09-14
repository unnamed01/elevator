<?php

namespace Elevator\Handler;

use Elevator\Config;
use Elevator\Handler\LevelReachedHandler\BackendFactory;

class LevelReachedHandlerFactory
{
    public function create()
    {
        $config = new Config;

        $backendFactory = new BackendFactory;
        $backend = $backendFactory->create();

        return new LevelReachedHandler($config, $backend);
    }
}