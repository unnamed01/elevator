<?php

namespace Elevator\Http;


interface HttpResponseInterface
{
    public function setBody($body);
    public function getBody();
    public function hasBody();

    public function setData(array $data);
    //public function getData();

    public function setError($errorMessage);

    public function getDataAsJson();
}