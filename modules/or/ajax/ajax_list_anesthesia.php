<?php

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require "./roots.php";
require "{$root_path}include/inc_environment_global.php";
require "{$root_path}include/care_api_classes/billing/class_ops.php";
require "{$root_path}classes/json/json.php";

global $db;

header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json");

$page = (int) $_REQUEST['page'];
$maxRows = (int) $_REQUEST['mr'];
$offset = ($page-1) * $maxRows;
$key = $_REQUEST['search'];

$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';
$sortMap = array(
	'name' => 'name'
);

$sortName = $_REQUEST['sort'];
if (!$sortName || !in_array($sortName, $sortMap))
	$sortName = 'name';

$filters = array(
	'sort' => $sortMap[$sortName]." ".$sortDir
);
$opsObj = new SegOps;
$data = array();
$result = $opsObj->get_anesthesia_procedures($filters,$maxRows, $offset, $key);
if ($result !== FALSE) {
	$total = $opsObj->FoundRows();
	$mode="edit";
	while ($row = $result->FetchRow()) {
		$data[] = array(
			'name'=> $row['name'],
			'options'=> '<img src="'.$root_path.'images/cashier_edit.gif" name="edit" onclick="open_tray_anesthesia(\''.$mode.'\',\''.$row['id'].'\',\''.$row['name'].'\');" class="link"/>'.
									'&nbsp;&nbsp;<img src="'.$root_path.'images/cashier_delete_small.gif" name="delete" onclick="open_delete_anesthproc(\''.$row['id'].'\');" class="link"/>'
		);
	}
}

$response = array(
	'total'=>$total,
	'data'=>$data
 );

$json = new Services_JSON;
print $json->encode($response);