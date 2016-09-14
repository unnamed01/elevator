<?php

namespace Elevator\Waypoint;

interface WaypointFilterInterface
{
    const EQUAL = '==';
    const GREATER_THAN = '>';
    const LESS_THAN = '<';
    const ANY = 'any';

    public function filter(WaypointCollectionInterface $waypointCollection, array $filters = null);
}