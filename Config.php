<?php

namespace Elevator;


class Config implements ConfigInterface
{
    const BASE_DIR = '/home/vagrant/p/elevator';

    public function getBaseDir()
    {
        return __DIR__;
        //return self::BASE_DIR;
    }

    public function getElevators()
    {
        return [
            [
                'id' => 0,
                'htmlId' => 'elevator0',
                'name' => 'elevator#1'
            ],
            [
                'id' => 1,
                'htmlId' => 'elevator1',
                'name' => 'elevator#2'
            ],
        ];

    }

    public function getFloors()
    {
        return [
            [
                'id'    => 'a',
                'name'  => '-1 floor',
                'level' => -1,
            ],
            [
                'id'    => 'b',
                'name'  => 'ground floor',
                'level' => 0,
             ],
            [
                'id'    => 'c',
                'name'  => '+1 floor',
                'level' => 1,
            ],
            [
                'id'    => 'd',
                'name'  => '+2 floor',
                'level' => 2,
            ],
        ];
    }

    public function getFloorById($id)
    {
        $floors = $this->getFloors();

        $theRequired = array_reduce($floors, function($carry, $item) use ($id) {
            if($item['id']===$id){
                return $item;
            } else {
                return $carry;
            }
        });

        return $theRequired;
    }

    public function getFloorByLevel($level)
    {
        $floors = $this->getFloors();

        $theRequired = array_reduce($floors, function($carry, $item) use ($level) {
            if($item['level']===$level){
                return $item;
            } else {
                return $carry;
            }
        });

        return $theRequired;
    }

    public function getPushSocketDsn()
    {
        return 'tcp://localhost:5555';
    }

    public function getPullSocketDsn()
    {
        return 'tcp://127.0.0.1:5555';
    }

    public function getWebSocketHost()
    {
        $host = '0.0.0.0'; // Binding to 0.0.0.0 means remotes can connect
        return $host;
    }

    public function getWebSocketPort()
    {
        $port = 8080;
        return $port;
    }
}