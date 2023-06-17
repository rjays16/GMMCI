<?php

	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	require_once($root_path."/classes/excel/Writer.php");
	require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
	require_once($root_path.'include/care_api_classes/class_department.php');

	class ExcelGen_Distribution_Beds extends Spreadsheet_Excel_Writer
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

			function ExcelGen_Distribution_Beds($from, $to, $sclass)
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
							'Actual Number of Beds Utilized',
							'Type of Service', 'Allocated No. of Beds', 'Non-Philhealth',
							'Philhealth/ OWWA /HMO','Total', 'Total In-Patient Service Days',
							'Actual BOR (%)', 'No. of Staff',
							'Full Time Equivalent', 'Pay', 'Srvc', 'Total', 'FT', 'PT'
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
					$this->worksheet->setHeader("".$row['hosp_country']."\n".$row['hosp_agency']."\n".$row['hosp_name']."\n".$row['hosp_addr1']."\n\nDISTRIBUTION OF BEDS".$label_dept."\n".$text."\n",0.5);

					$this->worksheet->setColumn(0, 0, 25);
					$this->worksheet->setColumn(0, 3, 8);
					$this->worksheet->setColumn(0, 5, 8);
					$this->worksheet->setColumn(0, 7, 8);
					$this->worksheet->setColumn(0, 10, 8);
					$this->worksheet->setColumn(0, 11, 8);
					$this->worksheet->setColumn(0, 12, 8);
					$this->worksheet->setColumn(0, 13, 8);
					$this->worksheet->setColumn(0, 14, 8);
					$this->worksheet->setColumn(0, 15, 10);

					$this->worksheet->write(0, 5, $this->Headers[0], $this->format1);
					$this->worksheet->write(1, 0, $this->Headers[1], $this->format1);
					$this->worksheet->write(1, 2, $this->Headers[2], $this->format1a);
					$this->worksheet->write(1, 4, $this->Headers[3], $this->format1a);
					$this->worksheet->write(1, 6, $this->Headers[4], $this->format1a);
					$this->worksheet->write(1, 8, $this->Headers[5], $this->format1);
					$this->worksheet->write(1, 11, $this->Headers[6], $this->format1a);
					$this->worksheet->write(1, 12, $this->Headers[7], $this->format1a);
					$this->worksheet->write(1, 13, $this->Headers[8], $this->format1a);
					$this->worksheet->write(1, 15, $this->Headers[9], $this->format1a);

					$this->worksheet->write(2, 1, $this->Headers[10], $this->format1);
					$this->worksheet->write(2, 2, $this->Headers[11], $this->format1);
					$this->worksheet->write(2, 3, $this->Headers[12], $this->format1);
					$this->worksheet->write(2, 4, $this->Headers[10], $this->format1);
					$this->worksheet->write(2, 5, $this->Headers[11], $this->format1);
					$this->worksheet->write(2, 6, $this->Headers[10], $this->format1);
					$this->worksheet->write(2, 7, $this->Headers[11], $this->format1);
					$this->worksheet->write(2, 8, $this->Headers[10], $this->format1);
					$this->worksheet->write(2, 9, $this->Headers[11], $this->format1);
					$this->worksheet->write(2, 10, $this->Headers[12], $this->format1);
					$this->worksheet->write(2, 13, $this->Headers[13], $this->format1);
					$this->worksheet->write(2, 14, $this->Headers[14], $this->format1);

			}

			function FetchData()
			{
					global $db;
					$authorized_bed_number = 1; //subject to change :D
					$period = 1; //subject to change

				 $sql = "SELECT d.name_formal AS Type_Of_Service, d.nr,
							(SELECT SUM(nr_of_beds) FROM care_room AS rs INNER JOIN care_ward AS ws ON ws.nr=rs.ward_nr
								WHERE w.accomodation_type=2 AND ws.dept_nr=d.nr) AS pay_no_beds,
							(SELECT SUM(nr_of_beds) FROM care_room AS rs INNER JOIN care_ward AS ws ON ws.nr=rs.ward_nr
								WHERE (w.accomodation_type=1 OR w.accomodation_type IS NULL) AND ws.dept_nr=d.nr) AS charity_no_beds,

							SUM(CASE WHEN (i.hcare_id<>18 OR i.hcare_id IS NULL) AND (w.accomodation_type=2) THEN 1 ELSE 0 END) AS pay_non_phic,
							SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL) AND (w.accomodation_type=2) THEN 1 ELSE 0 END) AS pay_phic,
							SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id=5 AND (w.accomodation_type=2) THEN 1 ELSE 0 END) AS pay_phic_indigent,
							SUM(CASE WHEN em.memcategory_id=3 AND (w.accomodation_type=2) THEN 1 ELSE 0 END) AS pay_owwa,
							SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL) AND (w.accomodation_type=2) THEN 1 ELSE 0 END) AS pay_phic_member,
							SUM(CASE WHEN (w.accomodation_type=2) THEN 1 ELSE 0 END) AS total_pay,
							SUM(CASE WHEN (i.hcare_id<>18 OR i.hcare_id IS NULL) AND (w.accomodation_type=1 OR w.accomodation_type IS NULL) THEN 1 ELSE 0 END) AS charity_non_phic,
							SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL) AND (w.accomodation_type=1 OR w.accomodation_type IS NULL) THEN 1 ELSE 0 END) AS charity_phic,
							SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id=5 AND (w.accomodation_type=1 OR w.accomodation_type IS NULL) THEN 1 ELSE 0 END) AS charity_phic_indigent,
							SUM(CASE WHEN em.memcategory_id=3 AND (w.accomodation_type=1 OR w.accomodation_type IS NULL) THEN 1 ELSE 0 END) AS charity_owwa,
							SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL) AND (w.accomodation_type=1 OR w.accomodation_type IS NULL) THEN 1 ELSE 0 END) AS charity_phic_member,
							SUM(CASE WHEN (w.accomodation_type=1 OR w.accomodation_type IS NULL) THEN 1 ELSE 0 END) AS total_charity,
							SUM(CASE WHEN (w.accomodation_type=1 OR w.accomodation_type=2 OR w.accomodation_type IS NULL) THEN 1 ELSE 0 END) AS total_discharge,

							SUM(DATEDIFF(e.discharge_date,e.admission_dt)) total_days

							FROM care_department AS d
							INNER JOIN care_encounter AS e ON e.current_dept_nr=d.nr
							LEFT JOIN care_ward AS w ON e.current_ward_nr=w.nr
							LEFT JOIN seg_encounter_insurance AS i ON i.encounter_nr=e.encounter_nr
							LEFT JOIN care_person_insurance AS pti ON pti.pid=e.pid
							LEFT JOIN seg_encounter_memcategory AS em ON em.encounter_nr=e.encounter_nr
							LEFT JOIN seg_memcategory AS m ON m.memcategory_id=em.memcategory_id

							WHERE e.encounter_type IN (3,4)
							/*AND d.nr='136'*/
							AND (DATE(e.admission_dt) BETWEEN '".$this->from."' AND '".$this->to."')
							AND e.status NOT IN ('deleted','hidden','inactive','void')
							GROUP BY d.name_formal
							ORDER BY d.name_formal";

						$result=$db->Execute($sql);
							if ($result) {
							$this->count = $result->RecordCount();
							$i=1;
							$newrow=3;
							$col=0;
								while ($row=$result->FetchRow())
								{
									$total_beds = $row['pay_no_beds'] + $row['charity_no_beds'];
									$pay_phic = $row['pay_phic'] + $row['pay_phic_indigent'] + $row['pay_owwa'];
									$charity_phic = $row['charity_phic'] + $row['charity_phic_indigent'] + $row['charity_owwa'];
									$grandtotal = $row['total_pay'] + $row['total_charity'];
									$bor = ($row['total_days'] * 100)/($authorized_bed_number * $period);

									if($row['pay_no_beds']==NULL)
										$row['pay_no_beds'] = 0;
									if($row['charity_no_beds']==NULL)
										$row['charity_no_beds'] = 0;
									if($row['total_days']==NULL)
										$row['total_days'] = 0;

							 $this->worksheet->write($newrow, $col, $row['Type_Of_Service'], $this->format2);
							 $this->worksheet->write($newrow, $col+1, $row['pay_no_beds'], $this->format2);
							 $this->worksheet->write($newrow, $col+2, $row['charity_no_beds'], $this->format3);
							 $this->worksheet->write($newrow, $col+3, $total_beds, $this->format3);
							 $this->worksheet->write($newrow, $col+4, $row['pay_non_phic'], $this->format3);
							 $this->worksheet->write($newrow, $col+5, $row['charity_non_phic'], $this->format3);
							 $this->worksheet->write($newrow, $col+6, $pay_phic, $this->format3);
							 $this->worksheet->write($newrow, $col+7, $charity_phic, $this->format3);
							 $this->worksheet->write($newrow, $col+8, $row['total_pay'], $this->format3);
							 $this->worksheet->write($newrow, $col+9, $row['total_charity'], $this->format3);
							 $this->worksheet->write($newrow, $col+10, $grandtotal, $this->format3);
							 $this->worksheet->write($newrow, $col+11, $row['total_days'], $this->format3);
							 $this->worksheet->write($newrow, $col+12, $bor, $this->format3);
							 $this->worksheet->write($newrow, $col+13, '', $this->format3);
							 $this->worksheet->write($newrow, $col+14, '', $this->format3);
							 $this->worksheet->write($newrow, $col+15, '', $this->format3);


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

			}
	}

	$rep = new ExcelGen_Distribution_Beds($_GET['from'], $_GET['to'], $_GET['sclass']);
	$rep->ExcelHeader();
	$rep->FetchData();
	$rep->AfterData();
	$rep->send('rep_rep_distribution_beds.xls');
	$rep->close();
?>