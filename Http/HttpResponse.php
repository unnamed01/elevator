<?php

namespace Elevator\Http;

class HttpResponse implements HttpResponseInterface
{
    /**
     * @var string
     */
    private $body;

    /**
     * @var string
     */
    private $data;

    public function __construct()
    {
        $this->body = null;
        $this->data = [
            'error' => false,
            'data'  => null
        ];
    }


    public function setBody($body)
    {
        $this->body = $body;
    }

    public function setData(array $data)
    {
        $this->data = [
            'error' => false,
            'data'  => $data
        ];
    }

    public function setError($errorMessage)
    {
        $this->data = [
            'error'   => true,
            'message' => $errorMessage
        ];
    }

    public function hasBody()
    {
        return $this->body !== null;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getDataAsJson()
    {
        return json_encode($this->data);    //JSON_FORCE_OBJECT
    }
}