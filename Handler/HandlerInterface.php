<?php

namespace Elevator\Handler;

use Elevator\Http\HttpRequestInterface;
use Elevator\Http\HttpResponseInterface;

interface HandlerInterface
{
    public function handle(HttpRequestInterface $request, HttpResponseInterface $response);
}