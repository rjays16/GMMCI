<?php
require_once('roots.php');
require_once($root_path.'include/inc_jasperReporting.php');
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
require_once $root_path.'include/care_api_classes/billing/class_transmittal.php';
require_once $root_path.'include/inc_environment_global.php';

$from = date('Y-m-d', strtotime($_GET['fromdte']));
$to = date('Y-m-d', strtotime($_GET['todte']));
$date = $_GET['date'];
$firm_id = $_GET['firm_id'];
$ptype = $_GET['ptype'];
$searchby = $_GET['by'];
$keyword = $_GET['keyword'];

$objInfo = new Hospital_Admin();
$objTransmittal = new Transmittal();

if ($row1 = $objInfo->getAllHospitalInfo()) {
	$row1['hosp_name']   = strtoupper($row1['hosp_name']);
	$row1['hosp_addr']   = strtoupper($row1['hosp_addr1']);
}
else {
	$row1['hosp_name']    = "Gonzales Maranan Medical Center Incorporated";
	$row1['hosp_addr']   = "Quezon Ave., Digos City, Davao del Sur";
}
#--------------------------------------------------------------------------------------

$date_span =  date('M d, Y', strtotime($_GET['from'])).' to '.date('M d, Y', strtotime($_GET['from']));

$totals = array("claim"=>0);

$rs = $objTransmittal->getTransmittals($from, $to, $date, $firm_id, $ptype, $searchby, $keyword);

if($rs){
	if($rs->RecordCount()>0){
		$i = 0;
		while($row = $rs->FetchRow()){
			$data[$i] = array('trans_date'=>$row['transmit_dte'],
							  'trans_no'=>$row['transmit_no'],
							  'insurance'=>$row['firm_id'],
							  'patient'=>$row['patient'],
							  'encounter_no'=>$row['encounter_nr'],
							  'confinement'=>$row['confine_period'],
							  'claim'=>number_format($row['claim'],2),
							  'no'=>($i+1)
				             );

			$totals['claim'] += $row['claim'];
			$i++;
		}
	}else{
		$data['patient'][0] = "No data";
	}
}else{
	$data['patient'][0] = "No data";
}

$params = array("hosp_name"=>$row1['hosp_name'],
	            "hosp_addr"=>$row1['hosp_addr1'],
	            "date_range"=>$date_span,
	            "total_claim"=>number_format($totals['claim'],2),
	            "rep_title"=>"TRANSMITTAL SUMMARY"
	           );

showReport('transmittalRep',$params,$data,$_GET['reportFormat']);
?>