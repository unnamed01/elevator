<?php

namespace Elevator\Waypoint;

interface WaypointManagerInterface
{
    public function addWaypoint($floorId, $direction, $elevatorId);

    //public function deleteWaypoint($floor, $elevatorId);
}