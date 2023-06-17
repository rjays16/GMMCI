<?php

/**
* <code>DashletException</code> is a simple wrapper of PHP's ntive Exception class. This class
* defines the possible exceptions that a Dashlet can throw when it encounters an error.
*/

class DashletException extends Exception
{

	const EXCEPTION_UNDEFINED 					= 0x0000;
	const EXCEPTION_DB_ERROR 						= 0x0001;
	const EXCEPTION_INVALID_CLASS 			= 0x0002;

	private $systemMessages;

	public function __construct( $code=0, $customMessage=null )
	{

		if (!$customMessage)
		{
			$message = self::getSystemMessage($code);
		}
		else
		{
			$message = $customMessage;
		}
		parent::__construct( $message, $code );
	}



	public static function getSystemMessage($code)
	{
		switch ($code)
		{
			case self::EXCEPTION_INVALID_CLASS:
				return 'Invalid class name or missing class definition encountered!';
				break;

			case self::EXCEPTION_DB_ERROR:
				return 'Database error encountered!';
				break;

			case self::EXCEPTION_UNDEFINED:
			default:
				return 'Undefined error encountered!';
				break;
		}
	}

}