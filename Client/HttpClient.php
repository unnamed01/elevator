<?php

namespace Elevator\Client;

use Elevator\Http\HttpAdapterInterface;
use Elevator\Http\HttpClientResponseInterface;
use Elevator\Http\HttpResponseFactory;

class HttpClient implements ClientInterface
{
    /**
     * @var HttpAdapterInterface
     */
    protected $httpAdapter;
    
    /**
     * @var HttpResponseFactory
     */
    private $httpResponseFactory;

    public function __construct(HttpAdapterInterface $httpAdapter, HttpResponseFactory $httpResponseFactory)
    {
        $this->httpAdapter = $httpAdapter;
        $this->httpResponseFactory = $httpResponseFactory;
    }

    /**
     * @param $url
     * @param array $data
     * @return HttpClientResponseInterface
     */
    protected function request($url, array $data = [])
    {
        $response = $this->httpAdapter->request($url, $data);

        return $response;
    }
}