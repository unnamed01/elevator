<?php

namespace Elevator;

use Elevator\SystemState\ElevatorState\ElevatorStateInterface;
use Elevator\SystemState\SystemStateInterface;

class Helper
{
    const HTML_DOOR_OPENED = 'opened';
    const HTML_DOOR_CLOSED = 'closed';


    const HTML_ACTION_IDLE = 'idle';
    const HTML_ACTION_LOAD_UNLOAD = 'load/unload';
    const HTML_ACTION_MOVING_UP = 'moving_up';
    const HTML_ACTION_MOVING_DOWN = 'moving_down';


    /**
     * @var SystemStateInterface
     */
    private $systemState;

    public function __construct(Config $config, SystemStateInterface $systemState)
    {
        $this->config = $config;
        $this->systemState = $systemState;
    }

    public function renderElevators()
    {
        $blocks = [];
        foreach($this->config->getElevators() as $elevator) {
            $blocks[] = $this->renderElevator($elevator);
        }

        $output = join(PHP_EOL, $blocks);

        return $output;
    }

    public function renderElevator(array $elevatorData)
    {
        $output = sprintf('<div id="%s" class="elevator">
            <h2>%s</h2>
            %s
            
            %s
        
            %s
            
            <br style="clear:both;" />
            %s
        </div>',
            $elevatorData['htmlId'],
            ucfirst($elevatorData['name']),
            $this->renderInfoBlock(),
            $this->renderRequestButtonsBlock(),
            $this->renderCabinButtonsBlock(),
            $this->renderSpecialButtonsBlock()
        );

        return $output;
    }

    public function renderWaypointsTable()
    {
        $floors = $this->config->getFloors();

        $theLowestLevel = array_reduce($floors, function($carry, $item) {
            if($item['level']<$carry) {
                return $item['level'];
            } else {
                return $carry;
            }
        }, PHP_INT_MAX);

        $theBiggestLevel = array_reduce($floors, function($carry, $item) {
            if($item['level']>$carry) {
                return $item['level'];
            } else {
                return $carry;
            }
        }, -PHP_INT_MAX);

        $directions = [
            ElevatorStateInterface::DIRECTION_UP,
            ElevatorStateInterface::DIRECTION_DOWN,
            ElevatorStateInterface::DIRECTION_NONE,
        ];

        //header
        $headerColumns = [];
        $headerColumns[] = '<th>-</th>';
        foreach($floors as $floor) {
            if ($floor['level']==$theLowestLevel || $floor['level']==$theBiggestLevel) {
                $colspan = 2;
            } else {
                $colspan = 3;
            }

            $headerColumns[] = sprintf('<th colspan="%d"">%s</th>', $colspan, $floor['name']);
        }

        //header2
        $header2Columns = [];
        $header2Columns[] = '<th>-</th>';
        foreach($floors as $floor) {
            foreach($directions as $direction) {
                if ($direction==ElevatorStateInterface::DIRECTION_DOWN && $floor['level']==$theLowestLevel) {
                    continue;
                }
                if ($direction==ElevatorStateInterface::DIRECTION_UP && $floor['level']==$theBiggestLevel) {
                    continue;
                }
                $header2Columns[] = sprintf('<th>%s</th>', $direction);
            }
        }


        //body
        $rows = [];
        $rows[] = sprintf('<tr>%s</tr>', join('', $headerColumns));
        $rows[] = sprintf('<tr>%s</tr>', join('', $header2Columns));
        foreach($this->config->getElevators() as $elevatorData) {
            $columns = [];
            $columns[] = sprintf('<th>%s</th>', $elevatorData['name']);

            foreach($floors as $floor) {
                foreach($directions as $direction) {
                    if ($direction==ElevatorStateInterface::DIRECTION_DOWN && $floor['level']==$theLowestLevel) {
                        continue;
                    }
                    if ($direction==ElevatorStateInterface::DIRECTION_UP && $floor['level']==$theBiggestLevel) {
                        continue;
                    }
                    $class = sprintf('%s-level%s-direction%s', $elevatorData['htmlId'], $floor['level'], $direction);
                    $columns[] = sprintf('<td class="%s">-</td>', $class);
                }
            }
            $row = sprintf('<tr>%s</tr>', join('', $columns));
            $rows[] = $row;
        }

        $output = sprintf('<div class="waypoints">
        <h3>Waypoints</h3>
        <table>
        %s
        </table>
        </div>', join(PHP_EOL, $rows));

        return $output;
    }

    public function renderInitButtons()
    {
        $propertiesInit = [];
        $propertiesReInit = [];
        if ($this->isInitialized()) {
            $propertiesInit[] = 'disabled="disabled"';
        } else {
            $propertiesReInit[] = 'class="disabled"';
            $propertiesReInit[] = 'disabled="disabled"';
        }
        $buttons = [];
        $buttons[] = sprintf('<button id="init" %s>init</button>', join(' ', $propertiesInit));
        $buttons[] = sprintf('<button id="reinit" %s>reinit</button>', join(' ', $propertiesReInit));

        $output = join($buttons);

        return $output;
    }

    public function isInitialized()
    {
        return $this->systemState->isSystemInitialized();
    }

    protected function renderInfoBlock()
    {
        $zeroFloor = array_reduce($this->config->getFloors(), function($carry, $item) {
            if($carry!==null) {
                return $carry;
            }
            if($item['level']===0){
                return $item;
            }

            return null;
        });

        $initWeight = 0;

        $output = sprintf('
            <div class="info">
                <b>level</b>: <i class="level">%s</i><br />
                <b>doors</b>: <i class="doors">%s</i><br />
                <b>action</b>: <i class="action">%s</i><br />
                <b>weight</b>: <i class="weight">%s</i><br />
             </div>',
            $zeroFloor['name'],
            self::HTML_DOOR_CLOSED,
            self::HTML_ACTION_IDLE,
            $initWeight
        );

        return $output;
    }

    protected function renderRequestButtonsBlock()
    {
        $floors = $this->config->getFloors();

        $theLowestLevel = array_reduce($floors, function($carry, $item) {
            if($item['level']<$carry) {
                return $item['level'];
            } else {
                return $carry;
            }
        }, PHP_INT_MAX);

        $theBiggestLevel = array_reduce($floors, function($carry, $item) {
            if($item['level']>$carry) {
                return $item['level'];
            } else {
                return $carry;
            }
        }, -PHP_INT_MAX);

        $buttons = [];
        foreach($floors as $floor) {
            $buttons[] = sprintf(
                '<button class="floor up" data-floorid="%s" data-level="%d" data-direction="%s"%s>%s ↑</button>',
                $floor['id'],
                $floor['level'],
                ElevatorStateInterface::DIRECTION_UP,
                $floor['level']===$theBiggestLevel ? ' style="visibility:hidden;"' : '',
                $floor['name']
            );
        }

        $buttons[] = '<br />';
        foreach($floors as $floor) {
            $buttons[] = sprintf(
                '<button class="floor down" data-floorid="%s" data-level="%d" data-direction="%s"%s>%s ↓</button>',
                $floor['id'],
                $floor['level'],
                ElevatorStateInterface::DIRECTION_DOWN,
                $floor['level']===$theLowestLevel ? ' style="visibility:hidden;"' : '',
                $floor['name']
            );
        }

        $output = sprintf('<div class="request"><h3>Request buttons</h3>
        %s
        </div>', join(PHP_EOL, $buttons));

        return $output;
    }

    protected function renderCabinButtonsBlock()
    {
        $buttons = [];
        foreach($this->config->getFloors() as $floor) {
            $buttons[] = sprintf('<button class="cabin" data-floorid="%s" data-level="%s">%s</button>',
                $floor['id'],
                $floor['level'],
                $floor['name']
            );
        }

        $output = sprintf('<div class="cabin">
        <h3>Cabin buttons</h3>
        %s
        </div>', join(PHP_EOL, $buttons));

        return $output;
    }

    protected function renderSpecialButtonsBlock()
    {
        $buttons = [];
        $buttons[] = sprintf('<button class="enter">enter</button>');
        $buttons[] = sprintf('<button class="exit">exit</button>');
        $buttons[] = sprintf('<button class="letDoorClose">Let door close</button>');
        $buttons[] = sprintf('<button class="moveToNextFloor">Move to next floor</button>');
        //$buttons[] = sprintf('<button class="openDoor">Open door</button>');

        $output = join(PHP_EOL, $buttons);

        return $output;
    }
}












