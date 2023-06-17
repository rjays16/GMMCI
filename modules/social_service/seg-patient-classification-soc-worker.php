<?php

require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

class RepGen_PatientClassification extends RepGen{
var $colored = TRUE;
var $from, $to;

	 function RepGen_PatientClassification ($from, $to) {
		global $db;
		$this->RepGen("MEDICAL RECORDS: PATIENT CLASSIFICATION");

		$this->ColumnWidth = array(50, 10,10,10,10,10, 10,10,10,10,10, 10,10,10,10,10);
		$this->RowHeight = 5;
		$this->TextHeight = 5;
		$this->TextPadding = 0.2;
		$this->Alignment = array('L', 'R', 'R', 'R','R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R');
		$this->TableLabel = array('SOCIAL WORKER', 'OPD', 'ER', 'ADMISSION');
		$this->TableLabel2 = array('C1', 'C2', 'C3', 'D', 'SC');
		$this->PageOrientation = "P";
		$this->NoWrap = FALSE;
		$this->LEFTMARGIN = 8;

		if ($from) $this->from=date("Y-m-d",strtotime($from));
				if ($to) $this->to=date("Y-m-d",strtotime($to));

		$this->useMultiCell = TRUE;
		#$this->SetFillColor(0xFF);
		$this->SetFillColor(255);
		if ($this->colored)  $this->SetDrawColor(0xDD);

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
		$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',40,8,20);
		$this->SetFont("Arial","I","9");
		//$total_w = 165;
		//$this->Cell(50,4);
			#$this->Cell($total_w,4,'Republic of the Philippines',$border2,1,'C');
		$this->Cell($total_w,4,$row['hosp_country'],$border2,1,'C');
		//$this->Cell(50,4);
			#$this->Cell($total_w,4,'DEPARTMENT OF HEALTH',$border2,1,'C');
		$this->Cell($total_w,4,$row['hosp_agency'],$border2,1,'C');
			$this->Ln(2);
		$this->SetFont("Arial","B","10");
		//$this->Cell(50,4);
			#$this->Cell($total_w,4,'Gonzales Maranan Medical Center Incorporated',$border2,1,'C');
		$this->Cell($total_w,4,$row['hosp_name'],$border2,1,'C');
		$this->SetFont("Arial","","9");
		//$this->Cell(50,4);
			#$this->Cell($total_w,4,'Bajada, Davao City',$border2,1,'C');
		$this->Cell($total_w,4,$row['hosp_addr1'],$border2,1,'C');
			$this->Ln(4);
			$this->SetFont('Arial','B',12);
		//$this->Cell(50,5);

		$this->Cell($total_w,4,'PATIENT CLASSIFICATION STATISTICS',$border2,1,'C');
		 $this->SetFont('Arial','B',9);
		//$this->Cell(50,5);

		if ($this->from==$this->to)
			$text = "For ".date("F j, Y",strtotime($this->from));
		else
				#$text = "Full History";
			$text = "From ".date("F j, Y",strtotime($this->from))." To ".date("F j, Y",strtotime($this->to));

			$this->Cell($total_w,4,$text,$border2,1,'C');
		$this->Ln(5);

		# Print table header
		#$this->Cell(30,4,'No. of Records : ',$border2,0,'L');
		#$this->SetFont('Arial','B',9);
		#$this->Cell(20,4,$this->_count,$border2,1,'L');

			$this->SetFont('Arial','B',8);
		#if ($this->colored) $this->SetFillColor(0xED);
		if ($this->colored) $this->SetFillColor(255);
		$this->SetTextColor(0);
		$row=6;
		$colwidth1 = $this->ColumnWidth[1] + $this->ColumnWidth[2] + $this->ColumnWidth[3] +$this->ColumnWidth[4] + $this->Columnwidth[5] + $this->ColumnWidth[6];
		$colwidth2 = $this->ColumnWidth[6]+$this->ColumnWidth[7]+$this->ColumnWidth[8]+$this->ColumnWidth[9]+$this->ColumnWidth[10];
		$colwidth3 = $this->ColumnWidth[11]+$this->ColumnWidth[12]+$this->ColumnWidth[13]+$this->ColumnWidth[14]+$this->ColumnWidth[15];

		$this->Cell($this->ColumnWidth[0], $row, $this->TableLabel[0], "TLR", 0,'C');
		$this->Cell($colwidth1, $row, $this->TableLabel[1], 1, 0, 'C');
		$this->Cell($colwidth2, $row, $this->TableLabel[2], 1, 0, 'C');
		$this->Cell($colwidth3, $row, $this->TableLabel[3], 1, 1, 'C');

		$this->Cell($this->ColumnWidth[0], $row, "", "BLR", 0);
		$num_class = 3;
		for($cnt=0; $cnt<$num_class; $cnt++){
			$this->Cell($this->ColumnWidth[1], $row, $this->TableLabel2[0], 1, 0, 'C');
			$this->Cell($this->ColumnWidth[2], $row, $this->TableLabel2[1], 1, 0, 'C');
			$this->Cell($this->ColumnWidth[3], $row, $this->TableLabel2[2], 1, 0, 'C');
			$this->Cell($this->ColumnWidth[4], $row, $this->TableLabel2[3], 1, 0, 'C');
			$this->Cell($this->ColumnWidth[5], $row, $this->TableLabel2[4], 1, 0, 'C');
		}
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

	 $sql ="SELECT CONCAT(IF (trim(p.name_last) IS NULL,'',trim(p.name_last)),', ',
		IF(trim(p.name_first) IS NULL ,'',trim(p.name_first)),' ',
		IF(trim(p.name_middle) IS NULL,'',trim(p.name_middle))) AS Social_Worker,
		a.personell_nr,
		SUM(CASE WHEN e.encounter_type = '2' AND cha.discountid='C1' then 1 else 0 end) AS OPD_c1,
		SUM(CASE WHEN e.encounter_type = '2' AND cha.discountid='C2' then 1 else 0 end) AS OPD_c2,
		SUM(CASE WHEN e.encounter_type = '2' AND (d.parentid='C3' OR cha.discountid='C3') then 1 else 0 end) AS OPD_c3,
		SUM(CASE WHEN e.encounter_type = '2' AND ((d.parentid='D' AND cha.discountid!='SC') OR cha.discountid='D') then 1 else 0 end) AS OPD_d,
		SUM(CASE WHEN e.encounter_type = '2' AND cha.discountid='SC' then 1 else 0 end) AS OPD_sc,
		SUM(CASE WHEN e.encounter_type = '1' AND cha.discountid='C1' then 1 else 0 end) AS ER_c1,
		SUM(CASE WHEN e.encounter_type = '1' AND cha.discountid='C2' then 1 else 0 end) AS ER_c2,
		SUM(CASE WHEN e.encounter_type = '1' AND (d.parentid='C3' OR cha.discountid='C3') then 1 else 0 end) AS ER_c3,
		SUM(CASE WHEN e.encounter_type = '1' AND ((d.parentid='D' AND cha.discountid!='SC') OR cha.discountid='D') then 1 else 0 end) AS ER_d,
		SUM(CASE WHEN e.encounter_type = '1' AND cha.discountid='SC' then 1 else 0 end) AS ER_sc,
		SUM(CASE WHEN (e.encounter_type = '3' OR e.encounter_type = '4') AND cha.discountid='C1' then 1 else 0 end) AS Admission_c1,
		SUM(CASE WHEN (e.encounter_type = '3' OR e.encounter_type = '4') AND cha.discountid='C2' then 1 else 0 end) AS Admission_c2,
		SUM(CASE WHEN (e.encounter_type = '3' OR e.encounter_type = '4') AND (d.parentid='C3' OR cha.discountid='C3') then 1 else 0 end) AS Admission_c3,
		SUM(CASE WHEN (e.encounter_type = '3' OR e.encounter_type = '4') AND ((d.parentid='D' AND cha.discountid!='SC') OR cha.discountid='D') then 1 else 0 end) AS Admission_d,
		SUM(CASE WHEN (e.encounter_type = '3' OR e.encounter_type = '4') AND cha.discountid='SC' then 1 else 0 end) AS Admission_sc
		FROM care_personell_assignment AS a
		INNER JOIN care_personell AS ps ON ps.nr=a.personell_nr
		INNER JOIN care_person AS p ON p.pid = ps.pid
		LEFT JOIN (SELECT scp.pid, scp.discountid, scp.grant_dte, scp.sw_nr FROM seg_charity_grants_pid AS scp
					WHERE DATE(scp.grant_dte) BETWEEN '".$this->from."' AND '".$this->to."' UNION ALL
					SELECT ce.pid,  scg.discountid, scg.grant_dte, scg.sw_nr FROM seg_charity_grants AS scg
					INNER JOIN care_encounter AS ce ON ce.encounter_nr = scg.encounter_nr
					WHERE DATE(scg.grant_dte) BETWEEN '".$this->from."' AND '".$this->to."') AS cha ON cha.sw_nr = a.personell_nr
		LEFT JOIN care_encounter AS e ON (e.pid = cha.pid)
		LEFT JOIN seg_discount AS d ON d.discountid = cha.discountid
		WHERE a.location_nr='168'
		AND a.status NOT IN ('deleted','hidden','void')
		GROUP BY Social_Worker, a.personell_nr ASC
				;";
			#echo "".$sql;
			$result=$db->Execute($sql);
				if ($result) {

					$this->_count = $result->RecordCount();
						$this->Data=array();

					while ($row=$result->FetchRow()) {
										$this->Data[]=array(
												strtoupper($row['Social_Worker']),
												$row['OPD_c1'],
												$row['OPD_c2'],
												$row['OPD_c3'],
												$row['OPD_d'],
												$row['OPD_sc'],
												$row['ER_c1'],
												$row['ER_c2'],
												$row['ER_c3'],
												$row['ER_d'],
												$row['ER_sc'],
												$row['Admission_c1'],
												$row['Admission_c2'],
												$row['Admission_c3'],
												$row['Admission_d'],
												$row['Admission_sc']
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

$rep = new RepGen_PatientClassification($_GET['from'], $_GET['to']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>