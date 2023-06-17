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

$specific_id = $_REQUEST['id'];

$page = (int) $_REQUEST['page'];
$maxRows = (int) $_REQUEST['mr'];
$offset = ($page-1) * $maxRows;

$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';
$sortMap = array(
	'sub_anesth_id' => 'sub_anesth_id',
	'description' => 'description'
);

$sortName = $_REQUEST['sort'];
if (!$sortName || !in_array($sortName, $sortMap))
	$sortName = 'description';

$filters = array(
	'sort' => $sortMap[$sortName]." ".$sortDir
);
$opsObj = new SegOps;
$data = array();
$result = $opsObj->get_anesthesia_specific($filters, $specific_id);
if ($result !== FALSE) {
	$total = $opsObj->FoundRows();    
	while ($row = $result->FetchRow()) {
		$data[] = array(
			'id'=> $row['sub_anesth_id'],
			'name'=> $row['description'],
			'options'=> '<img src="'.$root_path.'images/cashier_edit.gif" name="edit" onclick="open_edit_anesth_spec(\''.$row['sub_anesth_id'].'\', \''.$row['description'].'\');" class="link"/>'.
									'&nbsp;&nbsp;<img src="'.$root_path.'images/cashier_delete_small.gif" name="delete" onclick="open_delete_anesth_spec(\''.$row['sub_anesth_id'].'\', \''.$row['description'].'\');" class="link"/>'
		);
	}                             
}

$response = array(
	'total'=>$total,
	'data'=>$data
 );

$json = new Services_JSON;
print $json->encode($response);