<?php

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require "./roots.php";
require "{$root_path}include/inc_environment_global.php";
require_once($root_path.'include/care_api_classes/sponsor/class_cmap_walkin.php');
require "{$root_path}classes/json/json.php";

global $db;
$cmapObj = new CmapWalkin();

header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json");

$page = (int) $_REQUEST['page'];
$maxRows = (int) $_REQUEST['mr'];
$offset = ($page-1) * $maxRows;

$check = $_REQUEST["check"];
$name = $_REQUEST["name"];
$date_type = $_REQUEST["date_type"];
$date_specific = $_REQUEST["date_specific"];
$date_between1= $_REQUEST["date_between1"];
$date_between2= $_REQUEST["date_between2"];

$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';
$sortMap = array(
	'walkin_name' => 'walkin_name',
	'walkin_createdt' => 'create_time'
);

$sortName = $_REQUEST['sort'];
if (!$sortName || !array_key_exists($sortName, $sortMap))
	$sortName = 'walkin_name';

$filters = array(
	'sort' => $sortMap[$sortName]." ".$sortDir
);
$data = array();
$phFilters = array();
if(is_array($filters))
{
	foreach ($filters as $i=>$v) {
		switch (strtolower($i)) {
			case 'sort': $sort_sql = $v; break;
		}
	}
}

$reqFilters = array();
if($name)
	$reqFilters['NAME'] = $name;
if($date_type=='today')
	$reqFilters['TODAY'] = "";
if($date_type=='week')
		$reqFilters['WEEK'] = "";
if($date_type=='month')
		$reqFilters['MONTH'] = "";
if($date_type=='specific')
		 $reqFilters['SPECIFIC'] = $date_specific;
if($date_type=='between')
		 $reqFilters['BETWEEN'] = array($date_between1, $date_between2);

if(is_null($reqFilters))
	$reqFilters['TODAY'] = "";

$reqFilters['SORT'] = $sort_sql;
$reqFilters['OFFSET'] = $offset;
$reqFilters['MAXROWS'] = $maxRows;

$result = $cmapObj->searchWalkin($reqFilters);
/*echo "<pre>";
print_r($cmapObj->getLastQuery());
echo "</pre>";*/

if ($result !== FALSE) {
	$total = $db->GetOne("SELECT FOUND_ROWS()");
	while ($row = $result->FetchRow()) {
		$options =
			"<button class='segButton' onclick='updateWalkinDetails(\"".$row['id']."\"); return false;'><img src='../../gui/img/common/default/user_edit.png'/>Edit</button>".
			"<button class='segButton' onclick='deleteWalkin(\"".$row['id']."\"); return false;'><img src='../../gui/img/common/default/user_delete.png'/>Delete</button>";

		$data[] = array(
			'walkin_name' => $row["walkin_name"],
			'walkin_address' => $row['address'],
			'walkin_gender' => $row['gender'],
			'walkin_createdt' => date("d-M-Y h:ia", strtotime($row["create_time"])),
			'options' =>$options
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