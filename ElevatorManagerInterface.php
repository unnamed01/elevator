<?php

namespace Elevator;

interface ElevatorManagerInterface
{
    /**
     * @param int $elevatorId
     * @return ElevatorInterface
     */
    public function getElevatorById($elevatorId);

    /**
     * @return ElevatorInterface[]
     */
    public function getElevators();

    public function saveElevator(ElevatorInterface $elevator);
}