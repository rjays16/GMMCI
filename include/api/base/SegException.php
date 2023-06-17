<?php
/**
 * Base exception class for all HIS API classes
 * @package segworks.base
 * @author Alvin Quinones
 */
class SegException extends Exception 
{
	protected $httpCode;
	protected $details;

	/**
	 * Description
	 * @param string $message 
	 * @return type
	 */
	public function __construct($message, $details = null)
	{
		parent::__construct($message);
        $this->details = $details;
	}

	/**
	 * Description
	 * @return int
	 */
	public function getHttpCode()
	{
		return $this->httpCode;
	}

	/**
	 * Description
	 * @return string
	 */
	public function getDetails()
	{
		return $this->details;
	}
}