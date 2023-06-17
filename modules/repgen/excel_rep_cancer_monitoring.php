<?php
	//Created by Cherry 07-30-09
	//Cancer/Tumor Monitoring (Excel)
	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	require_once($root_path."/classes/excel/Writer.php");
	require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

	class Excel_Cancer_Monitoring extends Spreadsheet_Excel_Writer{
		var $from, $to;
		var $tmp=0;

				function Excel_Cancer_Monitoring($from, $to, $code, $sclass){
					$this->Spreadsheet_Excel_Writer();
					$this->worksheet = & $this->addWorksheet();
					$this->worksheet->setPaper(5);      // Legal
					$this->worksheet->setLandscape();
					$this->worksheet->setMarginTop(2);
					$this->worksheet->setMarginLeft(0.3);
					$this->worksheet->setMarginRight(0.1);
					$this->worksheet->setMarginBottom(0.3);
					$this->Headers = array('#','CASE NO', 'DATE ADMITTED', "PATIENT'S NAME", 'AGE', 'SEX', 'ADDRESS',
																'DIAGNOSIS','ICD CODE', 'ICD DESCRIPTION', 'RESULT', 'PHYSICIAN');
					$this->ColumnWidth = array(5,10, 15, 20, 5, 5, 30, 20, 8, 20, 10, 15);

					//format for table headers
					$this->format1=& $this->addFormat();
					$this->format1->setSize(9);
					$this->format1->setBold();
					$this->format1->setAlign('center');

					//format for data
					$this->format2=& $this->addFormat();
					$this->format2->setSize(8);
					$this->format2->setAlign('left');
					$this->format2->setTextWrap(1);

					$this->format3=& $this->addFormat();
					$this->format3->setSize(8);
					$this->format3->setAlign('center');
					$this->format3->setTextWrap(1);

					$this->format4=& $this->addFormat();
					$this->format4->setSize(10);
					$this->format4->setBold();
					$this->format4->setAlign('left');


					if($from) $this->from=$from;
					if($to) $this->to=$to;
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

					if($this->from == $this->to){
						$text = "As of ".date("F j, Y", strtotime($this->from));
					}else{
						$text = "From ".date("F j, Y",strtotime($this->from))." To ".date("F j, Y",strtotime($this->to));
					}

					$this->worksheet->setHeader("".$row['hosp_country']."\n".$row['hosp_agency']."\n".$row['hosp_name']."\n".$row['hosp_addr1']."\nCANCER/TUMOR MONITORING\n".$text."\n\n".$text2."\n",0.5);

					//print table
					$tablerow = 0;
					$this->worksheet->setColumn($tablerow, 0, $this->ColumnWidth[0]);
					$this->worksheet->write($tablerow, 0, $this->Headers[0], $this->format1);
					$this->worksheet->setColumn($tablerow, 1, $this->ColumnWidth[1]);
					$this->worksheet->write($tablerow, 1, $this->Headers[1], $this->format1);
					$this->worksheet->setColumn($tablerow, 2, $this->ColumnWidth[2]);
					$this->worksheet->write($tablerow, 2, $this->Headers[2], $this->format1);
					$this->worksheet->setColumn($tablerow, 3, $this->ColumnWidth[3]);
					$this->worksheet->write($tablerow, 3, $this->Headers[3], $this->format1);
					$this->worksheet->setColumn($tablerow, 4, $this->ColumnWidth[4]);
					$this->worksheet->write($tablerow, 4, $this->Headers[4], $this->format1);
					$this->worksheet->setColumn($tablerow, 5, $this->ColumnWidth[5]);
					$this->worksheet->write($tablerow, 5, $this->Headers[5], $this->format1);
					$this->worksheet->setColumn($tablerow, 6, $this->ColumnWidth[6]);
					$this->worksheet->write($tablerow, 6, $this->Headers[6], $this->format1);
					$this->worksheet->setColumn($tablerow, 7, $this->ColumnWidth[7]);
					$this->worksheet->write($tablerow, 7, $this->Headers[7], $this->format1);
					$this->worksheet->setColumn($tablerow, 8, $this->ColumnWidth[8]);
					$this->worksheet->write($tablerow, 8, $this->Headers[8], $this->format1);
					$this->worksheet->setColumn($tablerow, 9, $this->ColumnWidth[9]);
					$this->worksheet->write($tablerow, 9, $this->Headers[9], $this->format1);
					$this->worksheet->setColumn($tablerow, 10, $this->ColumnWidth[10]);
					$this->worksheet->write($tablerow, 10, $this->Headers[10], $this->format1);
					$this->worksheet->setColumn($tablerow, 11, $this->ColumnWidth[11]);
					$this->worksheet->write($tablerow, 11, $this->Headers[11], $this->format1);

			}

			function FetchData()
			{
					global $db;

			$sql = "SELECT DISTINCT ed.*
						FROM seg_rep_medrec_patient_icd_tbl AS ed
						WHERE DATE(ed.admission_dt) BETWEEN  '".$this->from."' AND '".$this->to."'
						".$this->type_cond."
						AND ed.encounter_type IN (3,4)
						AND ed.icd IN (SELECT diagnosis_code FROM care_icd10_en WHERE diagnosis_code LIKE 'c%' OR diagnosis_code LIKE 'd%' OR diagnosis_code LIKE '%/%')
						ORDER BY ed.patient_name, ed.icd, DATE(ed.admission_dt) ASC";

			#echo "sql = ".$sql;
			$result=$db->Execute($sql);
			$this->count = $result->RecordCount();
			if($this->count==0){
				$this->worksheet->write($this->tmp, 0, "No records found for this report....", $this->format4);
			}

			if ($result) {
			$this->_count = $result->RecordCount();
			$this->Data=array();
			$i=1;
			$temp = 1;
			while ($row=$result->FetchRow()) {
				if ((!(empty($row['discharge_date']))) || ($row['discharge_date']!='0000-00-00'))
					$discharged_date = date("m/d/Y",strtotime($row['discharge_date']));

				if ((!(empty($row['admission_dt']))) || ($row['admission_dt']!='0000-00-00'))
					$admission_date = date("m/d/Y h:i A",strtotime($row['admission_dt']));

				if ((!(empty($row['fatality']))) || ($row['fatality']!='0000-00-00'))
					$fatality = date("m/d/Y",strtotime($row['fatality']));

				$sql3 = "SELECT ser.encounter_nr,
										SUBSTRING(MAX(CONCAT(ser.modify_time,ser.result_code)),20,1) AS result_code
										FROM seg_encounter_result AS ser
										WHERE ser.encounter_nr='".$row['encounter_nr']."'
										GROUP BY ser.encounter_nr";

					$result3 = $db->Execute($sql3);
					if (is_object($result3)){
						$row3 = $result3->FetchRow();
					}

					$sql4 = "SELECT * FROM seg_results WHERE result_code='".$row3[result_code]."'";
					$result4 = $db->Execute($sql4);
					if (is_object($result4)){
						$row_rem = $result4->FetchRow();
						$remarks = trim($row_rem['result_desc']);
					}

					$this->worksheet->write($temp, 0, $i, $this->format2);
					$this->worksheet->write($temp, 1, trim($row['encounter_nr']), $this->format2);
					$this->worksheet->write($temp, 2, trim($admission_date), $this->format2);
					$this->worksheet->write($temp, 3, mb_strtoupper(trim($row['patient_name'])), $this->format2);
					$this->worksheet->write($temp, 4, trim($row['age']), $this->format3);
					$this->worksheet->write($temp, 5, strtoupper(trim($row['sex'])), $this->format3);
					$this->worksheet->write($temp, 6, mb_strtoupper(trim($row['address'])), $this->format2);
					$this->worksheet->write($temp, 7, trim($row['diagnosis']), $this->format2);
					$this->worksheet->write($temp, 8, trim($row['icd']), $this->format3);
					$this->worksheet->write($temp, 9, trim($row['icd_desc']), $this->format2);
					$this->worksheet->write($temp, 10, $remarks, $this->format3);
					$this->worksheet->write($temp, 11, mb_strtoupper(trim($row['physician'])), $this->format2);
					$temp++;
					$i++;
				}
		}

			}
	}

	$from = $_GET['from'];
	$to = $_GET['to'];
	$code = $_GET['icd'];

	$rep = new Excel_Cancer_Monitoring($from, $to, $code, $_GET['sclass']);
	$rep->ExcelHeader();
	$rep->FetchData();
	$rep->send('excel_rep_cancer_monitoring.xls');
	$rep->close();
?>
