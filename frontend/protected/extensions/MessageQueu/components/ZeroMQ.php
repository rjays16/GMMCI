<?php
/**
 * 
 * @author Jolly Caralos <jadcaralos@gmail.com>
 * @copyright Copyright &copy; 2013-2016. Segworks Technologies Corporation
 * @version 1.0
 */

namespace SegHis\extensions\MessageQueu\components;

class ZeroMQ
{

    /**
     * Socket
     * @var null
     */
    private $_socket = null;

    public function __construct($ip, $port, $pattern, $route = '') 
    {
        $context = new \ZMQContext();
        $this->_socket = $context->getSocket($pattern, $route);
        $this->_socket->connect($this->_formatURI($ip, $port));
    }

    private function _formatURI($ip, $port) 
    {
        return "tcp://" . $ip . ":" . $port;
    }

    public function send() 
    {
        $args = func_get_args();
        $this->_socket->send(json_encode(
            array_pop(func_get_args())
        ));
    }

}