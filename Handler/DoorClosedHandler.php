<?php

namespace Elevator\Handler;

use Elevator\Config;
use Elevator\ElevatorManagerInterface;
use Elevator\Handler\LevelReachedHandler\Backend;
use Elevator\Http\HttpRequestInterface;
use Elevator\Http\HttpResponseInterface;
use Elevator\MovementManagerInterface;

class DoorClosedHandler implements HandlerInterface
{
    private $backend;

    private $config;

    /**
     * @var MovementManagerInterface
     */
    private $movementManager;

    /**
     * @var ElevatorManagerInterface
     */
    private $elevatorManager;

    public function __construct(
        Config $config,
        Backend $backend,
        MovementManagerInterface $movementManager,
        ElevatorManagerInterface $elevatorManager
    ) {
        $this->config = $config;
        $this->backend = $backend;
        $this->movementManager = $movementManager;
        $this->elevatorManager = $elevatorManager;
    }

    /**
     * @param HttpRequestInterface $request
     * @param HttpResponseInterface $response
     * @return $this
     */
    public function handle(HttpRequestInterface $request, HttpResponseInterface $response)
    {
        $elevatorId = $request->getDataItem('elevatorId');

        $elevator = $this->elevatorManager->getElevatorById($elevatorId);

        if($elevator->getWaypoints()->isEmpty()) {
            $elevator->goIdle();
            $this->elevatorManager->saveElevator($elevator);

            $response->setData([
                'idle'       => true,
                'elevatorId' => $elevatorId,
            ]);

            return $this;
        }

        $this->movementManager->moveForward($elevator);

        $response->setData([
            'idle'       => false,
            'elevatorId' => $elevatorId,
            'direction'  => $elevator->getState()->getDirection(),
            'level'      => $elevator->getState()->getLevel(),
        ]);

        return $this;
    }
}