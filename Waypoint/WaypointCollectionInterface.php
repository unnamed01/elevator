<?php

namespace Elevator\Waypoint;

interface WaypointCollectionInterface extends \Countable
{
    /**
     * @return WaypointInterface
     */
    public function getTheOnlyWaypoint();

    /**
     * @return WaypointInterface[]
     */
    public function getAllWaypoints();

    /**
     * @param WaypointInterface $waypoint
     * @return $this
     */
    public function addWaypoint(WaypointInterface $waypoint);

    /**
     * @param string $direction
     * @param int $level
     * @return $this
     */
    public function deleteWaypoint($direction, $level);

    /**
     * @param int $direction
     * @param int $level
     * @return bool
     */
    public function hasWaypoint($direction, $level);

    /**
     * @return bool
     */
    public function isEmpty();
}