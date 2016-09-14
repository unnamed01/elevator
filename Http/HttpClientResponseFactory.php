<?php

namespace Elevator\Http;

class HttpClientResponseFactory
{
    public function create($data)
    {
        return new HttpClientClientResponse($data);
    }
}