<?php
/**
 * @author Jolly Caralos <jadcaralos@gmail.com>
 * @copyright Copyright &copy; 2013-2016. Segworks Technologies Corporation
 * @version 1.0
 */

namespace SegHis\extensions\MessageQueu;

use SegHis\extensions\MessageQueu\components\ZeroMQ;

class MessageQueu
{
    
    public $ip = '127.0.0.1';
    public $port = '5555';

    /**
     * Socket
     * @var null
     */
    private $mqlib = null;

    /**
     * Remove if used in a non-Yii framework.
     * Or change to constructor.
     * 
     * @return [type] [description]
     */
    public function init() 
    {
        // require __DIR__ . '/../../vendors/autoload.php';
        $this->mqlib = new ZeroMQ(
            $this->ip, $this->port, \ZMQ::SOCKET_PUSH, 'default_route');
    }

    public function publish($topic, $data = array()) 
    {
        $this->mqlib->send(array(
            'topic' => $topic,
            'data' => $data
        ));
    }

}