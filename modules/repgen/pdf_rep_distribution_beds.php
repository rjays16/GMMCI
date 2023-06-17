<?php
#created by Cherry 11-26-09
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

class RepGen_DistributionBeds extends RepGen{
var $colored = TRUE;
var $from, $to;
var $dept_nr;

	 function RepGen_DistributionBeds ($from, $to, $dept_nr) {
				global $db;
				$this->RepGen("MEDICAL RECORDS: DISTRIBUTION OF BEDS");

				$this->ColumnWidth = array(45, 14,14,14, 14,14, 14,14, 14,14,14, 18, 18, 14,14, 20);
				$this->RowHeight = 5;
				$this->TextHeight = 5;
				$this->TextPadding = 0.2;
				$this->Alignment = array('L', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R','R');
				$this->PageOrientation = "L";
				$this->Label = array('Actual Number of Beds Utilized', 'Total In-', 'Patient',
												'Service', 'Days', 'Type of Service','Allocated No. of Beds', 'Non-Philhealth',
												'Philhealth/', 'OWWA/HMO','Total', 'Actual', 'BOR(%)','No. of Staff',
												'Full Time','Equivalent', 'Pay', 'Srvc','FT','PT');
				$this->NoWrap = FALSE;
				$this->LEFTMARGIN = 5;
				$this->dept_nr = $dept_nr;

				if ($from) $this->from=date("Y-m-d",strtotime($from));
				if ($to) $this->to=date("Y-m-d",strtotime($to));

				$this->useMultiCell = TRUE;
				#$this->SetFillColor(0xFF);
				$this->SetFillColor(255);
				if ($this->colored)    $this->SetDrawColor(0xDD);

		}


		function Header() {
				global $root_path, $db;
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
				$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',30,6,17);
				$this->SetFont("Arial","I","9");
				#$total_w = 165;
				$total_w = 0;
				$this->Cell(0,4,$row['hosp_country'],$border2,1,'C');
				$this->Cell(0,4,$row['hosp_agency'],$border2,1,'C');
				$this->Ln(2);
				$this->SetFont("Arial","B","10");
				$this->Cell(0,4,$row['hosp_name'],$border2,1,'C');
				$this->SetFont("Arial","","9");
				$this->Cell(0,4,$row['hosp_addr1'],$border2,1,'C');
				$this->Ln(4);
				$this->SetFont('Arial','B',11);

				$this->Cell(0,4,'DISTRIBUTION OF BEDS',$border2,1,'C');
				$this->SetFont('Arial','B',9);

				if ($this->from==$this->to)
						$text = "For ".date("F j, Y",strtotime($this->from));
				else
						$text = "From ".date("F j, Y",strtotime($this->from))." To ".date("F j, Y",strtotime($this->to));

				$this->Cell($total_w,4,$text,$border2,1,'C');
				$this->Ln(5);

				# Print table header
				$this->SetFont('Arial','B',8);
				#if ($this->colored) $this->SetFillColor(0xED);
				if ($this->colored) $this->SetFillColor(255);
				$this->SetTextColor(0);
				$row=4;
				$allocate_no_beds = $this->ColumnWidth[1] + $this->ColumnWidth[2] + $this->ColumnWidth[3];
				$non_philhealth = $this->ColumnWidth[4] + $this->ColumnWidth[5];
				$philhealth = $this->ColumnWidth[6] + $this->ColumnWidth[7];
				$total = $this->ColumnWidth[8] + $this->ColumnWidth[9] + $this->ColumnWidth[10];
				$actual_number_beds = $non_philhealth + $philhealth + $total;
				$no_of_staff = $this->ColumnWidth[13] + $this->ColumnWidth[14];

				$this->Cell($this->ColumnWidth[0], $row, "", "TLR", 0);
				$this->Cell($allocate_no_beds, $row, "", "TLR", 0);
				$this->Cell($actual_number_beds, $row, $this->Label[0], 1, 0, 'C');
				$this->Cell($this->ColumnWidth[11], $row, $this->Label[1], "TLR", 0, 'C');
				$this->Cell($this->ColumnWidth[12], $row, "", "TLR", 0);
				$this->Cell($no_of_staff, $row, "", "TLR", 0);
				$this->Cell($this->ColumnWidth[15], $row, "", "TLR", 1);

				$this->Cell($this->ColumnWidth[0], $row, "", "LR", 0);
				$this->Cell($allocate_no_beds, $row, $this->Label[6], "LR", 0, 'C');
				$this->Cell($non_philhealth, $row, "", "LR", 0);
				$this->Cell($philhealth, $row, $this->Label[8], "LR", 0, 'C');
				$this->Cell($total, $row, "", "LR", 0);
				$this->Cell($this->ColumnWidth[11], $row, $this->Label[2], "LR", 0, 'C');
				$this->Cell($this->ColumnWidth[12], $row, $this->Label[11], "LR", 0, 'C');
				$this->Cell($no_of_staff, $row, $this->Label[13], "LR", 0, 'C');
				$this->Cell($this->ColumnWidth[15], $row, $this->Label[14], "LR", 1, 'C');

				$this->Cell($this->ColumnWidth[0], $row, $this->Label[5], "LR", 0, 'C');
				$this->Cell($allocate_no_beds, $row, "", "LR", 0);
				$this->Cell($non_philhealth, $row, $this->Label[7], "LR", 0, 'C');
				$this->Cell($philhealth, $row, $this->Label[9], "LR", 0, 'C');
				$this->Cell($total, $row, $this->Label[10], "LR", 0, 'C');
				$this->Cell($this->ColumnWidth[11], $row, $this->Label[3], "LR", 0, 'C');
				$this->Cell($this->ColumnWidth[12], $row, $this->Label[12], "LR", 0, 'C');
				$this->Cell($no_of_staff, $row, "", "LR", 0, 'C');
				$this->Cell($this->ColumnWidth[15], $row, $this->Label[15], "LR", 1, 'C');

				$this->Cell($this->ColumnWidth[0], $row, "", "BLR", 0);
				$this->Cell($this->ColumnWidth[1], $row, $this->Label[16], 1, 0, 'C');
				$this->Cell($this->ColumnWidth[2], $row, $this->Label[17], 1, 0, 'C');
				$this->Cell($this->ColumnWidth[3], $row, $this->Label[10], 1, 0, 'C');
				$this->Cell($this->ColumnWidth[4], $row, $this->Label[16], 1, 0, 'C');
				$this->Cell($this->ColumnWidth[5], $row, $this->Label[17], 1, 0, 'C');
				$this->Cell($this->ColumnWidth[6], $row, $this->Label[16], 1, 0, 'C');
				$this->Cell($this->ColumnWidth[7], $row, $this->Label[17], 1, 0, 'C');
				$this->Cell($this->ColumnWidth[8], $row, $this->Label[16], 1, 0, 'C');
				$this->Cell($this->ColumnWidth[9], $row, $this->Label[17], 1, 0, 'C');
				$this->Cell($this->ColumnWidth[10], $row, $this->Label[10], 1, 0, 'C');
				$this->Cell($this->ColumnWidth[11], $row, $this->Label[4], "BLR", 0, 'C');
				$this->Cell($this->ColumnWidth[12], $row, "", "BLR", 0, 'C');
				$this->Cell($this->ColumnWidth[13], $row, $this->Label[18], 1, 0, 'C');
				$this->Cell($this->ColumnWidth[14], $row, $this->Label[19], 1, 0, 'C');
				$this->Cell($this->ColumnWidth[15], $row, "", "BLR", 0);

				$this->Ln();
		}

		function Footer(){
				$this->SetY(-7);
				$this->SetFont('Arial','I',8);
				$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
		}

		function BeforeRow() {
				$this->FONTSIZE = 8;
				if ($this->colored) {
						if (($this->ROWNUM%2)>0)
								#$this->FILLCOLOR=array(0xee, 0xef, 0xf4);
								$this->FILLCOLOR=array(255, 255, 255);
						else
								$this->FILLCOLOR=array(255,255,255);
						$this->DRAWCOLOR = array(0xDD,0xDD,0xDD);
						#$this->DRAWCOLOR = array(255,255,255);
				}
		}

		function BeforeData() {
				if ($this->colored) {
						$this->DrawColor = array(0xDD,0xDD,0xDD);
						#$this->DrawColor = array(255,255,255);
				}
		}

		function BeforeCellRender() {
				$this->FONTSIZE = 8;
				if ($this->colored) {
						if (($this->RENDERPAGEROWNUM%2)>0)
								#$this->RENDERCELL->FillColor=array(0xee, 0xef, 0xf4);
								$this->RENDERCELL->FillColor=array(255, 255, 255);
						else
								$this->RENDERCELL->FillColor=array(255,255,255);
				}
		}

		function AfterData() {
				global $db;

				if (!$this->_count) {
						$this->SetFont('Arial','B',9);
						$this->SetFillColor(255);
						$this->SetTextColor(0);
						$this->Cell(200, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
				}

				$cols = array();
		}

		function FetchData(){
			global $db;
			$authorized_bed_number = 1200; //subject to change :D
			$period = 1;
			$sql_period = "SELECT DATEDIFF('".$this->to."','".$this->from."') AS period";
			$rs_period = $db->Execute($sql_period);
			if ($rs_period)
				 $row_period = $rs_period->FetchRow();
			$period = $row_period['period']; //subject to change

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

							SUM(DATEDIFF(p.discharge_date,p.admission_dt)) total_days

							FROM seg_rep_medrec_patient_icd_tbl AS p
							LEFT JOIN care_department AS d ON d.nr=p.current_dept_nr
							LEFT JOIN care_ward AS w ON p.current_ward_nr=w.nr
							LEFT JOIN seg_encounter_insurance AS i ON i.encounter_nr=p.encounter_nr
							LEFT JOIN care_person_insurance AS pti ON pti.pid=p.pid
							LEFT JOIN seg_encounter_memcategory AS em ON em.encounter_nr=p.encounter_nr
							LEFT JOIN seg_memcategory AS m ON m.memcategory_id=em.memcategory_id

							WHERE p.encounter_type IN (3,4)
							AND (DATE(p.admission_dt) BETWEEN '".$this->from."' AND '".$this->to."')
							GROUP BY d.name_formal
							ORDER BY d.name_formal";

				 #echo $sql;
						$result=$db->Execute($sql);
						if ($result) {

							$this->_count = $result->RecordCount();
								$this->Data=array();

							while ($row=$result->FetchRow()) {
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

								$this->Data[]=array(
									$row['Type_Of_Service'],
									$row['pay_no_beds'],
									$row['charity_no_beds'],
									$total_beds,
									$row['pay_non_phic'],
									$row['charity_non_phic'],
									$pay_phic,
									$charity_phic,
									$row['total_pay'],
									$row['total_charity'],
									$grandtotal,
									$row['total_days'],
									number_format($bor,2,'.',','),
									'',
									'',
									''
								 );
									$i++;
									//$percentage = 0;
							}

				}
				else {
					print_r($sql);
					print_r($db->ErrorMsg());
					exit;
					# Error
				}
		}
}

$rep = new RepGen_DistributionBeds($_GET['from'], $_GET['to'], $_GET['dept_nr_sub']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>