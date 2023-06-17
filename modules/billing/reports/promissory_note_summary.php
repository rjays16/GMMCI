<?php
//created by Maimai 11/25/2014

require('./roots.php');
include_once($root_path . 'include/care_api_classes/reports/JasperReport.php');
require_once($root_path.'include/care_api_classes/class_promissory_note.php');
require_once($root_path.'include/care_api_classes/class_personell.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/inc_environment_global.php');

global $db;

$jasper = new JasperReport();
$objPromi = new Promissory_note();
$objPersonell = new Personell();
$objInfo = new Hospital_Admin();

$date_from = date('Y-m-d', strtotime($_GET['date_from']));
$date_to = date('Y-m-d', strtotime($_GET['date_to']));

$prepared_by = $_SESSION['sess_temp_userid'];
$prepared_by_position = 'Billing Clerk';

$result = $objPromi->getSummary($date_from, $date_to);

$total_due = 0;
$total_balance = 0;
$total_paid_amount = 0;
$total_final_bill = 0;
$no = 1;

$data[0]['name'] = "No results Found..";

if($result){
	$i=0;
	while ($rows = $result->FetchRow()){

		$balance = $rows['total_bill'] - $rows['total_payment'];

		$data[$i] = array('no'=>$no,
							'due_date'=>date("m/d/y", strtotime($rows['due_date'])),
							'name'=>$rows['pname'],
							'amount_due'=>number_format($rows['amount'], 2),
							'balance'=> ($balance >0 ? number_format($balance,2) : 'PAID'),
							'remarks'=>$rows['remarks'],
							'encounter_nr'=>$rows['encounter_nr'],
							'paid_amount'=> $rows['total_payment'] ? number_format($rows['total_payment'], 2) : 0,
							'final_bill'=>number_format($rows['total_bill'], 2));		
		$i++;
		$no++;

		$total_due += $rows['amount'];
		$total_balance += $balance;
		$total_paid_amount += $rows['total_payment'];
		$total_final_bill += $rows['total_bill'];
	}
}


if ($row = $objInfo->getAllHospitalInfo()) {
			$row['hosp_add'] = strtoupper($row['hosp_addr1']);
			$row['hosp_name']   = strtoupper($row['hosp_name']);
}else{
	$row['hosp_name'] = "GONZALES MARANAN MEDICAL CENTER, INC.";
	$row['hosp_name'] = "QUEZON AVE. ZONE 1, DIGOS CITY, DAVAO DEL SUR";
}


$jasper->setParams(array(
	'hosp_name' => $row['hosp_name'],
	'hosp_add' => $row['hosp_add'],
	'dates'=> "(".date("M d, Y", strtotime($_GET['date_from']))." to ".date("M d, Y", strtotime($_GET['date_to'])).")",
	'total_due'=> number_format($total_due, 2),
	'total_balance'=> number_format($total_balance, 2),
	'total_paid_amount' => number_format($total_paid_amount, 2),
	'total_final_bill' => number_format($total_final_bill, 2)
));

$jasper->setData($data);

$jasper->setJrxmlFilePath('pn_summary.jrxml');
$jasper->run();



