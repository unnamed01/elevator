<?php

namespace Elevator\Http;

class HttpResponseEmulator extends HttpResponse
{
    /**
     * @var string
     */
    private $body;

    /**
     * @var array
     */
    private $data;

    /**
     * @var array
     */
    private $emulatorData = [];

    public function getBody()
    {
        return $this->body;
    }

    public function getDataAsJson()
    {
        $data = [
            'error' => false,
            'data'  => $this->data
        ];

        return json_encode($data);
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function getData()
    {
        $data = $this->data;
        $data['emulator'] = $this->emulatorData;

        return $data;
    }

    public function setData(array $data)
    {
        $this->data = $data;
    }

    public function setError($errorMessage)
    {
        $this->data = [
            'error'   => true,
            'message' => $errorMessage
        ];
    }

    public function setEmulatorData($module, $action, array $params)
    {
        $emulatorData = [
            'module' => $module,
            'action' => $action,
            'params' => $params,
        ];
        $this->emulatorData[$module] = $emulatorData;
    }
}