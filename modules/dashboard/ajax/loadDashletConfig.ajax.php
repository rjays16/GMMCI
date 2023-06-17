<?php

define('NO_CHAIN',1);
require './roots.php';
require_once $root_path.'include/inc_environment_global.php';
require_once $root_path.'include/inc_front_chain_lang.php';
require_once $root_path.'gui/smarty_template/smarty_care.class.php';
require_once $root_path.'classes/json/json.php';


global $db;


header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/html");


// Create Smarty object
$smarty = new smarty_care('common');


// Create the dashlet
require_once $root_path.'include/care_api_classes/dashboard/DashletManager.php';
require_once $root_path.'include/care_api_classes/dashboard/DashletMode.php';
$manager = DashletManager::getInstance();

if ($_REQUEST['id'])
{
	$dashlet = $manager->loadDashlet($_REQUEST['id']);
	$html = $dashlet->render(array('mode'=>DashletMode::getEditMode()));
}
else
{
	$html = "Invalid request sent...";
}

print $html;