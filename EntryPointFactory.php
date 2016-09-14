<?php

namespace Elevator;

use Elevator\Http\HttpRequestFactory;
use Elevator\Http\HttpResponseFactory;

class EntryPointFactory
{
    /**
     * @return EntryPointInterface
     */
    public function create()
    {
        $requestFactory = new HttpRequestFactory;
        $responseFactory = new HttpResponseFactory;
        $dispatcher = new Dispatcher;

        $entryPoint = new EntryPoint($requestFactory, $responseFactory, $dispatcher);

        return $entryPoint;
    }
}