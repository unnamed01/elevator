<?php

namespace Elevator\Http;

class HttpAdapterCurl implements HttpAdapterInterface
{
    /**
     * @var HttpResponseFactory
     */
    private $httpResponseFactory;

    public function __construct(HttpResponseFactory $httpResponseFactory)
    {
        $this->httpResponseFactory = $httpResponseFactory;
    }

    /**
     * @param string $url
     * @param array $data
     * @return HttpClientResponseInterface
     */
    public function request($url, array $data = [])
    {
        //TODO transfer $data

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $result = curl_exec($ch);
        curl_close($ch);

        $response = $this->httpResponseFactory->create($result);

        return $response;
    }
}