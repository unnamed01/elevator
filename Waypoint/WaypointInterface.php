<?php

namespace Elevator\Waypoint;

interface WaypointInterface
{
    /**
     * @return int
     */
    public function getLevel();

    /**
     * @return int
     */
    public function getDirection();
}