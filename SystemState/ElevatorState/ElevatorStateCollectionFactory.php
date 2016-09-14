<?php

namespace Elevator\SystemState\ElevatorState;

class ElevatorStateCollectionFactory
{
    /**
     * @param array[] $items
     * @return ElevatorStateCollection
     */
    public function create(array $items)
    {
        return new ElevatorStateCollection($items);
    }
}