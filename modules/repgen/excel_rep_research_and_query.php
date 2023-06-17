<?php
	#created by CHA 07-21-09
	#Research and Query Report- for DMC

	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	require_once($root_path."/classes/excel/Writer.php");
	require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

	class ExcelGen_Research_And_Query extends Spreadsheet_Excel_Writer
	{
			var $worksheet;
			var $Headers;
			var $format1, $format2;

			var $fromdate;
			var $todate;
			var $code_type;
			var $icd_code;
			var $icp_code;
			var $patient_type;
			var $num_of_cases;

			function ExcelGen_Research_And_Query($fromdate, $todate, $codetype, $icd, $icp, $ptype)
			{
					$this->Spreadsheet_Excel_Writer();
					$this->worksheet = & $this->addWorksheet();
					$this->worksheet->setPaper(5);      // Legal
					$this->worksheet->setLandscape();
					$this->worksheet->setMarginTop(2.5);
					$this->worksheet->setMarginLeft(0.3);
					$this->worksheet->setMarginRight(0.3);
					$this->worksheet->setMarginBottom(0.3);
					$this->Headers = array(
							'HRN', 'Date Admitted',
							'Date Discharged', 'Patient Name',
							'Age', 'Sex', 'Address',
							'Results', 'Department', 'Doctor'
						);
					$this->format1=& $this->addFormat();
					$this->format1->setSize(9);
					$this->format1->setBold();
					$this->format1->setAlign('center');
					$this->format2=& $this->addFormat();
					$this->format2->setSize(8);
					$this->format2->setAlign('left');

					if($fromdate) $this->fromdate=$fromdate;
					if($todate) $this->todate=$todate;
					if($codetype) $this->code_type=$codetype;
					if($icd) $this->icd_code=$icd;
					if($icp) $this->icp_code=$icp;
					if($ptype) $this->patient_type=$ptype;
			}

			function ExcelHeader()
			{
					global $db;
					$objInfo = new Hospital_Admin();

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
					if ($this->fromdate==$this->todate)
						$text = "For ".date("F j, Y",strtotime($this->fromdate));
					else
						$text = "From ".date("F j, Y",strtotime($this->fromdate))." To ".date("F j, Y",strtotime($this->todate));
					if($this->patient_type=='1') $text2="ER PATIENT";
					else if($this->patient_type=='2') $text2="OUT PATIENT";
					else if($this->patient_type=='3,4') $text2="IN PATIENT";
					else if($this->patient_type=='all') $text2="ALL PATIENTS";

					# added by VAN 08-05-09

					if (($this->icp_code) || ($this->icd_code)){
							if ($this->icp_code){
								$sql_code = "SELECT * FROM care_ops301_en WHERE code='".$this->icp_code."'";
								$code = $this->icp_code;
							}else if ($this->icd_code){
								$sql_code = "SELECT * FROM care_icd10_en WHERE diagnosis_code='".$this->icd_code."'";
								$code = $this->icd_code;
							}

							if ($this->code_type=='ICD10')
									$label = "ICD : ";
							else
									$label = "ICP : ";

							$rs = $db->Execute($sql_code);
							if ($rs){
								$row = $rs->FetchRow();
								$desc = " - ".trim($row['description']);

								if (empty($desc))
										$desc = "- No description specified";
							}
					}else{
							 if ($this->code_type=='ICD10')
									$label = "ALL ICD";
							 else
									$label = "ALL ICP";

							 $code = "";
							 $desc = "";
					}
					$this->worksheet->setHeader("".$row['hosp_country']."\n".$row['hosp_agency']."\n".$row['hosp_name']."\n".$row['hosp_addr1']."\n\nGonzales Maranan Medical Center Incorporated\n Research and Query REPORT\n".$text."\n\n".$text2."\n".$label."\n",0.5);

					$this->worksheet->setColumn(0, 0, 10);
					$this->worksheet->write(0, 0, $this->Headers[0], $this->format1);
					$this->worksheet->setColumn(0, 1, 13);
					$this->worksheet->write(0, 1, $this->Headers[1], $this->format1);
					$this->worksheet->setColumn(0, 2, 13);
					$this->worksheet->write(0, 2, $this->Headers[2], $this->format1);
					$this->worksheet->setColumn(0, 3, 20);
					$this->worksheet->write(0, 3, $this->Headers[3], $this->format1);
					$this->worksheet->setColumn(0, 4, 8);
					$this->worksheet->write(0, 4, $this->Headers[4], $this->format1);
					$this->worksheet->setColumn(0, 5, 5);
					$this->worksheet->write(0, 5, $this->Headers[5], $this->format1);
					$this->worksheet->setColumn(0, 6, 38);
					$this->worksheet->write(0, 6, $this->Headers[6], $this->format1);
					$this->worksheet->setColumn(0, 7, 10);
					$this->worksheet->write(0, 7, $this->Headers[7], $this->format1);
					$this->worksheet->setColumn(0, 8, 15);
					$this->worksheet->write(0, 8, $this->Headers[8], $this->format1);
					$this->worksheet->setColumn(0, 9, 25);
					$this->worksheet->write(0, 9, $this->Headers[9], $this->format1);

			}

			function FetchData()
			{
					global $db;
					if($this->patient_type=='all') $this->patient_type='1,2,3,4';
					if($this->icd_code=='all') $this->icd_code='';
					if($this->icp_code=='all') $this->icp_code='';
					#echo $this->code_type;
					if($this->code_type=='ICD10')
					{
							$sql="select distinct ce.pid, ce.encounter_nr as CaseNo,
                                    ce.discharge_date as DateDischarged, ce.admission_dt as DateAdmission,
                                    CONCAT(IF (trim(c.name_last) IS NULL,'',trim(c.name_last)),', ',IF(trim(c.name_first) IS NULL ,'',trim(c.name_first)),' ',
                                    IF(trim(c.name_middle) IS NULL,'',trim(c.name_middle))) as PatientName,
                                    IF(fn_calculate_age(NOW(),c.date_birth),fn_get_age(NOW(),c.date_birth),age) AS Age,
                                    upper(c.sex) AS Sex,
                                    CONCAT(IF (trim(c.street_name) IS NULL,'',trim(c.street_name)),' ',
                                        IF (trim(sb.brgy_name) IS NULL,'',trim(sb.brgy_name)),' ',
                                        IF (trim(sm.mun_name) IS NULL,'',trim(sm.mun_name)),' ',
                                        IF (trim(sm.zipcode) IS NULL,'',trim(sm.zipcode)),' ',
                                        IF (trim(sp.prov_name) IS NULL,'',trim(sp.prov_name)),' ',
                                        IF (trim(sr.region_name) IS NULL,'',trim(sr.region_name))) AS 'Address',
                                    res.result_desc AS 'Results',
                                    cd.name_formal as 'Department',
                                    UPPER(IF (ce.current_att_dr_nr, fn_get_personell_name(ce.current_att_dr_nr),fn_get_personell_name(ce.consulting_dr_nr))) AS 'Doctor'
                                    from care_encounter as ce
                                    inner join care_person as c on c.pid=ce.pid
                                    inner join care_encounter_diagnosis as ced on ce.encounter_nr=ced.encounter_nr
                                    INNER JOIN care_icd10_en AS ci ON ci.diagnosis_code=ced.code
                                    LEFT JOIN care_department as cd on cd.nr=ce.consulting_dept_nr
                                    LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=c.brgy_nr
                                    LEFT JOIN seg_municity AS sm ON sm.mun_nr=c.mun_nr
                                    LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
                                    LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
                                    LEFT JOIN (SELECT DISTINCT ser.encounter_nr,
                                    SUBSTRING(MAX(CONCAT(ser.modify_time,ser.result_code)),20,1) AS result_code
                                    FROM seg_encounter_result AS ser
                                    INNER JOIN care_encounter AS em ON em.encounter_nr=ser.encounter_nr
                                    GROUP BY ser.encounter_nr
                                    ORDER BY ser.encounter_nr) AS sr ON sr.encounter_nr = ce.encounter_nr
                                    INNER JOIN seg_results AS res ON res.result_code=sr.result_code
                                    WHERE ce.status NOT IN ('deleted','hidden','inactive','void')
                                    AND cd.status NOT IN ('deleted','hidden','inactive','void')
                                    AND ced.status NOT IN ('deleted','hidden','inactive','void') 
                                    #AND (ce.discharge_date BETWEEN '".$this->fromdate."' and '".$this->todate."')
                                    #AND (ce.admission_dt BETWEEN '".$this->fromdate."' and '".$this->todate."')
                                    AND (ce.encounter_date BETWEEN '".$this->fromdate."' and '".$this->todate."')
                                    AND ce.encounter_type IN(".$this->patient_type.")
                                    AND ced.code='".$this->icd_code."'
                                    ORDER BY c.name_last, c.name_first, c.name_middle";
                                    
					}else if($this->code_type=='ICP')
					{
							$sql="select distinct ce.pid, ce.encounter_nr as CaseNo,
                                    ce.discharge_date as DateDischarged, ce.admission_dt as DateAdmission,
                                    CONCAT(IF (trim(c.name_last) IS NULL,'',trim(c.name_last)),', ',IF(trim(c.name_first) IS NULL ,'',trim(c.name_first)),' ',
                                    IF(trim(c.name_middle) IS NULL,'',trim(c.name_middle))) as PatientName,
                                    IF(fn_calculate_age(NOW(),c.date_birth),fn_get_age(NOW(),c.date_birth),age) AS Age,
                                    upper(c.sex) AS Sex,
                                    CONCAT(IF (trim(c.street_name) IS NULL,'',trim(c.street_name)),' ',
                                        IF (trim(sb.brgy_name) IS NULL,'',trim(sb.brgy_name)),' ',
                                        IF (trim(sm.mun_name) IS NULL,'',trim(sm.mun_name)),' ',
                                        IF (trim(sm.zipcode) IS NULL,'',trim(sm.zipcode)),' ',
                                        IF (trim(sp.prov_name) IS NULL,'',trim(sp.prov_name)),' ',
                                        IF (trim(sr.region_name) IS NULL,'',trim(sr.region_name))) AS 'Address',
                                    res.result_desc AS 'Results',
                                    cd.name_formal as 'Department',
                                    UPPER(IF (ce.current_att_dr_nr, fn_get_personell_name(ce.current_att_dr_nr),fn_get_personell_name(ce.consulting_dr_nr))) AS 'Doctor'
                                    from care_encounter as ce
                                    inner join care_person as c on c.pid=ce.pid
                                    inner join care_encounter_procedure as cp on ce.encounter_nr=cp.encounter_nr
                                    INNER JOIN care_ops301_en AS co ON co.code=cp.code
                                    LEFT JOIN care_department as cd on cd.nr=ce.consulting_dept_nr
                                    LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=c.brgy_nr
                                    LEFT JOIN seg_municity AS sm ON sm.mun_nr=c.mun_nr
                                    LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
                                    LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
                                    LEFT JOIN (SELECT DISTINCT ser.encounter_nr,
                                    SUBSTRING(MAX(CONCAT(ser.modify_time,ser.result_code)),20,1) AS result_code
                                    FROM seg_encounter_result AS ser
                                    INNER JOIN care_encounter AS em ON em.encounter_nr=ser.encounter_nr
                                    GROUP BY ser.encounter_nr
                                    ORDER BY ser.encounter_nr) AS sr ON sr.encounter_nr = ce.encounter_nr
                                    INNER JOIN seg_results AS res ON res.result_code=sr.result_code
                                    WHERE ce.status NOT IN ('deleted','hidden','inactive','void')
                                    AND cp.status NOT IN ('deleted','hidden','inactive','void')
                                    AND cd.status NOT IN ('deleted','hidden','inactive','void') 
                                    AND (ce.encounter_date BETWEEN '".$this->fromdate."' and '".$this->todate."')
                                    AND ce.encounter_type IN(".$this->patient_type.")
                                    AND cp.code = '".$this->icp_code."'
                                    ORDER BY c.name_last, c.name_first, c.name_middle";
					}
					#echo $sql;
					if($result=$db->Execute($sql))
					{
							//$this->Data=array();
							$row=1;
							$col=0;
							$this->num_of_cases=0;
							while($result!=null && $res=$result->FetchRow())
							{
                                     if ($res['DateDischarged']!='0000-00-00')
                                        $discharged_date = date("m/d/Y",strtotime($res['DateDischarged']));
                                    if ($res['DateAdmission']!='0000-00-00 00:00:00')
                                        $admission_date = date("m/d/Y",strtotime($res['DateAdmission']));
                                    
									$this->worksheet->write($row, $col, $res['pid'], $this->format2);
									$this->worksheet->write($row, $col+1, $admission_date, $this->format2);
									$this->worksheet->write($row, $col+2, $discharged_date, $this->format2);
									$this->worksheet->write($row, $col+3, $res['PatientName'], $this->format2);
									$this->worksheet->write($row, $col+4, $res['Age'], $this->format2);
									$this->worksheet->write($row, $col+5, $res['Sex'], $this->format2);
									$this->worksheet->write($row, $col+6, $res['Address'], $this->format2);
									$this->worksheet->write($row, $col+7, $res['Results'], $this->format2);
									$this->worksheet->write($row, $col+8, $res['Department'], $this->format2);
									$this->worksheet->write($row, $col+9, $res['Doctor'], $this->format2);
									$row++;
									$this->num_of_cases++;
							}

							$row+=2;
							$this->worksheet->write($row, $col, "TOTAL CASES: ".($this->num_of_cases), $this->format2);
					}
			}
	}

	$rep = new ExcelGen_Research_And_Query($_GET['fromdate'],$_GET['todate'],$_GET['codetype'],$_GET['icd'],$_GET['icp'],$_GET['ptype']);
	$rep->ExcelHeader();
	$rep->FetchData();
	$rep->send('rep_research_query.xls');
	$rep->close();
?>