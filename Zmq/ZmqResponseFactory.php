<?php

namespace Elevator\Zmq;

class ZmqResponseFactory
{
    /**
     * @return ZmqResponseInterface
     */
    public function create()
    {
        return new ZmqResponse;
    }
}