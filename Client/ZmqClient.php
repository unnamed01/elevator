<?php

namespace Elevator\Client;

use Elevator\Config;
use Elevator\Zmq\ZmqResponseFactory;
use Elevator\Zmq\ZmqResponseInterface;
use ZMQ;
use ZMQContext;

class ZmqClient implements ClientInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var ZmqResponseInterface
     */
    private $responseFactory;


    /**
     * @var \ZMQSocket
     */
    private $socket;

    public function __construct(Config $config, ZmqResponseFactory $responseFactory)
    {
        $this->config = $config;
        $this->responseFactory = $responseFactory;
    }

    protected function getSocket()
    {
        if ($this->socket === null) {
            $context = new ZMQContext();

            $this->socket = $context->getSocket(ZMQ::SOCKET_PUSH, 'my pusher');
            $this->socket->connect($this->config->getPushSocketDsn());
        }

        return $this->socket;
    }

    /**
     * @param string $command
     * @param array $data
     * @return ZmqResponseInterface
     */
    protected function sendCommand($command, array $data = [])
    {
        $data = array_merge(
            ['command' => $command],
            $data,
            ['time' => time()]
        );

        $this->getSocket()->send(json_encode($data));

        $response = $this->responseFactory->create();

        return $response;
    }
}
