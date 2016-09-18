<?php

namespace Elevator;

use Elevator\Handler\CabinButtonHandlerFactory;
use Elevator\Handler\DoorClosedHandlerFactory;
use Elevator\Handler\LevelReachedHandlerFactory;
use Elevator\Handler\InitHandlerFactory;
use Elevator\Handler\RequestButtonHandlerFactory;
use Elevator\Http\HttpRequestFactory;
use Elevator\Http\HttpRequestInterface;
use Elevator\Http\HttpResponseFactory;
use Elevator\Http\HttpResponseInterface;

class Dispatcher implements DispatcherInterface
{
    const REQUEST_TYPE_INIT = 0;
    const REQUEST_TYPE_REQUEST_BUTTON = 1;
    const REQUEST_TYPE_CABIN_BUTTON = 2;
    const REQUEST_TYPE_LEVEL_REACHED = 3;
    const REQUEST_TYPE_DOOR_CLOSED = 4;

    private $initHandlerFactory;
    private $requestButtonHandlerFactory;
    private $cabinButtonHandlerFactory;
    private $levelReachedHandlerFactory;
    private $doorClosedHandlerFactory;


    public function __construct()
    {
        $this->initHandlerFactory = new InitHandlerFactory;
        $this->requestButtonHandlerFactory = new RequestButtonHandlerFactory;
        $this->cabinButtonHandlerFactory = new CabinButtonHandlerFactory;
        $this->levelReachedHandlerFactory = new LevelReachedHandlerFactory;
        $this->doorClosedHandlerFactory = new DoorClosedHandlerFactory;
    }

    public function dispatch(HttpRequestInterface $request, HttpResponseInterface $response)
    {
        $handlers = $this->getHandlers();
        $handler = $handlers[$request->getRequestType()];

        $handler->handle($request, $response);
    }

    private function getHandlers()
    {
        return [
            self::REQUEST_TYPE_INIT           => $this->initHandlerFactory->create(),
            self::REQUEST_TYPE_REQUEST_BUTTON => $this->requestButtonHandlerFactory->create(),
            self::REQUEST_TYPE_CABIN_BUTTON   => $this->cabinButtonHandlerFactory->create(),
            self::REQUEST_TYPE_LEVEL_REACHED  => $this->levelReachedHandlerFactory->create(),
            self::REQUEST_TYPE_DOOR_CLOSED    => $this->doorClosedHandlerFactory->create(),
        ];
    }
}