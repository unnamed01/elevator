<?php

namespace Elevator\Waypoint;

use JsonSerializable;

class Waypoint implements WaypointInterface, JsonSerializable
{
    /**
     * @var int
     */
    private $direction;
    
    /**
     * @var int
     */
    private $level;

    /**
     * @param int $direction
     * @param int $level
     */
    public function __construct($direction, $level)
    {
        $this->direction = $direction;
        $this->level = $level;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @return int
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'direction' => $this->direction,
            'level'     => $this->level,
        ];
    }
}