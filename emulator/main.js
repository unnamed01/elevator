var REQUEST_TYPE_INIT = 0;
var REQUEST_TYPE_REQUEST_BUTTON = 1;
var REQUEST_TYPE_CABIN_BUTTON = 2;
var REQUEST_TYPE_LEVEL_REACHED = 3;
var REQUEST_TYPE_DOOR_CLOSED = 4;

var DIRECTION_UP = 'up';
var DIRECTION_DOWN = 'down';
var DIRECTION_NONE = 'none';

var DOORS_ACTION_OPEN = 'open';
var DOORS_ACTION_CLOSE = 'close';

var HTML_DOOR_OPENED = 'opened';
var HTML_DOOR_CLOSED = 'closed';


var HTML_ACTION_WAIT = 'waiting response';
var HTML_ACTION_IDLE = 'idle';
var HTML_ACTION_LOAD_UNLOAD = 'load/unload';
var HTML_ACTION_MOVING_UP = 'moving up';
var HTML_ACTION_MOVING_DOWN = 'moving down';

var elevator = function(elevatorId){
    var PASSENGERS_MAX = 5;
    var elevator = this;

    this.currentLevel = 0;

    this.doors = {
        init: function(){
            this.connect();
            this.close();
        },

        connect: function(){
            connectionManager.registerComponent(
                'doors-' + elevatorId,
                function(topic, data){
                    console.log('Doors component received a message:', data);
                    switch (data.action){
                        case DOORS_ACTION_OPEN:
                            elevator.doors.onRemoteOpen(data);
                            break;

                        case DOORS_ACTION_CLOSE:
                            elevator.doors.onRemoteClose(data);
                            break;

                        default:
                            throw new Error('Unknown door action ' + data.action);
                    }
                }
            );
        },

        open: function()
        {
            elevator.special.enterButton.enable();
            elevator.special.exitButton.enable();
            elevator.special.doorCloseButton.enable();

            elevator.indicator.action.set(HTML_ACTION_LOAD_UNLOAD);
            elevator.indicator.doors.set(HTML_DOOR_OPENED);

            //elevator.requestButtons.disable();    //when doors open disable current level cabin buttons,  disable current level request button in current direction
        },

        close: function()
        {
            elevator.special.enterButton.disable();
            elevator.special.exitButton.disable();
            elevator.special.doorCloseButton.disable();

            elevator.indicator.action.set(HTML_ACTION_WAIT);
            elevator.indicator.doors.set(HTML_DOOR_CLOSED);
        },

        onRemoteOpen: function(/*data*/){
            this.open();
        },

        onRemoteClose: function(/*data*/){
            this.close();
        }
    };

    this.requestButtons = {
        check: function(){
            elevator._check('div#' + elevatorId + ' button.floor');
        },

        init: function(){
            this._attachHandler();
            this.disableAll();
        },

        _attachHandler: function(){
            var selector = 'div#' + elevatorId + ' button.floor';
            $(selector).on('click', function(event){
                var floorId = $(event.target).data('floorid');
                var level = $(event.target).data('level');
                var direction = $(event.target).data('direction');

                waypointManager.addWaypoint(elevatorId, level, direction);
                elevator.requestButtons.disable(level, direction);  //disable on request click

                client.notifyRequestButtonPressed(elevatorId, floorId, direction);
            });
        },

        disable: function(level, direction){
            var selector = 'div#' + elevatorId + ' button.floor';
            $(selector).each(function(index, element){
                element = $(element);
                if(element.data('level')==level && element.data('direction') == direction){
                    element.prop('disabled', true);
                }
            });
        },

        enable: function(level, direction){
            var selector = 'div#' + elevatorId + ' button.floor';
            $(selector).each(function(index, element){
                element = $(element);
                if(element.data('level')==level && element.data('direction') == direction){
                    element.prop('disabled', false);
                }
            });
        },

        disableAll: function(){
            var selector = 'div#' + elevatorId + ' button.floor';
            $(selector).each(function(index, element){
                element = $(element);
                element.prop('disabled', true);
            });
        },

        enableAll: function(){
            var selector = 'div#' + elevatorId + ' button.floor';
            $(selector).each(function(index, element){
                element = $(element);
                element.prop('disabled', false);
            });
        }
    };

    this.cabinButtons = {
        check: function(){
            elevator._check('div#' + elevatorId + ' button.cabin');
        },

        init: function(){
            this._attachHandler();
            this.disableAll();
        },

        _attachHandler: function(){
            var selector = 'div#' + elevatorId + ' button.cabin';
            $(selector).on('click', function(event){
                var floorId = $(event.target).data('floorid');
                var level = $(event.target).data('level');

                waypointManager.addWaypoint(elevatorId, level, DIRECTION_NONE);
                elevator.cabinButtons.disable(level);   //disable on cabin click

                client.notifyCabinButtonPressed(elevatorId, floorId);
            });
        },

        disable: function(level){
            var selector = 'div#' + elevatorId + ' button.cabin';
            $(selector).each(function(index, element){
                element = $(element);
                if(element.data('level') == level){
                    element.prop('disabled', true);
                }
            });
        },

        disableAll: function(){
            var selector = 'div#' + elevatorId + ' button.cabin';
            $(selector).each(function(index, element){
                element = $(element);
                element.prop('disabled', true);
            });
        },

        enableAll: function(){
            var selector = 'div#' + elevatorId + ' button.cabin';
            $(selector).each(function(index, element){
                element = $(element);
                element.prop('disabled', false);
            });
        },

        enable: function(level){
            var selector = 'div#' + elevatorId + ' button.cabin';
            $(selector).each(function(index, element){
                element = $(element);
                if(element.data('level') == level){
                    element.prop('disabled', false);
                }
            });
        },

        enableAllExceptCurrentLevel: function(){
            var selector = 'div#' + elevatorId + ' button.cabin';
            $(selector).each(function(index, element){
                if ($(element).data('level') != elevator.currentLevel){
                    element = $(element);
                    element.prop('disabled', false);
                }
            });
        }
    };

    this.motor = {
        init: function(){
            this.connect();
        },

        connect: function(){
            connectionManager.registerComponent(
                'motor-' + elevatorId,
                function(topic, data){
                    console.log('Motor component received a message:', data);
                    switch (data.direction){
                        case DIRECTION_UP:
                            elevator.motor.onStartMovingUp(data);
                            break;

                        case DIRECTION_DOWN:
                            elevator.motor.onStartMovingDown(data);
                            break;

                        default:
                            throw new Error('Unknown movement direction ' + data.direction);
                    }
                }
            );
        },

        onStartMovingUp: function(data){
            var text = HTML_ACTION_MOVING_UP + ' from "' + config.getFloorByLevel(data.fromLevel).name
                + '" to "' + config.getFloorByLevel(data.toLevel).name + '"';
            elevator.indicator.action.set(text);
            elevator.special.move.enable();
            elevator.special.move.data = data;
        },

        onStartMovingDown: function(data){
            var text = HTML_ACTION_MOVING_DOWN + ' from "' + config.getFloorByLevel(data.fromLevel).name + '" to "'
                + config.getFloorByLevel(data.toLevel).name + '"';
            elevator.indicator.action.set(text);
            elevator.special.move.enable();
            elevator.special.move.data = data;
        }
    };

    this.indicator = {
        check: function(){
            elevator._check('div#' + elevatorId + ' i.level');
            elevator._check('div#' + elevatorId + ' i.doors');
            elevator._check('div#' + elevatorId + ' i.action');
            elevator._check('div#' + elevatorId + ' i.weight');
        },
        init: function(){
            this.weight.init();
        },
        level: {
            set: function(title){
                var selector = 'div#' + elevatorId + ' i.level';
                $(selector).html(title);
            }
        },
        doors: {
            set: function(title){
                var selector = 'div#' + elevatorId + ' i.doors';
                $(selector).html(title);
            }
        },
        action: {
            set: function(title){
                var selector = 'div#' + elevatorId + ' i.action';
                $(selector).html(title);
            }
        },
        weight: {
            init: function(){
                this.set('0');
            },

            increment: function(){
                var selector = 'div#' + elevatorId + ' i.weight';
                $(selector).html(parseInt($(selector).html()) + 1);
            },

            decrement: function(){
                var selector = 'div#' + elevatorId + ' i.weight';
                $(selector).html($(selector).html()*1 - 1);
            },

            set: function(value){
                var selector = 'div#' + elevatorId + ' i.weight';
                $(selector).html(value);
            },
            get: function(){
                var selector = 'div#' + elevatorId + ' i.weight';
                return parseInt($(selector).html());
            }
        },
        spinner: {
            set: function(selector, duration){
                $(selector).append('<div id="spinner"></div>');
                setTimeout(function(){
                    $('div#spinner').remove();
                }, duration);
            }
        }
    };

    this.special = {
        check: function(){
            elevator._check('div#' + elevatorId + ' button.moveToNextFloor');
            elevator._check('div#' + elevatorId + ' button.letDoorClose');
            elevator._check('div#' + elevatorId + ' button.enter');
            elevator._check('div#' + elevatorId + ' button.exit');

        },

        init: function(){
            this.enterButton.init();
            this.exitButton.init();
            this.move.init();
            this.doorCloseButton.init();
        },

        move: {
            data: null,

            init: function(){
                this._attachHandler();
                elevator.special.move.disable();
            },
            
            _attachHandler: function(){
                var selector = 'div#' + elevatorId + ' button.moveToNextFloor';
                $(selector).on('click', function(/*event*/){
                    elevator.indicator.spinner.set('div#' + elevatorId, 1000);
                    elevator.special.move.disable();
                    elevator.special.move.pretendLevelReached(elevator.special.move.data);
                });
            },

            enable: function()
            {
                var selector = 'div#' + elevatorId + ' button.moveToNextFloor';
                $(selector).prop('disabled', false);
            },

            disable: function()
            {
                var selector = 'div#' + elevatorId + ' button.moveToNextFloor';
                $(selector).prop('disabled', true);
            },

            pretendLevelReached: function(movementData){
                elevator.indicator.action.set(HTML_ACTION_WAIT);
                client.notifyLevelReached(elevatorId, movementData.direction, movementData.fromLevel, movementData.toLevel);
                elevator.indicator.level.set(config.getFloorByLevel(movementData.toLevel).name);
                elevator.currentLevel = movementData.toLevel;
            }
        },

        doorCloseButton: {
            timerId: null,
            counter: 5,

            init: function(){
                this._attachHandler();
                elevator.special.doorCloseButton.disable();
            },

            _attachHandler: function(){
                var selector = 'div#' + elevatorId + ' button.letDoorClose';
                $(selector).on('click', function(/*event, a*/){
                    elevator.special.doorCloseButton.removeCountDown();
                    elevator.special.doorCloseButton.pretendDoorClosed();

                    //enable current level button
                    //elevator.requestButtons.enable(data.level, direction);
                });
            },

            enable: function()
            {
                var selector = 'div#' + elevatorId + ' button.letDoorClose';
                $(selector).prop('disabled', false);

                //setTimeout(this.countDown.bind(elevator.special.doorCloseButton), 1000);
            },

            disable: function()
            {
                var selector = 'div#' + elevatorId + ' button.letDoorClose';
                $(selector).prop('disabled', true);
            },

            pretendDoorClosed: function(){
                elevator.doors.close();
                client.notifyDoorClosed(elevatorId);
            },

            removeCountDown: function(){
                //clearTimeout(elevator.special.doorCloseButton.timerId);
                //elevator.special.doorCloseButton.timerId = null;
                //$(selector).html('Let door close');
            },

            countDown: function(){
                var selector = 'div#' + elevatorId + ' button.letDoorClose';
                $(selector).html('Let door close ' + this.counter--);
                if(this.counter == 0)
                {
                    clearTimeout(elevator.special.doorCloseButton.timerId);
                    this.timerId = null;

                    $(selector).click();
                }
                else
                {
                    this.timerId = setTimeout(this.countDown.bind(this), 1000);
                }
            }
        },

        enterButton: {
            init: function(){
                elevator.special.enterButton.disable();
                this._attachHandler();
            },

            _attachHandler: function(){
                var selector = 'div#' + elevatorId + ' button.enter';
                $(selector).on('click', function(){
                    if(elevator.indicator.weight.get() < PASSENGERS_MAX){
                        elevator.indicator.weight.increment();
                    }
                    if(elevator.indicator.weight.get() > 0){
                        elevator.cabinButtons.enableAllExceptCurrentLevel();    //enable cabin on enter
                    }
                });
            },

            enable: function()
            {
                var selector = 'div#' + elevatorId + ' button.enter';
                $(selector).prop('disabled', false);
            },

            disable: function()
            {
                var selector = 'div#' + elevatorId + ' button.enter';
                $(selector).prop('disabled', true);
            }
        },

        exitButton: {
            init: function(){
                elevator.special.exitButton.disable();
                this._attachHandler();
            },

            _attachHandler: function(){
                var selector = 'div#' + elevatorId + ' button.exit';
                $(selector).on('click', function(){
                    if(elevator.indicator.weight.get() > 0){
                        elevator.indicator.weight.decrement();
                    }

                    if(elevator.indicator.weight.get() == 0){
                        elevator.cabinButtons.disableAll();
                    }
                });
            },

            enable: function()
            {
                var selector = 'div#' + elevatorId + ' button.exit';
                $(selector).prop('disabled', false);
            },

            disable: function()
            {
                var selector = 'div#' + elevatorId + ' button.exit';
                $(selector).prop('disabled', true);
            }
        }
    };

    this.check = function(){
        this._check('div#' + elevatorId);
        this.requestButtons.check();
        this.cabinButtons.check();
        this.indicator.check();
        this.special.check();
    };

    this.init = function(){
        this.doors.init();
        this.indicator.init();
        this.special.init();
        this.requestButtons.init();
        this.cabinButtons.init();
        this.motor.init();

        this.disable();
    };

    this.enable = function(){
        var selector = 'div#' + elevatorId;
        $(selector).removeClass('disabled');

        this.requestButtons.enableAll();
    };

    this.disable = function(){
        var selector = 'div#' + elevatorId;
        $(selector).addClass('disabled');
    };

    this.idle = function(){
        this.indicator.action.set(HTML_ACTION_IDLE);
        this.requestButtons.enableAll();
        this.cabinButtons.disableAll();
    };

    this._check = function(selector){
        var result = $(selector).length!==0;

        if(result){
            /*var padding = new Array(50 - selector.length).join(' ');
            console.info('Check ' + selector + ':' + padding + 'ok');*/
        } else {
            console.warn('Check ' + selector + ':' + 'fail');
        }
    };
};

var initButton = {
    init: function(){
        this._attachHandler();
    },

    _attachHandler: function(){
        $('button#init').on('click', function(/*event*/){
            client.notifyInitButtonPressed();
            initButton.disableInit();
            initButton.enableReInit();
        });

        $('button#reinit').on('click', function(/*event*/){
            client.notifyReInitButtonPressed();
        });
    },

    disableInit: function(){
        $('button#init').addClass('disabled').prop('disabled', true);
    },

    enableReInit: function(){
        $('button#reinit').removeClass('disabled').prop('disabled', false);
    }
};

var client = {
    baseUrl: 'http://e.com:4568/index.php',
    stateUrl: 'http://e.com:4568/state.php',

    loadState: function(){
        var data = {};
        var json = JSON.stringify(data);
        $.get(this.stateUrl, {error:false, data:json}, this.onStateLoaded);
    },

    onStateLoaded: function(response/*, textStatus*/){
        if(response.error){
            throw new Error(response.message);
        }

        stateManager.applyState(response.data);
        connectionManager.connect();

    },

    notifyRequestButtonPressed: function(elevatorId, floorId, direction){
        var data = {
            type: REQUEST_TYPE_REQUEST_BUTTON,
            elevatorId: elevatorId,
            direction: direction,
            floorId: floorId
        };
        var json = JSON.stringify(data);
        $.get(this.baseUrl, {error:false, data:json});
    },

    notifyCabinButtonPressed: function(elevatorId, floorId){
        var data = {
            type: REQUEST_TYPE_CABIN_BUTTON,
            elevatorId: elevatorId,
            direction: DIRECTION_NONE,
            floorId: floorId
        };
        var json = JSON.stringify(data);
        $.get(this.baseUrl, {error:false, data:json});
    },

    notifyInitButtonPressed: function(){
        var data = {
            type: REQUEST_TYPE_INIT
        };
        var json = JSON.stringify(data);
        $.get(this.baseUrl, {error:false, data:json}, function(response/*, textStatus*/){
            if(response.error){
                throw new Error(response.message);
            }

            client.loadState();
        });
    },

    notifyReInitButtonPressed: function(){
        var data = {
            type: REQUEST_TYPE_INIT
        };
        var json = JSON.stringify(data);
        $.get(this.baseUrl, {error:false, data:json}, function(response/*, textStatus*/){
            if(response.error){
                throw new Error(response.message);
            }

            window.location.reload();
        });
    },

    notifyLevelReached: function(elevatorId, direction, fromLevel, toLevel){
        var data = {
            type: REQUEST_TYPE_LEVEL_REACHED,
            elevatorId: elevatorId,
            direction: direction,
            fromLevel: fromLevel,
            toLevel: toLevel
        };
        var json = JSON.stringify(data);
        $.get(this.baseUrl, {error:false, data:json});
    },

    notifyDoorClosed: function(elevatorId){
        var data = {
            type: REQUEST_TYPE_DOOR_CLOSED,
            elevatorId: elevatorId
        };
        var json = JSON.stringify(data);
        $.get(this.baseUrl, {error: false, data: json}, this.doorClosedAfter);
    },

    doorClosedAfter: function(response/*, textStatus, jqXHR*/){
        var elevator = elevatorManager.getElevator(response.data.elevatorId);
        if(response.data.idle){
            elevator.idle();
        } else {
            elevator.requestButtons.enable(response.data.level, response.data.direction);   //enable request on door closed, before move
            elevator.cabinButtons.enable(response.data.level);                              //enable request on door closed, before move
        }
    }
};
    
var config = {
    items: [],

    setData: function(data){
        this.items = data;
    },

    getFloorByLevel: function(level){
        if(!this.items.length){
            throw new Error('Empty floors data');
        }

        for(var i in this.items){
            if((this.items.hasOwnProperty(i) && this.items[i].level == level))
            {
                return this.items[i];
            }
        }

        throw new Error('Floor not found for level ' + level);
    }
};

var elevatorManager = {
    elevators: {},
    init: function(){
    },
    all: function(elevators){
        elevators.forEach(function(elevatorData){
            var elevatorId = 'elevator' + elevatorData.id;
            var elevatorObject = new elevator(elevatorId);
            elevatorObject.check();
            elevatorObject.init();
            elevatorObject.enable();
            elevatorManager.elevators[elevatorId] = elevatorObject;
        });
    },
    /*createElevators: function(elevators){
     elevators.forEach(function(elevatorData){
     var elevatorId = 'elevator' + elevatorData.id;
     //var elevatorId = elevator.name.replace(/[^a-z0-9_\s-]/, ' ').replace(/[\s-]+/, ' ').replace(/[\s_]/, '-');
     elevatorManager.elevators[elevatorId] = new elevator(elevatorId);
     });
     },
     initElevators: function(){
     $.each(this.elevators, function(index,elevator){
     elevator.init();
     });
     },
     enableElevators: function(){
     $.each(this.elevators, function(index,elevator){
     elevator.enable();
     });
     },*/

    getElevator: function(elevatorId){
        if(this.elevators[elevatorId] == undefined){
            throw new Error('Unknown elevator id ' + elevatorId);
        }

        return this.elevators[elevatorId];
    }

    /*,getElevators: function(){
        return this.elevators;
    }*/
};

var waypointManager = {
    data: {},

    init: function(elevatorId){
    },

    enable: function(){
        $('.waypoints.disabled').removeClass('disabled');
    },

    applyState: function(waypoints){
        $.each(waypoints, function(elevatorId, waypoints){
            waypoints.forEach(function(waypoint){
                var level = config.getFloorByLevel(waypoint.level).level;
                var direction = waypoint.direction;

                waypointManager.addWaypoint(elevatorId, level, direction);
                elevatorManager.getElevator(elevatorId).requestButtons.disable(level, waypoint.direction);  //disable on apply state
            });
        });
    },

    disable: function(){
        $('.waypoints.disabled').addClass('disabled');
    },

    addWaypoint: function(elevatorId, level, direction){
        if(!this.data[elevatorId]){
            this.data[elevatorId] = {};
        }
        if(!this.data[elevatorId][level]){
            this.data[elevatorId][level] = {};
        }
        this.data[elevatorId][level][direction] = 1;

        var selector = 'table td.' + elevatorId + '-level' + level + '-direction' + direction;
        $(selector).html('+');
    }

    /*,deleteWaypoint: function(elevatorId, level, direction){
        this.data[elevatorId][level][direction] = 0;

        var selector = 'table td.' + elevatorId + '-level' + level + '-direction' + direction;
        $(selector).html('+');
    }*/
};

var stateManager = {
    applyState: function(state){
        if(state.isInitialized){
            initButton.disableInit();
        }

        if(state.isInitialized){
            elevatorManager.init();
            elevatorManager.all(state.elevators);

            config.setData(state.floors);
            waypointManager.enable();
            waypointManager.applyState(state.waypoints);
        } else {
            //do nothing
            //throw new Error('Unable to load state. Application is not initialized.');
        }
    }
};

var connectionManager = {
    topics: [],

    registerComponent: function(topic, callback){
        this.topics[this.topics.length] = {
            name: topic,
            callback: callback
        };
    },

    connect: function(){
        var self = this;
        var conn = new ab.Session(
            'ws://e.com:8080',
            function(){
                self.topics.forEach(function(topic){
                    console.log('Subscribe: ', topic.name);
                    conn.subscribe(topic.name, topic.callback);
                });
            },
            function(){
                console.warn('WebSocket connection closed');
            },
            {'skipSubprotocolCheck': true}
        );

        return conn;
    }
};