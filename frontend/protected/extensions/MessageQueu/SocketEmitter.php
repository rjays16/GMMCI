<?php
/**
 * 
 * @author Jolly Caralos <jadcaralos@gmail.com>
 * @copyright Copyright &copy; 2013-2015. Segworks Technologies Corporation
 * @version 1.0
 */
namespace SegHis\extensions\MessageQueu;

class SocketEmitter
{

    public function __construct() 
    {
        // require __DIR__ . '/../vendors/autoload.php';
    }

    public function send() 
    {
        $emitter = new SocketIO\Emitter(array('port' => '403', 'host' => '127.0.0.1'));
        $response = $emitter->broadcast->emit('diagnosticOrderDeleted', json_encode(array(
            'user' => Yii::app()->user->personnel->personnel_id,
            'time' => time(),
            'more' => array(
                'hello' => 'world'
            )
        )));
        echo 'tryin to send `bar` to the event `foo`';
    }

}