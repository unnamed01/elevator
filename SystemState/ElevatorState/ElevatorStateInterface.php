<?php

namespace Elevator\SystemState\ElevatorState;

interface ElevatorStateInterface
{
    const DIRECTION_UP = 'up';
    const DIRECTION_DOWN = 'down';
    const DIRECTION_NONE = 'none';

    public function getId();

    /**
     * @return bool
     */
    public function isFree();

    /**
     * @param bool $isFree
     * @return $this
     */
    public function setFree($isFree);

    public function getLevel();
    public function setLevel($level);

    public function getDirection();
    public function setDirection($direction);

    public function getHtmlId();
}