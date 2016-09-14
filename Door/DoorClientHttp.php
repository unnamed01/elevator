<?php

namespace Elevator\Door;

use Elevator\Client\HttpClient;

class DoorClientHttp extends HttpClient implements DoorClientInterface
{
    const COMMAND_DOOR_OPEN_URL = 'open';
    const COMMAND_DOOR_CLOSE_URL = 'close';

    public function sendOpenDoorsCommand($elevatorId, $level)
    {
        $data = [];
        $data['elevatorId'] = $elevatorId;
        $data['floor'] = $level;

        $response = $this->request(self::COMMAND_DOOR_OPEN_URL, $data);
        
        return $response;

    }

    public function sendCloseDoorsCommand($elevatorId, $floor)
    {
        $data = [];
        $data['elevatorId'] = $elevatorId;
        $data['floor'] = $floor;

        $response = $this->request(self::COMMAND_DOOR_CLOSE_URL, $data);

        return $response;
    }
}