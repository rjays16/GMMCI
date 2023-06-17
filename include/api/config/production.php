<?php

$rootpath = dirname(dirname(dirname(dirname(__FILE__)))).DIRECTORY_SEPARATOR;

// Old way of retrieving DB object. Needs to be updated...
require_once $rootpath.'include/inc_environment_global.php';

global $db;
return array(
	// application settings
	'app.rootPath' => $rootpath,
	'app.connections' => array(
		'core' => $db
	)
);