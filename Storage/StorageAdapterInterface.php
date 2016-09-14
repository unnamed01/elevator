<?php

namespace Elevator\Storage;

interface StorageAdapterInterface
{
    public function getElevatorStateById($elevatorId);
    public function getElevatorStates();

    public function getElevatorWaypointsById($elevatorId);
    public function getElevatorWaypoints();

    public function saveWaypoints($elevatorId, $waypoints);
    public function saveElevatorState($elevatorId, $state);

    public function isInitFileExists();
    public function createInitFile();

    public function clearSystemData();
}