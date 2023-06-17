<?php
define('NO_CHAIN',1);
require './roots.php';
require_once $root_path.'include/inc_environment_global.php';
require_once $root_path.'include/inc_front_chain_lang.php';
require_once $root_path.'include/care_api_classes/class_core.php';
require_once $root_path.'classes/json/json.php';

global $db;

// set Content type to JSON
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json");


$db->StartTrans();

// Save Dashboard settings
$core = new Core;
$core->setTable('seg_dashboards', $fetchMetadata=true);
$ok = $core->delete(	array('id' => $_REQUEST['dashboard']), $logical_delete=true );

if (!$ok)
{
	$db->FailTrans();
}
$db->CompleteTrans();


$data = Array(
	'success' => ($ok ? 1 : 0)
	//'debug' => $debug

);
$json = new Services_JSON();
print $json->encode($data);