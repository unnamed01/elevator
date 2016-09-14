<?php

namespace Elevator\Http;

class HttpClientClientResponse implements HttpClientResponseInterface
{
    public function __construct($data)
    {

    }

    public function getData()
    {
        return [
            'a' => 'bc',
            'd' => 'ef',
            'g' => 'hi',
        ];
    }
}