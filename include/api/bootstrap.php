<?php
/**
 * Bootstrap file for loading SegHIS2 API
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2005-2013, Segworks Technologies Corporation
 */

// Load Core utility classes
require_once 'core/Debugger.php';
// Load Environment
require_once 'core/Environment.php';

// Retrieve the 'production' environment configuration
$production = include 'config/production.php';
Environment::add('production', $production);
Environment::load('production');

// Load class Loader
require_once 'core/Loader.php';
define('APP_PATH', Environment::getRootPath());
define('API_PATH', APP_PATH.'include'.DIRECTORY_SEPARATOR.'api');
Loader::registerPath(APP_PATH);
Loader::registerPath(API_PATH);

// register aliases
Loader::registerAlias('app', APP_PATH);
Loader::registerAlias('api', API_PATH);
spl_autoload_register(array('Loader','autoload'));