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

		$this->ColumnWidth = array(31 , 50, 35, 35, 35);
		$this->RowHeight = 5;
		$this->TextHeight = 5;
		$this->TextPadding = 0.2;
		$this->Alignment = array('L', 'L', 'R', 'R', 'R');
		$this->TableLabel = array('CLASSIFICATION', 'DESCRIPTION', 'OPD', 'ER', 'ADMISSION');
		$this->PageOrientation = "P";
		$this->NoWrap = FALSE;
		$this->LEFTMARGIN = 15;

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
		$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',50,8,20);
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
		$this->Cell($this->ColumnWidth[0], $row, $this->TableLabel[0], 1, 0,'C');
		$this->Cell($this->ColumnWidth[1], $row, $this->TableLabel[1], 1, 0, 'C');
		$this->Cell($this->ColumnWidth[2], $row, $this->TableLabel[2], 1, 0, 'C');
		$this->Cell($this->ColumnWidth[3], $row, $this->TableLabel[3], 1, 0, 'C');
		$this->Cell($this->ColumnWidth[4], $row, $this->TableLabel[4], 1, 0, 'C');
		#$this->Cell($this->ColumnWidth[5], $row, $this->TableLabel[5], 1, 0, 'C');
		#$this->Cell($this->ColumnWidth[6], $row, $this->TableLabel[6], 1, 0, 'C');
		#$this->Cell($this->ColumnWidth[7], $row, $this->TableLabel[7], 1, 0, 'C');
		#$this->Cell($this->ColumnWidth[8], $row, $this->TableLabel[8], 1, 0, 'C');
		#$this->Cell($this->ColumnWidth[9], $row, $this->TableLabel[9], 1, 0, 'C');
		#$this->Cell($this->ColumnWidth[10], $row, $this->TableLabel[10], 1, 0, 'C');
		#$this->Cell($this->ColumnWidth[11], $row, $this->TableLabel[11], 1, 0, 'C');
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
		$this->FetchData();
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


				/*$sql = "SELECT d.discountid, d.parentid, d.discountdesc,
								SUM(CASE WHEN e.encounter_type = '2' THEN 1 ELSE 0 END) AS OPD,
								'' AS ER,
								'' AS Admission,
								'1' AS opd_class
								FROM care_encounter AS e
								INNER JOIN seg_charity_grants_pid AS scp ON e.pid=scp.pid
								INNER JOIN seg_discount AS d ON d.discountid=scp.discountid
								WHERE DATE(scp.grant_dte) BETWEEN '".$this->from."' AND '".$this->to."'
								GROUP BY d.discountid
								HAVING d.discountid <> 'LINGAP'
								UNION
								SELECT d.discountid, d.parentid, d.discountdesc,
								'' AS OPD,
								SUM(CASE WHEN e.encounter_type = '1' THEN 1 ELSE 0 END) AS ER,
								SUM(CASE WHEN e.encounter_type = '3' OR e.encounter_type = '4' THEN 1 ELSE 0 END) AS Admission,
								'0' AS opd_class
								FROM care_encounter AS e
								INNER JOIN  seg_charity_grants AS scp ON e.encounter_nr=scp.encounter_nr
								INNER JOIN seg_discount AS d ON d.discountid=scp.discountid
								WHERE DATE(scp.grant_dte) BETWEEN '".$this->from."' AND '".$this->to."'
								GROUP BY d.discountid
								HAVING d.discountid <> 'LINGAP'
								ORDER BY discountid";*/

			#edited by VAN 03-17-2011
			$db->Execute("CALL sp_populate_ss_data('".$this->from."','".$this->to."')");

			$sql = "SELECT scp.discountid, d.parentid, d.discountdesc,
							SUM(CASE WHEN (encounter_type = '2') THEN 1 ELSE 0 END) AS OPD,
							SUM(CASE WHEN (encounter_type = '1') THEN 1 ELSE 0 END) AS ER,
							SUM(CASE WHEN (encounter_type = '3' OR encounter_type = '4') THEN 1 ELSE 0 END) AS Admission
							FROM seg_rep_ss_data_tbl AS scp
							INNER JOIN seg_discount AS d ON d.discountid=scp.discountid
							WHERE DATE(grant_date) BETWEEN '".$this->from."' AND '".$this->to."'
							GROUP BY d.parentid, scp.discountid";

			#echo "".$sql;
			$result=$db->Execute($sql);
				if ($result) {

					$this->_count = $result->RecordCount();
					$this->Data=array();

					while ($row=$result->FetchRow()) {

						if(!$row['parentid']==''){
						#if($row['parentid']!=''){
							$classification = $row['parentid']."   - ".$row['discountid'];
						}else{
							$classification = $row['discountid'];
						}

										$this->Data[]=array(
												$classification,
												$row['discountdesc'],
												$row['OPD'],
												$row['ER'],
												$row['Admission']

										 );

							$prev_discountid = $row['discountid'];
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
#$this->FetchData();
$rep->Report();
?>