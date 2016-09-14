<?php

namespace Elevator\Waypoint;

use Elevator\Config;

class WaypointFactory
{
    public function create($direction, $floorId)
    {
        $config = new Config;

        $floorData = $config->getFloorById($floorId);

        return new Waypoint($direction, $floorData['level']);
    }
}