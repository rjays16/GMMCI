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
$key = $_REQUEST['search'];

$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';
$sortMap = array(
	'grp_name' => 'name'
);

$sortName = $_REQUEST['sort'];
if (!$sortName || !in_array($sortName, $sortMap))
	$sortName = 'grp_name';

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
$sql = "SELECT SQL_CALC_FOUND_ROWS group_id, name FROM seg_lab_result_groupname AS gn ".
"WHERE (ISNULL(gn.status) OR gn.status!='deleted') AND (ISNULL(status) OR status!='deleted')";
if($key)
{
	$sql.=" AND (gn.group_id LIKE '%$key%' OR gn.name LIKE '%$key%') ";
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
	$mode = "edit";
	$caption = "Edit Test Group";
	$edit_txt = "Edit Group";
	$del_txt = "Delete Group";
	while ($row = $result->FetchRow()) {
		$data[] = array(
			'grp_code'=> $row['group_id'],
			'grp_name'=>$row['name'],
			'options'=> '<img src="../../../images/cashier_edit.gif" name="edit" class="segSimulatedLink" onmouseover="tooltip(\''.$edit_txt.'\')" onmouseout="nd();" onclick="openGroupTray(\''.$mode.'\',\''.$caption.'\',\''.$row['group_id'].'\');return false;"/>'.
									'&nbsp;&nbsp;<img src="../../../images/cashier_delete_small.gif" name="delete" class="segSimulatedLink" onmouseover="tooltip(\''.$del_txt.'\')" onmouseout="nd();" onclick="deleteGroup(\''.$row['group_id'].'\');return false;"/>'
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