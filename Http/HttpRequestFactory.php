<?php

namespace Elevator\Http;

class HttpRequestFactory
{
    /**
     * @param array $requestData
     * @return HttpRequestJson
     */
    public function create(array $requestData)
    {
        return new HttpRequestJson($requestData);
    }
}