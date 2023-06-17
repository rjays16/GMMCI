<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require "./roots.php";
require "{$root_path}include/inc_environment_global.php";
require "{$root_path}classes/json/json.php";
require_once($root_path.'include/care_api_classes/class_request_cancellation.php');
global $db;
$reqObj = new SegRequestCancel();

require_once($root_path.'include/care_api_classes/class_seg_fis_mapping.php');
$objFis = new FisMapping();


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
$accountTransaction = $_REQUEST['transctioncode'];
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
	case 'ld': 
		$aread = 'LD';
		$result = $objFis->GetLabService($name, $offset, $maxRows, $accountTransaction); 
		break;

	case 'rd': 
		$aread = 'RD';
		$result = $objFis->GetRadioService($name, $offset, $maxRows, $accountTransaction); 
		break;

	case 'ph': 
		$aread = 'PH';
		$result = $objFis->GetPharmaItems($name, $offset, $maxRows, $accountTransaction); 
		break;

	case 'ot': 
		$aread = 'OT';
		$result = $objFis->GetMiscItems($name, $offset, $maxRows, $accountTransaction); 
		break;

	default: 
		$result = FALSE; 
		break;
}

if ($result !== FALSE) {
	$total = $objFis->FoundRows();

	while ($row = $result->FetchRow()) {

		$title_flag = "Update Account";
		$disabled = FALSE;

		if($row['setup'] == 'setup'){
			$set = 1;
			$UpdateFlag = '<img src="../../../gui/img/common/default/flag_blue.png" title="'.$title_flag.'" onclick=checkfunction("'.$row['item_code'].'","'.$accountTransaction.'","'.$aread.'","'.$set.'","'.$row['code_area'].'"); />';
		}else{
			$set = 0;
			$UpdateFlag = '<img src="../../../gui/img/common/default/flag_blue.png" title="'.$title_flag.'" onclick=checkfunction("'.$row['item_code'].'","'.$accountTransaction.'","'.$aread.'","'.$set.'","'.$row['code_area'].'"); />';
		}

		$options = $UpdateFlag;

		if($row['group_name']){
			$groupname = $row['group_name'];
		}else{
			$groupname = 'N/A';
		}

		$data[] = array(
			'service_code' => $row["item_code"],
			'group_name' => $groupname,
			'item_name' => $row["item_name"],
			'setup' => $row['setup'],
			'options' => $options,
		);
	}
}

$resultTransaction = $objFis->GetAccount($accountTransaction);
if($resultTransaction[0] !== 0){
	switch (strtolower($resultTransaction[2])) {
		case 'ins':
			$aread = "insurance";
			$result = $objFis->Getinsurances($name, $offset, $maxRows, $accountTransaction);
			break;
		case 'com':
			$aread = "company";
			$result = $objFis->GetCompany($name, $offset, $maxRows, $accountTransaction);
			break;
		case 'dc':
			$aread = "discount";
			$result = $objFis->GetDiscount($name, $offset, $maxRows, $accountTransaction);
			break;
		case 'dp':
			$aread = "deposit";
			$result = $objFis->GetDeposit($name, $offset, $maxRows, $accountTransaction);
			break;
		default:
			$result = FALSE;
			break;
	}
}

if ($result !== FALSE) {
	$total = $objFis->FoundRows();

	while ($row = $result->FetchRow()) {

		$title_flag = "Update Account";
		$disabled = FALSE;

		if($row['setup'] == 'setup'){
			$set = 1;
			$UpdateFlag = '<img src="../../../gui/img/common/default/flag_blue.png" title="'.$title_flag.'" onclick=checkfunction("'.$row['id_code'].'","'.$accountTransaction.'","'.$aread.'","'.$set.'","'.$row['code_area'].'"); />';
		}else{
			$set = 0;
			$UpdateFlag = '<img src="../../../gui/img/common/default/flag_blue.png" title="'.$title_flag.'" onclick=checkfunction("'.$row['id_code'].'","'.$accountTransaction.'","'.$aread.'","'.$set.'","'.$row['code_area'].'"); />';
		}

		$options = $UpdateFlag;

		if($row['group_name']){
			$groupname = $row['group_name'];
		}else{
			$groupname = 'N/A';
		}

		$data[] = array(
			'service_code' => $row["id_code"],
			'group_name' => $groupname,
			'item_name' => $row["name"],
			'setup' => $row['setup'],
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