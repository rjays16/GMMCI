<?php
#created by Nick, 1/30/2014
require_once('roots.php');
require_once($root_path.'include/inc_jasperReporting.php');

require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
require_once $root_path.'include/inc_environment_global.php';

global $db;

if ($_GET['report'] == 'acr_daily'){
	$date_span = date('M d,Y', strtotime($_GET['date']));
}else if($_GET['report'] == 'acr_monthly'){
	$tmp_date = strtotime($_GET['year'].'-'.$_GET['month'].'-01');
	$date_span = date('M',$tmp_date) . " " . date('Y',$tmp_date);
}

$objInfo = new Hospital_Admin();
if ($row = $objInfo->getAllHospitalInfo()) {
	$row['hosp_agency'] = strtoupper($row['hosp_agency']);
	$row['hosp_name']   = strtoupper($row['hosp_name']);
}
else {
	$row['hosp_country'] = "Republic of the Philippines";
	$row['hosp_agency']  = "DEPARTMENT OF HEALTH";
	$row['hosp_name']    = "Gonzales Maranan Medical Center Incorporated";
	$row['hosp_addr1']   = "Quezon Ave., Digos City, Davao del Sur";
}
#--------------------------------------------------------------------------------------

$report_type = $_GET['report'];
$delete_type = $_GET['dtype'];
$personnel = $_GET['personnel'];

if($delete_type=='SA'){
	$delete_condition = '';
	$header_dtype = "All Bills";
}else if($delete_type=='DB'){
	$delete_condition = "fb.is_deleted = '1' AND";
	$header_dtype = "Deleted Bills";
}else /*if($delete_type=='FB')*/{
	$delete_condition = 'fb.is_deleted is NULL AND';
	$header_dtype = "Final Bills";
}

if($report_type=='acr_daily'){
	
	$date_condition = "(fb.bill_dte LIKE " . $db->qstr(date('Y-m-d', strtotime($_GET['date'])).'%')  . ")";

}else /*if($report_type=='monthly_bills_rendered')*/{
	
	$startDate = strtotime($_GET['year'].'-'.$_GET['month'].'-01');
	if ($startDate === false) {
		die('Invalid month/year specified');
	}
	$endDate = strtotime('+1 month', $startDate);
	$date_condition = "DATE(fb.bill_dte) BETWEEN  DATE(".$db->qstr(date('Y-m-d',$startDate)).") AND DATE(".$db->qstr(date('Y-m-d',$endDate)).")";
}



$params = array("hosp_country"=>$row['hosp_country'],
	            "hosp_agency"=>$row['hosp_agency'],
	            "hosp_name"=>$row['hosp_name'],
	            "hosp_addr1"=>$row['hosp_addr1'],
	            "date_span"=>$date_span,
	            "delete_type"=>$header_dtype
	           );

if($personnel == 'all'){
	$personnel_condition = '';
}else{
	$personnel_condition = "fb.create_id = '".$personnel."' AND";
}

$query = "SELECT 
			  fb.bill_nr,
			  fb.bill_dte,
			  fu.name `prepared_by`,
			  fn_get_person_name (e.pid) `patient`,
			  e.encounter_nr,
			  department.id `department`,
			  CONCAT(
			    IF (
			      e.encounter_type IN (3, 4),
			      IF(
			        fb.accommodation_type = 1,
			        'Service',
			        'Pay'
			      ),
			      et.`type`
			    ),
			    IF(
			      (
			        IFNULL(cov.total_acc_coverage, 0) + IFNULL(cov.total_med_coverage, 0) + IFNULL(cov.total_srv_coverage, 0) + 
			        IFNULL(cov.total_ops_coverage, 0) + IFNULL(cov.total_msc_coverage, 0) + IFNULL(cov.total_d1_coverage, 0) + 
			        IFNULL(cov.total_d2_coverage, 0) + IFNULL(cov.total_d3_coverage, 0) + IFNULL(cov.total_d4_coverage, 0) + 
			        IFNULL(cov.total_services_coverage, 0)
			      ) > 0,
			      '/PHIC',
			      ''
			    )
			  ) `type`,
			  (
			    fb.total_acc_charge + fb.total_med_charge + fb.total_srv_charge + 
			    fb.total_ops_charge + fb.total_doc_charge + fb.total_msc_charge
			  ) `total_charge`,
			  (
			    (IFNULL(dsc.total_acc_discount, 0) + IFNULL(dsc.total_med_discount, 0) + IFNULL(dsc.total_ops_discount, 0) + 
			    IFNULL(dsc.total_srv_discount, 0) + IFNULL(dsc.total_msc_discount, 0) + IFNULL(dsc.total_d1_discount, 0) + 
			    IFNULL(dsc.total_d2_discount, 0) + IFNULL(dsc.total_d3_discount, 0) + IFNULL(dsc.total_d4_discount, 0)) + 
			    IFNULL(dsc.`hospital_income_discount`, 0)
			  ) `total_discount`,
			  (
			    IFNULL(cov.total_acc_coverage, 0) + IFNULL(cov.total_med_coverage, 0) + IFNULL(cov.total_srv_coverage, 0) + 
			    IFNULL(cov.total_ops_coverage, 0) + IFNULL(cov.total_msc_coverage, 0) + IFNULL(cov.total_services_coverage, 0)
			  ) `hci`,
			  (
			    IFNULL(cov.total_d1_coverage, 0) + IFNULL(cov.total_d2_coverage, 0) + IFNULL(cov.total_d3_coverage, 0) + 
			    IFNULL(cov.total_d4_coverage, 0)
			  ) `doc_pf`,
			  fb.total_prevpayments `previous_payment`,
			  IFNULL((SELECT sbc.amount FROM seg_billing_caserate sbc WHERE sbc.rate_type = '1' AND sbc.bill_nr = fb.`bill_nr`),0) AS cs_first,
			  IFNULL((SELECT sbc.amount FROM seg_billing_caserate sbc WHERE sbc.rate_type = '2' AND sbc.bill_nr = fb.`bill_nr`),0) AS cs_second,
			  IFNULL((SELECT sbc.package_id FROM seg_billing_caserate sbc WHERE sbc.rate_type = '1' AND sbc.bill_nr = fb.`bill_nr`),' ') AS first_code,
			  IFNULL((SELECT sbc.package_id FROM seg_billing_caserate sbc WHERE sbc.rate_type = '2' AND sbc.bill_nr = fb.`bill_nr` ),' ')AS second_code,
			  `fn_billing_compute_gross_amount`(fb.bill_nr) `excess`,
			  
			  sm.`memcategory_desc` AS phic_category,
			  IF(e.`is_medico` = '1' ,'YES','NO') AS medico_legal,
			  bd.`discountid`,
			  IFNULL(e.`admission_dt`,e.encounter_date)  AS ADMISSION_DATE,
			  CONCAT(e.`discharge_date`,' ',e.`discharge_time`) AS DISCHARGE_DATE,
			  DATEDIFF(DATE(CONCAT(e.`discharge_date`,' ',e.`discharge_time`)),DATE(IFNULL(e.`admission_dt`,e.encounter_date))) AS NUMBER_OF_DAYS


			FROM
			  seg_billing_encounter fb 
			  INNER JOIN care_encounter e 
			    ON e.encounter_nr = fb.encounter_nr 
			  INNER JOIN care_department `department` 
			    ON department.nr = e.current_dept_nr 
			  LEFT JOIN seg_encounter_insurance i 
			    ON i.encounter_nr = e.encounter_nr 
			    AND hcare_id = '18' 
			  LEFT JOIN care_type_encounter et 
			    ON e.encounter_type = et.type_nr 
			  LEFT JOIN seg_billing_coverage cov 
			    ON cov.bill_nr = fb.bill_nr 
			  LEFT JOIN seg_billingcomputed_discount dsc 
			    ON dsc.bill_nr = fb.bill_nr 
			  LEFT JOIN seg_billing_discount bd 
			    ON bd.bill_nr = fb.bill_nr 
			  LEFT JOIN seg_discount dd 
			    ON dd.discountid = bd.discountid 
			  LEFT JOIN care_users fu 
			    ON fb.modify_id = fu.login_id
			  LEFT JOIN seg_encounter_memcategory sem
			    ON sem.`encounter_nr` = e.`encounter_nr`
			  LEFT JOIN seg_memcategory sm
			    ON sm.`memcategory_id` = sem.`memcategory_id`
			WHERE
				".$personnel_condition."
				".$delete_condition."
				".$date_condition;

//echo $query; exit();

$rs = $db->Execute($query);
if($rs){
	if($rs->RecordCount()>0){
		$i = 0;
		while($row = $rs->FetchRow()){
			if ($row['is_deleted']) {
                $IsDeleted = 'Cancelled';
                $Deletedby = $row['name'];
                $deletedtime = $row['modifydate'];
            }
            else{
                $IsDeleted = '';
                $Deletedby = '';
                $deletedtime = '';
            }
			if (($row['classification'] == 'Infirmary') || ($row['classification'] == 'Senior Citizen') ) {
                $ClassificationShow = $row['classification'];
                $totalDiscount = (float) $row['discount_fixed'] ? 
                $row['discount_fixed'] : 
                ((float) $row['excess']) * $row['discount_pct'];
                $AmountDueShow = (float) $row['excess'] - $totalDiscount;
                $OrNumberShow = $row['or'];
                $OrDateShow = $row['or_date'] ? date('Y-m-d h:i A', strtotime($row['or_date'])) : '-';
                $AmountPayableShow = $row['or_amount'];
                $ClerkShow = $row['or_clerk'];

            }
            else{
                $ClassificationShow = '';
                $totalDiscount = '';
                $AmountDueShow = '';
                $OrNumberShow = '';
                $OrDateShow = '';
                $AmountPayableShow = '';
                $ClerkShow = '';
            }

            if ($row['DISCHARGE_DATE']=='0000-00-00 00:00:00' || is_null($row['DISCHARGE_DATE'])) {
            	$discharge_date = '';
            	$num_days = '';
            } else {
            	$discharge_date = $row['DISCHARGE_DATE'];
            	$num_days = $row['NUMBER_OF_DAYS'];
            }
			$data[$i] = array('bill_ref'=>$row['bill_nr'] . (($row['is_deleted']) ? ' - D':''),
				              'bill_date'=>date('Y-m-d h:i A', strtotime($row['bill_dte'])),
				              'prepared_by'=>$row['prepared_by'],
				              'patient_name'=>ucwords($row['patient']),
				              'case_no'=>$row['encounter_nr'],
				              'department'=>$row['department'],
				              'type'=>$row['type'],
				              'actual_charges'=>(double)$row['total_charge'],
				              'discount'=>(double)$row['total_discount'],
				              'phic_coverage'=>(double)$row['hci'] + (double)$row['doc_pf'],
				              'deposit'=>(double)$row['previous_payment'],
				              'cs_first'=>(double)$row['cs_first'],
				              'cs_second'=>(double)$row['cs_second'],
				              'total_package'=>(double)$row['cs_first'] + (double)$row['cs_second'],
				              'hci'=>(double)$row['hci'],
				              'doc_pf'=>(double)$row['doc_pf'],
				              'excess'=> (($row['discountid']=='NBB' || $row['discountid']== 'HSM') ? (double)0.00 : (double)$row['excess'] ),
				              'phic_category'=>$row['phic_category'],
				              'medico_legal'=>$row['medico_legal'],
				              'first_code'=>$row['first_code'],
				              'second_code'=>$row['second_code'],
				              'nbb_excess' => (($row['discountid']=='NBB' || $row['discountid']== 'HSM') ? (double)$row['excess'] : (double)0.00 ),
				              'admission_date'=>$row['ADMISSION_DATE'],
				              'discharge_date'=>$discharge_date,
				              'num_days'=>$num_days
				             );
			$i++;
		}
	}else{
		$data['bill_ref'][0] = "No data";
	}
}else{
	$data['bill_ref'][0] = "No data";
}

showReport('ACR_2',$params,$data,$_GET['reportFormat']);
?>