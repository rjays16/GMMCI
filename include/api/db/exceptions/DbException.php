<?php
Loader::import('base.SegException');

/**
 * Generic exception for database-related classes
 * @package db.exceptions
 */
class DbException extends SegException
{
	protected $code = 500;
	protected $httpCode = 500;
}