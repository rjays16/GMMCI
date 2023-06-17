<?php

	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	require_once($root_path."/classes/excel/Writer.php");
	require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
	require_once($root_path.'include/care_api_classes/class_department.php');

	class ExcelGen_ResultOfTreatment_Discharge extends Spreadsheet_Excel_Writer
	{
			var $worksheet;
			var $Headers;
			var $format1, $format1a, $format2, $format3;

			var $fromdate;
			var $todate;
			var $count;

			function ExcelGen_ResultOfTreatment_Discharge($fromdate, $todate)
			{
					$this->Spreadsheet_Excel_Writer();
					$this->worksheet = & $this->addWorksheet();
					$this->worksheet->setPaper(1);      // Letter
					$this->worksheet->setLandscape();
					$this->worksheet->setMarginTop(1.8);
					$this->worksheet->setMarginLeft(0.3);
					$this->worksheet->setMarginRight(0.3);
					$this->worksheet->setMarginBottom(0.3);
					$this->Headers = array(
								'Result of Treatment/Condition on Discharge'  ,
								'Recovered', 'Improved', 'Unimproved', 'Died',
								'Disch', 'Tran', 'Hama', 'Abs', 'Total',
								'<48', '>=48',
								'Types of Services', 'Grand Total'
						);
					$this->format1=& $this->addFormat();
					$this->format1->setSize(9);
					$this->format1->setBold();
					$this->format1->setAlign('center');
					$this->format1a=& $this->addFormat();
					$this->format1a->setSize(9);
					$this->format1a->setBold();
					$this->format1a->setAlign('center');
					$this->format1a->setTextWrap(1);
					$this->format2=& $this->addFormat();
					$this->format2->setSize(8);
					$this->format2->setAlign('left');
					$this->format2->setTextWrap(1);
					$this->format3=& $this->addFormat();
					$this->format3->setSize(9);
					$this->format3->setAlign('center');

					if ($fromdate) $this->fromdate=date("Y-m-d",strtotime($fromdate));
					if ($todate) $this->todate=date("Y-m-d",strtotime($todate));
			}

			function ExcelHeader()
			{
					$dept_obj = new Department();
					$objInfo = new Hospital_Admin();

					if ($row = $objInfo->getAllHospitalInfo()) {
						$row['hosp_agency'] = strtoupper($row['hosp_agency']);
						$row['hosp_name']   = strtoupper($row['hosp_name']);
					}
					else {
						$row['hosp_country'] = "Republic of the Philippines";
						$row['hosp_agency']  = "PROVINCE OF BUKIDNON";
						$row['hosp_name']    = "Bukidnon Provincial Hospital - Malaybalay";
						$row['hosp_addr1']   = "Malaybalay, Bukidnon";
					}
					if ($this->fromdate==$this->todate)
						$text = "For ".date("F j, Y",strtotime($this->fromdate));
					else
						$text = "From ".date("F j, Y",strtotime($this->fromdate))." To ".date("F j, Y",strtotime($this->todate));

					if ($this->dept_nr){
						$deptinfo = $dept_obj->getDeptAllInfo($this->dept_nr);
						$deptname = $deptinfo['name_formal'];
					}else
						$deptname = "All Department";

					$this->worksheet->setHeader("".$row['hosp_country']."\n".$row['hosp_agency']."\n".$row['hosp_name']."\n".$row['hosp_addr1']."\n\nRESULT OF TREATMENT/CONDITION ON DISCHARGE\n".$text."\n\n",0.5);

					$this->worksheet->setColumn(0, 0, 15);
					$this->worksheet->setColumn(0, 1, 5);
					$this->worksheet->setColumn(0, 18, 5);
					$this->worksheet->setColumn(0, 19, 10);
					$this->worksheet->write(0, 8, $this->Headers[0], $this->format1);
					$this->worksheet->write(1, 3, $this->Headers[1], $this->format1);
					$this->worksheet->write(1, 8, $this->Headers[2], $this->format1);
					$this->worksheet->write(1, 13, $this->Headers[3], $this->format1);
					$this->worksheet->write(1, 17, $this->Headers[4], $this->format1);
					$this->worksheet->write(2, 0, $this->Headers[12], $this->format1);
					$this->worksheet->write(2, 1, $this->Headers[5], $this->format1);
					$this->worksheet->write(2, 2, $this->Headers[6], $this->format1);
					$this->worksheet->write(2, 3, $this->Headers[7], $this->format1);
					$this->worksheet->write(2, 4, $this->Headers[8], $this->format1);
					$this->worksheet->write(2, 5, $this->Headers[9], $this->format1);
					$this->worksheet->write(2, 6, $this->Headers[5], $this->format1);
					$this->worksheet->write(2, 7, $this->Headers[6], $this->format1);
					$this->worksheet->write(2, 8, $this->Headers[7], $this->format1);
					$this->worksheet->write(2, 9, $this->Headers[8], $this->format1);
					$this->worksheet->write(2, 10, $this->Headers[9], $this->format1);
					$this->worksheet->write(2, 11, $this->Headers[5], $this->format1);
					$this->worksheet->write(2, 12, $this->Headers[6], $this->format1);
					$this->worksheet->write(2, 13, $this->Headers[7], $this->format1);
					$this->worksheet->write(2, 14, $this->Headers[8], $this->format1);
					$this->worksheet->write(2, 15, $this->Headers[9], $this->format1);
					$this->worksheet->write(2, 16, $this->Headers[10], $this->format1);
					$this->worksheet->write(2, 17, $this->Headers[11], $this->format1);
					$this->worksheet->write(2, 18, $this->Headers[9], $this->format1);
					$this->worksheet->write(2, 19, $this->Headers[13], $this->format1);
			}

			function FetchData()
			{
					global $db;
					if ($this->from) {
						$where[]="DATE(discharge_date) BETWEEN '".$this->from."' AND '".$this->to."'";
					}

					if ($where)
						$whereSQL = "AND (".implode(") AND (",$where).")";

					 $sql = "SELECT d.name_formal AS Type_Of_Service,
							SUM(CASE WHEN p.result_code=5 AND p.disp_code = 7 then 1 else 0 end) AS rec_disch,
							SUM(CASE WHEN p.result_code=5 AND p.disp_code = 8 then 1 else 0 end) AS rec_trans,
							SUM(CASE WHEN p.result_code=5 AND p.disp_code = 9 then 1 else 0 end) AS rec_hama,
							SUM(CASE WHEN p.result_code=5 AND p.disp_code = 10 then 1 else 0 end) AS rec_absc,
							SUM(CASE WHEN (p.result_code=5 AND(p.disp_code = 7 OR p.disp_code = 8
									OR p.disp_code = 9 OR p.disp_code = 10)) then 1 else 0 end) AS total_rec,

							SUM(CASE WHEN p.result_code=6 AND p.disp_code = 7 then 1 else 0 end) AS imp_disch,
							SUM(CASE WHEN p.result_code=6 AND p.disp_code = 8 then 1 else 0 end) AS imp_trans,
							SUM(CASE WHEN p.result_code=6 AND p.disp_code = 9 then 1 else 0 end) AS imp_hama,
							SUM(CASE WHEN p.result_code=6 AND p.disp_code = 10 then 1 else 0 end) AS imp_absc,
							SUM(CASE WHEN (p.result_code=6 AND(p.disp_code = 7
									OR p.disp_code = 8 OR p.disp_code = 9 OR p.disp_code = 10)) then 1 else 0 end) AS total_imp,

							SUM(CASE WHEN p.result_code=7 AND p.disp_code = 7 then 1 else 0 end) AS unimp_disch,
							SUM(CASE WHEN p.result_code=7 AND p.disp_code = 8 then 1 else 0 end) AS unimp_trans,
							SUM(CASE WHEN p.result_code=7 AND p.disp_code = 9 then 1 else 0 end) AS unimp_hama,
							SUM(CASE WHEN p.result_code=7 AND p.disp_code = 10 then 1 else 0 end) AS unimp_absc,
							SUM(CASE WHEN (p.result_code=7 AND(p.disp_code = 7 OR p.disp_code = 8
									OR p.disp_code = 9 OR p.disp_code = 10)) then 1 else 0 end) AS total_unimp,

							SUM(CASE WHEN (p.result_code=8
									AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),p.age))<48)
									THEN 1 ELSE 0 END) AS deathbelow48,
							SUM(CASE WHEN (p.result_code=8
									AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),p.age))>=48)
									THEN 1 ELSE 0 END) AS deathabove48,
							SUM(CASE WHEN (p.result_code=8
									AND ((floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),p.age))<48)
									OR (floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),p.age))>=48)))
									THEN 1 ELSE 0 END) AS total_death

				FROM seg_rep_medrec_patient_icd_tbl AS p
				LEFT JOIN care_department AS d ON d.nr=p.current_dept_nr
				WHERE  p.encounter_type IN (3,4)
				AND p.discharge_date IS NOT NULL
				$whereSQL
				GROUP BY d.name_formal";

						$result=$db->Execute($sql);
						if ($result)
						{
								$this->count = $result->RecordCount();
								$newrow=3;
								while ($row=$result->FetchRow())
								{
									$col=0;
									$total =  $row['total_rec'] +  $row['total_imp'] +  $row['total_unimp'] +  $row['total_death'];
									$this->worksheet->write($newrow, $col, $row['Type_Of_Service'], $this->format2);
									$this->worksheet->write($newrow, $col+1, $row['rec_disch'], $this->format3);
									$this->worksheet->write($newrow, $col+2, $row['rec_trans'], $this->format3);
									$this->worksheet->write($newrow, $col+3, $row['rec_hama'], $this->format3);
									$this->worksheet->write($newrow, $col+4, $row['rec_absc'], $this->format3);
									$this->worksheet->write($newrow, $col+5, $row['total_rec'], $this->format3);
									$this->worksheet->write($newrow, $col+6, $row['imp_disch'], $this->format3);
									$this->worksheet->write($newrow, $col+7, $row['imp_trans'], $this->format3);
									$this->worksheet->write($newrow, $col+8, $row['imp_hama'], $this->format3);
									$this->worksheet->write($newrow, $col+9, $row['imp_absc'], $this->format3);
									$this->worksheet->write($newrow, $col+10, $row['total_imp'], $this->format3);
									$this->worksheet->write($newrow, $col+11, $row['unimp_disch'], $this->format3);
									$this->worksheet->write($newrow, $col+12, $row['unimp_trans'], $this->format3);
									$this->worksheet->write($newrow, $col+13, $row['unimp_hama'], $this->format3);
									$this->worksheet->write($newrow, $col+14, $row['unimp_absc'], $this->format3);
									$this->worksheet->write($newrow, $col+15, $row['total_unimp'], $this->format3);
									$this->worksheet->write($newrow, $col+16, $row['deathbelow48'], $this->format3);
									$this->worksheet->write($newrow, $col+17, $row['deathabove48'], $this->format3);
									$this->worksheet->write($newrow, $col+18, $row['total_death'], $this->format3);
									$this->worksheet->write($newrow, $col+19, $total, $this->format3);
									$newrow++;
								}
						}
			}

			function AfterData()
			{
					if (!$this->count)
					{
						$this->worksheet->write(3, 0, "No records found for this report...");
					}
			}
	}

	$rep = new ExcelGen_ResultOfTreatment_Discharge($_GET['from'], $_GET['to']);
	$rep->ExcelHeader();
	$rep->FetchData();
	$rep->AfterData();
	$rep->send('rep_result_discharge.xls');
	$rep->close();
?>