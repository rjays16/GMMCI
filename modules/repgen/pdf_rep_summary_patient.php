<?php
#edited by VAN 03-10-2009
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

class RepGen_ResultOfTreatment_Discharge extends RepGen{
var $colored = TRUE;
var $from, $to;
var $t_male_below1, $t_female_below1, $t_male_1to4, $t_female_1to4;
var $t_male_5to9, $t_female_5to9, $t_male_10to14, $t_female_10to14;
var $t_male_15to19, $t_female_15to19,$t_male_20to44,$t_female_20to44;
var $t_male_45to59, $t_female_45to59, $t_male_60up, $t_female_60up;
var $t_male_total, $t_female_total, $t_total;

	 function RepGen_ResultOfTreatment_Discharge ($from, $to) {
				global $db;
				$this->RepGen("MEDICAL RECORDS: STATISTICAL SUMMARY");

		$this->ColumnWidth = array(25,20,20, 20,25, 30,30, 25,20, 20, 20,20);
				$this->RowHeight = 5;
				$this->TextHeight = 5;
				$this->TextPadding = 0.2;
				$this->Alignment = array('C','C','C', 'C','C', 'C','C', 'C','C', 'C', 'C','C');
				$this->PageOrientation = "L";
				$this->NoWrap = FALSE;
				$this->LEFTMARGIN = 2;

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
				$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',50,8,20);
				$this->SetFont("Arial","I","9");
				$total_w = 165;
				$this->Cell(50,4);
					#$this->Cell($total_w,4,'Republic of the Philippines',$border2,1,'C');
				$this->Cell($total_w,4,$row['hosp_country'],$border2,1,'C');
				$this->Cell(50,4);
					#$this->Cell($total_w,4,'DEPARTMENT OF HEALTH',$border2,1,'C');
				$this->Cell($total_w,4,$row['hosp_agency'],$border2,1,'C');
					$this->Ln(2);
				$this->SetFont("Arial","B","10");
				$this->Cell(50,4);
					#$this->Cell($total_w,4,'Gonzales Maranan Medical Center Incorporated',$border2,1,'C');
				$this->Cell($total_w,4,$row['hosp_name'],$border2,1,'C');
				$this->SetFont("Arial","","9");
				$this->Cell(50,4);
					#$this->Cell($total_w,4,'Bajada, Davao City',$border2,1,'C');
				$this->Cell($total_w,4,$row['hosp_addr1'],$border2,1,'C');
					$this->Ln(4);
					$this->SetFont('Arial','B',12);
				$this->Cell(50,5);

				$this->Cell($total_w,4,'SUMMARY OF PATIENTS IN THE HOSPITAL',$border2,1,'C');
				 $this->SetFont('Arial','B',9);
				$this->Cell(50,5);

				if ($this->from==$this->to)
						$text = "For ".date("F j, Y",strtotime($this->from));
				else
							#$text = "Full History";
						$text = "From ".date("F j, Y",strtotime($this->from))." To ".date("F j, Y",strtotime($this->to));

					$this->Cell($total_w,4,$text,$border2,1,'C');
				$this->Ln(5);

				# Print table header
				$this->SetFont('Arial','B',8);
				#if ($this->colored) $this->SetFillColor(0xED);
				if ($this->colored) $this->SetFillColor(255);
				$this->SetTextColor(0);
				$row=6;

				$this->Cell(25, 4, "Month, Year", "LRT", 0, 'C');
		$this->Cell(20, 4, "Total", "LRT", 0, 'C');
		$this->Cell(20, 4, "Total", "LRT", 0, 'C');

		$this->Cell(20, 4, "Total", "LRT", 0, 'C');
		$this->Cell(25, 4, "Total", "LRT", 0, 'C');

		$this->Cell(30, 4, "Total", "LRT", 0, 'C');
		$this->Cell(30, 4, "Total", "LRT", 0, 'C');

		$this->Cell(25, 4, "Total", "LRT", 0, 'C');
		$this->Cell(20, 4, "Total", "LRT", 0, 'C');

		$this->Cell(20, 4, "Total", "LRT", 0, 'C');

		$this->Cell(20, 4, "Total", "LRT", 0, 'C');
		$this->Cell(20, 4, "Total", "LRT", 1, 'C');


		$this->Cell(25, 4, "", "LR", 0, 'C');
		$this->Cell(20, 4, "Admission", "LR", 0, 'C');
		$this->Cell(20, 4, "Admission", "LR", 0, 'C');

		$this->Cell(20, 4, "Discharges", "LR", 0, 'C');
		$this->Cell(25, 4, "Discharges", "LR", 0, 'C');

		$this->Cell(30, 4, "Discharges", "LR", 0, 'C');
		$this->Cell(30, 4, "Discharges", "LR", 0, 'C');

		$this->Cell(25, 4, "Inpatient", "LR", 0, 'C');
		$this->Cell(20, 4, "Inpatient", "LR", 0, 'C');

		$this->Cell(20, 4, "Inpatient", "LR", 0, 'C');

		$this->Cell(20, 4, "Admission", "LR", 0, 'C');
		$this->Cell(20, 4, "Discharges", "LR", 1, 'C');

		$this->Cell(25, 4, "", "LRB", 0, 'C');
		$this->Cell(20, 4, "Exc Newborn", "LRB", 0, 'C');
		$this->Cell(20, 4, "Only Newborn", "LRB", 0, 'C');

		$this->Cell(20, 4, "Exc Newborn", "LRB", 0, 'C');
		$this->Cell(25, 4, "Only Newborn", "LRB", 0, 'C');

		$this->Cell(30, 4, "Exc Newborn (Alive)", "LRB", 0, 'C');
		$this->Cell(30, 4, "Only Newborn (Alive)", "LRB", 0, 'C');

		$this->Cell(25, 4, "Death Exc NB", "LRB", 0, 'C');
		$this->Cell(20, 4, "Death Only NB", "LRB", 0, 'C');

		$this->Cell(20, 4, "Deaths", "LRB", 0, 'C');

		$this->Cell(20, 4, "", "LRB", 0, 'C');
		$this->Cell(20, 4, "", "LRB", 0, 'C');

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
						$this->Cell(275, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
				}

				$cols = array();
		}

		function FetchData(){
			global $db;
		#          AND DATE(e.admission_dt) BETWEEN '".$this->from."' AND '".$this->to."'
		 $sql = "SELECT (SELECT DATE_FORMAT('$this->from','%M %Y')) AS month,

				SUM(CASE WHEN (DATE(e.admission_dt) BETWEEN '$this->from' AND '$this->to') AND e.is_discharged=0 AND e.current_dept_nr NOT IN (191,174) then 1 else 0 end) AS total_still_admitted_excNB,
				SUM(CASE WHEN (DATE(e.admission_dt) BETWEEN '$this->from' AND '$this->to') AND e.is_discharged=0 AND e.current_dept_nr IN (191,174) then 1 else 0 end) AS total_still_admitted_NB,
				SUM(CASE WHEN (DATE(e.admission_dt) BETWEEN '$this->from' AND '$this->to') AND e.is_discharged=0 then 1 else 0 end) AS total_still_admitted,
				SUM(CASE WHEN (DATE(e.admission_dt) BETWEEN '$this->from' AND '$this->to') then 1 else 0 end) AS total_admitted,
				SUM(CASE WHEN (DATE(e.admission_dt) BETWEEN '$this->from' AND '$this->to') AND e.current_dept_nr NOT IN (191,174) then 1 else 0 end) AS total_admitted_excNB,
				SUM(CASE WHEN (DATE(e.admission_dt) BETWEEN '$this->from' AND '$this->to') AND e.current_dept_nr IN (191,174) then 1 else 0 end) AS total_admitted_NB,

				SUM(CASE WHEN (DATE(e.admission_dt) BETWEEN '$this->from' AND '$this->to') AND e.is_discharged=1 AND (DATE(e.discharge_date) BETWEEN '2008-11-01' AND '2008-11-30') AND e.current_dept_nr NOT IN (191,174)  then 1 else 0 end) AS total_discharge_excNB,
				SUM(CASE WHEN (DATE(e.admission_dt) BETWEEN '$this->from' AND '$this->to') AND e.is_discharged=1 AND (DATE(e.discharge_date) BETWEEN '2008-11-01' AND '2008-11-30') AND e.current_dept_nr IN (191,174)  then 1 else 0 end) AS total_discharge_NB,
				SUM(CASE WHEN (DATE(e.admission_dt) BETWEEN '$this->from' AND '$this->to') AND e.is_discharged=1 AND (DATE(e.discharge_date) BETWEEN '2008-11-01' AND '2008-11-30') AND e.current_dept_nr NOT IN (191,174) AND er.result_code<>8  then 1 else 0 end) AS total_discharge_alive_excNB,
				SUM(CASE WHEN (DATE(e.admission_dt) BETWEEN '$this->from' AND '$this->to') AND e.is_discharged=1 AND (DATE(e.discharge_date) BETWEEN '2008-11-01' AND '2008-11-30') AND e.current_dept_nr IN (191,174) AND er.result_code<>8  then 1 else 0 end) AS total_discharge_alive_NB,
				SUM(CASE WHEN (DATE(e.admission_dt) BETWEEN '$this->from' AND '$this->to') AND e.is_discharged=1 AND (DATE(e.discharge_date) BETWEEN '2008-11-01' AND '2008-11-30') AND er.result_code<>8  then 1 else 0 end) AS total_discharge_alive,
				SUM(CASE WHEN (DATE(e.admission_dt) BETWEEN '$this->from' AND '$this->to') AND e.is_discharged=1 AND (DATE(e.discharge_date) BETWEEN '2008-11-01' AND '2008-11-30') then 1 else 0 end) AS total_discharge,

				SUM(CASE WHEN (DATE(e.admission_dt) BETWEEN '$this->from' AND '$this->to') AND er.result_code=8 then 1 else 0 end) AS total_deaths,
				SUM(CASE WHEN (DATE(e.admission_dt) BETWEEN '$this->from' AND '$this->to') AND er.result_code=8 AND e.current_dept_nr IN (191,174) then 1 else 0 end) AS total_deaths_NB,
				SUM(CASE WHEN (DATE(e.admission_dt) BETWEEN '$this->from' AND '$this->to') AND er.result_code=8 AND e.current_dept_nr NOT IN (191,174) then 1 else 0 end) AS total_deaths_excNB

				FROM care_encounter AS e

								LEFT JOIN (SELECT ser.encounter_nr,SUBSTRING(MAX(CONCAT(ser.create_time,ser.result_code)),20) AS result_code,
																	MAX(ser.modify_time) AS modify_time
																	FROM seg_encounter_result AS ser
																	INNER JOIN care_encounter AS em ON em.encounter_nr=ser.encounter_nr
																	WHERE (DATE(discharge_date) BETWEEN '$this->from' AND '$this->to')
																	AND em.encounter_type IN (3,4)
																	AND em.discharge_date IS NOT NULL
																	GROUP BY ser.encounter_nr
																	ORDER BY ser.encounter_nr, ser.create_time DESC) AS er ON er.encounter_nr=e.encounter_nr

								WHERE  e.encounter_type IN (3,4)";

						#echo "sql = ".$sql;

						$result=$db->Execute($sql);
						if ($result) {

							$this->_count = $result->RecordCount();
								$this->Data=array();

							while ($row=$result->FetchRow()) {

								$this->Data[]=array(
										$row['month'],
					$row['total_admitted_excNB'],
					$row['total_admitted_NB'],
					$row['total_discharge_excNB'],
					$row['total_discharge_NB'],
					$row['total_discharge_alive_excNB'],
					$row['total_discharge_alive_NB'],
					$row['total_deaths_excNB'],
					$row['total_deaths_NB'],
					$row['total_deaths'],
					$row['total_admitted'],
					$row['total_discharge']
								 );

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

$rep = new RepGen_ResultOfTreatment_Discharge($_GET['from'], $_GET['to']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>