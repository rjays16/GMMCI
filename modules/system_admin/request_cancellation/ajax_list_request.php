<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require "./roots.php";
require "{$root_path}include/inc_environment_global.php";
require "{$root_path}classes/json/json.php";
require_once($root_path.'include/care_api_classes/class_request_cancellation.php');

global $db;
$reqObj = new SegRequestCancel();

header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json");

$page = (int) $_REQUEST['page'];
$maxRows = (int) $_REQUEST['mr'];
$offset = ($page-1) * $maxRows;

$area = $_REQUEST['cost_center'];
$name = $_REQUEST['search_name'];
$pid = $_REQUEST['search_pid'];
$encounter_nr = $_REQUEST['search_encounter'];

$search_filters = array();
if($name) {
	$search_filters['NAME'] = $name;
}
if($pid) {
	$search_filters['PID'] = $pid;
}
if($encounter_nr) {
	$search_filters['CASENR'] = $encounter_nr;
}
$_REQUEST['dir'] = 0;
$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';
$sortMap = array(
	'request_date' => 'request_date',
	'refno' => 'refno',
	'patient_name' => 'patient_name',
	'item_name' => 'item_name',
	'request_flag' => 'request_flag',
	'request_status' => 'request_status'
);

$sortName = $_REQUEST['sort'];
if (!$sortName || !array_key_exists($sortName, $sortMap))
	$sortName = 'request_date';

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

switch(strtolower($area))
{
	case 'ld': $result = $reqObj->getLaboratoryRequests($search_filters, $sort_sql, $offset, $maxRows); break;
	case 'rd': $result = $reqObj->getRadiologyRequests($search_filters, $sort_sql, $offset, $maxRows); break;
	case 'ph': $result = $reqObj->getPharmacyRequests($search_filters, $sort_sql, $offset, $maxRows); break;
	case 'ot': $result = $reqObj->getMiscellaneousRequests($search_filters, $sort_sql, $offset, $maxRows); break;
	default: $result = FALSE; break;
}

/*echo "<pre>";
print_r($reqObj->sql);
echo "</pre>";*/

if ($result !== FALSE) {
	$total = $reqObj->FoundRows();

	while ($row = $result->FetchRow()) {

		$title_flag = "Cancel Flag";
		$title_status = "Cancel Status";
		$title_delete = "Delete Request";
		$disabled = FALSE;
		if(strtolower($row["request_flag"])=="paid" || strtolower($row["request_flag"])=="cmap" || strtolower($row["request_flag"])=="lingap" || $row["is_billed_main"]==1 || $row["is_billed_ic"]==1) {
			$disabled =  TRUE;
			$title_flag = "Cannot cancel.";
			$title_status = "Cannot cancel.";
			$title_delete = "Cannot delete.";
			$msg = "Cancel the transaction in PIAD first.";
			if($row["is_billed_main"]==1) {
				$msg = "Cancel the billing transaction first.";
				$bill_label = "Billed (Main)";
			}
			if($row["is_billed_ic"]==1) {
				$msg = "Cancel the billing transaction first.";
				$bill_label = "Billed (IC)";
			}
			if($row["request_flag"]=="paid") {
				$msg = "Cancel the or number in Cashier first.";
			}
		}

		$cancel_flag = '<img src="../../../gui/img/common/default/flag_blue.png" title="'.$title_flag.'"
							 '.($disabled==TRUE?'onclick="alertFlag(\''.$msg.'\');return false;"':'style="cursor:pointer;"
							 onclick="cancelFlag(\''.$area.'\',\''.$row["refno"].'\',\''.$row["item_code"].'\',\''.$row["request_flag"].'\');return false;"').'/>';
		$cancel_status = '<img src="../../../gui/img/common/default/flag_green.png" title="'.$title_status.'"
							 '.($disabled==TRUE?'onclick="alertFlag(\''.$msg.'\');return false;"':'style="cursor:pointer;"
							 onclick="cancelStatus(\''.$area.'\',\''.$row["refno"].'\',\''.$row["item_code"].'\');return false;"').'/>';
		$delete_request = '<img src="../../../images/cashier_delete_small.gif" title="'.$title_delete.'"
							 '.($disabled==TRUE?'onclick="alertFlag(\''.$msg.'\');return false;"':'style="cursor:pointer;"
							 onclick="deleteRequest(\''.$area.'\',\''.$row["refno"].'\',\''.$row["item_code"].'\');return false;"').'/>';

		$options = $cancel_status."&nbsp;".$cancel_flag."&nbsp;".$delete_request;
		if($row["request_status"]!="done" && $row["request_status"]!="serve") {
			$options = $cancel_flag."&nbsp;".$delete_request;
		}

		$data[] = array(
			'request_date' => $row["request_date"],
			'patient_id' => $row["pid"],
			'patient_enc' => $row["encounter_nr"],
			'refno' => $row["refno"],
			'patient_name' => ucfirst($row["patient_name"]),
			'item_name' => $row["item_name"],
			'request_bill' => strtoupper($row["is_billed_main"]==1 || $row["is_billed_ic"]==1 ?$bill_label:'no'),
			'request_status' => strtoupper($row["request_status"]),
			'request_flag' => $row["request_flag"]?strtoupper($row["request_flag"]):'NONE',
			'options' => $options,
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