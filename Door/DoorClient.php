<?php

namespace Elevator\Door;

use Elevator\Client\ZmqClient;

class DoorClient extends ZmqClient implements DoorClientInterface
{
    const TOPIC_TEMPLATE = 'doors-elevator%s';
    const ACTION_OPEN = 'open';
    const ACTION_CLOSE = 'close';

    /**
     * @param int $elevatorId
     * @param int $level
     * @return \Elevator\Zmq\ZmqResponseInterface
     */
    public function sendOpenDoorsCommand($elevatorId, $level)
    {
        $data = [];
        $data['elevatorId'] = $elevatorId;
        $data['level'] = $level;
        $data['action'] = self::ACTION_OPEN;

        $topic = $this->getTopic($elevatorId);

        $response = $this->sendCommand($topic, $data);

        return $response;
    }

    /**
     * @param int $elevatorId
     * @param $floor
     * @return \Elevator\Zmq\ZmqResponseInterface
     */
    public function sendCloseDoorsCommand($elevatorId, $floor)
    {
        $data = [];
        $data['elevatorId'] = $elevatorId;
        $data['floor'] = $floor;
        $data['action'] = self::ACTION_CLOSE;

        $topic = $this->getTopic($elevatorId);

        $response = $this->sendCommand($topic, $data);

        return $response;
    }
    
    private function getTopic($elevatorId)
    {
        $topic = sprintf(self::TOPIC_TEMPLATE, $elevatorId);

        return $topic;
    }

}