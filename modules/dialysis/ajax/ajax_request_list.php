<?php

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require "./roots.php";
require "{$root_path}include/inc_environment_global.php";
require_once($root_path.'include/care_api_classes/dialysis/class_dialysis.php');
require "{$root_path}classes/json/json.php";

global $db;

header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json");

$page = (int) $_REQUEST['page'];
$maxRows = (int) $_REQUEST['mr'];
$offset = ($page-1) * $maxRows;

$check = $_REQUEST["check"];
$pid = $_REQUEST["pid"];
$encounter_nr = $_REQUEST["encounter_nr"];
$name = $_REQUEST["name"];
//$status = $_REQUEST["status"];
$date_type = $_REQUEST["date_type"];
$date_specific = $_REQUEST["date_specific"];
$date_between1= $_REQUEST["date_between1"];
$date_between2= $_REQUEST["date_between2"];

$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';
$sortMap = array(
	'refno' => 'refno',
	'date_visited' => 'request_date',
	'request' => 'request_type',
	'session' => 'session_type',
	'iscash' => 'is_cash'
);

$sortName = $_REQUEST['sort'];
if (!$sortName || !array_key_exists($sortName, $sortMap))
	$sortName = 'refno';

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


$sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT dt.refno, dt.request_date, dt.pid, dt.request_type, \n".
			" dt.session_type, dt.encounter_nr, d.request_flag, dt.is_cash, \n".
			"CONCAT(IF(cp.name_last!='',cp.name_last,' '),', ',IF(cp.name_first!='',cp.name_first,' '),' ',IF(cp.name_middle!='',cp.name_middle,' ')) as `patient_name`, ce.is_discharged \n".
			//"EXISTS(SELECT bill_nr FROM seg_billing_encounter AS b WHERE b.encounter_nr=dt.encounter_nr) AS `is_billed`\n".
			"FROM seg_dialysis_request AS dt \n".
			"INNER JOIN seg_dialysis_request_details as d ON dt.refno=d.refno\n".
			"INNER JOIN care_encounter AS ce ON dt.encounter_nr=ce.encounter_nr \n".
			"INNER JOIN care_person AS cp ON dt.pid=cp.pid \n".
			" WHERE dt.is_deleted=0 AND ce.is_discharged=0 \n";
switch($check)
{
	case 'patient':
		 if($pid){
				$sql.=" AND dt.pid='$pid'  \n";
			}
			if($encounter_nr) {
				$sql.="  AND dt.encounter_nr='$encounter_nr' \n";
			}
			if($name) {
				if(strpos($name,',')!==FALSE){
					$split_name = explode(',', $name);
					$sql.= " AND (cp.name_last LIKE ".$db->qstr(trim($split_name[0])."%").
					" AND cp.name_first LIKE ".$db->qstr(trim($split_name[1])."%").") \n";
				}else {
					$sql.= "  AND (cp.name_last LIKE '$name%' OR cp.name_first LIKE '$name%') \n";
				}
			}
		 break;
	/*case 'status':
		if($status) {
			$sql.=" AND dt.status='".strtoupper($status)."' ";
		}
		break;*/
	case 'date':
		if($date_type=='today') {
				$sql.= " AND (DATE(dt.request_date)=DATE(NOW() )) \n";
		} else if($date_type=='week') {
				$sql.=" AND (YEAR(dt.request_date)=YEAR(NOW()) AND WEEK(dt.request_date)=WEEK(NOW()))  \n";
		} else if($date_type=='month') {
				$sql.=" AND (YEAR(dt.request_date)=YEAR(NOW()) AND MONTH(dt.request_date)=MONTH(NOW()))  \n";
		} else if($date_type=='specific') {
				 $sql.=" AND (DATE(dt.request_date)=".$db->qstr(date('Y-m-d', strtotime($date_specific))).") ";
		} else if($date_type=='between') {
				 $sql.=" AND (DATE(dt.request_date) BETWEEN ".$db->qstr(date('Y-m-d',strtotime($date_between1)))." AND ".$db->qstr(date('Y-m-d',strtotime($date_between2))).") \n";
		}
	break;
	case 'both':
		if($pid){
				$sql.=" AND dt.pid='$pid' \n";
			}
			if($encounter_nr) {
				$sql.=" AND dt.encounter_nr='$encounter_nr' \n";
			}
			if($name) {
				if(strpos($name,',')!==FALSE){
					$split_name = explode(',', $name);
					$sql.= " AND (cp.name_last LIKE ".$db->qstr(trim($split_name[0])."%").
					" AND cp.name_first LIKE ".$db->qstr(trim($split_name[1])."%").") \n";
				}else {
					$sql.= " AND (cp.name_last LIKE '$name%' OR cp.name_first LIKE '$name%') \n";
				}
			}
			/*if($status) {
				$sql.=" AND dt.status='".strtoupper($status)."' ";
			}*/
			if($date_type=='today') {
					$sql.= " AND (DATE(dt.request_date)=DATE(NOW() )) \n";
			} else if($date_type=='week') {
					$sql.=" AND (YEAR(dt.request_date)=YEAR(NOW()) AND WEEK(dt.request_date)=WEEK(NOW()))  \n";
			} else if($date_type=='month') {
					$sql.=" AND (YEAR(dt.request_date)=YEAR(NOW()) AND MONTH(dt.request_date)=MONTH(NOW()))  \n";
			} else if($date_type=='specific') {
					 $sql.=" AND (DATE(dt.request_date)=".$db->qstr(date('Y-m-d', strtotime($date_specific))).")  \n";
			} else if($date_type=='between') {
					 $sql.=" AND (DATE(dt.request_date) BETWEEN ".$db->qstr(date('Y-m-d',strtotime($date_between1)))." AND ".$db->qstr(date('Y-m-d',strtotime($date_between2))).") \n";
			}
			break;
			default: $sql.= " AND (DATE(dt.request_date)=DATE(NOW() )) \n"; break;
}

if($sort_sql) {
	$sql.="\n ORDER BY {$sort_sql} ";
}
if($maxRows) {
	$sql.="\n LIMIT $offset, $maxRows";
}
$result = $db->Execute($sql);


/*echo "<pre>";
print_r($sql);
echo "</pre>";*/

if ($result !== FALSE) {
	$total = $db->GetOne("SELECT FOUND_ROWS()");
	$dialysis_obj = new SegDialysis();
	while ($row = $result->FetchRow()) {

		$is_disabled = 0;
		if(strtolower($row["request_flag"])=="paid" || strtolower($row["request_flag"])=="lingap" || strtolower($row["request_flag"])=="cmap") {
			$is_disabled=1;
		} else {
			if($row["is_cash"]==0) {
				$charges_exists = $dialysis_obj->requestChecker($row["encounter_nr"], $row["refno"]);

				if($charges_exists!==FALSE)
					$is_disabled = 1;
			}
		}

		$req_btn = '<button class="segButton" '.($row["request_flag"]==NULL && $row["is_cash"]==1? 'disabled="disabled" onclick="return false;"':'style="cursor: pointer;" onclick="openRequestTray(\''.$row["encounter_nr"].'\',\''.$row["pid"].'\');return false;"').' title="Create charges"><img src="../../gui/img/common/default/package_add.png"/>Charges</button>';
		$delete_btn = '<button class="segButton" '.($is_disabled==1? 'disabled="disabled"onclick="return false;"':'style="cursor: pointer;" onclick="deleteRequest(\''.$row["encounter_nr"].'\',\''.$row["pid"].'\',\''.$row["refno"].'\');return false;"').' title="Delete dialysis request"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
		$details_btn = '<button class="segButton" '.($row["request_flag"]==NULL && $row["is_cash"]==1? 'disabled="disabled" onclick="return false;"':'style="cursor: pointer;" onclick="openDetailsTray(\''.$row["encounter_nr"].'\',\''.$row["pid"].'\',\''.$row["refno"].'\');return false;"').'  title="Update dialysis request"><img src="../../gui/img/common/default/page_go.png"/>Details</button>';
		//$billing_btn = '<button class="segButton" '.($is_disabled==1 ? 'disabled="disabled" onclick="return false;"':'onclick="openBillingTray(\''.$row["encounter_nr"].'\',\''.$row["pid"].'\');return false;"').'><img src="../../gui/img/common/default/calculator.png" '.($is_disabled==1 ? 'style="opacity:0.5;" disabled=""':'').'/>Bill</button>';

		$data[] = array(
			'refno' => $row["refno"],
			'patient_name' => strtoupper($row["patient_name"]),
			'patient_id' => $row["pid"],
			'date_visited' => date("d-M-Y h:ia", strtotime($row["request_date"])),
			'request' => $row["request_type"],
			'session' => $row["session_type"],
			'iscash' => strtoupper($row["is_cash"]==1 ?  ($row["request_flag"]!=NULL ? $row["request_flag"] : 'cash') :'charge'),
			'options' => $details_btn."".$req_btn."".$delete_btn
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