<?php

namespace Elevator;


use Elevator\SystemState\ElevatorState\ElevatorStateInterface;
use Elevator\Waypoint\WaypointCollectionInterface;

interface ElevatorInterface
{
    public function getId();

    /**
     * @return ElevatorStateInterface
     */
    public function getState();

    /**
     * @return WaypointCollectionInterface
     */
    public function getWaypoints();

    public function addWaypoint($direction, $floorId);
    public function deleteWaypoint($direction, $level);

    public function setLevel($level);
    public function setDirection($direction);
    
    public function switchDirection();

    public function goIdle();
    public function goActive();
}