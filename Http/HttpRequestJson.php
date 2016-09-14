<?php

namespace Elevator\Http;

class HttpRequestJson implements HttpRequestInterface
{
    /**
     * @var array
     */
    private $requestData;

    /**
     * @var
     */
    private $data;

    public function __construct(array $requestData)
    {
        $this->requestData = $requestData;
    }

    /**
     * @return array
     * @throws \HttpException
     */
    public function getData()
    {
        if (!$this->data) {
            $this->data = $this->decode($this->requestData['data']);
        }

        if (!$this->data) {
            throw new \HttpException('Unable to decode HTTP JSON request');
        }

        return $this->data;
    }

    /**
     * @param string $item
     * @return string
     * @throws \HttpException
     */
    public function getDataItem($item)
    {
        $data = $this->getData();

        return $data[$item];
    }

    /**
     * @return string
     */
    public function getRequestType()
    {
        return $this->getDataItem('type');
    }

    private function decode($requestData)
    {
        return json_decode($requestData, true);
    }
}