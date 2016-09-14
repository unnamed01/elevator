<?php

namespace Elevator\SystemState;

use Elevator\Storage\StorageAdapterInterface;

class SystemStateManager implements SystemStateManagerInterface
{
    /**
     * @var SystemStateFactory
     */
    private $systemStateFactory;

    /**
     * @var StorageAdapterInterface
     */
    private $storageAdapter;

    public function __construct(SystemStateFactory $systemStateFactory, StorageAdapterInterface $storageAdapter)
    {
        $this->systemStateFactory = $systemStateFactory;
        $this->storageAdapter = $storageAdapter;
    }

    public function getSystemState()
    {
        return $this->systemStateFactory->create();
    }
}