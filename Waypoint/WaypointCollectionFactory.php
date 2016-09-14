<?php

namespace Elevator\Waypoint;

class WaypointCollectionFactory
{
    public function create($waypointData)
    {
        $waypointFactory = new WaypointFactory;

        $waypoints = [];
        foreach ($waypointData as $item) {
            $waypoint = $waypointFactory->create($item['direction'], $item['floorId']);
            $waypoints[] = $waypoint;
        }
        return new WaypointCollection($waypoints);
    }
}