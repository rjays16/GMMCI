<?php

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require "./roots.php";
require "{$root_path}include/inc_environment_global.php";
require "{$root_path}include/care_api_classes/sponsor/class_cmap_account.php";
require "{$root_path}classes/json/json.php";

global $db;

header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json");

$page = (int) $_REQUEST['page'];
$maxRows = (int) $_REQUEST['mr'];
$offset = ($page-1) * $maxRows;

$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';
$sortMap = array(
	'account_name' => 'account_name',
	'running_balance' => 'running_balance'
);

$sortName = $_REQUEST['sort'];
if (!$sortName || !in_array($sortName, $sortMap))
	$sortName = 'name';

$filters = array(
	'sort' => $sortMap[$sortName]." ".$sortDir
);
$cmap_obj = new SegCMAPAccount();
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
$sql = "SELECT SQL_CALC_FOUND_ROWS account_nr, account_name, account_address, running_balance FROM seg_cmap_accounts WHERE is_deleted='0'";
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
	$mode="edit";
	while ($row = $result->FetchRow()) {
		$data[] = array(
			'account_name'=> $row['account_name'],
			'account_address'=>$row['account_address'],
			'running_balance'=>$row['running_balance'],
			'options'=> '<img src="../../images/cashier_edit.gif" name="edit" class="link" onclick="edit_account(\''.$row["account_nr"].'\',\''.$row["account_name"].'\',\''.$row["account_address"].'\');"/>'.
									'&nbsp;&nbsp;<img src="../../images/cashier_delete_small.gif" name="delete" class="link" onclick="delete_account(\''.$row["account_nr"].'\');"/>'
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