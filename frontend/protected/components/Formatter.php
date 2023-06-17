<?php

/**
 *
 * Formatter.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2014, Segworks Technologies Corporation
 */

/**
 * Description of Formatter
 *
 * @package
 */
class Formatter extends CFormatter {
    /**
	 * @var string the format string to be used to format a date using PHP date() function. Defaults to 'Y/m/d'.
	 */
	public $dateFormat='M j, Y';
	/**
	 * @var string the format string to be used to format a time using PHP date() function. Defaults to 'h:i:s A'.
	 */
	public $timeFormat='h:i A';
	/**
	 * @var string the format string to be used to format a date and time using PHP date() function. Defaults to 'Y/m/d h:i:s A'.
	 */
	public $datetimeFormat='M j, Y h:i A';
}
