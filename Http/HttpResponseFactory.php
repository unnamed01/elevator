<?php

namespace Elevator\Http;

class HttpResponseFactory
{
    /**
     * @return HttpResponseInterface
     */
    public function create()
    {
        return new HttpResponse;
    }
}