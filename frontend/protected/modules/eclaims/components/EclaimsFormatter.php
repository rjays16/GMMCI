<?php

/**
 *
 * EclaimsFormatter.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2014, Segworks Technologies Corporation
 */

/**
 * Description of EclaimsFormatter
 *
 * @package
 */
class EclaimsFormatter extends Formatter {
    /**
     * @var string the format string to be used to format a date using PHP date() function. Defaults to 'Y/m/d'.
     */
    public $dateFormat='m-d-Y';
    /**
     * @var string the format string to be used to format a time using PHP date() function. Defaults to 'h:i:s A'.
     */
    public $timeFormat='h:i:sA';
    /**
     * @var string the format string to be used to format a date and time using PHP date() function. Defaults to 'Y/m/d h:i:s A'.
     */
    public $datetimeFormat='m-d-Y h:i:sA';


    /**
     * Formats a flat PHIC Member PIN to the format specified by PHIC
     *
     * @param  string $value
     * @return string
     */
    public function formatPin($value)
    {
        if (empty($value)) {
            return $value;
        } else {
            $pin = str_replace("-","",$value);
            $pin = substr_replace($pin,'-',2,0);
            $pin = substr_replace($pin,'-',-1,0);
            return $pin;
        }
    }

    /**
     *
     *
     * @param  mixed $value
     * @return string
     */
    public function formatDbDate($value) {
        if (empty($value)) {
            return null;
        }

        $ts = $this->normalizeDateValue(str_replace('-', '/', $value));
        if ($ts !== false) {
            return date('Ymd', $ts);
        } else {
            return null;
        }
    }

    /**
     *
     *
     * @param  mixed $value
     * @return string
     */
    public function formatDbTime($value) {
        if (empty($value)) {
            return null;
        }
        $ts = $this->normalizeDateValue($value);
        if ($ts !== false) {
            return date('His', $ts);
        } else {
            return null;
        }
    }

    /**
     *
     *
     * @param  mixed $value
     * @return string
     */
    public function formatDbDatetime($value) {
        if (empty($value)) {
            return null;
        }

        $ts = $this->normalizeDateValue(str_replace('-', '/', $value));
        if ($ts !== false) {
            return date('YmdHis', $ts);
        } else {
            return null;
        }
    }

    /**
     * Note: Convert to a Widget!
     * 
     * @param model CActiveRecord
     * 
     * @param attribute mixed
     * Value field attribute or an array of array('value1', 'value2')
     * 
     * @param isAttribute String
     * Boolean field attribute. The value will be used to check wheter to
     * return a "text-success" or "text-error value";
     * 
     * @return String
     * @author Jolly Caralos
     */
    public function formatEligibility($model, $attribute, $isAttribute, $_default = 'No Eligibility') 
    {
        $_value = CHtml::value($model, $attribute);
        $_isAttribute = CHtml::value($model, $isAttribute);

        if(empty($_value)) {
            $_value = CHtml::tag('em', array('class' => 'muted'), $_default);
        } else {

            if($_isAttribute)
                $_value = CHtml::tag('em', array('class' => 'text-success'), 'Eligible');
            else 
                $_value = CHtml::tag('em', array('class' => 'text-error'), 'Not Eligible');
        }
        return $_value;
    }

    /**
     * @param $_data String
     * @param $_defualt String
     * Default: '.'
     * 
     * @return String
     *  
     * @author Jolly Caralos
     */
    public function formatDefaultEmpty($_data = null, $_default = '.') 
    {
        if(empty($_data))
            return $_default;
        return $_data;
    }

    /**
     * Parses the 'yes' string to TRUE and
     * 'no' to FALSE.
     * 
     * @param $_data String
     * 
     * @return boolean
     * 
     * @author Jolly Caralos
     */
    public function formatStringToBoolean($_data = null)
    {
        if(empty($_data))
            return false;
        
        switch(strtolower($_data)) {
            case 'yes':
                return true;
                break;
        }
        return false;
    }

}