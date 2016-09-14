<?php

namespace Elevator;

use Elevator\Http\HttpRequestInterface;
use Elevator\Http\HttpResponseInterface;

interface DispatcherInterface
{
    public function dispatch(HttpRequestInterface $request, HttpResponseInterface $response);
}