<?php

namespace Elevator\Waypoint\Filter;

use Elevator\Waypoint\WaypointFilterInterface;

class ComparatorFactory
{
    private $map = [
        WaypointFilterInterface::EQUAL        => \Elevator\Waypoint\Filter\Comparator\Equal::class,
        WaypointFilterInterface::GREATER_THAN => \Elevator\Waypoint\Filter\Comparator\GreaterThan::class,
        WaypointFilterInterface::LESS_THAN    => \Elevator\Waypoint\Filter\Comparator\LessThan::class,
        WaypointFilterInterface::ANY          => \Elevator\Waypoint\Filter\Comparator\Any::class,
    ];


    public function create($comparatorDescriptor)
    {
        $className = $this->map[$comparatorDescriptor];

        return new $className;
    }
}