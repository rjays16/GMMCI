<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');

include_once($root_path.'include/care_api_classes/class_department.php');
include_once($root_path.'include/care_api_classes/class_insurance.php');
include_once($root_path.'include/care_api_classes/class_personell.php');
include_once($root_path."include/care_api_classes/class_hospital_admin.php");
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/inc_jasperReporting.php');
include_once($root_path.'include/care_api_classes/reports/JasperReport.php');

define('TEMPLATE_NAME', 'ipd_er_report');
define('REPORT_NAME', 'TOTAL ADMISSION REPORT FOR IPD AND ER');
define('REPORT_TYPE', 'PDF');

$jasper = new JasperReport();
$params = array();
$data = array();

class RepGen_OPD_Trans {
	var $from_date;
	var $to_date;
	var $dept_nr;
	var $hcare_id;
	var $senior;
	var $count_rows = 0;

	function RepGen_OPD_Trans($from, $to, $dept_nr, $hcare_id, $is_senior) {
		global $db;

		$this->dept_nr = $dept_nr;
		$this->hcare_id = $hcare_id;
		$this->is_senior = $is_senior;

		if ($from) $this->from=date("Y-m-d",strtotime($from));
		if ($to) $this->to=date("Y-m-d",strtotime($to));

	}
	
	function FetchData() {
		$pers_obj=new Personell();
		$dept_obj = new Department();

		global $db;
		if (empty($this->to)) $end_date="NOW()";
		#else $end_date="'$end_date'";
		else $end_date=$this->to;
		#if (empty($start_date)) $start_date="0000-00-00";
		if (empty($this->from)) $start_date="NOW()";
		else
		$start_date=$this->from;
		#$start_date="$start_date";

		//Added by Cherry 04-08-09
		$sql_dept="";
	 # echo "dep = ".$this->dept_nr;
	 if ($this->dept_nr){

			$sql_dept = " AND (ce.current_dept_nr='".$this->dept_nr."' OR ce.current_dept_nr IN (SELECT nr FROM care_department AS d WHERE d.parent_dept_nr='".$this->dept_nr."'))";

		}

		$where;


		if($this->hcare_id == 'all'){

		}else if($this->hcare_id == 'non-phic'){
			$where = "AND '18' NOT IN ((SELECT 
					      cif.`hcare_id`
					    FROM
					      seg_billing_coverage sbc 
					      LEFT JOIN seg_billing_encounter sbe 
					        ON sbe.bill_nr = sbc.bill_nr 
					      LEFT JOIN care_insurance_firm cif 
					        ON cif.hcare_id = sbc.hcare_id 
					    WHERE sbe.encounter_nr = ce.encounter_nr 
					      AND (
					        sbe.is_deleted <> 1 
					        OR sbe.is_deleted IS NULL
					      ))) ";
		}else{
			$where = "AND '$this->hcare_id' IN ((SELECT 
					      cif.`hcare_id`
					    FROM
					      seg_billing_coverage sbc 
					      LEFT JOIN seg_billing_encounter sbe 
					        ON sbe.bill_nr = sbc.bill_nr 
					      LEFT JOIN care_insurance_firm cif 
					        ON cif.hcare_id = sbc.hcare_id 
					    WHERE sbe.encounter_nr = ce.encounter_nr 
					      AND (
					        sbe.is_deleted <> 1 
					        OR sbe.is_deleted IS NULL
					      ))) ";
		}
		
	$is_senior_where = "";
		switch($this->is_senior){
			case 0:
				$is_senior_where = " AND fn_get_ageyr(DATE(ce.`encounter_date`), DATE(cp.`date_birth`)) < 60 ";
			break;
			case 1:
				$is_senior_where = " AND fn_get_ageyr(DATE(ce.`encounter_date`), DATE(cp.`date_birth`)) >= 60 ";
			break;
		}

	$sql =
				"SELECT DISTINCT ce.encounter_nr, cp.pid,ce.encounter_date,
					CAST(admission_dt as DATE) as admission_date,
					CAST(admission_dt AS TIME) AS admission_time,
								CONCAT(IFNULL(name_last,''),', ',IFNULL(name_first,''),' ',IFNULL(name_middle,'')) AS patientname,
					cp.street_name,  sb.brgy_name, sm.mun_name, sm.zipcode, sp.prov_name,
					cp.date_birth,IF(fn_calculate_age(NOW(),cp.date_birth),fn_get_age(CAST(encounter_date AS date),cp.date_birth),age) AS age,
								UPPER(sex) AS p_sex,cp.civil_status,
					cd.name_formal,IFNULL(fn_get_personell_firstname_last(ce.current_att_dr_nr),(SELECT 
							    GROUP_CONCAT(
							      fn_get_personell_firstname_last (dr.`dr_nr`)
							      SEPARATOR ';'
							    ) 
							  FROM
							    seg_encounter_dr_add dr 
							  WHERE dr.`encounter_nr` = ce.`encounter_nr` 
							    AND (dr.`is_attending` = 1 OR dr.`is_consulting` = 1))) current_att_dr_nr,
							  IFNULL(fn_get_personell_firstname_last(ce.consulting_dr_nr),(SELECT 
							    GROUP_CONCAT(
							      fn_get_personell_firstname_last (dr.`dr_nr`)
							      SEPARATOR ';'
							    ) 
							  FROM
							    seg_encounter_dr_add dr 
							  WHERE dr.`encounter_nr` = ce.`encounter_nr` 
							    AND dr.`is_admitting` = 1)) consulting_dr_nr,
					ce.er_opd_diagnosis,
										 addr_str,cd.id,IFNULL((SELECT 
						  GROUP_CONCAT(cif.firm_id) AS insurance 
						FROM
						  seg_billing_coverage sbc 
						  LEFT JOIN seg_billing_encounter sbe 
						    ON sbe.bill_nr = sbc.bill_nr 
						  LEFT JOIN care_insurance_firm cif 
						    ON cif.hcare_id = sbc.hcare_id 
						WHERE sbe.encounter_nr =  ce.encounter_nr AND (sbe.is_deleted <> 1 OR sbe.is_deleted IS NULL)), 'Non-PHIC') AS insurance,ce.`discharge_date`,ce.`discharge_time`,
					 ser.`result_code`,ce.current_room_nr, sgr.result_desc, sd.`disp_desc`, (SELECT 
						    GROUP_CONCAT(
						      CONCAT(CODE, ' ', diagnosis_description)
						    ) 
						  FROM
						    care_encounter_diagnosis 
						  WHERE encounter_nr = ce.`encounter_nr` 
						    AND STATUS <> 'deleted') description, sed.`disp_code`,ce.encounter_type
				FROM (care_encounter AS ce
					INNER JOIN care_person AS cp ON ce.pid = cp.pid)

					LEFT JOIN care_department AS cd ON ce.current_dept_nr = cd.nr
					LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=cp.brgy_nr
					LEFT JOIN seg_municity AS sm ON sm.mun_nr=cp.mun_nr
					LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
					LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
					LEFT JOIN seg_encounter_result AS ser ON ser.`encounter_nr` = ce.`encounter_nr`
					LEFT JOIN seg_encounter_disposition_refer AS sed ON sed.`encounter_nr` = ce.`encounter_nr`
					LEFT JOIN seg_encounter_insurance b ON ce.`encounter_nr` =  b.`encounter_nr`
					LEFT JOIN care_insurance_firm c ON b.`hcare_id` = c.`hcare_id`
					LEFT JOIN seg_results sgr ON sgr.`result_code` = ser.`result_code` 
					LEFT JOIN seg_dispositions sd 
    					ON sd.`disp_code` = sed.`disp_code` 
				WHERE 
					(ce.create_time >= '$start_date'
					AND CONCAT(CAST(ce.create_time AS date), ' 00:00:00') < DATE_ADD('$end_date', INTERVAL 1 DAY)) AND ce.encounter_status <> 'cancelled'
					AND ce.encounter_type IN (1,3,4) $where $is_senior_where 
					$sql_dept";
			
		$sql .= "GROUP BY ce.encounter_nr ORDER BY ce.encounter_date, admission_date, admission_time";
	// echo $sql;
		$result=$db->Execute($sql);
		$this->_count = $result->RecordCount();
		if ($result) {
			$this->Data=array();
			$i=1;
			while ($row=$result->FetchRow()) {

			if (trim($row['street_name'])){
					if (trim($row["brgy_name"])!="NOT PROVIDED"){
						$street_name = trim($row['street_name']).", ";
					}
					else{
						$street_name = trim($row['street_name']).", ";
					}
			}else{
					$street_name = "";
			}

			if ((!(trim($row["brgy_name"]))) || (trim($row["brgy_name"])=="NOT PROVIDED")){
				$brgy_name = "";
			}
			else{
				$brgy_name  = trim($row["brgy_name"]).", ";
			}

			if ((!(trim($row["mun_name"]))) || (trim($row["mun_name"])=="NOT PROVIDED")){
				$mun_name = "";
			}
			else{
				if ($brgy_name){
					$mun_name = trim($row["mun_name"]);
				}
				else{
					$mun_name = trim($row["mun_name"]);
				}
			}

			if ((!(trim($row["prov_name"]))) || (trim($row["prov_name"])=="NOT PROVIDED")){
				$prov_name = "";
			}
			else{
				$prov_name = trim($row["prov_name"]);
			}

			if(stristr(trim($row["mun_name"]), 'city') === FALSE){
				if ((!empty($row["mun_name"]))&&(!empty($row["prov_name"]))){
					if ($prov_name!="NOT PROVIDED"){
						$prov_name = ", ".trim($prov_name);
					}
					else{
						$prov_name = trim($prov_name);
					}
				}else{
					#$province = trim($prov_name);
					$prov_name = "";
				}
			}else
				$prov_name = "";

			$addr = trim($street_name).trim($brgy_name).trim($mun_name).trim($prov_name);

			$name_doctor = $row['current_att_dr_nr'];

			$name_doctor1 = $row["consulting_dr_nr"];
			

				if($row['discharge_date'] AND $row['discharge_time']){
					$discharge_date = date("m/d/y",strtotime($row['discharge_date']))." ".date("h:iA",strtotime($row['discharge_time']));
				}
				else{
					$discharge_date = '';
				}

				$disp_code = $row['disp_code'];

				switch ($disp_code) {
				    case '7':
				        $disp_code = "Discharged";
				        break;
				    case '8':
				        $disp_code = "Transfered";
				        break;   
				    case '9':
				       	$disp_code = "HAMA";
				        break;  
				    case '10':
				       	$disp_code = "Absconded";
				        break; 
				    default:
				    	$disp_code = "";
				        break;
				}


				if($row['current_room_nr']){
					$room = $row['current_room_nr'];
				}
				else{
					$room = "";
				}


				if($row['status'] == NULL){
					$findiagnosis = $row['description'];
				}
				else{
					$findiagnosis = ""; 
				}

				$encounter_type = $row['encounter_type'];

				switch ($encounter_type) {
				    case '1':
				        $encounter_type = "ER";
				        break;
				    case '2':
				        $encounter_type = "OPD";
				        break;
				    case '3':
				        $encounter_type = "IPD";
				        break;   
				    case '4':
				        $encounter_type = "IPD";
				        break;  
				    default:
				    	$encounter_type = "";
				        break;
				}
				

				if ($row['civil_status']=='married'){
					$cstatus = "M";
				}
				elseif ($row['civil_status']=='single'){
					$cstatus = "S";
				}
				elseif ($row['civil_status']=='child'){
					$cstatus = "CH";
				}
				elseif ($row['civil_status']=='divorced'){
					$cstatus = "D";
				}
				elseif ($row['civil_status']=='widowed'){
					$cstatus = "W";
				}
				elseif ($row['civil_status']=='separated'){
					$cstatus = "S";
				}

				$age ='';
				if (($row['date_birth']) && ($row['date_birth']!='0000-00-00') ){
					$bdate = date("m/d/Y",strtotime($row['date_birth']));
				}else{
					$bdate = 'unknown';
				}

				if (stristr($row['age'],'years')){
					$age = substr($row['age'],0,-5);
					$age = floor($age).' y';
				}elseif (stristr($row['age'],'year')){
					$age = substr($row['age'],0,-4);
					$age = floor($age).' y';
				}elseif (stristr($row['age'],'months')){
					$age = substr($row['age'],0,-6);
					$age = floor($age).' m';
				}elseif (stristr($row['age'],'month')){
					$age = substr($row['age'],0,-5);
					$age = floor($age).' m';
				}elseif (stristr($row['age'],'days')){
					$age = substr($row['age'],0,-4);

					if ($age>30){
						$age = $age/30;
						$label = 'm';
					}else{
						$label = 'd';
					}

					$age = floor($age).' '.$label;
				}elseif (stristr($row['age'],'day')){
					$age = substr($row['age'],0,-3);
					$age = floor($age).' d';
				}else{
					$age = floor($row['age']).' y';
				}	

				
				if($row['admission_date']){
					$admission_date = date("m/d/y",strtotime($row['admission_date']))." ".date("h:iA",strtotime($row['admission_time']));
				}else{
					$admission_date = date("m/d/y h:iA",strtotime($row['encounter_date']));
				}

				$this->Data[]=array(
					'pid' => $row['pid'],
					'patient' => mb_strtoupper(trim($row['patientname'])),
					'age' => $age,
					'sex' => mb_strtoupper($row['p_sex']),
					'adm_date' => $admission_date,
					'addr' => ucwords(mb_strtolower(trim($addr))),
					'insurance' => $row['insurance'].($room ? ' #' : ' ').$room,
					'ad_dx' => trim($row['er_opd_diagnosis']),
					'discharge_date' => $discharge_date,
					'dob' => $bdate,
					'f_dx' => $findiagnosis,
					'enc_type' => $encounter_type,
					'disp' => $row['disp_desc'],
					'att_dr' => ucwords(mb_strtolower($name_doctor)),
					'adm_dr' => ucwords(mb_strtolower($name_doctor1))
				);

				$this->count_rows++;
			}
		}
		else
			echo $this->Conn->ErrorMsg();
	}


}

$rep = new RepGen_OPD_Trans($_GET['from'],$_GET['to'],$_GET['dept_nr'], $_GET['modkey4'], $_GET['is_senior']);
$rep->FetchData();
$row_data = $rep->Data;

$objInfo = new Hospital_Admin();


if ($row = $objInfo->getAllHospitalInfo()) {
    $hosp_name   = mb_strtoupper($row['hosp_name']);
}else{
	$hosp_name   = 'GONZALES MARANAN MEDICAL CENTER, INC.';
}

$range = date("m/d/Y",strtotime($_GET['from']))." - ".date("m/d/Y",strtotime($_GET['to']));

switch($_GET['modkey4']){
			case "all":
				$insurance = "ALL INSURANCES";
			break;

			case 'non-phic':
				$insurance = "NON-PHIC";
			break;

			default:
				$insurance_obj = new Insurance();
				$insurance_data = $insurance_obj->getInsuranceInfo($_GET['modkey4']);
				$insurance = $insurance_data['firm_id'];
			break;
		}

$params = array(
			'report_name'   	=> REPORT_NAME,
			'hospital_name'  	=> $hosp_name,
			'insurance'  		=> $insurance,
			'range'  	 		=> $range,
			'row_count'  	 	=> (String)$rep->count_rows,
			'generate'  	 	=> 'Generated: '.date("m/d/Y h:iA")
			);

foreach ($row_data as $val) {
			$data[]=array(
					'pid' 				=> $val['pid'],
					'patient' 			=> $val['patient'],
					'age' 				=> $val['age'],
					'sex' 				=> $val['sex'],
					'adm_date' 			=> $val['adm_date'],
					'addr' 				=> $val['addr'],
					'insurance' 		=> $val['insurance'],
					'ad_dx' 			=> $val['ad_dx'],
					'discharge_date' 	=> $val['discharge_date'],
					'dob' 				=> $val['dob'],
					'f_dx' 				=> $val['f_dx'],
					'enc_type' 			=> $val['enc_type'],
					'disp' 				=> $val['disp'],
					'att_dr' 			=> $val['att_dr'],
					'adm_dr' 			=> $val['adm_dr'],
				);
}
// var_dump($data);exit();

showReport(TEMPLATE_NAME,$params,$data,REPORT_TYPE);
?>