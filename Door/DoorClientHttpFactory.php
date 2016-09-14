<?php

namespace Elevator\Door;

use Elevator\Http\HttpAdapterCurl;
use Elevator\Http\HttpResponseFactory;

class DoorClientHttpFactory
{
    public function create()
    {
        $httpResponseFactory = new HttpResponseFactory;
        $httpAdapter = new HttpAdapterCurl($httpResponseFactory);

        $httpResponseFactory = new HttpResponseFactory;

        return new DoorClient($httpAdapter, $httpResponseFactory);
    }
}