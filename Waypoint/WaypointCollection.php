<?php

namespace Elevator\Waypoint;

use Elevator\SystemState\ElevatorState\ElevatorStateInterface;
use JsonSerializable;

class WaypointCollection implements WaypointCollectionInterface, JsonSerializable
{
    /**
     * @var WaypointInterface[]
     */
    private $waypoints;

    public function __construct(array $waypoints)
    {
        $this->waypoints = $this->sort($waypoints);
    }

    /**
     * @return WaypointInterface
     */
    public function getTheOnlyWaypoint()
    {
        $waypoints = $this->getAllWaypoints();
        $waypoint = reset($waypoints);  //get 1st

        return $waypoint;
    }

    /**
     * @return WaypointInterface[]
     */
    public function getAllWaypoints()
    {
        $waypoints = array_merge(
            [],
            $this->waypoints[ElevatorStateInterface::DIRECTION_UP],
            $this->waypoints[ElevatorStateInterface::DIRECTION_DOWN],
            $this->waypoints[ElevatorStateInterface::DIRECTION_NONE]
        );

        return $waypoints;
    }

    /**
     * @param WaypointInterface $waypoint
     * @return $this
     * @throws \Exception
     */
    public function addWaypoint(WaypointInterface $waypoint)
    {
        if (isset($this->waypoints[$waypoint->getDirection()][$waypoint->getLevel()])) {
            $message = sprintf(
                'Waypoint already exist [direction=%s level=%s]',
                $waypoint->getDirection(),
                $waypoint->getLevel()
            );
            throw new \Exception($message);
        }
        
        $this->waypoints[$waypoint->getDirection()][$waypoint->getLevel()] = $waypoint;

        return $this;
    }

    /**
     * @param string $direction
     * @param int $level
     * @return $this
     */
    public function deleteWaypoint($direction, $level)
    {
        unset($this->waypoints[$direction][$level]);

        return $this;
    }

    public function hasWaypoint($direction, $level)
    {
        return isset($this->waypoints[$direction][$level]);
    }

    public function isEmpty()
    {
        return $this->count() === 0;
    }

    public function count()
    {
        return count($this->waypoints[ElevatorStateInterface::DIRECTION_UP])
            + count($this->waypoints[ElevatorStateInterface::DIRECTION_DOWN])
            + count($this->waypoints[ElevatorStateInterface::DIRECTION_NONE]);
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
        return $this->getAllWaypoints();
    }

    private function sort(array $waypoints)
    {
        $waypointByDirectionByLevel = [
            ElevatorStateInterface::DIRECTION_UP => [],
            ElevatorStateInterface::DIRECTION_DOWN => [],
            ElevatorStateInterface::DIRECTION_NONE => [],
        ];

        /** @var WaypointInterface $waypoint */
        foreach($waypoints as $waypoint) {
            $direction = $waypoint->getDirection();
            $level = $waypoint->getLevel();

            $waypointByDirectionByLevel[$direction][$level] = $waypoint;
        }

        ksort($waypointByDirectionByLevel[ElevatorStateInterface::DIRECTION_UP], SORT_NUMERIC);
        ksort($waypointByDirectionByLevel[ElevatorStateInterface::DIRECTION_DOWN], SORT_NUMERIC);
        
        return $waypointByDirectionByLevel;
    }
}