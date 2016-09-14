<?php

namespace Elevator\SystemState;

interface SystemStateManagerInterface
{
    /**
     * @return SystemStateInterface
     */
    public function getSystemState();
}