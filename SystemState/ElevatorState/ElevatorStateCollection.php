<?php

namespace Elevator\SystemState\ElevatorState;

use JsonSerializable;

class ElevatorStateCollection implements ElevatorStateCollectionInterface, JsonSerializable
{
    /**
     * @var ElevatorStateInterface[]
     */
    private $items = [];

    /**
     * @var ElevatorStateInterface[]
     */
    private $itemsById = [];

    /**
     * @var int
     */
    private $index;

    /**
     * ElevatorStateCollection constructor.
     * @param ElevatorStateInterface[] $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
        $this->setItems($items);
    }

    /**
     * @param int $id
     * @return ElevatorStateInterface
     */
    public function getItemById($id)
    {
        if (!$this->hasId($id)) {
            throw new \InvalidArgumentException(sprintf('Item with id=%s has not found', $id));
        }
        return $this->itemsById[$id];
    }

    public function rewind()
    {
        $this->index = 0;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return isset($this->items[$this->index]);
    }

    /**
     * @return ElevatorStateInterface
     */
    public function current()
    {
        return $this->items[$this->index];
    }

    /**
     * @return int
     */
    public function key()
    {
        return $this->index;
    }

    public function next()
    {
        $this->index++;
    }

    public function count()
    {
        return count($this->items);
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->items;
    }

    /**
     * @param ElevatorStateInterface[] $items
     */
    private function setItems(array $items)
    {
        foreach ($items as $item) {
            if (array_key_exists($item->getId(), $this->itemsById)) {
                throw new \InvalidArgumentException('Array of elevator states contains duplicate ids');
            }

            $this->itemsById[$item->getId()] = $item;
        }
    }

    /**
     * @param int $id
     * @return bool
     */
    private function hasId($id)
    {
        return isset($this->itemsById[$id]);
    }
}