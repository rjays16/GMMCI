<?php
#edited by VAN 03-10-2009
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

class RepGen_ResultOfTreatment_Discharge extends RepGen{
var $colored = TRUE;
var $from, $to;

	 function RepGen_ResultOfTreatment_Discharge ($from, $to) {
		global $db;
		$this->RepGen("MEDICAL RECORDS: RESULT OF TREATMENT/CONDITION ON DISCHARGE");
		$this->ColumnWidth = array(40,12,12,12,12,12, 12,12,12,12,12, 12,12,12,12,12, 10,10,10, 20);
		$this->RowHeight = 5;
		$this->TextHeight = 5;
		$this->TextPadding = 0.2;
		$this->Alignment = array('L', 'R','R','R','R','R', 'R','R','R','R','R','R','R','R','R','R', 'R','R','R','R');
		$this->PageOrientation = "L";
		$this->NoWrap = FALSE;
		$this->LEFTMARGIN = 2;

		if ($from) $this->from=date("Y-m-d",strtotime($from));
				if ($to) $this->to=date("Y-m-d",strtotime($to));

		$this->useMultiCell = TRUE;
		#$this->SetFillColor(0xFF);
		$this->SetFillColor(255);
		if ($this->colored)	$this->SetDrawColor(0xDD);

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

		$this->Cell($total_w,4,'RESULT OF TREATMENT/CONDITION ON DISCHARGE',$border2,1,'C');
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

		$this->Cell(40, 4, "", "TLR", 0, 'C');
		$this->Cell(210, 4, "Result of Treatment/Condition on Discharge", "1", 0, 'C');
		$this->Cell(20, 4, "", "TLR", 1, 'C');
		$this->Cell(40, 4, "Type of Services", "LR", 0, 'C');

		$this->Cell(60, 4, "Recovered", "1", 0, 'C');
		$this->Cell(60, 4, "Improved", "1", 0, 'C');
		$this->Cell(60, 4, "Unimproved", "1", 0, 'C');
		$this->Cell(30, 4, "Died", "1", 0, 'C');

		$this->Cell(20, 4, "Grand Total", "LR", 1, 'C');
		$this->Cell(40, 4, "", "BLR", 0, 'C');

		#recovered
		$this->Cell(12, 4, "Disch", "1", 0, 'C');
		$this->Cell(12, 4, "Tran", "1", 0, 'C');
		$this->Cell(12, 4, "Hama", "1", 0, 'C');
		$this->Cell(12, 4, "Abs", "1", 0, 'C');
		$this->Cell(12, 4, "Total", "1", 0, 'C');

		#improved
		$this->Cell(12, 4, "Disch", "1", 0, 'C');
		$this->Cell(12, 4, "Tran", "1", 0, 'C');
		$this->Cell(12, 4, "Hama", "1", 0, 'C');
		$this->Cell(12, 4, "Abs", "1", 0, 'C');
		$this->Cell(12, 4, "Total", "1", 0, 'C');

		#unimproved
		$this->Cell(12, 4, "Disch", "1", 0, 'C');
		$this->Cell(12, 4, "Tran", "1", 0, 'C');
		$this->Cell(12, 4, "Hama", "1", 0, 'C');
		$this->Cell(12, 4, "Abs", "1", 0, 'C');
		$this->Cell(12, 4, "Total", "1", 0, 'C');

		#died
		$this->Cell(10, 4, "<48", "1", 0, 'C');
		$this->Cell(10, 4, ">=48", "1", 0, 'C');
		$this->Cell(10, 4, "Total", "1", 0, 'C');

		$this->Cell(20, 4, "", "BLR", 0, 'C');

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
			$this->Cell(0, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
		}

		$cols = array();
	}

	function FetchData(){
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

							/*SUM(CASE WHEN (p.result_code=8
									AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),p.age))<48)
									THEN 1 ELSE 0 END) AS deathbelow48,
							SUM(CASE WHEN (p.result_code=8
									AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),p.age))>=48)
									THEN 1 ELSE 0 END) AS deathabove48,
							SUM(CASE WHEN (p.result_code=8
									AND ((floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),p.age))<48)
									OR (floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),p.age))>=48)))
									THEN 1 ELSE 0 END) AS total_death*/

							SUM(CASE WHEN (p.result_code=8
											AND (DATEDIFF((IF(p.death_date='1970-01-01',p.discharge_date, p.death_date)), DATE(p.admission_dt))<=2))
									THEN 1 ELSE 0 END) AS deathbelow48,

							SUM(CASE WHEN (p.result_code=8
										 AND (DATEDIFF((IF(p.death_date='1970-01-01',p.discharge_date, p.death_date)), DATE(p.admission_dt))>2))
									THEN 1 ELSE 0 END) AS deathabove48,

							SUM(CASE WHEN p.result_code=8 THEN 1 ELSE 0 END) AS total_death

				FROM seg_rep_medrec_patient_icd_tbl AS p
				LEFT JOIN care_department AS d ON d.nr=p.current_dept_nr
				WHERE  p.encounter_type IN (3,4)
				AND p.discharge_date IS NOT NULL
				$whereSQL
				GROUP BY d.name_formal
				ORDER BY count(p.encounter_nr) DESC";

			#echo "sql = ".$sql;

			$result=$db->Execute($sql);
				if ($result) {

					$this->_count = $result->RecordCount();
						$this->Data=array();

					while ($row=$result->FetchRow()) {

				$total =  $row['total_rec'] +  $row['total_imp'] +  $row['total_unimp'] +  $row['total_death'];
				$this->Data[]=array(
							$row['Type_Of_Service'],
							$row['rec_disch'],
							$row['rec_trans'],
							$row['rec_hama'],
							$row['rec_absc'],
							$row['total_rec'],
							$row['imp_disch'],
							$row['imp_trans'],
							$row['imp_hama'],
							$row['imp_absc'],
							$row['total_imp'],
							$row['unimp_disch'],
							$row['unimp_trans'],
							$row['unimp_hama'],
							$row['unimp_absc'],
							$row['total_unimp'],
							$row['deathbelow48'],
							$row['deathabove48'],
							$row['total_death'],
					$total
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
