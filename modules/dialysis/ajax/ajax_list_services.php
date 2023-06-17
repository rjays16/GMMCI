<?php

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require "./roots.php";
require "{$root_path}include/inc_environment_global.php";
require "{$root_path}classes/json/json.php";
require_once($root_path.'include/care_api_classes/dialysis/class_dialysis_manager.php');

global $db;
$dialysis_obj = new SegDialysisManager();

header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json");

$page = (int) $_REQUEST['page'];
$maxRows = (int) $_REQUEST['mr'];
$offset = ($page-1) * $maxRows;

$search_key = $_REQUEST['search_key'];
$cost_center = $_REQUEST['cost_center'];
$mode = $_REQUEST['mode'];

$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';
$sortMap = array(
	'item_code' => 'item_code',
	'item_name' => 'item_name'
);

$sortName = $_REQUEST['sort'];
if (!$sortName || !array_key_exists($sortName, $sortMap))
	$sortName = 'item_name';

$filters = array(
	'sort' => $sortMap[$sortName]." ".$sortDir
);
$data = array();
if(is_array($filters))
{
	foreach ($filters as $i=>$v) {
		switch (strtolower($i)) {
			case 'sort': $sort_sql = $v; break;
		}
	}
}

$result = $dialysis_obj->searchServiceForCompany($cost_center, $search_key, $sort_sql, $offset, $maxRows);

/*echo "<pre>";
print_r($dialysis_obj->sql);
echo "</pre>";*/

if ($result !== FALSE) {
	$total = $dialysis_obj->FoundRows();
	while ($row = $result->FetchRow()) {

		$button = '<button class="segButton" onclick="addItemToList(\''.$row['item_code'].'\',\''.$row['item_name'].'\',\''.$row['item_area'].'\',\'1\');return false;"><img src="../../gui/img/common/default/add.png">Add</button>';
		$item_price = '<span style="color:#000000;">'.number_format($row[item_price],2).'</span>';

		$data[] = array(
			'item_code' => strtoupper($row['item_code']),
			'item_name' => ucfirst($row['item_name']),
			'item_price' => $item_price,
			'options' => $button
		);
	}
}

$response = array(
	'currentPage'=>$page,
	'total'=>$total,
	'data'=>$data
 );

$json = new Services_JSON;
print $json->encode($response);