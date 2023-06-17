<?php
Loader::import('base.SegException');

/**
 * Exception raised each time an SQL error is encountered
 * @package db.exceptions
 */
class SqlException extends SegException
{
	protected $httpCode = 500;
	protected $query;

	/**
	 * Description
	 * @param int $code 
	 * @param string $message 
	 * @param string $details 
	 */
	public function __construct($code, $message, $details='', $query='' )
	{
		parent::__construct($message);
		$this->code = $code;
		$this->message = $message;
		$this->details = $details;
		$this->query = $query;
	}

	public function getQuery()
	{
		return $this->query;
	}
}