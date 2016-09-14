<?php

namespace Elevator;

interface MovementManagerInterface
{
    public function moveToTheOnlyWaypoint(ElevatorInterface $elevator);
    public function moveForward(ElevatorInterface $elevator);
}