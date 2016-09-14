<?php

namespace Elevator\Socket;

use ZMQContext;
use ZMQSocket;

/**
 * @method string|int getOpt(string $key) Returns the value of a context option.
 * @method ZMQContext setOpt(int $key, mixed $value) Sets a ZMQ context option. The type of the value depends on the key.
 * @method bool isPersistent(string $key) Whether the context is persistent. Persistent context is needed
 *  for persistent connections as each socket is allocated from a context.
 * @method ZMQSocket getSocket($type, $persistent_id = null, $on_new_socket = null) Shortcut for creating new sockets
 *  from the context. If the context is not persistent the persistent_id parameter is ignored and the socket falls back
 *  to being non-persistent. The on_new_socket is called only when a new underlying socket structure is created.
 */
class Context extends \React\ZMQ\Context
{
}