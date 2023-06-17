<?php

require './roots.php';
require_once 'Dashlet.php';


/**
* Provides API layer for handling session data for each Dashlet.
*
* DashletPreferences differ from DashletSession in that preferences for a dashlet are
* usually set during the CONFIG mode of a Dashlet. Sessions are generally accessed and
* modified during processing of Dashlet actions and render requests. Moreover, Sessions
* do not persist when the user changes location or user another workstation.
*
*/
class DashletSession
{

	const SCOPE_APPLICATION = 'app';
	const SCOPE_DASHBOARD 	= 'db';
	const SCOPE_DASHLET 		= 'dl';

	const EMPTY_VALUE = null;

	protected $scope;
	protected static $instances;


	/**
	* Singleton
	*
	* @param String $scope
	* @param String $handle
	* @return DashletSession
	*/
	private function __construct( $scope, $handle )
	{
		$this->scope = $scope;
		$this->handle = $handle;
	}



	/**
	* put your comment there...
	*
	* @param mixed $scope
	* @param mixed $handle
	* @return DashletSession
	* @throws Exception
	*/
	public static function getInstance($scope, $handle)
	{
		if (!is_array(self::$instances))
		{
			self::$instances = array();
		}
		switch ($scope) {
			case self::SCOPE_APPLICATION:
				if (!self::$instances[$scope])
				{
					return self::$instances[$scope];
				}
				self::$instances[$scope] = new DashletSession($scope, '');
			break;

			case self::SCOPE_DASHBOARD: case self::SCOPE_DASHLET:
				if (!is_array(self::$instances[$scope]))
				{
					self::$instances[$scope] = Array();
				}
				if (!self::$instances[$scope][$handle])
				{
					self::$instances[$scope][$handle] = new DashletSession($scope, $handle);
				}
				return self::$instances[$scope][$handle];
			break;

			default:
				throw new Exception('Invalid Session scope!');
				return false;
			break;
		}
	}


	/**
	* Returns the namespace used for accessing the Session data
	*
	*/
	public function getNameSpace() {
		return $this->scope.$this->handle;
	}

	/**
	* Retrives the value of variable set during the dashlet's session.
	*
	* <p>A dashlet session variable is stored in a single data structure
	* represented in multi-dimensional array format, with each node reference
	* through a Session path string.</p>
	*
	* <p>For example, a Session path value of <code>form.data.name</code> references
	* the <code>name</code> variable under the <code>data</code> array, which itself
	* is under the top-level array <code>form</code>.</p>
	*
	* @param mixed $sessionPath The session path referencing the session data
	* @return mixed Returns DashletSession::EMPTY_VALUE if the Session path does not contain a value
	*/
	public function get($sessionPath)
	{

		if (!$this->_isAValidSessionPath($sessionPath))
		{
			return DashletSession::EMPTY_VALUE;
		}

		$paths = explode('.', $sessionPath);
		if ( !isset($_SESSION[$this->getNameSpace()]) )
		{
			return DashletSession::EMPTY_VALUE;
		}

		$pointer =& $_SESSION[$this->getNameSpace()];

		foreach ($paths as $path)
		{
			if ( !isset( $pointer[$path] ))
			{
				return DashletSession::EMPTY_VALUE;
			}
			$pointer =& $pointer[$path];
		}

		// the '.' index in the session namespace refers to the data object
		// for that sessionPath
		if (isset($pointer['.']))
		{
			return $pointer['.'];
		}
		else
		{
			return DashletSession::EMPTY_VALUE;
		}
	}


	/**
	* Sets the value of a session variable in the dashlet's session data
	*
	* <p>A dashlet session variable is stored in a single data structure
	* represented in multi-dimensional array format, with each node reference
	* through a Session path string.</p>
	*
	* <p>For example, a Session path value of <code>form.data.name</code> references
	* the <code>name</code> variable under the <code>data</code> array, which itself
	* is under the top-level array <code>form</code>.</p>
	*
	* @param mixed $sessionPath
	* @param mixed $value
	*/
	public function set($sessionPath, $value) {

		if (!is_scalar($value)) {
			return false;
		}

		if (!$this->_isAValidSessionPath($sessionPath))
		{
			return DashletSession::EMPTY_VALUE;
		}


		$paths = explode('.', $sessionPath);
		$pointer =& $_SESSION[$this->getNameSpace()];
		foreach ($paths as $path)
		{
			if (!is_array($pointer[$path]))
			{
				$pointer[$path] = Array();
			}
			$pointer =& $pointer[$path];
		}

		$pointer['.'] = $value;
		return;
	}



	/**
	* Removes a session variable from the dashlet's session data
	*
	* @param mixed $sessionPath
	*/
	public function delete($sessionPath) {
		if (!$this->_isAValidSessionPath($sessionPath))
		{
			return DashletSession::EMPTY_VALUE;
		}


		$paths = explode('.', $sessionPath);
		$pointer =& $_SESSION[$this->getNameSpace()];
		foreach ($paths as $path)
		{
			if (!is_array($pointer[$path]))
			{
				return;
			}
			$pointer =& $pointer[$path];
		}

		$pointer = DashletSession::EMPTY_VALUE;
		return;
	}



	/**
	* Returns a debug-oriented string representation of the session variable
	*
	* @param mixed $sessionPath
	* @return mixed
	*/
	public function inspect($sessionPath='') {
		if (!$sessionPath)
		{
			return var_export( $_SESSION[$this->getNameSpace()], true );
		}
		else
		{


			if (!$this->_isAValidSessionPath($sessionPath))
			{
				return DashletSession::EMPTY_VALUE;
			}

			$paths = explode('.', $sessionPath);
			$pointer =& $_SESSION[$this->getNameSpace()];

			foreach ($paths as $path)
			{
				if (!is_array($pointer[$path]))
				{
					return DashletSession::EMPTY_VALUE;
				}
				$pointer =& $pointer[$path];
			}

			return var_export($pointer, true);
		}
	}

	protected function _isAValidSessionPath($String)
	{
		$pattern = '/^[A-Z0-9A-Z0-90-9_]+(\.[A-Z0-9A-Z0-90-9_]+)*$/';
		return preg_match($pattern, $String);
	}


}