<?php

namespace Elevator\Handler;

use Elevator\Config;
use Elevator\Handler\LevelReachedHandler\Backend;
use Elevator\Http\HttpRequestInterface;
use Elevator\Http\HttpResponseInterface;

class LevelReachedHandler implements HandlerInterface
{
    private $backend;
    private $config;

    public function __construct(
        Config $config,
        Backend $backend
    ) {
        $this->config = $config;
        $this->backend = $backend;
    }

    public function handle(HttpRequestInterface $request, HttpResponseInterface $response)
    {
        $elevatorId = $request->getDataItem('elevatorId');
        $toLevel = $request->getDataItem('toLevel');
        //$direction = $request->getDataItem('direction');
        //$fromLevel = $request->getDataItem('fromLevel');

        $this->backend->handle($elevatorId, $toLevel);
    }
}