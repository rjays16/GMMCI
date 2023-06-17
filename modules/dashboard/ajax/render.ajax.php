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

if ($_REQUEST['dashlet'])
{
	/**
	* Loads a saved Dashlet and adds it to the Dashboard.
	*/
	$manager = DashletManager::getInstance();
	$dashlet = $manager->loadDashlet($_REQUEST['dashlet']);
	if (false !== $dashlet)
	{
	}
}

if ($dashlet)
{

	// Create Smarty object
	$smarty = new smarty_care('common');

	$preferencesSmarty = Array(
		'contentHeight' => $dashlet->getPreferences()->get('contentHeight')
	);

	$dashletSmarty = Array(
		'id' => $dashlet->getId(),
		'title' => $dashlet->getTitle(),
		'icon' => "../../gui/img/common/default/".$dashlet->getIcon(),
		'state' => $dashlet->getState()->getName(),
		'contents' => $dashlet->render()
	);

	$smarty->assign('dashlet', $dashletSmarty);
	$smarty->assign('preferences', $preferencesSmarty);

	$html = $smarty->fetch($root_path.'modules/dashboard/templates/dashlet.tpl');
}
else
{
	$html = "";
}

$data = Array(
	'id' 				=> $dashlet->getId(),
	'className' => $dashlet->getClassName(),
	'group'			=> $dashlet->getGroupList(),
	'render'		=> $html
);

$json = new Services_JSON();
print $json->encode($data);

//print_r($data);