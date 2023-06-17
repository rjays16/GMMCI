<?php

	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	require_once($root_path."/classes/excel/Writer.php");
	require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
	require_once($root_path.'include/care_api_classes/class_department.php');

	class ExcelGen_OPD_Summary extends Spreadsheet_Excel_Writer
	{
			var $worksheet;
			var $Headers;
			var $format1, $format1a, $format2, $format3;

			var $fromdate;
			var $todate;

			var $t_male_below1, $t_female_below1, $t_male_1to4, $t_female_1to4;
			var $t_male_5to9, $t_female_5to9, $t_male_10to14, $t_female_10to14;
			var $t_male_15to19, $t_female_15to19,$t_male_20to44,$t_female_20to44;
			var $t_male_45to59, $t_female_45to59, $t_male_60up, $t_female_60up;
			var $t_male_total, $t_female_total, $t_total;

			var $cur_row;
			var $count;
			var $dept_nr;

			function ExcelGen_OPD_Summary($from, $to, $sclass)
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
							'Age Distribution of Patients',
							'OPD Department/Clinic (All)',
							'<1', '1-4', '5-9', '10-14', '15-19', '20-44',
							'45-59', '60 up', 'Total',
							'Over - all Total', 'M', 'F'
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

					$label_dept = "";

					$deptname = mb_strtoupper($deptname);
					$this->worksheet->setHeader("".$row['hosp_country']."\n".$row['hosp_agency']."\n".$row['hosp_name']."\n".$row['hosp_addr1']."\n\nOPD SUMMARY OF PATIENT".$label_dept."\n".$text."\n",0.5);

					$this->worksheet->setColumn(0, 0, 33);
					$this->worksheet->setColumn(0, 16, 5);
					$this->worksheet->setColumn(0, 18, 6);
					$this->worksheet->setColumn(0, 19, 12);
					$this->worksheet->write(0, 8, $this->Headers[0], $this->format1);
					$this->worksheet->write(1, 0, $this->Headers[1], $this->format1);
					$this->worksheet->write(1, 1, $this->Headers[2], $this->format1);
					$this->worksheet->write(1, 3, $this->Headers[3], $this->format1);
					$this->worksheet->write(1, 5, $space.$this->Headers[4], $this->format1);
					$this->worksheet->write(1, 7, $this->Headers[5], $this->format1);
					$this->worksheet->write(1, 9, $this->Headers[6], $this->format1);
					$this->worksheet->write(1, 11, $this->Headers[7], $this->format1);
					$this->worksheet->write(1, 13, $this->Headers[8], $this->format1);
					$this->worksheet->write(1, 15, $this->Headers[9], $this->format1);
					$this->worksheet->write(1, 17, $this->Headers[10], $this->format1);
					$this->worksheet->write(1, 19, $this->Headers[11], $this->format1);
					$this->worksheet->write(2, 1, $this->Headers[12], $this->format1);
					$this->worksheet->write(2, 2, $this->Headers[13], $this->format1);
					$this->worksheet->write(2, 3, $this->Headers[12], $this->format1);
					$this->worksheet->write(2, 4, $this->Headers[13], $this->format1);
					$this->worksheet->write(2, 5, $this->Headers[12], $this->format1);
					$this->worksheet->write(2, 6, $this->Headers[13], $this->format1);
					$this->worksheet->write(2, 7, $this->Headers[12], $this->format1);
					$this->worksheet->write(2, 8, $this->Headers[13], $this->format1);
					$this->worksheet->write(2, 9, $this->Headers[12], $this->format1);
					$this->worksheet->write(2, 10, $this->Headers[13], $this->format1);
					$this->worksheet->write(2, 11, $this->Headers[12], $this->format1);
					$this->worksheet->write(2, 12, $this->Headers[13], $this->format1);
					$this->worksheet->write(2, 13, $this->Headers[12], $this->format1);
					$this->worksheet->write(2, 14, $this->Headers[13], $this->format1);
					$this->worksheet->write(2, 15, $this->Headers[12], $this->format1);
					$this->worksheet->write(2, 16, $this->Headers[13], $this->format1);
					$this->worksheet->write(2, 17, $this->Headers[12], $this->format1);
					$this->worksheet->write(2, 18, $this->Headers[13], $this->format1);

			}

			function FetchData()
			{
					global $db;

					if ($this->from) {
						$where[]="DATE(e.encounter_date) BETWEEN '".$this->from."' AND '".$this->to."'";
					}

					if ($where)
						$whereSQL = "AND (".implode(") AND (",$where).")";

				 $sql = "SELECT d.name_formal AS Type_Of_Service,

						SUM(CASE WHEN p.sex='m' AND (floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))<1 OR floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age)) IS NULL) then 1 else 0 end) AS male_below1,
						SUM(CASE WHEN p.sex='f' AND (floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))<1 OR floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age)) IS NULL) then 1 else 0 end) AS female_below1,

						SUM(CASE WHEN p.sex='m'
								AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))>=1
								AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))<=4 then 1 else 0 end) AS male_1to4,
						SUM(CASE WHEN p.sex='f'
								AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))>=1
								AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))<=4 then 1 else 0 end) AS female_1to4,

						SUM(CASE WHEN p.sex='m'
								AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))>=5
								AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))<=9 then 1 else 0 end) AS male_5to9,
						SUM(CASE WHEN p.sex='f'
								AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))>=5
								AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))<=9 then 1 else 0 end) AS female_5to9,

						SUM(CASE WHEN p.sex='m'
								AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))>=10
								AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))<=14 then 1 else 0 end) AS male_10to14,
						SUM(CASE WHEN p.sex='f'
								AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))>=10
								AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))<=14 then 1 else 0 end) AS female_10to14,

						SUM(CASE WHEN p.sex='m'
								AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))>=15
								AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))<=19 then 1 else 0 end) AS male_15to19,
						SUM(CASE WHEN p.sex='f'
								AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))>=15
								AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))<=19 then 1 else 0 end) AS female_15to19,

						SUM(CASE WHEN p.sex='m'
								AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))>=20
								AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))<=44 then 1 else 0 end) AS male_20to44,
						SUM(CASE WHEN p.sex='f'
								AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))>=20
								AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))<=44 then 1 else 0 end) AS female_20to44,

						SUM(CASE WHEN p.sex='m'
								AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))>=45
								AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))<=59 then 1 else 0 end) AS male_45to59,
						SUM(CASE WHEN p.sex='f'
								AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))>=45
								AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))<=59 then 1 else 0 end) AS female_45to59,

						SUM(CASE WHEN p.sex='m' AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))>=60 then 1 else 0 end) AS male_60up,
						SUM(CASE WHEN p.sex='f' AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))>=60 then 1 else 0 end) AS female_60up,

						SUM(CASE WHEN p.sex='m' then 1 else 0 end) AS male_total,
						SUM(CASE WHEN p.sex='f' then 1 else 0 end) AS female_total,

						SUM(CASE WHEN ((p.sex='m') OR (p.sex='f')) THEN 1 ELSE 0 END) AS total

						FROM care_person AS p
						INNER JOIN care_encounter AS e ON e.pid=p.pid
						LEFT JOIN care_department AS d ON d.nr=e.current_dept_nr
						WHERE  e.encounter_type IN (2)
						AND e.encounter_class_nr IN (2)
                        AND e.status NOT IN ('deleted','hidden','inactive','void') 
						$whereSQL
						GROUP BY d.name_formal
						ORDER BY SUM(CASE WHEN ((p.sex='m') OR (p.sex='f')) THEN 1 ELSE 0 END) DESC";

						$result=$db->Execute($sql);
							if ($result) {
							$this->count = $result->RecordCount();
							$i=1;
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

									$this->t_male_45to59 += $row['male_45to59'];
									$this->t_female_45to59 += $row['female_45to59'];

									$this->t_male_60up += $row['male_60up'];
									$this->t_female_60up += $row['female_60up'];

									$this->t_male_total += $row['male_total'];
									$this->t_female_total += $row['female_total'];
									$this->t_total += $row['total'];

							 $this->worksheet->write($newrow, $col, $row['Type_Of_Service'], $this->format2);
							 $this->worksheet->write($newrow, $col+1, $row['male_below1'], $this->format2);
							 $this->worksheet->write($newrow, $col+2, $row['female_below1'], $this->format3);
							 $this->worksheet->write($newrow, $col+3, $row['male_1to4'], $this->format3);
							 $this->worksheet->write($newrow, $col+4, $row['female_1to4'], $this->format3);
							 $this->worksheet->write($newrow, $col+5, $row['male_5to9'], $this->format3);
							 $this->worksheet->write($newrow, $col+6, $row['female_5to9'], $this->format3);
							 $this->worksheet->write($newrow, $col+7, $row['male_10to14'], $this->format3);
							 $this->worksheet->write($newrow, $col+8, $row['female_10to14'], $this->format3);
							 $this->worksheet->write($newrow, $col+9, $row['male_15to19'], $this->format3);
							 $this->worksheet->write($newrow, $col+10, $row['female_15to19'], $this->format3);
							 $this->worksheet->write($newrow, $col+11, $row['male_20to44'], $this->format3);
							 $this->worksheet->write($newrow, $col+12, $row['female_20to44'], $this->format3);
							 $this->worksheet->write($newrow, $col+13, $row['male_45to59'], $this->format3);
							 $this->worksheet->write($newrow, $col+14, $row['female_45to59'], $this->format3);
							 $this->worksheet->write($newrow, $col+15, $row['male_60up'], $this->format3);
							 $this->worksheet->write($newrow, $col+16, $row['female_60up'], $this->format3);
							 $this->worksheet->write($newrow, $col+17, $row['male_total'], $this->format3);
							 $this->worksheet->write($newrow, $col+18, $row['female_total'], $this->format3);
							 $this->worksheet->write($newrow, $col+19, $row['total'], $this->format3);

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
						$this->worksheet->write(3, 0, "No records found for this report...");
					}
					else
					{
									$col=0;
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

							}
			}
	}

	$rep = new ExcelGen_OPD_Summary($_GET['from'], $_GET['to'], $_GET['sclass']);
	$rep->ExcelHeader();
	$rep->FetchData();
	$rep->AfterData();
	$rep->send('rep_opd_summary_patient.xls');
	$rep->close();
?>