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
$searchkey = $_REQUEST['search_key'];

$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';
$sortMap = array(
	'paramgrp_name' => 'name'
);

$sortName = $_REQUEST['sort'];
if (!$sortName || !in_array($sortName, $sortMap))
	$sortName = 'paramgrp_name';

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

$sql = "SELECT SQL_CALC_FOUND_ROWS param_group_id, name FROM seg_lab_result_paramgroups WHERE status <> 'deleted' OR status IS NULL";

if($searchkey)
{
	$sql.=" AND name like '%$searchkey%' ";
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

	$edit_txt = "Edit Param Group";
	$del_txt = "Delete Param Group";
	while ($row = $result->FetchRow()) {
		$data[]=array(
			'paramgrp_name'=>$row["name"],
			'options'=> '<img src="../../../images/cashier_edit.gif" name="edit" class="segSimulatedLink" onmouseover="tooltip(\''.$edit_txt.'\')" onmouseout="nd();" onclick="openUpdateParamGrp(\''.$row["param_group_id"].'\',\''.$row["name"].'\');return false;"/>'.
									'&nbsp;&nbsp;<img src="../../../images/cashier_delete_small.gif" name="delete" class="segSimulatedLink" onmouseover="tooltip(\''.$del_txt.'\')" onmouseout="nd();" onclick="deleteParamGrp(\''.$row["param_group_id"].'\');return false;"/>'
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