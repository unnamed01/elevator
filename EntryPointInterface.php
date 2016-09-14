<?php

namespace Elevator;

use Elevator\Http\HttpResponseInterface;

interface EntryPointInterface
{
    /*const EVENT_TYPE_FLOOR_BUTTON_PRESSED = 0;
    const EVENT_TYPE_CABIN_BUTTON_PRESSED = 1;
    const EVENT_TYPE_CALL_ASSIGNED = 2;
    const EVENT_TYPE_FLOOR_REACHED = 3;
    const EVENT_TYPE_DOORS_CLOSED = 4;

    const COMPONENT_DISPATCHER = 0;
    const COMPONENT_ELEVATOR_CONTROLLER = 1;
    const COMPONENT_DOORS = 2;
    const COMPONENT_INDICATORS = 3;
    const COMPONENT_CABIN_BUTTON_HANDLER = 4;*/

    /**
     * @param array $request
     * @return HttpResponseInterface
     */
    public function handle(array $request);
}