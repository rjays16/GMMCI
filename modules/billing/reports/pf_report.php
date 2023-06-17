<?php
require_once('roots.php');
require_once($root_path.'include/inc_jasperReporting.php');
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
require_once $root_path.'include/care_api_classes/billing/class_billing_new.php';
require_once $root_path.'include/care_api_classes/class_personell.php';
require_once $root_path.'include/inc_environment_global.php';

define('REPORT_NAME','( PF REPORT )');

$from = date('Y-m-d', strtotime($_GET['from']));
$to = date('Y-m-d', strtotime($_GET['to']));

$objInfo = new Hospital_Admin();
$objPersonell = new Personell();
$objBilling = new Billing();

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

$date_span =  date('M d, Y', strtotime($_GET['from'])).' to '.date('M d, Y', strtotime($_GET['to']));

if($dr_nr){
	$where .= " AND h.dr_nr = ".$db->qstr($dr_nr);
	$dr_res = $objPersonell->get_Person_name($dr_nr);
	$dr_name = "DR. ".$dr_res['dr_name'];
}

$totals = array("charge"=>0, "phic"=>0, "hmo"=>0, "senior"=>0, "discount"=>0, "excess"=>0, "payment"=>0);

$rs = $objBilling->getDrPf($dr_nr, $from, $to, $is_senior);

$count = $rs->RecordCount();

if($rs){
	if($rs->RecordCount()>0){
		$i = 0;
		while($row = $rs->FetchRow()){
			$excess = $row['dr_charge']+$row['dr_charge_add'] - ($row['phic']+$row['hmo']+$row['senior']+$row['discount']);
			$data[$i] = array('patient'=>ucwords($row['patient']),
				              'confinement'=>$row['confinement'],
				              'actual_charge'=>number_format($row['dr_charge']+$row['dr_charge_add'],2),
				              'phic'=>number_format($row['phic'],2),
				              'hmo'=>number_format($row['hmo'],2),
				              'senior'=>number_format($row['senior'],2),
				              'discount'=>number_format($row['discount'],2),
				              'excess'=>number_format($excess,2),
				              'no'=>($i+1),
				              'payment'=>number_format($row['amount'],2),
				              'insurance'=>$row['insurance'],
				              'or_no'=>$row['or_no']
				             );

			$totals['charge'] += $row['dr_charge']+$row['dr_charge_add'];
			$totals['phic'] += $row['phic'];
			$totals['hmo'] += $row['hmo'];
			$totals['senior'] += $row['senior'];
			$totals['discount'] += $row['discount'];
			$totals['excess'] += $excess;
			$totals['payment'] += $row['amount'];
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
	            "dr_name"=>$dr_name,
	            "report_name"=>REPORT_NAME,
	            "total_charge"=>number_format($totals['charge'],2),
	            "total_phic"=>number_format($totals['phic'],2),
	            "total_hmo"=>number_format($totals['hmo'],2),
	            "total_senior"=>number_format($totals['senior'],2),
	            "total_discount"=>number_format($totals['discount'],2),
	            "total_excess"=>number_format($totals['excess'],2),
	            "total_payment"=>number_format($totals['payment'], 2),
	            "TOTAL"=>'Total Patient '.$count
	           );

showReport('pfReport2',$params,$data,$_GET['reportFormat']);