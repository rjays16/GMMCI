<?php
	#created by CHA 07-21-09
	#Research and Query Report- for DMC

	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	require_once($root_path."/classes/excel/Writer.php");
	require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
	require_once($root_path.'include/care_api_classes/class_department.php');

	class ExcelGen_Causes_ERCases extends Spreadsheet_Excel_Writer
	{
			var $worksheet;
			var $Headers;
			var $format1, $format1a, $format2, $format3;

			var $from;
			var $to;

			var $t_male_below1, $t_female_below1, $t_male_1to4, $t_female_1to4;
			var $t_male_5to9, $t_female_5to9, $t_male_10to14, $t_female_10to14;
			var $t_male_15to19, $t_female_15to19,$t_male_20to44,$t_female_20to44;
			var $t_male_45to64, $t_female_45to64, $t_male_65up, $t_female_65up;
			var $t_male_total, $t_female_total, $t_total;
			var $cur_row;
			var $count;
			var $dept_nr;

			function ExcelGen_Causes_ERCases($from, $to, $sclass)
			{
					$this->Spreadsheet_Excel_Writer();
					$this->worksheet = & $this->addWorksheet();
					$this->worksheet->setPaper(5);      // Legal
					$this->worksheet->setLandscape();
					$this->worksheet->setMarginTop(1.8);
					$this->worksheet->setMarginLeft(0.8);
					$this->worksheet->setMarginRight(0.8);
					$this->worksheet->setMarginBottom(0.3);
					$this->Headers = array(
							'Age Distribution of Patients',
							'#', 'Cause of Death (Underlying)',
							'<1', '1-4', '5-9', '10-14', '15-19', '20-44',
							'45-64', '>=65', 'Total', '% of Total',
							'ICD10 Tab No.', 'M', 'F', 'T'
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
					#if($dept_nr) $this->dept_nr=$dept_nr;
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

					/*if ($this->dept_nr){
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
					$deptname = mb_strtoupper($deptname);        */

					$label = "";
					if ($this->sclass_label!='All')
						$label = '('.strtoupper($this->sclass_label).')';

					$this->worksheet->setHeader("".$row['hosp_country']."\n".$row['hosp_agency']."\n".$row['hosp_name']."\n".$row['hosp_addr1']."\n\nLEADING CAUSES OF EMERGENCY CASES IN THE ER DEPARTMENT".$label."\n".$text."\n",0.5);

					$this->worksheet->setColumn(0, 0, 3);
					$this->worksheet->setColumn(0, 1, 20);
					$this->worksheet->setColumn(0, 2, 3);
					$this->worksheet->setColumn(0, 20, 5);
					$this->worksheet->setColumn(0, 21, 10);
					$this->worksheet->setColumn(0, 21, 10);
					$this->worksheet->write(0, 8, $this->Headers[0], $this->format1);
					$this->worksheet->write(1, 0, $this->Headers[1], $this->format1);
					$this->worksheet->write(1, 1, $this->Headers[2], $this->format1a);
					$this->worksheet->write(1, 2, $this->Headers[3], $this->format1);
					$this->worksheet->write(1, 4, $this->Headers[4], $this->format1);
					$this->worksheet->write(1, 6, $this->Headers[5], $this->format1);
					$this->worksheet->write(1, 8, $this->Headers[6], $this->format1);
					$this->worksheet->write(1, 10, $this->Headers[7], $this->format1);
					$this->worksheet->write(1, 12, $this->Headers[8], $this->format1);
					$this->worksheet->write(1, 14, $this->Headers[9], $this->format1);
					$this->worksheet->write(1, 16, $this->Headers[10], $this->format1);
					$this->worksheet->write(1, 19, $this->Headers[11], $this->format1);
					$this->worksheet->write(2, 2, $this->Headers[14], $this->format1);
					$this->worksheet->write(2, 3, $this->Headers[15], $this->format1);
					$this->worksheet->write(2, 4, $this->Headers[14], $this->format1);
					$this->worksheet->write(2, 5, $this->Headers[15], $this->format1);
					$this->worksheet->write(2, 6, $this->Headers[14], $this->format1);
					$this->worksheet->write(2, 7, $this->Headers[15], $this->format1);
					$this->worksheet->write(2, 8, $this->Headers[14], $this->format1);
					$this->worksheet->write(2, 9, $this->Headers[15], $this->format1);
					$this->worksheet->write(2, 10, $this->Headers[14], $this->format1);
					$this->worksheet->write(2, 11, $this->Headers[15], $this->format1);
					$this->worksheet->write(2, 12, $this->Headers[14], $this->format1);
					$this->worksheet->write(2, 13, $this->Headers[15], $this->format1);
					$this->worksheet->write(2, 14, $this->Headers[14], $this->format1);
					$this->worksheet->write(2, 15, $this->Headers[15], $this->format1);
					$this->worksheet->write(2, 16, $this->Headers[14], $this->format1);
					$this->worksheet->write(2, 17, $this->Headers[15], $this->format1);
					$this->worksheet->write(2, 18, $this->Headers[14], $this->format1);
					$this->worksheet->write(2, 19, $this->Headers[15], $this->format1);
					$this->worksheet->write(2, 20, $this->Headers[16], $this->format1);
					$this->worksheet->write(2, 21, $this->Headers[12], $this->format1a);
					$this->worksheet->write(2, 22, $this->Headers[13], $this->format1a);
			}

			function FetchData()
			{
					global $db;

					$tot_sql = "SELECT SUM(CASE WHEN (p.sex='f' OR p.sex = 'm') THEN 1 ELSE 0 END) AS total
									FROM seg_rep_medrec_patient_icd_tbl AS p
									WHERE p.encounter_class_nr IN (1)
									AND p.encounter_type IN (3,4)
									AND DATE(p.encounter_date) BETWEEN '".$this->from."' AND '".$this->to."'
									AND p.encounter_status NOT IN ('direct_admission')
									".$this->type_cond;

				$tot_result=$db->Execute($tot_sql);
				$over_alltotal  = $tot_result->FetchRow();

				 $sql = "
						SELECT p.icd AS subcode,
						(SELECT IF(INSTR(p.code_parent,'.'),
												SUBSTRING(p.code_parent, 1, 3),
												IF(INSTR(p.code_parent,'/'),
												SUBSTRING(p.code_parent, 1, 5),p.code_parent))) AS code,
						p.icd_desc AS diagnosis,
						SUM(CASE WHEN p.sex='m'
											AND (floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<1
											OR floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age)) IS NULL)
								 THEN 1 ELSE 0 END) AS male_below1,
						SUM(CASE WHEN p.sex='f'
											AND (floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<1
											OR floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age)) IS NULL)
								 THEN 1 ELSE 0 END) AS female_below1,
						SUM(CASE WHEN p.sex='m'
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=1
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<=4
								 THEN 1 ELSE 0 END) AS male_1to4,
						SUM(CASE WHEN p.sex='f'
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=1
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<=4
								 THEN 1 ELSE 0 END) AS female_1to4,
						SUM(CASE WHEN p.sex='m'
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=5
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<=9
								 THEN 1 ELSE 0 END) AS male_5to9,
						SUM(CASE WHEN p.sex='f'
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=5
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<=9
								 THEN 1 ELSE 0 END) AS female_5to9,
						SUM(CASE WHEN p.sex='m'
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=10
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<=14
								 THEN 1 ELSE 0 END) AS male_10to14,
						SUM(CASE WHEN p.sex='f'
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=10
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<=14
											then 1 else 0 end) AS female_10to14,
						SUM(CASE WHEN p.sex='m'
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=15
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<=19
								 THEN 1 ELSE 0 END) AS male_15to19,
						SUM(CASE WHEN p.sex='f'
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=15
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<=19
								 THEN 1 ELSE 0 END) AS female_15to19,
						SUM(CASE WHEN p.sex='m'
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=20
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<=44
								 THEN 1 ELSE 0 END) AS male_20to44,
						SUM(CASE WHEN p.sex='f'
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=20
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<=44
								 THEN 1 ELSE 0 END) AS female_20to44,
						SUM(CASE WHEN p.sex='m'
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=45
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<=64
								 THEN 1 ELSE 0 END) AS male_45to64,
						SUM(CASE WHEN p.sex='f'
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=45
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<=64
								 THEN 1 ELSE 0 END) AS female_45to64,
						SUM(CASE WHEN p.sex='m'
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=65
								 THEN 1 ELSE 0 END) AS male_65up,
						SUM(CASE WHEN p.sex='f'
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=65
								 THEN 1 ELSE 0 END) AS female_65up,
						SUM(CASE WHEN p.sex='m' THEN 1 ELSE 0 END) AS male_total,
						SUM(CASE WHEN p.sex='f' THEN 1 ELSE 0 END) AS female_total,

						SUM(CASE WHEN (p.sex='f' OR p.sex = 'm') THEN 1 ELSE 0 END) AS total

						FROM seg_rep_medrec_patient_icd_tbl AS p
						WHERE p.encounter_class_nr IN (1)
						AND p.encounter_type IN (3,4)
						AND DATE(p.encounter_date) BETWEEN '".$this->from."' AND '".$this->to."'
						AND p.encounter_status NOT IN ('direct_admission')
						".$this->type_cond."
						GROUP BY p.icd
						ORDER BY count(p.icd) DESC ";

						$result=$db->Execute($sql);
							if ($result) {
							$this->count = $result->RecordCount();
							$i=1;
							$percentage = 0;
							$newrow=3;
							$col=0;
								while ($row=$result->FetchRow())
								{
												$this->t_male_below1 += $row['male_below1'];
												$this->t_female_below1 += $row['female_below1'];

												$this->t_male_1to4 += $row['male_1to4'];
												$this->t_female_1to4 += $row['female_1to4'];

												$this->t_male_5to9 += $row['male_5to9'];
												$this->t_female_5to9 += $row['female_5to9'];

												$this->t_male_10to14 += $row['male_10to14'];
												$this->t_female_10to14 += $row['female_10to14'];

												$this->t_male_15to19 += $row['male_15to19'];
												$this->t_female_15to19 += $row['female_15to19'];

												$this->t_male_20to44 += $row['male_20to44'];
												$this->t_female_20to44 += $row['female_20to44'];

												$this->t_male_45to64 += $row['male_45to64'];
												$this->t_female_45to64 += $row['female_45to64'];

												$this->t_male_65up += $row['male_65up'];
												$this->t_female_65up += $row['female_65up'];

												$this->t_male_total += $row['male_total'];
												$this->t_female_total += $row['female_total'];
												$this->t_total += $row['total'];

												$percentage = ($row['total'] / $over_alltotal['total']) * 100;
												$percentage = round($percentage,2);

							 $this->worksheet->write($newrow, $col, $i, $this->format2);
							 $this->worksheet->write($newrow, $col+1, $row['diagnosis'], $this->format2);
							 $this->worksheet->write($newrow, $col+2, $row['male_below1'], $this->format3);
							 $this->worksheet->write($newrow, $col+3, $row['female_below1'], $this->format3);
							 $this->worksheet->write($newrow, $col+4, $row['male_1to4'], $this->format3);
							 $this->worksheet->write($newrow, $col+5, $row['female_1to4'], $this->format3);
							 $this->worksheet->write($newrow, $col+6, $row['male_5to9'], $this->format3);
							 $this->worksheet->write($newrow, $col+7, $row['female_5to9'], $this->format3);
							 $this->worksheet->write($newrow, $col+8, $row['male_10to14'], $this->format3);
							 $this->worksheet->write($newrow, $col+9, $row['female_10to14'], $this->format3);
							 $this->worksheet->write($newrow, $col+10, $row['male_15to19'], $this->format3);
							 $this->worksheet->write($newrow, $col+11, $row['female_15to19'], $this->format3);
							 $this->worksheet->write($newrow, $col+12, $row['male_20to44'], $this->format3);
							 $this->worksheet->write($newrow, $col+13, $row['female_20to44'], $this->format3);
							 $this->worksheet->write($newrow, $col+14, $row['male_45to64'], $this->format3);
							 $this->worksheet->write($newrow, $col+15, $row['female_45to64'], $this->format3);
							 $this->worksheet->write($newrow, $col+16, $row['male_65up'], $this->format3);
							 $this->worksheet->write($newrow, $col+17, $row['female_65up'], $this->format3);
							 $this->worksheet->write($newrow, $col+18, $row['male_total'], $this->format3);
							 $this->worksheet->write($newrow, $col+19, $row['female_total'], $this->format3);
							 $this->worksheet->write($newrow, $col+20, $row['total'], $this->format3);
							 $this->worksheet->write($newrow, $col+21, $percentage."%", $this->format3);
							 $this->worksheet->write($newrow, $col+22, $row['subcode'], $this->format3);

								$i++;
								$newrow++;
								$percentage = 0;
						}
						$this->cur_row=$newrow;
				 }
			}

			function AfterData()
			{
					if (!$this->count)
					{
						$this->worksheet->write(3, 0, "No records found for this report...");
					}
					else
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
									$this->worksheet->write($this->cur_row, $col+13, $this->t_male_45to64, $this->format3);
									$this->worksheet->write($this->cur_row, $col+14, $this->t_female_45to64, $this->format3);
									$this->worksheet->write($this->cur_row, $col+15, $this->t_male_65up, $this->format3);
									$this->worksheet->write($this->cur_row, $col+16, $this->t_female_65up, $this->format3);
									$this->worksheet->write($this->cur_row, $col+17, $this->t_male_total, $this->format3);
									$this->worksheet->write($this->cur_row, $col+18, $this->t_female_total, $this->format3);
									$this->worksheet->write($this->cur_row, $col+19, $this->t_total, $this->format3);
									$this->worksheet->write($this->cur_row, $col+20, "100%", $this->format3);
									$this->worksheet->write($this->cur_row, $col+21, "xxx", $this->format3);
							}
			}
	}

	$rep = new ExcelGen_Causes_ERCases($_GET['from'], $_GET['to'], $_GET['sclass']);
	$rep->ExcelHeader();
	$rep->FetchData();
	$rep->AfterData();
	$rep->send('rep_leading_causes_er_cases.xls');
	$rep->close();
?>