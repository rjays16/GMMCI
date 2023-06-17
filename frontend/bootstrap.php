<?php
/**
 * bootstrap.php
 *
 * Bootstrap file for loading Yii classes without processing the controller/
 * action chain.
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2014. Segworks Technologies Corporation
 */

// change the following paths if necessary
$yii=dirname(__FILE__).'/../classes/yii/yii.php';
$config=dirname(__FILE__).'/protected/config/main.php';

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii);
require_once 'SegHis.php';
$application = new SegHis($config);

