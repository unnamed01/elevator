<?php

namespace Elevator\Waypoint\Filter\Comparator;

class GreaterThan implements ComparatorInterface
{
    /**
     * @param int $subject
     * @param int $context
     * @return bool
     */
    public function compare($subject, $context)
    {
        return $subject > $context;
    }
}