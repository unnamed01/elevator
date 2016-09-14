<?php

namespace Elevator\Waypoint\Filter\Comparator;

interface ComparatorInterface
{
    /**
     * @param int $subject
     * @param int $context
     * @return bool
     */
    public function compare($subject, $context);
}