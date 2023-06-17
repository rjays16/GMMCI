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

$dashboard = Dashboard::loadDashboard($_REQUEST['dashboard']);
$manager = DashletManager::getInstance();
$append = true;
if ($_REQUEST['dashlet'])
{
	/**
	* Loads a saved Dashlet and adds it to the Dashboard.
	*/
	$dashlet = $manager->loadDashlet( $_REQUEST['dashlet'] );
	if (false !== $dashlet)
	{
	}
}
elseif ($_REQUEST['name'])
{
	/**
	* Creates a new Dashlet item and adds it to the Dashboard
	*/

	$dashlet = $manager->createDashlet($_REQUEST['name']);
	$saveOk = false;
	if (false !== $dashlet)
	{
		$dashboard->addDashlet($dashlet);
		$saveOk = $manager->saveDashlet($dashlet, $dashboard);
	}
	if (false === $saveOk)
	{
		$dashlet = null;
	}
	$append = false;
}


if ($dashlet)
{
//	$dashletId = $dashlet->getId();
//	$pref = Array(
//		'contentHeight' => $dashlet->getPreferences()->get('contentHeight')
//	);

//	$dashletSmarty = Array(
//		'id' => $dashletId,
//		'title' => $dashlet->getTitle(),
//		'icon' => "../../gui/img/common/default/".$dashlet->getIcon()
//	);

//	$smarty->assign('dashlet', $dashletSmarty);
//	$smarty->assign('preferences', $pref);
//	$html = $smarty->fetch($root_path.'modules/dashboard/templates/dashlet.tpl');
}
else
{
	$dashletId = null;
	$html = null;
}

$data = Array(
	'id' 			=> $dashlet->getId(),
	'append'	=> $append,
	'render'	=> $html
);

$json = new Services_JSON();
print $json->encode($data);