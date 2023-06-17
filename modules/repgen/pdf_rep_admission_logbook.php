<?php
/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');

include_once($root_path.'include/care_api_classes/class_department.php');
include_once($root_path.'include/care_api_classes/class_insurance.php');
include_once($root_path.'include/care_api_classes/class_personell.php');
include_once($root_path."include/care_api_classes/class_hospital_admin.php");
require($root_path.'include/inc_environment_global.php');
//require($root_path.'/modules/repgen/repgen.inc.php');


require($root_path.'/modules/repgen/themes/dmc/dmc.php');

class RepGen_OPD_Trans extends DMCRepGen {
//class RepGen_OPD_Trans extends RepGen {
	var $from_date;
	var $to_date;
	var $dept_nr;
	var $hcare_id;
	var $senior;

	function RepGen_OPD_Trans($from, $to, $dept_nr, $hcare_id, $is_senior) {
		global $db;

		$this->DMCRepGen("COMPUTERIZED ADMISSION LOGBOOK", "L", "A4", $db, TRUE);

		$this->Caption = "COMPUTERIZED ADMISSION LOGBOOK";
		//$this->dept_nr = $dept_nr;
		$this->dept_nr = $dept_nr;
		$this->hcare_id = $hcare_id;
		$this->is_senior = $is_senior;
		$this->SetAutoPageBreak(FALSE);
		$this->LEFTMARGIN=1.5;
		$this->DEFAULT_TOPMARGIN = 3;
		
		// orig...   $this->ColumnWidth = array(8,22,16,17,33,20,35,15,7,12,22,25,50);
		//edited by Cherry 04-08-09
		$this->ColumnWidth = array(8,18,22,8,8,20,22,22,28,22,18,28,22,24,24);
		$this->Columns = 15;

		$this->TotalWidth = array_sum($this->ColumnWidth);

			//edited by Cherry 04-08-09
			// $this->ColumnLabels = array(
			// 	'',
			// 	'Adm. No.',
			// 	'HRN',
			// 	'Patient',
			// 	'B-Date',
			// 	'Age',
			// 	'Sex',
			// 	'Admitted',
			// 	'Status',
			// 	'Address',
			// 	'PHIC Mem.',
			// 	'Department',
			// 	'Att. Physcian',
			// 	'Diagnosis',
			// 	'Discharged'
			// );

			$this->ColumnLabels = array(
				'',
				'HRN No.',
				'Patient',
				'Age',
				'Sex',
				'Admitted',
				'Address',
				'Membership',
				'Ad. Diagnosis',
				'Discharged',
				'DOB',
				'Fin. Diagnosis',
				'Disposition',
				'Att. Physcian',
				'Ad. Physcian'
			);


		#$this->textpadding
		$this->RowHeight = 5;
		$this->TextHeight = 3.5;

		$this->Alignment = array('C','C','C','C','C','C','C','C','C','C','C','C','C','C','C');

		#$this->PageOrientation = "L";

		if ($from) $this->from=date("Y-m-d",strtotime($from));
		if ($to) $this->to=date("Y-m-d",strtotime($to));

		$this->SetDrawColor(128,0,0); // added by genz
		$this->NoWrap = FALSE;

	}

	function Header() {

		$objInfo = new Hospital_Admin();
		$dept_obj=new Department();

		if ($row = $objInfo->getAllHospitalInfo()) {
			$row['hosp_agency'] = strtoupper($row['hosp_agency']);
			$row['hosp_name']   = strtoupper($row['hosp_name']);
		}
		else {
			$row['hosp_country'] = "Republic of the Philippines";
			$row['hosp_agency']  = "DEPARTMENT OF HEALTH";
			$row['hosp_name']    = "Gonzales Maranan Medical Center Incorporated";
			$row['hosp_addr1']   = "Quezon Ave., Digos City";
		}

		$this->LogoX = 94;
		$this->LogoY = 8;
		#$this->Image('../../gui/img/logos/dmc_logo.jpg',$this->LogoX,$this->LogoY,16,20);
		/*$this->SetFont("Arial","I","9");
		$this->Cell(0,4,$row['hosp_country'],$border2,1,'C');
		$this->Cell(0,4,$row['hosp_agency'],$border2,1,'C');*/
		$this->SetFont("Arial","B","10");
		$this->Cell(0,4,$row['hosp_name'],$border2,1,'C');
		#$this->SetFont("Arial","","9");
		#$this->Cell(0,4,$row['hosp_addr1'],$border2,1,'C');
			$this->SetFont('Arial','B',11);
		$this->Ln(2);

		$this->SetFont('Arial','',8);
		$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("m/d/Y h:iA"),0,0,'R');
		$this->Ln(2);
		$this->SetFont("Arial","B","12");
				$this->Cell(0,4,'COMPUTERIZED ADMISSION LOGBOOK',$border2,1,'C');
		#$this->Ln(0);

		#$this->Cell(0,5,date("m/d/Y",strtotime($this->from))."  ".$this->from_time." - ".date("m/d/Y",strtotime($this->to))."  ".$this->to_time,$border2,1,'C');
		#$this->Cell(0,4,date("m/d/Y",strtotime($this->from))."  ".date("h:i A",strtotime($this->from_time))." - ".date("m/d/Y",strtotime($this->to))."  ".date("h:i A",strtotime($this->to_time)),$border2,1,'C');

		/* Display insurances*/
		$insurance = "";

		switch($this->hcare_id){
			case "all":
				$insurance = "ALL INSURANCES";
			break;

			case 'non-phic':
				$insurance = "NON-PHIC";
			break;

			default:
				$insurance_obj = new Insurance();
				$insurance_data = $insurance_obj->getInsuranceInfo($this->hcare_id);
				$insurance = $insurance_data['firm_id'];
			break;
		}

		$this->Cell(0, 4, strtoupper($insurance), $border2, 1, 'C');

		/*end insurance*/
		
		if (($this->from)==($this->to))
			$this->Cell(0,4,"For ".date("m/d/Y",strtotime($this->from)),$border2,1,'C');
		else
			$this->Cell(0,4,date("m/d/Y",strtotime($this->from))." - ".date("m/d/Y",strtotime($this->to)),$border2,1,'C');

		$this->Cell(0,4,'Number of Records : '.$this->_count,$border2,1,'L');
		#$this->Ln(1);
		$from_dt=strtotime($this->from_date);
		$to_dt=strtotime($this->to_date);
		#$this->SetFont("Arial","","8");
		if (!empty($this->from_date) && !empty($this->to_date))
			$this->Cell(0,5,
				sprintf('%s-%s',date("F j, Y",$from_dt),date("F j, Y",$to_dt)),
				$border2,1,'C');

		$this->Ln(1);

		parent::Header();

	}

	function BeforeData() {
		$this->FONTSIZE = 8;
		if ($this->colored) {
			#$this->DrawColor = array(0xDD,0xDD,0xDD);
			#$this->DrawColor = array(255,255,255);
			$this->SetDrawColor(128,0,0);// added by genz
		}
	}

	function FetchData() {
		$pers_obj=new Personell();
		$dept_obj = new Department();

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
						    AND STATUS <> 'deleted') description, sed.`disp_code` 
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
					AND ce.encounter_type IN (3,4) $where $is_senior_where 
					$sql_dept";
					/*
					Added by genz c.`firm_id`,c.`hcare_id`,ce.`discharge_date`,ce.`discharge_time`, ced.`code`, ser.`result_code` on SELECT and 
					LEFT JOIN seg_encounter_insurance b ON ce.`encounter_nr` =  b.`encounter_nr` and 
					LEFT JOIN care_insurance_firm c ON b.`hcare_id` = c.`hcare_id` and
					LEFT JOIN care_encounter_diagnosis AS ced ON ced.`encounter_nr`= ce.`encounter_nr`
					LEFT JOIN seg_encounter_result AS ser ON ser.`encounter_nr` = ce.`encounter_nr`
					*/


	#	if ($this->dept_nr) {
	#		$sql .= " AND ce.current_dept_nr=".$this->dept_nr;
	#	}
		$sql .= "GROUP BY ce.encounter_nr ORDER BY ce.encounter_date, admission_date, admission_time";
	#echo $sql;
		$result=$this->Conn->Execute($sql);
		$this->_count = $result->RecordCount();
		$this->Conn->SetFetchMode(ADODB_FETCH_ASSOC);
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

			/*if ($row["current_att_dr_nr"]){
				$docInfo = $pers_obj->getPersonellInfo($row["current_att_dr_nr"]);
			}
			else{
				$docInfo = '';
			}*/
			$name_doctor = $row['current_att_dr_nr'];

			//Added by Genz
			/*if ($row["consulting_dr_nr"]){
				$docInfo1 = $pers_obj->getPersonellInfo($row["consulting_dr_nr"]);
			}
			else{
				$docInfo1 = '';
			}*/
			$name_doctor1 = $row["consulting_dr_nr"];
			//end by Genz



				/*$dr_middleInitial = "";
				if (trim($docInfo['name_middle'])!=""){
					$thisMI=split(" ",$docInfo['name_middle']);
					foreach($thisMI as $value){
						if (!trim($value)==""){
							$dr_middleInitial .= $value[0];
						}
					}
					if (trim($dr_middleInitial)!=""){
						$dr_middleInitial = " ".$dr_middleInitial.".";
					}
				}
				$name_doctor = $docInfo['name_last'].", ".$docInfo['name_first']." ".$dr_middleInitial;

				if (trim($name_doctor)==','){
					$name_doctor = "";
				}


				//Added by Genz
				$dr_middleInitial1 = "";
				if (trim($docInfo1['name_middle'])!=""){
					$thisMI1=split(" ",$docInfo1['name_middle']);
					foreach($thisMI1 as $value){
						if (!trim($value)==""){
							$dr_middleInitial1 .= $value[0];
						}
					}
					if (trim($dr_middleInitial1)!=""){
						$dr_middleInitial1 = " ".$dr_middleInitial1.".";
					}
				}
				$name_doctor1 = $docInfo1['name_last'].", ".$docInfo1['name_first']." ".$dr_middleInitial1;

				if (trim($name_doctor1)==','){
					$name_doctor1 = "";
				}*/
				

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
				
				//end by Genz



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

				//edited by Cherry 04-08-09
				// $this->Data[]=array(
				// 	$i,
				// 	$row['encounter_nr'],
				// 	$row['pid'],
				// 	mb_strtoupper(trim($row['patientname'])),
				// 	$bdate,
				// 	$age,
				// 	mb_strtoupper($row['p_sex']),
				// 	date("m/d/y",strtotime($row['admission_date']))." ".date("h:iA",strtotime($row['admission_time'])),
				// 	mb_strtoupper($cstatus),
				// 	ucwords(mb_strtolower(trim($addr))),
				// 	$phic,
				// 	$row['name_formal'],
				// 	ucwords(mb_strtolower($name_doctor)),
				// 	trim($row['er_opd_diagnosis']),
				// 	$row['discharge_date']." ".$row['discharge_time']
				// );


				if($row['admission_date']){
					$admission_date = date("m/d/y",strtotime($row['admission_date']))." ".date("h:iA",strtotime($row['admission_time']));
				}else{
					$admission_date = date("m/d/y h:iA",strtotime($row['encounter_date']));
				}

				$this->Data[]=array(
					$i,
					$row['pid'],
					mb_strtoupper(trim($row['patientname'])),
					$age,
					mb_strtoupper($row['p_sex']),
					$admission_date,
					ucwords(mb_strtolower(trim($addr))),
					$row['insurance'].($room ? ' #' : ' ').$room,
					trim($row['er_opd_diagnosis']),
					$discharge_date,
					$bdate,
					$findiagnosis,
					$row['disp_desc'],
					ucwords(mb_strtolower($name_doctor)),
					ucwords(mb_strtolower($name_doctor1))
				);

				$i++;
			}

		}
		else
			echo $this->Conn->ErrorMsg();
	}

}

$rep = new RepGen_OPD_Trans($_GET['from'],$_GET['to'],$_GET['dept_nr'], $_GET['modkey4'], $_GET['is_senior']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>