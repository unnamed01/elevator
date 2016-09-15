<?php

namespace Elevator\Handler;

use Elevator\Config;
use Elevator\ElevatorManagerInterface;
use Elevator\Http\HttpRequestInterface;
use Elevator\Http\HttpResponseInterface;

class CabinButtonHandler implements HandlerInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var ElevatorManagerInterface
     */
    private $elevatorManager;

    public function __construct(Config $config, ElevatorManagerInterface $elevatorManager)
    {
        $this->config = $config;
        $this->elevatorManager = $elevatorManager;
    }

    public function handle(HttpRequestInterface $request, HttpResponseInterface $response)
    {
        $floorId = $request->getDataItem('floorId');
        $elevatorId = $request->getDataItem('elevatorId');
        $direction = $request->getDataItem('direction');

        $elevator = $this->elevatorManager->getElevatorById($this->config->getElevatorByHtmlId($elevatorId)['id']);

        $elevator->addWaypoint($direction, $floorId);
        $this->elevatorManager->saveElevator($elevator);

        return $this;
    }
}