<?php

namespace Elevator\Waypoint;

use Elevator\Waypoint\Filter\ComparatorFactory;

class WaypointFilter implements WaypointFilterInterface
{
    /**
     * @var ComparatorFactory
     */
    private $comparatorFactory;

    public function __construct()
    {
        $this->comparatorFactory = new ComparatorFactory;
    }

    /**
     * @param WaypointCollectionInterface $waypointCollection
     * @param array|null $filters
     * @return array
     * @throws \Exception
     */
    public function filter(WaypointCollectionInterface $waypointCollection, array $filters = null)
    {
        if ($filters === null) {
            return $waypointCollection->getAllWaypoints();
        }

        $output = [];
        foreach ($waypointCollection->getAllWaypoints() as $waypoint) {
            foreach($filters as $filter) {
                $context = $filter['context'];
                $comparator = $this->comparatorFactory->create($filter['comparator']);
                switch ($filter['field']) {
                    case 'level':
                        $value = $waypoint->getLevel();
                        break;

                    case 'direction':
                        $value = $waypoint->getDirection();
                        break;

                    default:
                        throw new \Exception(sprintf('Unknown waypoint field %s in filter', $filter['field']));
                }

                if (!$comparator->compare($value, $context)) {
                    continue(2);
                }
            }

            $output[] = $waypoint;
        }

        return $output;
    }
}