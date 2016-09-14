<?php

namespace Elevator;

use Elevator\Http\HttpRequestFactory;
use Elevator\Http\HttpResponseFactory;
use Elevator\SystemState\SystemStateFactory;
use Elevator\SystemState\SystemStateInterface;

class EntryPointState implements EntryPointInterface
{
    /**
     * @var HttpRequestFactory
     */
    private $requestFactory;

    /**
     * @var HttpResponseFactory
     */
    private $responseFactory;

    /**
     * @var SystemStateInterface
     */
    private $systemState;
    
    private $config;

    public function __construct()
    {
        $this->requestFactory = new HttpRequestFactory;
        $this->responseFactory = new HttpResponseFactory;

        $systemStateFactory = new SystemStateFactory();
        $this->systemState = $systemStateFactory->create();


        $this->config = new Config;
    }

    public function handle(array $requestData)
    {
        $request = $this->requestFactory->create($requestData);
        $response = $this->responseFactory->create();

        $data = [];
        if ($this->systemState->isSystemInitialized()) {
            $data['isInitialized'] = true;
            $data['elevators'] = $this->systemState->getElevatorStates();
            $data['floors'] = $this->config->getFloors();
            $data['waypoints'] = $this->systemState->getElevatorWaypoints();
        } else {
            $data['isInitialized'] = false;
        }

        $response->setData($data);

        return $response;
    }
}