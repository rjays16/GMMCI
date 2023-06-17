<?php

define('NO_CHAIN',1);
require './roots.php';
require_once $root_path.'include/inc_environment_global.php';
require_once $root_path.'include/inc_front_chain_lang.php';
require_once $root_path.'gui/smarty_template/smarty_care.class.php';
require_once $root_path.'classes/json/json.php';


global $db;


// set Content type to JSON
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json");


// load Dashboard object
require_once $root_path.'include/care_api_classes/dashboard/Dashlet.php';
require_once $root_path.'include/care_api_classes/dashboard/Dashboard.php';
require_once $root_path.'include/care_api_classes/dashboard/DashletManager.php';

//$dashboard = Dashboard::loadDashboard($_REQUEST['dashboard']);
$responses = array();


$parameters = $_REQUEST['parameters'];

if (is_array($parameters))
{
	/* Decode Action parameters from UTF-8 encoding to ISO-8859-1
	*
	*
	*/

	function decode_callback(&$item, $key)
	{
		if ('string' === gettype($item))
		{
			$encoding = mb_detect_encoding($item.'a', 'UTF-8, ISO-8859-1');
			if (strtolower($encoding) == 'utf-8')
			{
				$item = utf8_decode($item);
			}
		}
	}



	array_walk_recursive($parameters, 'decode_callback');
}


if (false !== $dashboard) {
	/**
	* Loads the Dashlet from the DB
	*/
	$manager = DashletManager::getInstance();
	$dashlet = $manager->loadDashlet($_REQUEST['dashlet']);

	if (false !== $dashlet)
	{
		$responses = $dashlet->processAction(new DashletAction($_REQUEST['action'], $parameters))->getResponses();
	}
}

$json = new Services_JSON();
print $json->encode($responses);