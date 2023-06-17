<?php

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require "./roots.php";
require "{$root_path}include/inc_environment_global.php";
require "{$root_path}classes/json/json.php";

global $db;

header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json");

$page = (int) $_REQUEST['page'];
$maxRows = (int) $_REQUEST['mr'];
$offset = ($page-1) * $maxRows;
$params = $_REQUEST["code"];

$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';
$sortMap = array(
	'testgrp_name' => 'name'
);

$sortName = $_REQUEST['sort'];
if (!$sortName || !in_array($sortName, $sortMap))
	$sortName = 'testgrp_name';

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

$sql = "SELECT SQL_CALC_FOUND_ROWS n.group_id, n.name FROM seg_lab_result_groupname AS n ".
			"LEFT JOIN seg_lab_result_groupparams AS p ON p.group_id=n.group_id ".
			"WHERE n.status <> 'deleted'  AND p.status <> 'deleted'";

if($params)
{
	$sql.=" AND p.service_code='$params' ";
}
if($sort_sql)
{
	$sql.=" ORDER BY {$sort_sql} ";
}
if($maxRows)
{
	$sql.=" LIMIT $offset, $maxRows";
}
$result = $db->Execute($sql);

if ($result !== FALSE) {
	$total = $db->GetOne("SELECT FOUND_ROWS()");

	while ($row = $result->FetchRow()) {
		$data[]=array(
			'testgrp_name'=>$row["name"],
			'options'=> '<img src="../../../images/cashier_delete_small.gif" name="delete" class="link" onclick="removeGrpAssignment(\''.$row["group_id"].'\');return false;"/>'
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