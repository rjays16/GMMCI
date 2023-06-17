<?php
require_once('roots.php');
require_once($root_path.'include/inc_jasperReporting.php');
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
require_once $root_path.'include/inc_environment_global.php';

$objInfo = new Hospital_Admin();
define('PHIC_ID', 18);
global $db;

//hospital info
if ($row1 = $objInfo->getAllHospitalInfo()) {
	$row1['hosp_name']   = strtoupper($row1['hosp_name']);
	$row1['hosp_addr']   = strtoupper($row1['hosp_addr1']);
}
else {
	$row1['hosp_name']    = "Gonzales Maranan Medical Center Incorporated";
	$row1['hosp_addr']   = "Quezon Ave., Digos City, Davao del Sur";
}

//get
$report = $_GET['status'];
$from_dte = date('Y-m-d', strtotime($_GET['fromdte']));
$to_dte = date('Y-m-d', strtotime($_GET['todte']));
$date_span =  date('M d, Y', strtotime($_GET['fromdte'])).' to '.date('M d, Y', strtotime($_GET['todte']));
$hcare_id = $_GET['hcare_id'];

//report
switch($report){
	case 'denied':
		$from_tbl = 'seg_claim_denied';
		break;
	case 'returned':
		$from_tbl = 'seg_claim_returned';
		break;
}

//hcare_id
switch($hcare_id){
	case 'non-phic':
		$where = ' AND scp.hcare_id <> '.PHIC_ID;
		break;
	case 'all':
		$where = '';
		break;
	default:
		$where = ' AND scp.hcare_id = '.$hcare_id;

}

//fetch
$sql = "SELECT 
			  cif.`firm_id`,
			  scp.`ref_no`,
			  fn_get_person_lastname_first (ce.`pid`) patient,
			  DATE_FORMAT(
			    (
			      CASE
			        WHEN ce.admission_dt IS NULL 
			        OR admission_dt = '' 
			        THEN ce.encounter_date 
			        ELSE ce.admission_dt 
			      END
			    ),
			    '%b %e, %Y %l:%i%p'
			  ) AS date_admission,
			  DATE_FORMAT(
			    STR_TO_DATE(
			      CONCAT(
			        ce.`discharge_date`,
			        ' ',
			        ce.`discharge_time`
			      ),
			      '%Y-%m-%d %H:%i:%s'
			    ),
			    '%b %e, %Y %l:%i%p'
			  ) AS date_discharge,
			  (
			    sbc.`total_acc_coverage` + sbc.`total_med_coverage` + sbc.`total_srv_coverage` + sbc.`total_msc_coverage` + sbc.`total_ops_coverage`
			  ) hci,
			  (
			    sbc.`total_d1_coverage` + sbc.`total_d2_coverage` + sbc.`total_d3_coverage` + sbc.`total_d4_coverage`
			  ) pf,
			  IFNULL(
			    IFNULL(
			      (SELECT 
			        GROUP_CONCAT(description) 
			      FROM
			        seg_encounter_diagnosis sed 
			      WHERE sed.encounter_nr = scd.`encounter_nr` 
			        AND sed.is_deleted <> 1 
			        AND CODE IN 
			        (SELECT 
			          package_id 
			        FROM
			          seg_billing_caserate 
			        WHERE bill_nr = sbe.`bill_nr`)),
			      (SELECT 
			        GROUP_CONCAT(description) 
			      FROM
			        seg_misc_ops_details sed 
			        LEFT JOIN seg_misc_ops smo 
			          ON smo.refno = sed.refno 
			      WHERE smo.encounter_nr = scd.`encounter_nr` 
			        AND ops_code IN 
			        (SELECT 
			          package_id 
			        FROM
			          seg_billing_caserate 
			        WHERE bill_nr = sbe.`bill_nr`))
			    ),
			    ''
			  ) AS final_diagnosis 
			FROM
			  ".$from_tbl." scd 
			  LEFT JOIN seg_claim_posting scp 
			    ON scp.`ref_no` = scd.`ref_no` 
			  LEFT JOIN care_encounter ce 
			    ON ce.`encounter_nr` = scd.`encounter_nr` 
			  LEFT JOIN seg_billing_encounter sbe 
			    ON (
			      sbe.`encounter_nr` = scd.`encounter_nr` 
			      AND is_final = 1 
			      AND is_deleted IS NULL
			    ) 
			  LEFT JOIN care_insurance_firm cif 
			    ON cif.`hcare_id` = scp.`hcare_id` 
			  LEFT JOIN seg_billing_coverage sbc 
			    ON (
			      sbc.`bill_nr` = sbe.`bill_nr` 
			      AND sbc.`hcare_id` = scp.`hcare_id`
			    ) 
			WHERE scp.post_dte BETWEEN DATE(".$db->qstr($from_dte).") 
			  AND DATE(".$db->qstr($to_dte).") ".$where;

	$result = $db->Execute($sql);

	$i=0;
	$total_pf=0;
	$total_hci=0;

	if($result){
		while($row=$result->FetchRow()){
			$data[$i++] = array('patient'=>$row['patient'],
									'confinement'=>$row['date_admission'].' to '.$row['date_discharge'],
									'hci'=>number_format($row['hci'],2),
									'pf'=>number_format($row['pf'],2),
									'hmo'=>$row['firm_id'],
									'no'=>($i)
									);

			$total_hci += $row['hci'];
			$total_pf += $row['pf'];
		}
	}

	$params = array("hosp_name"=>$row1['hosp_name'],
	            "hosp_add"=>$row1['hosp_addr1'],
	            "date_range"=>$date_span,
	            "report"=>strtoupper($report." claims"),
	            "total_hci"=>number_format($total_hci,2),
	            "total_pf"=>number_format($total_pf, 2)
	           );

	showReport('claims_report',$params,$data,$_GET['reportFormat']);
?>