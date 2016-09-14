<?php

namespace Elevator;

interface ConfigInterface
{
    public function getBaseDir();

    public function getElevators();

    public function getFloors();
}