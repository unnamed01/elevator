<?php

namespace Elevator\Handler;

use Elevator\ElevatorManagerInterface;
use Elevator\Http\HttpRequestInterface;
use Elevator\Http\HttpResponseInterface;
use Elevator\MovementManagerInterface;
use Elevator\RequestDispatcher\RequestDispatcherInterface;

class RequestButtonHandler implements HandlerInterface
{
    /**
     * @var RequestDispatcherInterface
     */
    private $requestDispatcher;

    /**
     * @var ElevatorManagerInterface
     */
    private $elevatorManager;
    
    /**
     * @var MovementManagerInterface
     */
    private $movementManager;

    public function __construct(
        RequestDispatcherInterface $requestDispatcher,
        ElevatorManagerInterface $elevatorManager,
        MovementManagerInterface $movementManager
    ) {
        $this->requestDispatcher = $requestDispatcher;
        $this->elevatorManager = $elevatorManager;
        $this->movementManager = $movementManager;
    }

    public function handle(HttpRequestInterface $request, HttpResponseInterface $response)
    {
        $floorId = $request->getDataItem('floorId');
        $direction = $request->getDataItem('direction');

        $elevatorState = $this->requestDispatcher->dispatch($request);
        $elevatorId = $elevatorState->getId();

        $elevator = $this->elevatorManager->getElevatorById($elevatorId);
        
        $elevator->addWaypoint($direction, $floorId);
        $this->elevatorManager->saveElevator($elevator);

        if ($elevator->getState()->isFree()) {
            //If elevator is free and we just added a waypoint means we have only one now
            //So we can skip resolver
            //But we can face concurrency issues if someone added a waypoint in parallel, but in such case we just process random waypoint
            //TODO What if new waypoint got deleted?
            $this->movementManager->moveToTheOnlyWaypoint($elevator);
        } else {
            //do nothing
        }

        return $this;
    }
}