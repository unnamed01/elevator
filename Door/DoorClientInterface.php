<?php

namespace Elevator\Door;

interface DoorClientInterface
{
    /**
     * @param int $elevatorId
     * @param int $level
     * @return mixed
     */
    public function sendOpenDoorsCommand($elevatorId, $level);

    /**
     * @param int $elevatorId
     * @param int $floor
     * @return mixed
     */
    public function sendCloseDoorsCommand($elevatorId, $floor);
}