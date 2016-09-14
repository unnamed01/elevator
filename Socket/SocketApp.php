<?php

namespace Elevator\Socket;

use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;

class SocketApp implements WampServerInterface
{
    protected $commands = array();

    public function onSubscribe(ConnectionInterface $connection, $command) {
        $this->commands[$command->getId()] = $command;
    }

    /**
     * @param string $entry JSON'ified string we'll receive from ZeroMQ
     */
    public function onMessage($entry) {
        $entryData = json_decode($entry, true);

        var_dump($entryData);

        // If the lookup topic object isn't set there is no one to publish to
        if (!array_key_exists($entryData['command'], $this->commands)) {
            return;
        }

        $component = $this->commands[$entryData['command']];

        // re-send the data to all the clients subscribed to that category
        $component->broadcast($entryData);
    }

    public function onUnSubscribe(ConnectionInterface $conn, $topic) {
    }

    public function onOpen(ConnectionInterface $conn) {
    }

    public function onClose(ConnectionInterface $conn) {
    }

    public function onCall(ConnectionInterface $conn, $id, $topic, array $params) {
        // In this application if clients send data it's because the user hacked around in console
        $conn->callError($id, $topic, 'You are not allowed to make calls')->close();
    }

    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible) {
        // In this application if clients send data it's because the user hacked around in console
        $conn->close();
    }

    //11
    public function onError(ConnectionInterface $conn, \Exception $e) {
    }
}