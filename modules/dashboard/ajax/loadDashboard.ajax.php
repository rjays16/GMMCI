<?php
/**
* ajax/deleteDashlet.ajax.php
*
* AJAX Response for the Dashboard.dashlets.remove AJAX call. The script accepts a single
* request parameter named <code>dashlet</code> which pertains to the unique id of the
* dashlet to be removed/deleted. The response returns a JSON-encoded object with 2
* properties: <code>status</code> and <code>error</code>
*
*/

define('NO_CHAIN',1);
require './roots.php';
require_once $root_path.'include/inc_environment_global.php';
require_once $root_path.'classes/json/json.php';
require_once $root_path.'include/care_api_classes/dashboard/Dashboard.php';


global $db;

// set Content type to JSON
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json");


$position = $db->GetOne("SELECT IFNULL(MAX(position)+10,0) FROM seg_dashboards WHERE owner=".$db->qstr($_SESSION['sess_temp_userid']));

$dashboard = new Dashboard;
$dashboard->setOwner($_SESSION['sess_temp_userid']);
$dashboard->setColumnCount(3);
$dashboard->setIcon('flag');
$dashboard->setPosition((int) $position);
$dashboard->setTitle($_REQUEST['title']);
$saveOk = $dashboard->save();

if (false !== $saveOk)
{
	$saveOk = 1;
	$id = $dashboard->getId();
	$icon = $dashboard->getIcon();
	$title = $dashboard->getTitle();
	$errorMessage = '';
}
else
{
	$saveOk = 0;
	$errorMessage = $db->ErrorMsg();
}

$json = new Services_JSON();
print $json->encode(Array(
	'success' => $saveOk,
	'id' 	=> $id,
	'icon' => $icon,
	'title' => $title,
	'error' => $errorMessage
));