<?php
require_once('roots.php');
require_once($root_path.'include/inc_jasperReporting.php');
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
require_once $root_path.'include/care_api_classes/class_personell.php';
require_once ($root_path.'include/care_api_classes/class_pf.php');
require_once $root_path.'include/inc_environment_global.php';

define('REPORT_NAME','( PF EXCESS REPORT )');

$from = date('Y-m-d', strtotime($_GET['from']));
$to = date('Y-m-d', strtotime($_GET['to']));

$objInfo = new Hospital_Admin();
$objPersonell = new Personell();
$objPf = new Pf();

if ($row1 = $objInfo->getAllHospitalInfo()) {
	$row1['hosp_name']   = strtoupper($row1['hosp_name']);
	$row1['hosp_addr']   = strtoupper($row1['hosp_addr1']);
}
else {
	$row1['hosp_name']    = "Gonzales Maranan Medical Center Incorporated";
	$row1['hosp_addr']   = "Quezon Ave., Digos City, Davao del Sur";
}
#--------------------------------------------------------------------------------------

$dr_nr = $_GET['dr_nr'];
$is_senior = $_GET['is_senior'];
$PrintType = $_GET['type'];


$date_span =  date('M d, Y', strtotime($_GET['from'])).' to '.date('M d, Y', strtotime($_GET['to']));

$total = 0;
$count = 0;
if($PrintType == 1){
$rs = $objPf->getPfExcess($dr_nr, $from, $to, $is_senior);
}else{
$rs = $objPf->getMonthlyPfExcess($dr_nr, $from, $to, $is_senior);
}
if($rs){
	$count = $rs->RecordCount();
	if($rs->RecordCount()>0){
		$i = 0;
		while($row = $rs->FetchRow()){
			$data[$i] = array('name'=>ucwords($row['name']),
				              'date'=>date('F j, Y', strtotime($row['create_dt'])),
				              'confinement'=>$row['confine_period'],
				              'no'=>($i+1),
				              'amount'=>number_format($row['amount'],2),
				              'vat'=>number_format($row['amount']*.12,2),
				              'tax'=>number_format($row['amount']*.15,2),
				              'inc_vat'=>number_format($row['amount']-($row['amount']*.12),2),
				              'inc_tax'=>number_format($row['amount']-($row['amount']*.15),2),
				              'doctor'=>ucwords($row['doctor'])
				             );

			$total += $row['amount'];
			$total_vat += $row['amount']*.12;
			$total_tax += $row['amount']*.15;
			$total_inc_vat += $row['amount']-($row['amount']*.12);
			$total_inc_tax += $row['amount']-($row['amount']*.15);
			$i++;
		}
	}else{
		$data['patient'][0] = "No data";
	}
}else{
	$data['patient'][0] = "No data";
}

$params = array("hosp_name"=>$row1['hosp_name'],
	            "hosp_addr1"=>$row1['hosp_addr1'],
	            "date_span"=>$date_span,
	            "report_name"=>REPORT_NAME,
	            "total_amount"=>number_format($total,2),
	            "total_vat"=>number_format($total_vat,2),
	            "total_tax"=>number_format($total_tax,2),
	            "total_inc_vat"=>number_format($total_inc_vat,2),
	            "total_inc_tax"=>number_format($total_inc_tax,2),
	            "is_senior"=>$is_senior == 'senior' ? 'Seniors' : ($is_senior == 'non-senior' ? 'Non-Seniors' : 'Seniors & Non-Seniors'),
	            "total"=>'Total Patient '.$count
	           );

showReport('pf_excess',$params,$data,$_GET['reportFormat']);