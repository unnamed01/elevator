<?php

namespace Elevator\SystemState\ElevatorState;

use JsonSerializable;

class ElevatorState implements ElevatorStateInterface, JsonSerializable
{
    /**
     * @var
     */
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getId()
    {
        return $this->data['id'];
    }

    /**
     * @return bool
     */
    public function isFree()
    {
        return $this->data['is_free'];
    }

    /**
     * @param bool $isFree
     * @return $this
     */
    public function setFree($isFree)
    {
        $this->data['is_free'] = $isFree;

        return $this;
    }

    public function getLevel()
    {
        return $this->data['level'];
    }

    public function setLevel($level)
    {
        $this->data['level'] = $level;
    }

    public function getDirection()
    {
        return $this->data['direction'];
    }

    public function setDirection($direction)
    {
        $this->data['direction'] = $direction;
    }

    public function getHtmlId()
    {
        return $this->data['htmlId'];
    }

    public function jsonSerialize()
    {
        return $this->data;
    }
}