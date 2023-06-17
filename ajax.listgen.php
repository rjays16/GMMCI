<?php

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require "./roots.php";
require "{$root_path}include/inc_environment_global.php";
require "{$root_path}include/care_api_classes/class_order.php";
require "{$root_path}classes/json/json.php";

global $db;

header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json");

$page = (int) $_REQUEST['page'];
$maxRows = (int) $_REQUEST['mr'];
$offset = ($page-1) * $maxRows;

$sortName = $_REQUEST['sort'];
if (!$sortName)
	$sortName = 'Name';
$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';
$sortMap = array(
	'DateReg' => 'date_reg',
	'Lastname' => 'name_last',
	'Firstname' => 'name_first',
	'Middlename' => 'name_middle',
	'Birthdate' => 'date_birth',
	'Sex' => 'sex',
	'Status' => 'status'
);
$sort = $sortMap[$sortName]." ".$sortDir;

$data = array();
$sql = "SELECT SQL_CALC_FOUND_ROWS date_reg, name_last, name_first, name_middle, date_birth, sex, status FROM care_person\n".
	"ORDER BY $sort\n".
	"LIMIT $offset, $maxRows";
$result = $db->Execute($sql);

if ($result !== FALSE) {
	$total = $db->GetOne("SELECT FOUND_ROWS()");
	while ($row = $result->FetchRow()) {
		$data[] = array(
			'DateReg' => $row['date_reg'],
			'Lastname' => $row['name_last'],
			'Firstname' => $row['name_first'],
			'Middlename' => $row['name_middle'],
			'Birthdate' => $row['date_birth'],
			'Sex' => $row['sex'],
			'Status' => $row['status']
		);
	}
}

//print_r($orderObj->sql);

$response = array(
	'currentPage'=>$page,
	'total'=>$total,
	'data'=>$data
 );

$json = new Services_JSON;
print $json->encode($response);