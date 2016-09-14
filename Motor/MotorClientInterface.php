<?php

namespace Elevator\Motor;

interface MotorClientInterface
{
    public function sendMoveUpCommand($elevatorId, $fromLevel, $toLevel);
    public function sendMoveDownCommand($elevatorId, $fromLevel, $toLevel);
}