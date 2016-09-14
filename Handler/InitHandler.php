<?php

namespace Elevator\Handler;

use Elevator\Config;
use Elevator\ElevatorFactory;
use Elevator\ElevatorManagerInterface;
use Elevator\Http\HttpRequestInterface;
use Elevator\Http\HttpResponseInterface;
use Elevator\SystemState\ElevatorState\ElevatorStateInterface;
use Elevator\SystemState\SystemStateInterface;

class InitHandler implements HandlerInterface
{
    const INITIAL_LEVEL = 0;
    const INITIAL_DIRECTION = ElevatorStateInterface::DIRECTION_NONE;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var ElevatorManagerInterface
     */
    private $elevatorManager;

    /**
     * @var ElevatorFactory
     */
    private $elevatorFactory;

    /**
     * @var SystemStateInterface
     */
    private $systemState;

    public function __construct(
        Config $config,
        ElevatorFactory $elevatorFactory,
        ElevatorManagerInterface $elevatorManager,
        SystemStateInterface $systemState
    ) {
        $this->config = $config;
        $this->elevatorFactory = $elevatorFactory;
        $this->elevatorManager = $elevatorManager;
        $this->systemState = $systemState;
    }

    public function handle(HttpRequestInterface $request, HttpResponseInterface $response)
    {
        $this->systemState->clearSystemState();

        foreach ($this->config->getElevators() as $elevatorData) {
            $stateData = $this->getInitialElevatorState();
            $stateData['id'] = $elevatorData['id'];
            $stateData['htmlId'] = $elevatorData['htmlId'];
            $waypointData = [
                /*[
                    'floorId'  => ,
                    'direction =>'
                ]*/
            ];

            $elevator = $this->elevatorFactory->createFromArray($stateData, $waypointData);

            $this->elevatorManager->saveElevator($elevator);
        }

        $this->systemState->setSystemInitialized();

        $data = [];
        $response->setData($data);
    }

    protected function getInitialElevatorState()
    {
        $initialStateData = [
            'id'        => null,
            'level'     => self::INITIAL_LEVEL,
            'direction' => self::INITIAL_DIRECTION,
            'is_free'   => true
        ];

        return $initialStateData;
    }
}