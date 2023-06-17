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

$pid = $_REQUEST["pid"];

$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';
$sortMap = array(
	'ref_no' => 'refno',
	'date_visited' => 'transaction_date',
	'dialysis_type' => 'dialysis_type',
	'status' => 'status'
);

$sortName = $_REQUEST['sort'];
if (!$sortName || !array_key_exists($sortName, $sortMap))
	$sortName = 'ref_no';

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


$sql = "SELECT SQL_CALC_FOUND_ROWS dt.refno, dt.transaction_date, \n".
	"(SELECT fn_get_person_name(cp.pid) \n".
	"	FROM care_personell AS pe INNER JOIN care_person AS cp ON pe.pid=cp.pid WHERE pe.nr=dt.requesting_doctor) AS `requesting_doctor`, \n".
	"(SELECT fn_get_person_name(cp.pid) \n".
	"	FROM care_personell AS pe INNER JOIN care_person AS cp ON cp.pid=pe.pid WHERE pe.nr=dt.attending_nurse) AS `attending_nurse`, \n".
	"dt.dialysis_type, dt.status, dt.encounter_nr, dt.is_deleted, \n".
	"EXISTS(SELECT bill_nr FROM seg_billing_encounter AS b WHERE b.encounter_nr=dt.encounter_nr) AS `is_billed`\n".
	"FROM seg_dialysis_transaction AS dt \n".
	"LEFT JOIN care_encounter AS ce ON dt.encounter_nr=ce.encounter_nr \n".
	"WHERE dt.pid='$pid'";
if($sort_sql)
{
	$sql.=" ORDER BY {$sort_sql} ";
}
if($maxRows)
{
	$sql.=" LIMIT $offset, $maxRows";
}
$result = $db->Execute($sql);

//echo $sql;
/*echo "<pre>";
print_r($_REQUEST);
echo "</pre>";*/

if ($result !== FALSE) {
	$total = $db->GetOne("SELECT FOUND_ROWS()");
	$status = array(0=>"UNDONE", 1=>"DONE");
	while ($row = $result->FetchRow()) {

		if(strtolower($row["status"])=="done" && $row["is_deleted"]=="0") {
			$buttons = '<button class="segButton" onclick="return false;"><img src="../../gui/img/common/default/cart_add.png" style="opacity:0.5;" disabled=""/>Request</button>'.
										'<button class="segButton" onclick="return false;"><img src="../../gui/img/common/default/calculator.png"/>Bill</button>'.
										'<button class="segButton" onclick="return false;"><img src="../../gui/img/common/default/cancel.png" style="opacity:0.5;" disabled=""/>Delete</button>';
		}
		else if($row["is_deleted"]=="1") {
			$buttons = '&nbsp;&nbsp;<span style="font:bold;color:#ff0000"><label>DELETED</label><span>';
		}
		else {
			$buttons = '<button class="segButton" onclick="openRequestTray(\''.$row["encounter_nr"].'\',\''.$pid.'\');return false;"><img src="../../gui/img/common/default/cart_add.png"/>Request</button>'.
										'<button class="segButton" onclick="openBillingTray(\''.$row["encounter_nr"].'\',\''.$pid.'\');return false;"><img src="../../gui/img/common/default/calculator.png"/>Bill</button>'.
										'<button class="segButton" onclick="deleteRequest(\''.$row["encounter_nr"].'\',\''.$pid.'\',\''.$row["refno"].'\');return false;"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
		}

		$status_options='<select class="segInput" id="stat_dialysis" onchange="changeStatus(this.id, \''.$row["refno"].'\', \''.$row["encounter_nr"].'\')" '.($row["is_deleted"]?'disabled="disabled"':'').'>';
		for($i=0;$i<count($status);$i++)
		{
			if($row["status"]==$status[$i]){
				$status_options.='<option value="'.$i.'" selected="">'.$status[$i].'</option>';
			}else{
				$status_options.='<option value="'.$i.'">'.$status[$i].'</option>';
			}
		}
		$status_options.='</select>';

		$data[] = array(
			'ref_no' => $row["refno"],
			'date_visited' => date("d-M-Y h:i: a", strtotime($row["transaction_date"])),
			'encounter_nr'=>$row["encounter_nr"],
			'dialysis_type' => $row["dialysis_type"],
			'status' => $status_options,
			'options' => $buttons
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