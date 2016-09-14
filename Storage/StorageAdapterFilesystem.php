<?php

namespace Elevator\Storage;

use Elevator\Config;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class StorageAdapterFilesystem implements StorageAdapterInterface
{
    const TMP_DIR = 'tmp';
    const WAYPOINTS_FILE_PREFIX = 'waypoints';
    const STATE_FILE_PREFIX = 'state';
    const INIT_FILE = 'init';

    const FILE_PERMISSIONS = 0666;

    /**
     * @var Config
     */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function getElevatorStateById($elevatorId)
    {
        $filename = $this->getStateFileName($elevatorId);
        $content = file_get_contents($filename);
        if ($content === false) {
            throw new \Exception('Unable to read elevator state file');
        }
        $data = unserialize($content);

        return $data;
    }

    public function getElevatorStates()
    {
        $states = [];
        foreach($this->config->getElevators() as $elevator)
        {
            $data = $this->getElevatorStateById($elevator['id']);
            $states[] = $data;
        }

        return $states;
    }

    public function getElevatorWaypointsById($elevatorId)
    {
        $filename = $this->getWaypointsFileName($elevatorId);
        $content = file_get_contents($filename);
        if ($content === false) {
            throw new \Exception('Unable to read elevator waypoints file');
        }
        $data = unserialize($content);

        return $data;
    }

    public function getElevatorWaypoints()
    {
        $waypoints = [];
        foreach($this->config->getElevators() as $elevator)
        {
            $data = $this->getElevatorWaypointsById($elevator['id']);
            $waypoints[$elevator['htmlId']] = $data;
        }

        return $waypoints;
    }

    public function saveWaypoints($elevatorId, $waypoints)
    {
        $filename = $this->getWaypointsFileName($elevatorId);
        
        $result = file_put_contents($filename, serialize($waypoints)).PHP_EOL;
        if ($result === 0) {
            throw new \Exception('Failed to save waypoints');
        }

        $result = chmod($filename, self::FILE_PERMISSIONS);
        if (!$result) {
            throw new \Exception('Failed to change file permissions when saving waypoints');
        }

        return $this;
    }

    public function saveElevatorState($elevatorId, $state)
    {
        $filename = $this->getStateFileName($elevatorId);

        $result = file_put_contents($filename, serialize($state));
        if ($result === 0) {
            throw new \Exception('Failed to save state');
        }

        $result = chmod($filename, self::FILE_PERMISSIONS);
        if (!$result) {
            throw new \Exception('Failed to change file permissions when saving state');
        }

        return $this;
    }

    public function isInitFileExists()
    {
        $filename = $this->getInitFileName();

        return file_exists($filename);
    }

    public function createInitFile()
    {
        $filename = $this->getInitFileName();

        $result = file_put_contents($filename, '1');
        if ($result === 0) {
            throw new \Exception('Failed to save init file');
        }

        $result = chmod($filename, self::FILE_PERMISSIONS);
        if ($result === 0) {
            throw new \Exception('Failed to change init file permissions');
        }


        return $this;
    }

    public function clearSystemData()
    {
        $dir = self::TMP_DIR;

        $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
        foreach($files as $file) {
            unlink($file->getRealPath());
        }
    }

    private function getWaypointsFileName($elevatorId)
    {
        $filename = sprintf(
            '%s/%s/%d-%s',
            $this->config->getBaseDir(),
            self::TMP_DIR,
            $elevatorId,
            self::WAYPOINTS_FILE_PREFIX
        );

        return $filename;
    }

    private function getStateFileName($elevatorId)
    {
        $filename = sprintf(
            '%s/%s/%d-%s',
            $this->config->getBaseDir(),
            self::TMP_DIR,
            $elevatorId,
            self::STATE_FILE_PREFIX
        );

        return $filename;
    }

    private function getInitFileName()
    {
        $filename = sprintf(
            '%s/%s/%s',
            $this->config->getBaseDir(),
            self::TMP_DIR,
            self::INIT_FILE
        );

        return $filename;
    }
}