<?php

	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	require_once($root_path."/classes/excel/Writer.php");
	require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
	require_once($root_path.'include/care_api_classes/class_department.php');

	class ExcelGen_Discharge_Accomodation extends Spreadsheet_Excel_Writer
	{
			var $worksheet;
			var $Headers;
			var $format1, $format1a, $format2, $format3, $format4;

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

			function ExcelGen_Discharge_Accomodation($from, $to)
			{
					$this->Spreadsheet_Excel_Writer();
					$this->worksheet = & $this->addWorksheet();
					$this->worksheet->setPaper(1);      // Letter
					$this->worksheet->setLandscape();
					$this->worksheet->setMarginTop(1.8);
					$this->worksheet->setMarginLeft(0.8);
					$this->worksheet->setMarginRight(0.8);
					$this->worksheet->setMarginBottom(0.3);
					$this->Headers = array(
							'Type of Service', 'Type of Accomodations', 'Total Discharge', 'Total Length of Stay',
							'Pay', 'Service', 'Non-PHIC', 'PHIC', 'Member/Dep', 'Indigent', 'OWWA'
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
					$this->format3->setAlign('right');

					$this->format4=& $this->addFormat();
					$this->format4->setSize(9);
					$this->format4->setAlign('center');

					$this->ColumnWidth = array(23, 8,10,8,8, 8,10,8,8, 10, 10);

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
					$this->worksheet->setHeader("".$row['hosp_country']."\n".$row['hosp_agency']."\n".$row['hosp_name']."\n".$row['hosp_addr1']."\n\nDISCHARGES: SERVICES RENDERED AND PATIENTS ATTENDED".$label_dept."\n".$text."\n",0.5);

					$this->worksheet->setColumn(0, 0, $this->ColumnWidth[0]);
					$this->worksheet->setColumn(0, 1, $this->ColumnWidth[1]);
					$this->worksheet->setColumn(0, 2, $this->ColumnWidth[2]);
					$this->worksheet->setColumn(0, 3, $this->ColumnWidth[3]);
					$this->worksheet->setColumn(0, 4, $this->ColumnWidth[4]);
					$this->worksheet->setColumn(0, 5, $this->ColumnWidth[5]);
					$this->worksheet->setColumn(0, 6, $this->ColumnWidth[6]);
					$this->worksheet->setColumn(0, 7, $this->ColumnWidth[7]);
					$this->worksheet->setColumn(0, 8, $this->ColumnWidth[8]);
					$this->worksheet->setColumn(0, 9, $this->ColumnWidth[9]);
					$this->worksheet->setColumn(0, 10, $this->ColumnWidth[10]);

					$this->worksheet->write(0, 5, $this->Headers[1], $this->format1);


					$this->worksheet->write(1, 2, $this->Headers[4], $this->format1);
					$this->worksheet->write(1, 6, $this->Headers[5], $this->format1);

					$this->worksheet->write(2, 3, $this->Headers[7], $this->format1);
					$this->worksheet->write(2, 7, $this->Headers[7], $this->format1);

					$this->worksheet->write(3, 0, $this->Headers[0], $this->format1);
					$this->worksheet->write(3, 1, $this->Headers[6], $this->format1);
					$this->worksheet->write(3, 2, $this->Headers[8], $this->format1);
					$this->worksheet->write(3, 3, $this->Headers[9], $this->format1);
					$this->worksheet->write(3, 4, $this->Headers[10], $this->format1);
					$this->worksheet->write(3, 5, $this->Headers[6], $this->format1);
					$this->worksheet->write(3, 6, $this->Headers[8], $this->format1);
					$this->worksheet->write(3, 7, $this->Headers[9], $this->format1);
					$this->worksheet->write(3, 8, $this->Headers[10], $this->format1);
					$this->worksheet->write(3, 9, $this->Headers[2], $this->format1a);
					$this->worksheet->write(3, 10, $this->Headers[3], $this->format1a);

			}

			function getLOS($current_dept_nr){
				 global $db;

				$tot_sql = "SELECT SUM(len.totallenstay) AS total_len_stay
										FROM (SELECT enc.current_dept_nr, enc.is_discharged, enc.discharge_date, enc.admission_dt, DATEDIFF(enc.discharge_date,enc.admission_dt) AS totallenstay
														FROM care_encounter AS enc
														WHERE enc.encounter_type IN (3,4)
														AND enc.discharge_date IS NOT NULL
														AND (DATE(enc.discharge_date) BETWEEN '".$this->from."' AND '".$this->to."')
														AND enc.status NOT IN ('deleted','hidden','inactive','void')
														AND enc.current_dept_nr='".$current_dept_nr."') AS len";

				#echo "<br><br>".$tot_sql;

				$tot_result=$db->Execute($tot_sql);
				$over_alltotal  = $tot_result->FetchRow();

				return  $over_alltotal['total_len_stay'];

			}

			function FetchData()
			{
					global $db;
					$sql = "SELECT d.name_formal AS Type_Of_Service, d.nr,
								SUM(CASE WHEN (i.hcare_id<>18 OR i.hcare_id IS NULL) AND w.accomodation_type=2 then 1 else 0 end) AS pay_non_phic,

								SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL) AND w.accomodation_type=2 then 1 else 0 end) AS pay_phic,
								SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id=5 AND w.accomodation_type=2 then 1 else 0 end) AS pay_phic_indigent,
								SUM(CASE WHEN em.memcategory_id=3 AND w.accomodation_type=2 then 1 else 0 end) AS pay_owwa,
								SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL)  AND w.accomodation_type=2 then 1 else 0 end) AS pay_phic_member,

								SUM(CASE WHEN w.accomodation_type=2 then 1 else 0 end) AS total_pay,

								SUM(CASE WHEN (i.hcare_id<>18 OR i.hcare_id IS NULL) AND(w.accomodation_type=1 OR w.accomodation_type IS NULL) then 1 else 0 end) AS charity_non_phic,
								SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL) AND (w.accomodation_type=1 OR w.accomodation_type IS NULL) then 1 else 0 end) AS charity_phic,
								SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id=5 AND (w.accomodation_type=1 OR w.accomodation_type IS NULL) then 1 else 0 end) AS charity_phic_indigent,
								SUM(CASE WHEN em.memcategory_id=3 AND (w.accomodation_type=1 OR w.accomodation_type IS NULL) then 1 else 0 end) AS charity_owwa,
								SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL)  AND (w.accomodation_type=1 OR w.accomodation_type IS NULL) then 1 else 0 end) AS charity_phic_member,

								SUM(CASE WHEN (w.accomodation_type=1 OR w.accomodation_type IS NULL) then 1 else 0 end) AS total_charity,

								SUM(CASE WHEN (w.accomodation_type=1 OR w.accomodation_type=2 OR w.accomodation_type IS NULL) then 1 else 0 end) AS total_discharge

						 FROM care_department AS d
						 INNER JOIN care_encounter AS e ON e.current_dept_nr=d.nr
						 LEFT JOIN care_ward AS w ON e.current_ward_nr=w.nr
						 LEFT JOIN seg_encounter_insurance AS i ON i.encounter_nr=e.encounter_nr
						 LEFT JOIN care_person_insurance AS pti ON pti.pid=e.pid
						 LEFT JOIN seg_encounter_memcategory AS em ON em.encounter_nr=e.encounter_nr
						 LEFT JOIN seg_memcategory AS m ON m.memcategory_id=em.memcategory_id

						 WHERE  e.encounter_type IN (3,4)
						 AND e.discharge_date IS NOT NULL
						 AND (DATE(e.discharge_date) BETWEEN '".$this->from."' AND '".$this->to."')
						 AND e.status NOT IN ('deleted','hidden','inactive','void')
						 GROUP BY d.name_formal
						 ORDER BY count(e.encounter_nr) DESC";

						$result=$db->Execute($sql);
							if ($result) {
							$this->_count = $result->RecordCount();
							$i=1;
							$newrow=4;
							$col=0;
							$this->total_pay_non_phic = 0;
							$this->total_pay_phic = 0;
							$this->total_pay_phic_indigent = 0;
							$this->total_pay_owwa = 0;
							$this->total_charity_non_phic = 0;
							$this->total_charity_phic = 0;
							$this->total_charity_phic_indigent = 0;
							$this->total_charity_owwa = 0;
							$this->total_total_discharge = 0;
							$this->total_LOS = 0;
								while ($row=$result->FetchRow())
								{

								$los = $this->getLOS($row['nr']);
								$this->total_pay_non_phic = $this->total_pay_non_phic + $row['pay_non_phic'];
								$this->total_pay_phic = $this->total_pay_phic + $row['pay_phic'];
								$this->total_pay_phic_indigent = $this->total_pay_phic_indigent + $row['pay_phic_indigent'];
								$this->total_pay_owwa = $this->total_pay_owwa + $row['pay_owwa'];
								$this->total_charity_non_phic = $this->total_charity_non_phic + $row['charity_non_phic'];
								$this->total_charity_phic = $this->total_charity_phic + $row['charity_phic'];
								$this->total_charity_phic_indigent = $this->total_charity_phic_indigent + $row['charity_phic_indigent'];
								$this->total_charity_owwa = $this->total_charity_owwa + $row['charity_owwa'];
								$this->total_total_discharge = $this->total_total_discharge + $row['total_discharge'];
								$this->total_LOS = $this->total_LOS + $los;

							 $this->worksheet->write($newrow, $col, $row['Type_Of_Service'], $this->format2);
							 $this->worksheet->write($newrow, $col+1, number_format($row['pay_non_phic']), $this->format3);
							 $this->worksheet->write($newrow, $col+2, number_format($row['pay_phic']), $this->format3);
							 $this->worksheet->write($newrow, $col+3, number_format($row['pay_phic_indigent']), $this->format3);
							 $this->worksheet->write($newrow, $col+4, number_format($row['pay_owwa']), $this->format3);
							 $this->worksheet->write($newrow, $col+5, number_format($row['charity_non_phic']), $this->format3);
							 $this->worksheet->write($newrow, $col+6, number_format($row['charity_phic']), $this->format3);
							 $this->worksheet->write($newrow, $col+7, number_format($row['charity_phic_indigent']), $this->format3);
							 $this->worksheet->write($newrow, $col+8, number_format($row['charity_owwa']), $this->format3);
							 $this->worksheet->write($newrow, $col+9, number_format($row['total_discharge']), $this->format4);
							 $this->worksheet->write($newrow, $col+10, number_format($los), $this->format4);

								$i++;
								$newrow++;
						}
						$this->cur_row=$newrow;
				 }
			}

			function AfterData()
			{
					if (!$this->_count)
					{
						$this->worksheet->write(4, 0, "No records found for this report...");
					}else{
						$this->worksheet->write($this->cur_row, 0, "Total =>", $this->format2);
						$this->worksheet->write($this->cur_row, 1, number_format($this->total_pay_non_phic), $this->format3);
						$this->worksheet->write($this->cur_row, 2, number_format($this->total_pay_phic), $this->format3);
						$this->worksheet->write($this->cur_row, 3, number_format($this->total_pay_phic_indigent), $this->format3);
						$this->worksheet->write($this->cur_row, 4, number_format($this->total_pay_owwa), $this->format3);
						$this->worksheet->write($this->cur_row, 5, number_format($this->total_charity_non_phic), $this->format3);
						$this->worksheet->write($this->cur_row, 6, number_format($this->total_charity_phic), $this->format3);
						$this->worksheet->write($this->cur_row, 7, number_format($this->total_charity_phic_indigent), $this->format3);
						$this->worksheet->write($this->cur_row, 8, number_format($this->total_charity_owwa), $this->format3);
						$this->worksheet->write($this->cur_row, 9, number_format($this->total_total_discharge), $this->format4);
						$this->worksheet->write($this->cur_row, 10, number_format($this->total_LOS), $this->format4);
					}

			}
	}

	$rep = new ExcelGen_Discharge_Accomodation($_GET['from'], $_GET['to']);
	$rep->ExcelHeader();
	$rep->FetchData();
	$rep->AfterData();
	$rep->send('rep_rep_discharge_accomodation.xls');
	$rep->close();
?>