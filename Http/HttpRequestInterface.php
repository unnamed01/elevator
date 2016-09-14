<?php

namespace Elevator\Http;

interface HttpRequestInterface
{
    /**
     * @return array
     */
    public function getData();

    /**
     * @return string
     */
    public function getRequestType();

    /**
     * @param string $item
     * @return string
     */
    public function getDataItem($item);
}