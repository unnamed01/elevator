<?php

namespace Elevator\SystemState;

use Elevator\SystemState\ElevatorState\ElevatorStateCollectionInterface;

interface SystemStateInterface
{
    /**
     * @return ElevatorStateCollectionInterface
     */
    public function getElevatorStates();

    public function isSystemInitialized();

    public function setSystemInitialized();

    public function clearSystemState();
}