<?php

$protectedDir = dirname(dirname(__FILE__));
$modulesDir = $protectedDir . DIRECTORY_SEPARATOR . 'modules';

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
require dirname(dirname(dirname(dirname(__FILE___)))).DIRECTORY_SEPARATOR.
    'include'.DIRECTORY_SEPARATOR.
    'inc_init_main.php';

return array(
	'basePath'=>dirname(dirname(__FILE__)),
	'name'=>'Embedded Modules',
    
    'aliases' => array(
        'bootstrap' => dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.
            'extensions'.DIRECTORY_SEPARATOR.
            'yiibooster',
        'SegHis' => $protectedDir,
    ),

	// preloading 'log' component
	'preload'=>array('log', 'bootstrap'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
	),

	'modules'=>array(
        'inventory' => array(),
        'or' => array(),
        'eclaims',
        'phic' => array(),
        'billing' => array(),
		// uncomment the following to enable the Gii tool

		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>false,
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),
		
	),
    'modulePath' => $modulesDir,
    // application components
	'components'=>array(
        'bootstrap' => array(
			'class' => 'ext.yiibooster.components.Bootstrap',
			'responsiveCss' => true,
            'fontAwesomeCss' => true
        ),
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>false,
            'class' => 'WebUser',
            'loginUrl' => 'main/login.php',
            'autoUpdateFlash' => false, // disable the flash counter
		),
		'format' => array(
            'class' => 'application.components.Formatter'
        ),
		// uncomment the following to enable URLs in path-format
//		'urlManager'=>array(
//			'urlFormat'=>'path',
//			'rules'=>array(
//				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
//				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
//				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
//			),
//		),
		'db'=>array(
			'connectionString' => 'mysql:host='.$dbhost.';dbname='.$dbname,
			'emulatePrepare' => true,
			'username' => $dbusername,
			'password' => $dbpassword,
			'charset' => 'utf8',
		),
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
//					'levels'=>'error, warning',
                    'levels'=>'error, trace',

                ),
				// uncomment the following to show log messages on web pages
//				array(
//					'class'=>'CWebLogRoute',
//				),
			),
		),
        'session' => array(
            'class' => 'CareHttpSession'
        )
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
	),
);