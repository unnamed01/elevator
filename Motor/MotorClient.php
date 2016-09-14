<?php

namespace Elevator\Motor;

use Elevator\Client\ZmqClient;
use Elevator\SystemState\ElevatorState\ElevatorStateInterface;

class MotorClient extends ZmqClient implements MotorClientInterface
{
    const TOPIC_TEMPLATE = 'motor-elevator%s';
    const DIRECTION_UP = ElevatorStateInterface::DIRECTION_UP;
    const DIRECTION_DOWN = ElevatorStateInterface::DIRECTION_DOWN;

    public function sendMoveUpCommand($elevatorId, $fromLevel, $toLevel)
    {
        $data = [];
        $data['elevatorId'] = $elevatorId;
        $data['fromLevel'] = $fromLevel;
        $data['toLevel'] = $toLevel;
        $data['direction'] = self::DIRECTION_UP;

        $topic = $this->getTopic($elevatorId);

        $response = $this->sendCommand($topic, $data);
        
        return $response;
    }

    public function sendMoveDownCommand($elevatorId, $fromLevel, $toLevel)
    {
        $data = [];
        $data['elevatorId'] = $elevatorId;
        $data['fromLevel'] = $fromLevel;
        $data['toLevel'] = $toLevel;
        $data['direction'] = self::DIRECTION_DOWN;

        $topic = $this->getTopic($elevatorId);

        $response = $this->sendCommand($topic, $data);

        return $response;
    }

    protected function getTopic($elevatorId)
    {
        $topic = sprintf(self::TOPIC_TEMPLATE, $elevatorId);

        return $topic;
    }
}