<?php
	//Created by Cherry 12-03-10
	//Report of Discharges (Excel)
	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	require_once($root_path."/classes/excel/Writer.php");
	require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

	class Excel_Rep_Discharges extends Spreadsheet_Excel_Writer{
		var $fromdate;
		var $dept_nr;
		var $todate;
		var $modkey;
		var $modkey2;

				function Excel_Rep_Discharges($fromdate, $todate, $dept_nr, $modkey, $modkey2, $modkey3){
					$this->Spreadsheet_Excel_Writer();
					$this->worksheet = & $this->addWorksheet();
					$this->worksheet->setPaper(1);      // Letter
					$this->worksheet->setLandscape();
					$this->worksheet->setMarginTop(2);
					$this->worksheet->setMarginLeft(0.5);
					$this->worksheet->setMarginRight(0.5);
					$this->worksheet->setMarginBottom(0.3);
					$this->Headers = array('','Patient Name', 'HRN', 'Received', '', 'Case #', 'Admitted',
													'Discharged', 'Department/Serv', 'Sex', 'Result');
					$this->ColumnWidth = array(5, 25, 10, 10, 5, 10, 10, 10, 20, 5, 10);

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

					if ($fromdate) $this->fromdate=date("Y-m-d",strtotime($fromdate));
					if ($todate) $this->todate=date("Y-m-d",strtotime($todate));

					$this->dept_nr = $dept_nr;

					$this->modkey = $modkey;
					$this->modkey2 = $modkey2;
					$this->modkey3 = $modkey3;
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
					if ($this->fromdate==$this->todate)
						$text = "For ".date("F j, Y",strtotime($this->fromdate));
					else
						$text = "From ".date("F j, Y",strtotime($this->fromdate))." To ".date("F j, Y",strtotime($this->todate));

					$this->worksheet->setHeader("".$row['hosp_country']."\n".$row['hosp_agency']."\n".$row['hosp_name']."\n".$row['hosp_addr1']."\nREPORT OF DISCHARGES\n".$text."\n\n".$text2."\n",0.5);

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
			}

			function FetchData()
			{
					global $db;

					if (($this->fromdate)&&($this->todate)) {
						#$where[]="DATE(e.discharge_date)='$this->date'";
						#$whereSQL = " AND (e.discharge_date>='".$this->fromdate."' AND e.discharge_date<='".$this->todate."')";
						$where[]="DATE(e.discharge_date) BETWEEN '$this->fromdate' AND '$this->todate'";
					}

					if ($where)
						$whereSQL = "AND (".implode(") AND (",$where).")";

					$sql = "SELECT CONCAT(IF (trim(p.name_last) IS NULL,'',trim(p.name_last)),', ' ,IF(trim(p.name_first) IS NULL ,' ',trim(p.name_first)),' ', IF(trim(p.name_middle) IS NULL,' ',trim(p.name_middle))) AS patient_name,
								p.sex, e.pid, e.encounter_nr, e.admission_dt AS admission_date, e.discharge_date,
								IF(p.fromtemp, 'Newborn (Born Alive)', d.name_formal) AS department_name,
								SUBSTRING(MAX(CONCAT(er.create_time,er.encounter_nr,er.result_code)),30) AS result_code,
								e.received_date, ins.hcare_id, IF(ins.hcare_id=18,'P','NP') AS insurance
								FROM care_encounter AS e
								LEFT JOIN care_person AS p ON p.pid=e.pid
								INNER JOIN care_department AS d ON d.nr=e.current_dept_nr
								INNER JOIN seg_encounter_result AS er ON er.encounter_nr=e.encounter_nr
								LEFT JOIN seg_encounter_insurance AS ins ON ins.encounter_nr=e.encounter_nr
								WHERE e.encounter_type IN (3,4)
								AND e.status NOT IN ('deleted','hidden','inactive','void')
								AND e.discharge_date IS NOT NULL $whereSQL\n";

					 if ($this->dept_nr) {
						$sql .= " AND e.current_dept_nr=".$this->dept_nr;
					 }

					 if ($this->modkey){
						if ($this->modkey==1)
								 $sql .= " AND e.received_date IS NULL ";
						elseif ($this->modkey==2)
								 $sql .= " AND e.received_date IS NOT NULL ";
						/*
						elseif ($this->modkey==3)
								 $sql .= " AND result_desc='Died' ";
						elseif ($this->modkey==4)
								 $sql .= " AND result_desc!='Died' ";
						*/
					 }

					if ($this->modkey2){
							if ($this->modkey2==1)
									 $sql .= " AND result_code IN (4,8) ";
							elseif ($this->modkey2==2)
									 $sql .= " AND result_code NOT IN (4,8) ";

					}

					if ($this->modkey3){
							if ($this->modkey3==1)
									 $sql .= " AND ins.hcare_id=18 ";
							elseif ($this->modkey3==2)
									 $sql .= " AND (ins.hcare_id<>18 OR ins.hcare_id IS NULL) ";

					}

					$sql .= " GROUP BY er.encounter_nr ORDER BY department_name, DATE(discharge_date),patient_name";
					 $result=$db->Execute($sql);
					 if($result){
							 $this->_count = $result->RecordCount();
							 $i=1;
							 $temp=1;
							 while($row=$result->FetchRow()){

								 $received_date = "";
								 if (!(empty($row['received_date'])))
									$received_date = date("m/d/Y",strtotime($row['received_date']));
								 else
									$received_date = 'not yet';

									$sql_res = "SELECT result_desc FROM seg_results WHERE result_code='".$row['result_code']."'";
									$rs = $db->Execute($sql_res);
									$row_res= $rs->FetchRow();

									if ($row_res['result_desc'])
										$result_desc = $row_res['result_desc'];
									else
										$result_desc = 'Recovered';

										$this->worksheet->write($temp, 0, $i, $this->format3);
										$this->worksheet->write($temp, 1, mb_strtoupper($row['patient_name']), $this->format2);
										$this->worksheet->write($temp, 2, $row['pid'], $this->format3);
										$this->worksheet->write($temp, 3, $received_date, $this->format3);
										$this->worksheet->write($temp, 4, $row['insurance'], $this->format3);
										$this->worksheet->write($temp, 5, $row['encounter_nr'], $this->format3);
										$this->worksheet->write($temp, 6, date("m/d/Y",strtotime($row['admission_date'])), $this->format3);
										$this->worksheet->write($temp, 7, date("m/d/Y",strtotime($row['discharge_date'])), $this->format3);
										$this->worksheet->write($temp, 8, $row['department_name'], $this->format2);
										$this->worksheet->write($temp, 9, strtoupper($row['sex']), $this->format3);
										$this->worksheet->write($temp, 10, $result_desc, $this->format2);
										$temp++;
										$i++;
							 }
					 }



			}
	}
	$rep = new Excel_Rep_Discharges($_GET['from'],$_GET['to'], $_GET['dept_nr'], $_GET['modkey'], $_GET['modkey2'], $_GET['modkey3']);
	$rep->ExcelHeader();
	$rep->FetchData();
	ini_set('memory_limit', '2048M');
	$rep->send('excel_rep_discharges.xls');
	$rep->close();
?>
