<?php
#created by Genz
require_once('roots.php');
require_once($root_path.'include/inc_jasperReporting.php');

require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
require_once $root_path.'include/inc_environment_global.php';
require_once $root_path.'include/care_api_classes/class_insurance.php';
require_once $root_path.'include/care_api_classes/class_personell.php';

global $db;

$from = date('Y-m-d', strtotime($_GET['from']));
$to = date('Y-m-d', strtotime($_GET['to']));
$var_senior = null;
$excess=0.00;
if ($_GET['report'] == 'claim_paid_dr'){
 	$date_span = date('M d,Y', strtotime($_GET['from'])).' - '.date('M d,Y', strtotime($_GET['to']));
}

$objInfo = new Hospital_Admin();
$objInsurance = new Insurance();
$objPersonell = new Personell();
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
$dr_nr = $_GET['dr_nr'];
$personnel = $_GET['personnel'];
$senior = $_GET['is_senior'];
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

if($dr_nr){
	$where .= " AND h.dr_nr = ".$db->qstr($dr_nr);
	$dr_res = $objPersonell->get_Person_name($dr_nr);
	$dr_name = "DR. ".$dr_res['dr_name'];
}

if($report_type=='claim_paid_dr'){
	$date_condition = "DATE(p.post_dte) BETWEEN DATE(".$db->qstr($from).") AND DATE(".$db->qstr($to).")";
}


if($personnel == 'all'){
	$personnel_condition = '';
}else{
	$personnel_condition = "fb.create_id = '".$db->qstr($personnel)."' AND";
}

switch($senior){
    case 'senior':
        $srWhere=" AND fn_get_age(DATE(sbe.`bill_dte`), DATE(cp.`date_birth`)) >= 60";
        $var_senior="All Seniors";
    break;
    case 'non-senior':
        $srWhere=" AND fn_get_age(DATE(sbe.`bill_dte`), DATE(cp.`date_birth`)) < 60";
        $var_senior="Non-Seniors";
    break;
    default:
        $srWhere="";
        $var_senior="Seniors & Non-Seniors";
    break;
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
			      '%b %e, %Y'
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
			sbp.dr_claim AS claim,
	  		spe.amount AS excess,
			 (h.`dr_pay`) AS amount,
			 h.`tax_wheld` 
			FROM
			  seg_claim_pay_pf h 
			  LEFT JOIN care_encounter ce 
			    ON ce.`encounter_nr` = h.`encounter_nr` 
			  LEFT JOIN seg_claim_posting p 
			    ON p.`ref_no` = h.`ref_no` 
			  LEFT JOIN care_person cp 
    			ON cp.`pid` = ce.`pid` 
			  LEFT JOIN seg_billing_encounter sbe 
			    ON (
			      sbe.`encounter_nr` = h.`encounter_nr` 
			      AND sbe.`is_deleted` IS NULL 
			      
			    ) 
			LEFT JOIN seg_billing_pf sbp 
		    ON (
		      sbp.`dr_nr` = h.`dr_nr` 
		      AND sbp.`bill_nr` = sbe.`bill_nr` 
		      AND sbp.`hcare_id` = p.`hcare_id`
		    ) 
		  LEFT JOIN seg_pf_excess spe 
		    ON spe.`dr_nr` = h.`dr_nr` 
		    AND spe.`encounter_nr` = sbe.`encounter_nr` 
		    WHERE 
			$date_condition $where $srWhere 
			AND sbp.dr_claim > 0
			ORDER BY NAME ASC ";


$rs = $db->Execute($query);
if($rs){
	if($rs->RecordCount()>0){
		$i = 0;
		while($row = $rs->FetchRow()){
		
		if($insurance_name=="PhilHealth")
				{
					$excess = $row['excess'];
				}else{
					$excess = $row['excess'];

				}
					$data[$i] = array('name'=>ucwords($row['name']),
						              'confinement'=>$row['confine_period'],
						              "excess"=>number_format($excess,2),
						              'amount'=>number_format($row['excess']+$row['claim'],2),
						              "no"=>($i+1),
						              "claim"=>number_format($row['claim'],2),
						           
						             );

					$totals['amount'] += $row['claim']+$row['excess'];
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
	            "total_amount"=>number_format($totals['amount'],2),
	            "insurance_name"=>strtoupper($insurance_name),
	            "dr_name"=>$dr_name,
	            "is_senior"=>$var_senior
	           );

showReport('claims_dr',$params,$data,$_GET['reportFormat']);
?>