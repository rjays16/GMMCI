<?php

/**
 *
 * ServiceCallException.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2005-2013, Segworks Technologies Corporation
 */

/**
 * Description of ServiceCallException
 *
 * @package
 */
class ServiceCallException extends CHttpException {


    protected $data;
    protected $reason = null;

    /**
     *
     * @param type $status
     * @param type $message
     * @param type $data
     */
    public function __construct($status, $message = null, $data=null) {
        $this->data = $data;
        if ($data['reason']) {
            $this->reason = $data['reason'];
        }
        if(empty($this->reason))
            $this->reason = $message;
        $this->reason = CHtml::encode($this->reason);
        
        parent::__construct($status, $message);
    }

    /**
     *
     * @return string
     */
    public function getData() {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getReason() {
        return $this->reason;
    }
}


