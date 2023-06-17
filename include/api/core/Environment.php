<?php
/**
 * Environment.php
 * @package api
 * @author Alvin Quinones
 */

/**
 * Represents an application environment. 
 * 
 */
class Environment
{
	protected static $configurations = array();
	protected static $active = null;

	protected $config;

	/**
	 * 
	 * @param array $config 
	 */
	protected function __construct($config)
	{
		$this->config = $config;
	}

	/**
	 * Returns the value of the configuration parameter $key
	 * @access protected
	 * @param string $key 
	 * @return mixed
	 */
	protected function getConfiguration($key)
	{
		if (isset($this->config[$key])) {
			return $this->config[$key];
		} else {
			return null;
		}
	}

	/**
	 * Adds an environment named $name with $config paramaters
	 * @static
	 * @param type $name 
	 * @param type $config 
	 * @return type
	 */
	public static function add($name, $config)
	{
		self::$configurations[$name] = new Environment($config);
	}


	/**
	 * Loads the environment $name as the active environment. Returns
	 * the newly loaded environment object.
	 * @static
	 * @param string $name 
	 * @return Environment
	 */
	public static function load($name)
	{
		if (array_key_exists($name, self::$configurations)) {
			self::$active = self::$configurations[$name];
		} else {
			throw new Exception('Environment does not exist');
		}
		return self::$active;
	}

	/**
	 * Returns the current environment
	 * @static
	 * @return Environment
	 */
	public static function getActiveEnvironment()
	{
		if (!self::$active instanceof Environment) {
			throw new Exception('No environment currently in use');
		}
		return self::$active;
	}

	/**
	 * Returns root path for the application using the current environment
	 * @static
	 * @return string
	 */
	public static function getRootPath()
	{
		$env = self::getActiveEnvironment();
		$rootPath = $env->getConfiguration('app.rootPath');
		if ($rootPath[strlen($rootPath)-1] !== '/') {
			$rootPath.='/';
		}
		return $rootPath;
	}

	/**
	 * Returns the database connection object identified by $dbName 
	 * of the current environment
	 * @static
	 * @param string $dbName 
	 * @return mixed
	 */
	public static function getConnection($dbName = null)
	{
		$env = self::getActiveEnvironment();
		$connections = $env->getConfiguration('app.connections');
		if (isset($connections[$dbName])) {
			return $connections[$dbName];
		} else {
			return null;
		}
	}

}