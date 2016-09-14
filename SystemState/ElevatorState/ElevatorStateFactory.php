<?php

namespace Elevator\SystemState\ElevatorState;

class ElevatorStateFactory
{
    public function create($data)
    {
        return new ElevatorState($data);
    }
}