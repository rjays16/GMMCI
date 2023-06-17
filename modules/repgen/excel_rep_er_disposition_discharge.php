<?php
	#created by CHA 07-21-09
	#Research and Query Report- for DMC

	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	require_once($root_path."/classes/excel/Writer.php");
	require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
	require_once($root_path.'include/care_api_classes/class_department.php');

	class ExcelGen_Disposition_Discharge extends Spreadsheet_Excel_Writer
	{
			var $worksheet;
			var $Headers;
			var $format1, $format1a, $format2, $format3;

			var $fromdate;
			var $todate;

			var $cur_row;
			var $count;
			var $dept_nr;

			function ExcelGen_Disposition_Discharge($from, $to, $sclass)
			{
					$this->Spreadsheet_Excel_Writer();
					$this->worksheet = & $this->addWorksheet();
					$this->worksheet->setPaper(5);      // Legal
					$this->worksheet->setLandscape();
					$this->worksheet->setMarginTop(1.8);
					$this->worksheet->setMarginLeft(1);
					$this->worksheet->setMarginRight(1);
					$this->worksheet->setMarginBottom(0.3);
					$this->Headers = array(
							' DISPOSITION  ',
							'TYPE OF SERVICES', 'ABSCONDED', 'HAMA', 'THOC', 'DISCHARGE', 'TOTAL',
							'                    DEATHS                    ', '                    ERWD                    ', 'DOA', '<48', '>=48', 'TOTAL',
							'OVER - ALL TOTAL'
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

					if ($sclass=='primary'){
						$this->type_cond = " AND p.type_nr='1' ";
						$this->sclass_label = "Primary";
					}elseif ($sclass=='secondary'){
						$this->type_cond = " AND p.type_nr='0' ";
						$this->sclass_label = "Secondary";
					}elseif ($sclass=='all'){
						$this->type_cond = "";
						$this->sclass_label = "All";
					}

					#if ($fromdate) $this->fromdate=date("Y-m-d",strtotime($fromdate));
					#if ($todate) $this->todate=date("Y-m-d",strtotime($todate));
					if ($from) $this->from=date("Y-m-d",strtotime($from));
					if ($to) $this->to=date("Y-m-d",strtotime($to));
					if($dept_nr) $this->dept_nr=$dept_nr;
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
						$row['hosp_name']    = "BPH - Malaybalay";
						$row['hosp_addr1']   = "Malaybalay, Bukidnon";
					}
					if ($this->from==$this->to)
						$text = "For ".date("F j, Y",strtotime($this->from));
					else
						$text = "From ".date("F j, Y",strtotime($this->from))." To ".date("F j, Y",strtotime($this->to));

					if ($this->dept_nr){
						#$deptinfo = $dept_obj->getDeptAllInfo($this->dept_nr);
						#$deptname = $deptinfo['name_formal'];
						if ($this->dept_nr==1)
								$deptname = "Gynecology";
						elseif ($this->dept_nr==2)
								$deptname = "Medicines";
						elseif ($this->dept_nr==3)
								$deptname = "Obstetrics";
						elseif ($this->dept_nr==4)
								$deptname = "Pediatrics";
						elseif ($this->dept_nr==5)
								$deptname = "Surgery";
						elseif ($this->dept_nr==6)
								$deptname = "ENT";
					}else
						$deptname = "All Department";

					$label_dept = "";
					if ($this->dept_nr)
						$label_dept = '('.mb_strtoupper($deptname).')';
					$deptname = mb_strtoupper($deptname);

					$this->worksheet->setHeader("".$row['hosp_country']."\n".$row['hosp_agency']."\n".$row['hosp_name']."\n".$row['hosp_addr1']."\n\nER DISPOSITION ON DISCHARGE".$label_dept."\n".$text."\n",0.5);

					$this->worksheet->setColumn(0, 0, 30);
					$this->worksheet->setColumn(0, 1, 12);
					$this->worksheet->setColumn(0, 2, 12);
					$this->worksheet->setColumn(0, 3, 15);
					$this->worksheet->setColumn(0, 4, 12);
					$this->worksheet->setColumn(0, 5, 12);
					$this->worksheet->setColumn(0, 6, 8);
					$this->worksheet->setColumn(0, 7, 8);
					$this->worksheet->setColumn(0, 8, 8);
					$this->worksheet->setColumn(0, 9, 8);
					$this->worksheet->setColumn(0, 10, 15);

					$this->worksheet->write(0, 3, $this->Headers[0], $this->format1);
					$this->worksheet->write(1, 7.5, $this->Headers[7],$this->format1);
					$this->worksheet->write(2, 0, $this->Headers[1], $this->format1);
					$this->worksheet->write(2, 1, $this->Headers[2], $this->format1);
					$this->worksheet->write(2, 2, $this->Headers[3], $this->format1);
					$this->worksheet->write(2, 3, $this->Headers[4], $this->format1);
					$this->worksheet->write(2, 4, $this->Headers[5], $this->format1);
					$this->worksheet->write(2, 5, $this->Headers[6], $this->format1);
					$this->worksheet->write(2, 7.5, $this->Headers[8], $this->format1);
					$this->worksheet->write(2, 10, $this->Headers[13], $this->format1a);
					$this->worksheet->write(3, 6, $this->Headers[9], $this->format1);
					$this->worksheet->write(3, 7, $this->Headers[10], $this->format1);
					$this->worksheet->write(3, 8, $this->Headers[11], $this->format1);
					$this->worksheet->write(3, 9, $this->Headers[12], $this->format1);



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
							SUM(CASE WHEN p.disp_code = 5 then 1 else 0 end) AS absconded,
							SUM(CASE WHEN p.disp_code = 4 then 1 else 0 end) AS hama,
							SUM(CASE WHEN ((p.disp_code = 3) || (p.disp_code = 1)) then 1 else 0 end) AS transferred,
							SUM(CASE WHEN p.disp_code = 2 then 1 else 0 end) AS discharge,
							SUM(CASE WHEN ((p.disp_code = 5)||(p.disp_code = 4)
															||((p.disp_code = 3) || (p.disp_code = 1))||(p.disp_code = 2)) then 1 else 0 end) AS total_disp,

							SUM(CASE WHEN is_DOA=1 then 1 else 0 end) AS doa,
							SUM(CASE WHEN (p.result_code=8
									AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),p.age))<48)
									THEN 1 ELSE 0 END) AS deathbelow48,
							SUM(CASE WHEN (p.result_code=8
									AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),p.age))>=48)
									THEN 1 ELSE 0 END) AS deathabove48,
							SUM(CASE WHEN ((is_DOA=1) OR (p.result_code=8
									AND ((floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),p.age))<48)
									OR (floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),p.age))>=48))))
									THEN 1 ELSE 0 END) AS total_death

				FROM seg_rep_medrec_patient_icd_tbl AS p
				LEFT JOIN care_department AS d ON d.nr=p.current_dept_nr
				WHERE  p.encounter_type IN (1)
				AND p.encounter_class_nr IN (1)
				AND p.encounter_status NOT IN ('direct_admission')
				AND p.discharge_date IS NOT NULL
				$whereSQL
				GROUP BY d.name_formal";

						$result=$db->Execute($sql);
							if ($result) {
							$this->count = $result->RecordCount();
							$i=1;
							$newrow=4;
							$col=0;
								while ($row=$result->FetchRow())
								{
									$total =  $row['total_disp'] +  $row['total_death'];

								 $this->worksheet->write($newrow, $col, $row['Type_Of_Service'], $this->format2);
								 $this->worksheet->write($newrow, $col+1, $row['absconded'], $this->format3);
								 $this->worksheet->write($newrow, $col+2, $row['hama'], $this->format3);
								 $this->worksheet->write($newrow, $col+3, $row['transferred'], $this->format3);
								 $this->worksheet->write($newrow, $col+4, $row['discharge'], $this->format3);
								 $this->worksheet->write($newrow, $col+5, $row['total_disp'], $this->format3);
								 $this->worksheet->write($newrow, $col+6, $row['doa'], $this->format3);
								 $this->worksheet->write($newrow, $col+7, $row['deathbelow48'], $this->format3);
								 $this->worksheet->write($newrow, $col+8, $row['deathabove48'], $this->format3);
								 $this->worksheet->write($newrow, $col+9, $row['total_death'], $this->format3);
								 $this->worksheet->write($newrow, $col+10, $total, $this->format3);

								$i++;
								$newrow++;
						}
						$this->cur_row=$newrow;
				 }
			}

			function AfterData()
			{
					if (!$this->count)
					{
						$this->worksheet->write(4, 0, "No records found for this report...");
					}
					/*else
					{
									$col=1;
									$this->worksheet->write($this->cur_row, $col, "Total =>", $this->format2);
									$this->worksheet->write($this->cur_row, $col+1, $this->t_male_below1, $this->format3);
									$this->worksheet->write($this->cur_row, $col+2, $this->t_female_below1, $this->format3);
									$this->worksheet->write($this->cur_row, $col+3, $this->t_male_1to4,$this->format3);
									$this->worksheet->write($this->cur_row, $col+4, $this->t_female_1to4, $this->format3);
									$this->worksheet->write($this->cur_row, $col+5, $this->t_male_5to9, $this->format3);
									$this->worksheet->write($this->cur_row, $col+6, $this->t_female_5to9, $this->format3);
									$this->worksheet->write($this->cur_row, $col+7, $this->t_male_10to14, $this->format3);
									$this->worksheet->write($this->cur_row, $col+8, $this->t_female_10to14, $this->format3);
									$this->worksheet->write($this->cur_row, $col+9, $this->t_male_15to19, $this->format3);
									$this->worksheet->write($this->cur_row, $col+10, $this->t_female_15to19, $this->format3);
									$this->worksheet->write($this->cur_row, $col+11, $this->t_male_20to44, $this->format3);
									$this->worksheet->write($this->cur_row, $col+12, $this->t_female_20to44, $this->format3);
									$this->worksheet->write($this->cur_row, $col+13, $this->t_male_45to59, $this->format3);
									$this->worksheet->write($this->cur_row, $col+14, $this->t_female_45to59, $this->format3);
									$this->worksheet->write($this->cur_row, $col+15, $this->t_male_60up, $this->format3);
									$this->worksheet->write($this->cur_row, $col+16, $this->t_female_60up, $this->format3);
									$this->worksheet->write($this->cur_row, $col+17, $this->t_male_total, $this->format3);
									$this->worksheet->write($this->cur_row, $col+18, $this->t_female_total, $this->format3);
									$this->worksheet->write($this->cur_row, $col+19, $this->t_total, $this->format3);
									$this->worksheet->write($this->cur_row, $col+20, "100%", $this->format3);
									$this->worksheet->write($this->cur_row, $col+21, "xxx", $this->format3);
							}                 */
			}
	}

	$rep = new ExcelGen_Disposition_Discharge($_GET['from'], $_GET['to'], $_GET['sclass']);
	$rep->ExcelHeader();
	$rep->FetchData();
	$rep->AfterData();
	$rep->send('rep_er_disposition_discharge.xls');
	$rep->close();
?>