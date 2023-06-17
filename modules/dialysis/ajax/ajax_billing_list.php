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

$check = $_REQUEST["check"];
$pid = $_REQUEST["pid"];
$encounter_nr = $_REQUEST["encounter_nr"];
$name = $_REQUEST["name"];
$date = $_REQUEST["date"];
$date_spec = date('Y-m-d',strtotime($_REQUEST["date_spec"]));
$date_from = date('Y-m-d',strtotime($_REQUEST["date_from"]));
$date_to = date('Y-m-d',strtotime($_REQUEST["date_to"]));

$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';
$sortMap = array(
	'bill_date' => 'bill_dte'
);

$sortName = $_REQUEST['sort'];
if (!$sortName || !array_key_exists($sortName, $sortMap))
	$sortName = 'bill_date';

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


$sql = "SELECT SQL_CALC_FOUND_ROWS b.bill_nr, b.bill_dte, fn_get_person_name(d.pid) AS `name`, \n".
			"b.encounter_nr, fn_compute_bill(b.bill_nr) AS `bill_amount` \n".
			"FROM seg_billing_encounter AS b \n".
			"LEFT JOIN seg_dialysis_transaction AS d ON b.encounter_nr=d.encounter_nr \n".
			"INNER JOIN care_encounter AS e ON d.encounter_nr=e.encounter_nr \n".
			"INNER JOIN care_person AS p ON d.pid=p.pid \n".
			"WHERE e.encounter_type='5' ";
switch($check)
{
	case 'patient':
		 if($pid){
				$sql.=" AND d.pid='$pid' ";
			}
			if($encounter_nr) {
				$sql.=" AND d.encounter_nr='$encounter_nr' ";
			}
			if($name) {
				if(strpos($name,',')!==FALSE){
					$split_name = explode(',', $name);
					$sql.= " AND (p.name_last LIKE ".$db->qstr(trim($split_name[0])."%").
					" AND p.name_first LIKE ".$db->qstr(trim($split_name[1])."%").") ";
				}else {
					$sql.= " AND (p.name_last LIKE '$name%' OR p.name_first LIKE '$name%') ";
				}
			}
		 break;
	case 'date':
		if($date=="today") {
			$sql.=" AND (DATE(b.bill_dte)=DATE(NOW())) ";
		}
		if($date=="week") {
			$sql.=" AND (YEAR(b.bill_dte)=YEAR(NOW()) AND WEEK(b.bill_dte)=WEEK(NOW())) ";
		}
		if($date=="month") {
			$sql.=" AND (YEAR(b.bill_dte)=YEAR(NOW()) AND MONTH(b.bill_dte)=MONTH(NOW())) ";
		}
		if($date=="specific") {
			$sql.=" AND (DATE(b.bill_dte)=".$db->qstr($date_spec).") ";
		}
		if($date=="between") {
			$sql.=" AND (b.bill_dte BETWEEN ".$db->qstr($date_from)." AND ".$db->qstr($date_to).") ";
		}
		break;
	case 'both':
		if($pid){
				$sql.=" dt.pid='$pid' AND ";
			}
			if($encounter_nr) {
				$sql.=" dt.encounter_nr='$encounter_nr' AND ";
			}
			if($name) {
				if(strpos($name,',')!==FALSE){
					$split_name = explode(',', $name);
					$sql.= " (cp.name_last LIKE ".$db->qstr(trim($split_name[0])."%").
					" AND cp.name_first LIKE ".$db->qstr(trim($split_name[1])."%").") AND ";
				}else {
					$sql.= " (cp.name_last LIKE '$name%' OR cp.name_first LIKE '$name%') AND ";
				}
			}
			if($date=="today") {
			$sql.=" AND (DATE(b.bill_dte)=DATE(NOW())) ";
			}
			if($date=="week") {
				$sql.=" AND (YEAR(b.bill_dte)=YEAR(NOW()) AND WEEK(b.bill_dte)=WEEK(NOW())) ";
			}
			if($date=="month") {
				$sql.=" AND (YEAR(b.bill_dte)=YEAR(NOW()) AND MONTH(b.bill_dte)=MONTH(NOW())) ";
			}
			if($date=="specific") {
				$sql.=" AND (DATE(b.bill_dte)=".$db->qstr($date_spec).") ";
			}
			if($date=="between") {
				$sql.=" AND (b.bill_dte BETWEEN ".$db->qstr($date_from)." AND ".$db->qstr($date_to).") ";
			}
			break;
}

if($sort_sql) {
	$sql.=" ORDER BY {$sort_sql} ";
}
if($maxRows) {
	$sql.=" LIMIT $offset, $maxRows";
}

$result = $db->Execute($sql);

//echo $sql;
/*
echo "<pre>";
print_r($_REQUEST);
echo "</pre>";*/

if ($result !== FALSE) {
	$total = $db->GetOne("SELECT FOUND_ROWS()");

	while ($row = $result->FetchRow()) {

		$details_btn = '<a href="../../modules/billing/billing-main.php?ntid=false&amp;lang=en&amp;userck=&amp;nr='.$row["bill_nr"].'&amp;from=billing-list" title="View"><img border="0" align="absmiddle" src="../../images/cashier_edit.gif" class="segSimulatedLink"></a>';
		$delete_btn = '<img border="0" align="absmiddle" onclick="if (confirm(\'Delete this billing?\')) deleteItem('.$row["bill_nr"].')" src="../../images/cashier_delete.gif" class="segSimulatedLink">';

		$data[] = array(
			'bill_date'=>date('M-d-Y h:i a', strtotime($row['bill_dte'])),
			'patient_name'=>$row['name'],
			'patient_enc'=>$row['encounter_nr'],
			'bill_amount'=>number_format($row['bill_amount'],2),
			'options' => "&nbsp;".$details_btn."&nbsp;".$delete_btn
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