<?php

namespace Elevator\Handler;

use Elevator\ElevatorManagerInterface;
use Elevator\Http\HttpRequestInterface;
use Elevator\Http\HttpResponseInterface;

class CabinButtonHandler implements HandlerInterface
{
    /**
     * @var ElevatorManagerInterface
     */
    private $elevatorManager;

    public function __construct(ElevatorManagerInterface $elevatorManager)
    {
        $this->elevatorManager = $elevatorManager;
    }

    public function handle(HttpRequestInterface $request, HttpResponseInterface $response)
    {
        $floorId = $request->getDataItem('floorId');
        $elevatorId = $request->getDataItem('elevatorId');
        $direction = $request->getDataItem('direction');

        $elevator = $this->elevatorManager->getElevatorById($elevatorId);

        $elevator->addWaypoint($direction, $floorId);
        $this->elevatorManager->saveElevator($elevator);

        return $this;
    }
}