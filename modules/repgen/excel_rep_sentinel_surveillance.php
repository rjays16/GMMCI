<?php
	#created by Cherry 09-29-10
	#Sentinel Surveillance - for SPMC

	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	require_once($root_path."/classes/excel/Writer.php");
	require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

	class SentinelSurveillance extends Spreadsheet_Excel_Writer{
		var $from;
		var $to;
		var $count_rows;
		var $code1;
		var $code2;
		var $temp=0;

			function SentinelSurveillance($from, $to, $code, $sclass)
			{
					$this->Spreadsheet_Excel_Writer();
					$this->worksheet = & $this->addWorksheet();
					$this->worksheet->setPaper(5);      // Legal
					$this->worksheet->setLandscape();
					$this->worksheet->setMarginTop(2);
					$this->worksheet->setMarginLeft(0.2);
					$this->worksheet->setMarginRight(0.1);
					$this->worksheet->setMarginBottom(0.3);
					$this->Headers = array(
							'#','CASE NO.', 'FULL NAME',
							'DATE ADMITTED', 'AGE',
							'SEX', 'COMPLETE ADDRESS', 'If Dengue Platelet below 100,000 or less per mm3',
							'FATALITY'
						);
					$this->ColumnWidth = array(8, 20, 35, 20, 10, 10, 45, 10, 10);
					$this->format1=& $this->addFormat();
					$this->format1->setSize(9);
					$this->format1->setBold();
					$this->format1->setAlign('center');
					$this->format1->setTextWrap(1);

					$this->format2=& $this->addFormat();
					$this->format2->setBold();
					$this->format2->setSize(9);
					$this->format2->setAlign('left');

					$this->format3=& $this->addFormat();
					$this->format3->setSize(9);
					$this->format3->setBold();
					$this->format3->setAlign('left');
					$this->format3->setTextWrap(1);

					$this->format4=& $this->addFormat();
					$this->format4->setSize(12);
					$this->format4->setBold();
					$this->format4->setAlign('left');

					/*if($from) $this->from=$from;
					if($to) $this->to=$to;
					if($code) $this->code=$code;  */

					$this->code1 = $code.".-";
					$this->code2 = $code."-";

						if($code != 'all')
							//$this->icd_cond = "AND (ed.code_parent = '".$this->code."'";
									#$this->icd_cond = "AND (ed.code_parent = '".$this->code1."' OR ed.code_parent = '".$this->code2."')";
									#edited by VAN 05-20-09
									$this->icd_cond = " AND IF(instr(c.diagnosis_code,'.'),
																			substr(c.diagnosis_code,1,IF(instr(c.diagnosis_code,'.'),instr(c.diagnosis_code,'.')-1,0)),
																			c.diagnosis_code) = '$code' ";
							else
									$this->icd_cond = "";

						if ($sclass=='primary'){
							$this->type_cond = " AND ed.type_nr='1' ";
						}elseif ($sclass=='secondary'){
							$this->type_cond = " AND ed.type_nr='0' ";
						}elseif ($sclass=='all')
							$this->type_cond = "";

						if ($from) $this->from=date("Y-m-d",strtotime($from));
						if ($to) $this->to=date("Y-m-d",strtotime($to));

			}

			function ExcelHeader()
			{
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
					/*if ($this->from==$this->to)
						$text = "For ".date("F j, Y",strtotime($this->from));
					else
						$text = "From ".date("F j, Y",strtotime($this->from))." To ".date("F j, Y",strtotime($this->to));
										 */

					if($this->from == $this->to){
						$text = "Period Covered : As of ".date("F j, Y",strtotime($this->from));
					}
					else{
						$text = "Period Covered  : From ".date("F j, Y", strtotime($this->from))." to ".date("F j, Y",strtotime($this->to));
					}

					$this->worksheet->setHeader("".$row['hosp_country']."\n".$row['hosp_agency']."\n".$row['hosp_name']."\n".$row['hosp_addr1']."\n SENTINEL SURVEILLANCE WORKSHEET\n".$text."\n\n".$text2."\n",0.5);

					$this->worksheet->setColumn(0, 0, $this->ColumnWidth[0]);
					$this->worksheet->write(0, 0, "", $this->format1);
					$this->worksheet->setColumn(0, 1, $this->ColumnWidth[1]);
					$this->worksheet->write(0, 1, "", $this->format1);
					$this->worksheet->setColumn(0, 2, $this->ColumnWidth[2]);
					$this->worksheet->write(0, 2, "", $this->format1);
					$this->worksheet->setColumn(0, 3, $this->ColumnWidth[3]);
					$this->worksheet->write(0, 3, "", $this->format1);
					$this->worksheet->setColumn(0, 4, $this->ColumnWidth[4]);
					$this->worksheet->write(0, 4, "", $this->format1);
					$this->worksheet->setColumn(0, 5, $this->ColumnWidth[5]);
					$this->worksheet->write(0, 5, "", $this->format1);
					$this->worksheet->setColumn(0, 6, $this->ColumnWidth[6]);
					$this->worksheet->write(0, 6, "", $this->format1);
					$this->worksheet->setColumn(0, 7, $this->ColumnWidth[7]);
					$this->worksheet->write(0, 7, "", $this->format1);
					$this->worksheet->setColumn(0, 8, $this->ColumnWidth[8]);
					$this->worksheet->write(0, 8, "", $this->format1);

			}

			function GetDiagnosisOccurrence($diag, $desc){
				global $db;
				$this->count_rows = 0;
				$sql_diag = "";
				if ($diag)
					$sql_diag = "AND ed.icd = '".$diag."'";

				$sql2 = "SELECT DISTINCT ed.*
									FROM seg_rep_medrec_patient_icd_tbl AS ed
									WHERE DATE(ed.admission_dt) BETWEEN  '".$this->from."' AND '".$this->to."'
									".$sql_diag."
									".$this->type_cond."
									AND ed.encounter_type IN (3,4)
									ORDER BY ed.icd, DATE(ed.admission_dt) ASC, ed.patient_name";
				#echo "<br>".$sql2;
				$result2 = $db->Execute($sql2);
				$this->count_rows = $result2->RecordCount();

				$i=1;
				while($row2 = $result2->FetchRow()){
					if (($row2['discharge_date']!='0000-00-00')&&(!(empty($row2['discharge_date']))))
						$discharged_date = date("m/d/Y",strtotime($row2['discharge_date']));
					else
						$discharged_date = 'still in';

					if (($row2['admission_dt']!='0000-00-00')&&(!(empty($row2['admission_dt']))))
						$admission_date = date("m/d/Y h:i A",strtotime($row2['admission_dt']));

					if (($row2['fatality']!='0000-00-00')&&(!(empty($row2['fatality']))))
						$fatality = date("m/d/Y",strtotime($row2['fatality']));

					$this->worksheet->write($this->temp, $cnt, $i, $this->format1);
					$this->worksheet->write($this->temp, $cnt+1, trim($row2['encounter_nr']), $this->format1);
					$this->worksheet->write($this->temp, $cnt+2, mb_strtoupper(trim($row2['patient_name'])), $this->format3);
					$this->worksheet->write($this->temp, $cnt+3, trim($admission_date), $this->format1);
					$this->worksheet->write($this->temp, $cnt+4, trim($row2['age']), $this->format1);
					$this->worksheet->write($this->temp, $cnt+5, strtoupper(trim($row2['sex'])), $this->format1);
					$this->worksheet->write($this->temp, $cnt+6, mb_strtoupper(trim($row2['address'])), $this->format3);
					$this->worksheet->write($this->temp, $cnt+7, trim($row2['if_dengue']), $this->format1);
					$this->worksheet->write($this->temp, $cnt+8, $fatality, $this->format3);
					$i++;
					$this->temp++;

					/*$this->Row(array($i,
													 trim($row2['encounter_nr']),
													 mb_strtoupper(trim($row2['patient_name'])),
													 trim($admission_date),
													 trim($row2['age']),
													 strtoupper(trim($row2['sex'])),
													 mb_strtoupper(trim($row2['address'])),
													 trim($row2['if_dengue']),
													 $fatality));
					$i++;  */
				}

				//return $row2;
			}

			function GetDiagnosisData(){
			global $db;
			$prev_diag = "";
			$cnt = 0;

			$sql = "SELECT DISTINCT ed.*
							FROM seg_rep_medrec_patient_icd_tbl AS ed
							LEFT JOIN care_icd10_en AS c ON c.diagnosis_code=ed.icd
							WHERE DATE(ed.admission_dt) BETWEEN  '".$this->from."' AND '".$this->to."'
							".$this->icd_cond."
							".$this->type_cond."
							AND ed.encounter_type IN (3,4)
							ORDER BY ed.icd, DATE(ed.admission_dt) ASC, ed.patient_name";

			#echo $sql;
			$result = $db->Execute($sql);
			$this->count = $result->RecordCount();
			if($this->count==0){
				$this->worksheet->write($this->temp, 0, "No records found for this report....", $this->format4);
			}
			if($result){
				while($rows = $result->FetchRow()){

				#echo $row['ICD'];
				$diag = $rows['icd'];
				$desc = $rows['icd_desc'];

					if($prev_diag != $diag){
						$this->worksheet->write($this->temp, 0, "Diagnosis :  ".$diag, $this->format4);
						$this->worksheet->write($this->temp, 2, "Description :  ".$desc, $this->format4);
						#$this->worksheet->write($this->temp, 7, "sclass :  ".$this->sclass, $this->format2);
						$this->temp++;

						$this->worksheet->write($this->temp, $cnt, $this->Headers[0], $this->format1);
						$this->worksheet->write($this->temp, $cnt+1, $this->Headers[1], $this->format1);
						$this->worksheet->write($this->temp, $cnt+2, $this->Headers[2], $this->format1);
						$this->worksheet->write($this->temp, $cnt+3, $this->Headers[3], $this->format1);
						$this->worksheet->write($this->temp, $cnt+4, $this->Headers[4], $this->format1);
						$this->worksheet->write($this->temp, $cnt+5, $this->Headers[5], $this->format1);
						$this->worksheet->write($this->temp, $cnt+6, $this->Headers[6], $this->format1);
						$this->worksheet->write($this->temp, $cnt+7, $this->Headers[7], $this->format1);
						$this->worksheet->write($this->temp, $cnt+8, $this->Headers[8], $this->format1);
						$this->temp++;

						$row2 = $this->GetDiagnosisOccurrence($diag, $desc);
						$prev_diag = $diag;
						$this->temp += 2;
					}
				}
			}

		}

			/*function GetDiagnosisOccurrence($diag, $desc){
				global $db;
				$this->count_rows = 0;
				$sql2 = "SELECT e.encounter_nr AS Case_No,
								CONCAT(IF (trim(p.name_last) IS NULL,'',trim(p.name_last)),', ' ,IF(trim(p.name_first) IS NULL ,' ',trim(p.name_first)),' ', IF(trim(p.name_middle) IS NULL,' ',trim(p.name_middle))) AS FullName,
								e.admission_dt AS Date_Admitted, IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age) AS Age,
								p.sex AS Sex,
								CONCAT(IF (trim(p.street_name) IS NULL,'',trim(p.street_name)),' ',
											IF (trim(sb.brgy_name) IS NULL,'',trim(sb.brgy_name)),' ',
											IF (trim(sm.mun_name) IS NULL,'',trim(sm.mun_name)),' ',
											IF (trim(sm.zipcode) IS NULL,'',trim(sm.zipcode)),' ',
											IF (trim(sp.prov_name) IS NULL,'',trim(sp.prov_name)),' ',
											IF (trim(sr.region_name) IS NULL,'',trim(sr.region_name))) AS Address,
								'' AS If_Dengue, IF(p.death_date!='0000-00-00' AND (p.death_encounter_nr=e.encounter_nr),p.death_date,'') AS Fatality,
								e.discharge_date AS 'Discharged Date'
								FROM care_encounter AS e
								INNER JOIN care_person AS p ON p.pid=e.pid
								INNER JOIN care_encounter_diagnosis AS ed ON ed.encounter_nr=e.encounter_nr
								INNER JOIN care_icd10_en AS c ON c.diagnosis_code=ed.code
								LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=p.brgy_nr
								LEFT JOIN seg_municity AS sm ON sm.mun_nr=p.mun_nr
								LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
								LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
								WHERE DATE(e.admission_dt) BETWEEN '".$this->from."' AND '".$this->to."'
								AND e.status NOT IN ('deleted','hidden','inactive','void')
								AND ed.status NOT IN ('deleted','hidden','inactive','void')
								AND ed.code = '".$diag."'
								ORDER BY DATE(e.admission_dt) ASC, p.name_last, p.name_first, p.name_middle";
				$result2 = $db->Execute($sql2);
				$this->count_rows = $result2->RecordCount();
				while($row2 = $result2->FetchRow()){
						$this->worksheet->write($this->temp, 0, $row2['Case_No'], $this->format1);
						$this->worksheet->write($this->temp, 1, $row2['FullName'], $this->format1);
						$this->worksheet->write($this->temp, 2, $row2['Date_Admitted'], $this->format1);
						$this->worksheet->write($this->temp, 3, $row2['Age'], $this->format1);
						$this->worksheet->write($this->temp, 4, $row2['Sex'], $this->format1);
						$this->worksheet->write($this->temp, 5, $row2['Address'], $this->format1);
						$this->worksheet->write($this->temp, 6, $row2['If_Dengue'], $this->format1);
						$this->worksheet->write($this->temp, 7, $row2['Fatality'], $this->format1);
						$this->temp++;
				}
				$this->temp += 2;
			}              */
			function FetchData(){
					global $db;

					$sql="SELECT ed.code_parent,ed.code AS ICD, c.description AS ICD_Description, e.encounter_nr AS Case_No,
						CONCAT(IF (trim(p.name_last) IS NULL,'',trim(p.name_last)),', ' ,IF(trim(p.name_first) IS NULL ,' ',trim(p.name_first)),' ', IF(trim(p.name_middle) IS NULL,' ',trim(p.name_middle))) AS FullName,
						e.admission_dt AS Date_Admitted, IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age) AS Age,
						p.sex AS Sex,
						CONCAT(IF (trim(p.street_name) IS NULL,'',trim(p.street_name)),' ',
									IF (trim(sb.brgy_name) IS NULL,'',trim(sb.brgy_name)),' ',
									IF (trim(sm.mun_name) IS NULL,'',trim(sm.mun_name)),' ',
									IF (trim(sm.zipcode) IS NULL,'',trim(sm.zipcode)),' ',
									IF (trim(sp.prov_name) IS NULL,'',trim(sp.prov_name)),' ',
									IF (trim(sr.region_name) IS NULL,'',trim(sr.region_name))) AS Address,
						'' AS If_Dengue, IF(p.death_date!='0000-00-00' AND (p.death_encounter_nr=e.encounter_nr),p.death_date,'') AS Fatality,
						e.discharge_date AS 'Discharged Date'
						FROM care_encounter AS e
						INNER JOIN care_person AS p ON p.pid=e.pid
						INNER JOIN care_encounter_diagnosis AS ed ON ed.encounter_nr=e.encounter_nr
						INNER JOIN care_icd10_en AS c ON c.diagnosis_code=ed.code
						LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=p.brgy_nr
						LEFT JOIN seg_municity AS sm ON sm.mun_nr=p.mun_nr
						LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
						LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
						WHERE DATE(e.admission_dt) BETWEEN '".$this->from."' AND '".$this->to."'
						AND e.status NOT IN ('deleted','hidden','inactive','void')
						AND ed.status NOT IN ('deleted','hidden','inactive','void')
						".$this->icd_cond."
						ORDER BY ed.code, DATE(e.admission_dt) ASC, p.name_last, p.name_first, p.name_middle";
					$result = $db->Execute($sql);
					#echo $sql;
					$this->count = $result->RecordCount();
					while($rows = $result->FetchRow()){
						$diag = $rows['ICD'];
						$desc = $rows['ICD_Description'];
						#echo $sql;
						if($prev_diag != $diag){
							$this->worksheet->write($this->temp, 0, "Diagnosis:", $this->format2);
							$this->worksheet->write($this->temp, 1, $diag, $this->format2);
							$this->worksheet->write($this->temp, 2, "Description:", $this->format2);
							$this->worksheet->write($this->temp, 3, $desc, $this->format2);

							$this->temp++;
							$this->worksheet->setColumn($this->temp, 0, $this->ColumnWidth[0]);
							$this->worksheet->write($this->temp, 0, $this->Headers[0], $this->format1);
							$this->worksheet->setColumn($this->temp, 1, $this->ColumnWidth[1]);
							$this->worksheet->write($this->temp, 1, $this->Headers[1], $this->format1);
							$this->worksheet->setColumn($this->temp, 2, $this->ColumnWidth[2]);
							$this->worksheet->write($this->temp, 2, $this->Headers[2], $this->format1);
							$this->worksheet->setColumn($this->temp, 3, $this->ColumnWidth[3]);
							$this->worksheet->write($this->temp, 3, $this->Headers[3], $this->format1);
							$this->worksheet->setColumn($this->temp, 4, $this->ColumnWidth[4]);
							$this->worksheet->write($this->temp, 4, $this->Headers[4], $this->format1);
							$this->worksheet->setColumn($this->temp, 5, $this->ColumnWidth[5]);
							$this->worksheet->write($this->temp, 5, $this->Headers[5], $this->format1);
							$this->worksheet->setColumn($this->temp, 6, $this->ColumnWidth[6]);
							$this->worksheet->write($this->temp, 6, $this->Headers[6], $this->format1);
							$this->worksheet->setColumn($this->temp, 7, $this->ColumnWidth[7]);
							$this->worksheet->write($this->temp, 7, $this->Headers[7], $this->format1);
							$this->temp++;
							$row2 = $this->GetDiagnosisOccurrence($diag, $desc);
							$prev_diag = $diag;
						}

					}
			}
	}

	$rep = new SentinelSurveillance($_GET['from'],$_GET['to'],$_GET['icd'], $_GET['sclass']);
	$rep->ExcelHeader();
	$rep->GetDiagnosisData();
	#$rep->FetchData();
	$rep->send('excel_rep_sentinel_surveillance.xls');
	$rep->close();
?>