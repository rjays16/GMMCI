<?php

/**
 * 
 * @package api.core
 */

class Loader {

	/**
	 * @var array $imported Mapping of successfully imported paths
	 */
	protected static $imported = array();
	/**
	 * @var array $paths Mapping of paths to the actual resolved file paths
	 */
	protected static $paths = array();
	/**
	 * @var array $aliases Mapping of aliases to their base paths
	 */
	protected static $aliases = array();
	/**
	 * @var array $classMap Mapping of classnames to their resolved file paths
	 */
	protected static $classMap = array();

	/**
	 * Called by spl_autoload when trying to load a non-existent class
	 * Do not call directly.
	 * @return void
	 */
	public static function autoload($className)
	{
		$included = false;
		if (isset(self::$classMap[$className])) {
			$included = include self::$classMap[$className];
		} else {

			foreach (self::$paths as $path) {
				$included = include $path.$className.'.php';
				if ($included) {
					return $included;
				}
			}
		}

	}

	/**
	 * Description
	 * @param type $path 
	 * @return type
	 */
	private static function cleanPath($path) 
	{
		$path=trim($path);
		if ($path[strlen($path)-1] !== DIRECTORY_SEPARATOR) {
			$path.=DIRECTORY_SEPARATOR;
		}
		return $path;
	}

	/**
	 * Loads a library/path into the autoloader list
	 * 
	 * @param string $path
	 * @param boolean $eagerLoading Optional. If set to TRUE, the file
	 * specified by $path will be included immediately
	 * @return string
	 */
	public static function import($path, $eagerLoading=false)
	{
		if (isset(self::$imported[$path])) {
			return self::$imported[$path];
		}

		$paths = explode('.', $path);
		$rootPath = '';
		$basePath = array_shift($paths);

		foreach (self::$aliases as $alias => $path) {
			if ($basePath == $alias) {
				$rootPath = $path;
			}
		}
		if (!$rootPath) {
			// Default root path points to API path
			$rootPath = self::$aliases['api'];
			array_unshift($paths, $basePath);
		}

		$filePath = $rootPath.implode(DIRECTORY_SEPARATOR, $paths).'.php';
		if (!file_exists($filePath)) {
			throw new Exception('Invalid import path');
		}

		$className = array_pop($paths);
		self::$classMap[$className] = $filePath;
		self::$imported[$path] = $filePath;

		// } else {
		// 	$pos = strrpos($path,'.');
		// 	if ($pos !== false) {
		// 		$className=(string)substr($alias,$pos+1);
		// 	} else {
		// 		// How did we get here?
		// 		throw new HISException('Unresolvable import path encountered');
		// 	}
		// }

		if ($eagerLoading && 
			!class_exists($className, false) && 
			!interface_exists($className, false)) 
		{
			self::autoload($className);
		}

		return $filePath;
	}

	/**
	 * 
	 * @param mixed $alias 
	 * @param mixed $path
	 * @return 
	 */
	public static function registerAlias($alias, $path=null)
	{
		if (is_array($alias)) {
			foreach ($alias as $_alias=>$_path) {
				self::registerAlias($_alias, $_path);
			}
		} else {
			if(empty($path))
				unset(self::$aliases[$alias]);
			else {
				$path = self::cleanPath($path);
				self::$aliases[$alias] = $path;
			}
		}
	}

	/**
	 * Description
	 * @return void
	 */
	public static function registerPath($path)
	{
		$path = self::cleanPath($path);
		if (!in_array($path, self::$paths)) {
			self::$paths[] = $path;
		}
	}

	/**
	 * Registers a new class autoloader.
	 * The new autoloader will be placed before {@link autoload} and after
	 * any other existing autoloaders.
	 * @param callback $callback a valid PHP callback (function name or array($className,$methodName)).
	 * @param boolean $append whether to append the new autoloader after the default Yii autoloader.
	 */
	public static function registerAutoloader($callback, $append=false)
	{
		if($append) {
			spl_autoload_register($callback);
		}
		else {
			spl_autoload_unregister(array('Loader','autoload'));
			spl_autoload_register($callback);
			spl_autoload_register(array('Loader','autoload'));
		}
	}


}