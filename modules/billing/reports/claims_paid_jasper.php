<?php
#created by Genz
require_once('roots.php');
require_once($root_path.'include/inc_jasperReporting.php');

require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
require_once $root_path.'include/inc_environment_global.php';
require_once $root_path.'include/care_api_classes/class_insurance.php';

global $db;

$from = date('Y-m-d', strtotime($_GET['from']));
$to = date('Y-m-d', strtotime($_GET['to']));

if ($_GET['report'] == 'claim_paid'){
 	$date_span = date('M d,Y', strtotime($_GET['from'])).' - '.date('M d,Y', strtotime($_GET['to']));
}

$objInfo = new Hospital_Admin();
$objInsurance = new Insurance();
if ($row1 = $objInfo->getAllHospitalInfo()) {
	$row1['hosp_agency'] = strtoupper($row1['hosp_agency']);
	$row1['hosp_name']   = strtoupper($row1['hosp_name']);
}
else {
	$row1['hosp_agency']  = "DEPARTMENT OF HEALTH";
	$row1['hosp_name']    = "Gonzales Maranan Medical Center Incorporated";
	$row1['hosp_addr1']   = "Quezon Ave., Digos City, Davao del Sur";
}
#--------------------------------------------------------------------------------------

$report_type = $_GET['report'];
$insurance_nr = strtolower($_GET['dtype']);
$personnel = $_GET['personnel'];
$totals = array("claim"=>0, "gross"=>0, "tax"=>0, "doctor"=>0, "net"=>0, "income"=>0);

$header_dtype = "List of Paid Claims";
$where = '';
if($insurance_nr == 'all'){
	$insurance_name = "ALL INSURANCE";
}else if($insurance_nr == 'non-phic'){
	$where .=" AND p.hcare_id <> ".$db->qstr(PHIC_ID);
	$insurance_name = "NON-PHIC";
}else{
	$where .= " AND p.hcare_id = ".$db->qstr($insurance_nr);
	$insurance_info = $objInsurance->getInsuranceInfo($insurance_nr);
	$insurance_name = $insurance_info['firm_id'];
}

if($report_type=='claim_paid'){
	$date_condition = "DATE(encounter_date) BETWEEN DATE(".$db->qstr($from).") AND DATE(".$db->qstr($to).")";
}


if($personnel == 'all'){
	$personnel_condition = '';
}else{
	$personnel_condition = "fb.create_id = '".$db->qstr($personnel)."' AND";
}


$query = "SELECT 
			  h.`ref_no`,
			  h.`encounter_nr`,
			  sbe.`bill_nr`,
			  fn_get_person_lastname_first (ce.`pid`) AS name,
			  CONCAT(
			    DATE_FORMAT(
			      (
			        CASE
			          WHEN admission_dt IS NULL 
			          OR admission_dt = '' 
			          THEN encounter_date 
			          ELSE admission_dt 
			        END
			      ),
			      '%b %e,%Y'
			    ),
			    ' to ',
			    (
			      CASE
			        WHEN ce.discharge_date IS NULL 
			        OR ce.discharge_date = '' 
			        THEN 'present' 
			        ELSE DATE_FORMAT(
			          STR_TO_DATE(
			            ce.discharge_date,
			            '%Y-%m-%d'
			          ),
			          '%b %e, %Y'
			        ) 
			      END
			    )
			  ) AS confine_period,
			  (
			    h.`acc_pay` + h.`med_pay` + h.`msc_pay` + h.`ops_pay` + h.`sup_pay` + h.`srv_pay`
			  ) hosp_pay,
			  (SELECT 
			    total_acc_coverage + total_med_coverage + total_sup_coverage + total_srv_coverage + total_ops_coverage + total_msc_coverage 
			  FROM
			    seg_billing_coverage 
			  WHERE bill_nr = sbe.bill_nr 
			    AND hcare_id = p.hcare_id) AS hosp_claim,
			  (SELECT 
			    SUM(dr_pay) 
			  FROM
			    seg_claim_pay_pf 
			  WHERE encounter_nr = h.`encounter_nr`) AS dr_pay,
			  (SELECT 
			    SUM(dr_claim) 
			  FROM
			    seg_billing_pf 
			  WHERE bill_nr = sbe.`bill_nr` 
			    AND hcare_id = p.`hcare_id`) dr_claim,
				h.`tax_wheld` AS hosp_tax,
			  (SELECT 
			    SUM(tax_wheld) 
			  FROM
			    seg_claim_pay_pf 
			  WHERE encounter_nr = h.encounter_nr) AS dr_tax,
			  (SELECT 
			    SUM(amount) 
			  FROM
			    seg_billing_caserate 
			  WHERE bill_nr = sbe.bill_nr ) AS gross_amt
			FROM
			  seg_claim_pay_hosp h 
			  LEFT JOIN care_encounter ce 
			    ON ce.`encounter_nr` = h.`encounter_nr` 
			  LEFT JOIN seg_claim_posting p 
			    ON p.`ref_no` = h.`ref_no` 
			  LEFT JOIN seg_billing_encounter sbe 
			    ON (
				      sbe.`encounter_nr` = h.`encounter_nr` 
			      AND sbe.`is_deleted` IS NULL 
			)	WHERE 
			$date_condition $where 
			ORDER BY encounter_date ";
// echo $query; exit();
$count = 0;
$rs = $db->Execute($query);
if($rs){
	$count = $rs->RecordCount();
	if($rs->RecordCount()>0){
		$i = 0;
		while($row = $rs->FetchRow()){
			
			$total_claim = $row['hosp_claim'] + $row['dr_claim'];
			$total_pay = $row['hosp_pay'] + $row['dr_claim'];
			$total_tax = ($row['gross_amt'] * 0.02);
			// $total_net = $total_pay - ($total_tax + $row['dr_claim']);
			$total_net = $row['hosp_pay'];
			$total_income = $row['gross_amt']-$total_claim;

			$data[$i] = array('case_no'=>$row['encounter_nr'],
				              'trans_no'=>$row['transmit_no'],
				              'patient_name'=>ucwords($row['name']),
				              'hospital_bill'=>(double)$hosp_bill,
				              'confine_period'=>$row['confine_period'],
				              'claim'=>number_format($total_claim,2),
				              'gross'=>number_format($row['gross_amt'],2),
				              'tax'=>number_format($total_tax,2),
				              'dr_pay'=>number_format($row['dr_claim'],2),
				              'net'=>number_format($total_net,2),
				              'income'=>number_format($total_income,2),
				              "no"=>($i+1)
				             );

			$totals['claim'] += $total_claim;
			$totals['gross'] += $row['gross_amt'];
			$totals['tax'] += $total_tax;
			$totals['doctor'] += $row['dr_claim'];
			$totals['net'] += $total_net;
			$totals['income'] += $total_income;
			$i++;
		}
	}else{
		$data['ref_no'][0] = "No data";
	}
}else{
	$data['ref_no'][0] = "No data";
}

$params = array("hosp_country"=>$row1['hosp_country'],
	            "hosp_agency"=>$row1['hosp_agency'],
	            "hosp_name"=>$row1['hosp_name'],
	            "hosp_addr1"=>$row1['hosp_addr1'],
	            "date_span"=>$date_span,
	            "insurance_type"=>$header_dtype,
	            "total_claim"=>number_format($totals['claim'],2),
	            "total_gross"=>number_format($totals['gross'],2),
	            "total_tax"=>number_format($totals['tax'],2),
	            "total_doctor"=>number_format($totals['doctor'],2),
	            "total_net"=>number_format($totals['net'],2),
	            "total_income"=>number_format($totals['income'],2),
	            "insurance_name"=>strtoupper($insurance_name),
	            "total"=>'Total Patient '.$count
	           );

showReport('claims_hosp',$params,$data,$_GET['reportFormat']);
?>