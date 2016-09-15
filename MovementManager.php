<?php

namespace Elevator;

use Elevator\Handler\LevelReachedHandler\BackendFactory;
use Elevator\Motor\MotorClientInterface;
use Elevator\SystemState\ElevatorState\ElevatorStateInterface;
use Elevator\Waypoint\WaypointInterface;

class MovementManager implements MovementManagerInterface
{
    /**
     * @var ElevatorManagerInterface
     */
    private $elevatorManager;

    /**
     * @var BackendFactory
     */
    private $backendFactory;

    /**
     * @var MotorClientInterface
     */
    private $motorClient;

    public function __construct(
        ElevatorManagerInterface $elevatorManager,
        BackendFactory $backendFactory,
        MotorClientInterface $motorClient
    ) {
        $this->elevatorManager = $elevatorManager;
        $this->backendFactory = $backendFactory;
        $this->motorClient = $motorClient;
    }

    public function moveToTheOnlyWaypoint(ElevatorInterface $elevator)
    {
        $waypoint = $elevator->getWaypoints()->getTheOnlyWaypoint();
        $this->moveTo($elevator, $waypoint);

        return $this;
    }

    public function moveForward(ElevatorInterface $elevator)
    {
        if ($elevator->getWaypoints()->count()===1) {
            $this->moveToTheOnlyWaypoint($elevator);
            return $this;
        }

        if ($elevator->getState()->getDirection()===ElevatorStateInterface::DIRECTION_UP) {
            $this->moveUp($elevator);
        } elseif ($elevator->getState()->getDirection()===ElevatorStateInterface::DIRECTION_DOWN) {
            $this->moveDown($elevator);
        } else {
            throw new \Exception('Invalid movement direction ' . $elevator->getState()->getDirection());
        }

        return $this;
    }

    private function moveTo(ElevatorInterface $elevator, WaypointInterface $waypoint)
    {
        $state = $elevator->getState();

        if ($state->getLevel() < $waypoint->getLevel()) {
            $this->moveUp($elevator);
        } elseif ($state->getLevel() > $waypoint->getLevel()) {
            $this->moveDown($elevator);
        } elseif ($state->getLevel() === $waypoint->getLevel()){
            $this->alreadyReached($elevator, $waypoint);
        } else {
            throw new \Exception('Impossible case happened');
        }
    }

    private function moveUp(ElevatorInterface $elevator)
    {
        $elevator->goActive();
        $elevator->setDirection(ElevatorStateInterface::DIRECTION_UP);
        $this->elevatorManager->saveElevator($elevator);

        $this->motorClient->sendMoveUpCommand(
            $elevator->getId(),
            $elevator->getState()->getLevel(),
            $elevator->getState()->getLevel()+1
        );
    }

    private function moveDown(ElevatorInterface $elevator)
    {
        $elevator->goActive();
        $elevator->setDirection(ElevatorStateInterface::DIRECTION_DOWN);
        $this->elevatorManager->saveElevator($elevator);

        $this->motorClient->sendMoveDownCommand(
            $elevator->getId(),
            $elevator->getState()->getLevel(),
            $elevator->getState()->getLevel()-1
        );
    }

    private function alreadyReached(ElevatorInterface $elevator, WaypointInterface $waypoint)
    {
        //NOTE We need to set direction to delete correct waypoint when doors will open
        $elevator->goActive();
        $elevator->setDirection($waypoint->getDirection());
        $this->elevatorManager->saveElevator($elevator);

        $levelReachedHandler = $this->backendFactory->create();
        $levelReachedHandler->fakeHandle($elevator->getState()->getHtmlId(), $waypoint->getLevel());
    }
}