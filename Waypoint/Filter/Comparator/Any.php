<?php

namespace Elevator\Waypoint\Filter\Comparator;

class Any implements ComparatorInterface
{
    /**
     * @param int $subject
     * @param int $context
     * @return bool
     */
    public function compare($subject, $context)
    {
        return true;
    }
}