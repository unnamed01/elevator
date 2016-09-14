<?php

namespace Elevator\Http;

interface HttpAdapterInterface
{
    /**
     * @param string $url
     * @param array $data
     * @return HttpClientResponseInterface
     */
    public function request($url, array $data = []);
}