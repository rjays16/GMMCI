<?php
		error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

class RepGen_SentinelSurveillance extends RepGen {
	var $from, $to;
	var $colored = TRUE;

	function RepGen_SentinelSurveillance ($from, $to, $code, $sclass) {
		global $db;
		$this->RepGen("SENTINEL SURVEILLANCE WORKSHEET","L","Legal");

		$this->ColumnWidth = array(8,22,55,50,20,25,10,10,15,47,50,22,22);
		$this->RowHeight = 5.5;
		$this->Alignment = array('L','L','L','L','L','L','C','C','L','L','L','L');
		#$this->PageOrientation = "P";
		$this->LEFTMARGIN=1;
		$this->DEFAULT_TOPMARGIN = 2;
		$this->SetAutoPageBreak(FALSE);
		$this->NoWrap = FALSE;

		$this->code1 = trim($code).".-";
		$this->code2 = trim($code)."-";

		if($code != 'all')
			//$this->icd_cond = "AND (ed.code_parent = '".$this->code."'";
			$this->icd_cond = " AND IF(instr(ed.icd,'.'),
														substr(ed.icd,1,IF(instr(ed.icd,'.'),instr(ed.icd,'.')-1,0)),
														ed.icd) = '$code' ";
		else
			$this->icd_cond = "";

		if ($sclass=='primary'){
			$this->type_cond = " AND ed.type_nr='1' ";
		}elseif ($sclass=='secondary'){
			$this->type_cond = " AND ed.type_nr='0' ";
		}elseif ($sclass=='all')
			$this->type_cond = "";

		if ($from) $this->from=date("Y-m-d",strtotime($from));
		if ($to) $this->to=date("Y-m-d",strtotime($to));

		$this->SetFillColor(255);

		if ($this->colored) $this->SetDrawColor(255);
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

		$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',30,8,20);
		$this->SetFont("Arial","I","9");
		$total_w = 0;
		$this->Cell($total_w,4,$row['hosp_country'],$border2,1,'C');
		$this->Cell($total_w,4,$row['hosp_agency'],$border2,1,'C');
		$this->Ln(2);
		$this->SetFont("Arial","B","10");
		$this->Cell($total_w,4,$row['hosp_name'],$border2,1,'C');
		$this->SetFont("Arial","","9");
		$this->Cell($total_w,4,$row['hosp_addr1'],$border2,1,'C');
		$this->Ln(4);
		$this->SetFont('Arial','B',12);
		$this->Cell($total_w,4,'SENTINEL SURVEILLANCE WORKSHEET',$border2,1,'C');
		$this->SetFont('Arial','B',12);

		if($this->from == $this->to){
			$this->Cell(0,6, "As of ".date("F j, Y",strtotime($this->from)), 0,1,'C');
		}else{
			$this->Cell(0, 4, "From ".date("F j, Y", strtotime($this->from))." to ".date("F j, Y",strtotime($this->to)),0,1,'C');
		}

		$this->Cell($total_w,4,$text,$border2,1,'C');
		$this->Ln(5);

		# Print table header
		$this->SetFont('Arial','B',10);
		#if ($this->colored) $this->SetFillColor(0xED);
		if ($this->colored) $this->SetFillColor(255);
		$this->SetTextColor(0);
		$row=6;

		$this->Cell($this->ColumnWidth[0], $row, "",1,0,'L');
		$this->Cell($this->ColumnWidth[1], $row, "CASE NO.", 1,0,'L');
		$this->Cell($this->ColumnWidth[2], $row, "PATIENT NAME", 1,0,'L');
		$this->Cell($this->ColumnWidth[3], $row, "DIAGNOSIS", 1,0,'L');
		$this->Cell($this->ColumnWidth[4], $row, "ADMITTED", 1,0,'L');
		$this->Cell($this->ColumnWidth[5], $row, "DISCHARGED", 1,0,'L');
		$this->Cell($this->ColumnWidth[6], $row, "AGE", 1,0,'L');
		$this->Cell($this->ColumnWidth[7], $row, "SEX", 1,0,'L');
		$this->Cell($this->ColumnWidth[8], $row, "ICD 10", 1,0,'L');
		$this->Cell($this->ColumnWidth[9], $row, "ICD DESCRIPTION", 1,0,'L');
		$this->Cell($this->ColumnWidth[10], $row, "ADDRESS", 1,0,'L');
		$this->Cell($this->ColumnWidth[11], $row, "IF DENGUE", 1,0,'L');
		$this->Cell($this->ColumnWidth[12], $row, "FATALITY", 1,0,'L');
		$this->Ln();

	}

	function Footer()
	{
		$this->SetY(-7);
		$this->SetFont('Arial','I',8);
		$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
	}

	function BeforeData() {
		$this->FONTSIZE = 10;
		if ($this->colored) {
			#$this->DrawColor = array(0xDD,0xDD,0xDD);
		$this->DrawColor = array(255,255,255);
		}
	}

	function BeforeCellRender() {
		$this->FONTSIZE = 10;

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
			$this->SetFont('Arial','B',12);
			$this->SetFillColor(255);
			$this->SetTextColor(0);
			$this->Cell(0, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
		}

		$cols = array();
	}

	function FetchData() {
		global $db;

		$sql = "SELECT DISTINCT ed.*
						FROM seg_rep_medrec_patient_icd_tbl AS ed
						WHERE DATE(ed.admission_dt) BETWEEN  '".$this->from."' AND '".$this->to."'
						".$this->type_cond."
						AND ed.encounter_type IN (3,4)
						ORDER BY ed.patient_name, ed.icd, DATE(ed.admission_dt) ASC";

	 # echo "sql = ".$sql;
		$result=$db->Execute($sql);
		if ($result) {
			$this->_count = $result->RecordCount();
			$this->Data=array();
			$i=1;
			while ($row=$result->FetchRow()) {
				if ((!(empty($row['discharge_date']))) || ($row['discharge_date']!='0000-00-00'))
					$discharged_date = date("m/d/Y",strtotime($row['discharge_date']));

				if ((!(empty($row['admission_dt']))) || ($row['admission_dt']!='0000-00-00'))
					$admission_date = date("m/d/Y h:i A",strtotime($row['admission_dt']));

				if ((!(empty($row['fatality']))) || ($row['fatality']!='0000-00-00'))
					$fatality = date("m/d/Y",strtotime($row['fatality']));

					$this->Data[]=array(
						$i,
						trim($row['encounter_nr']),
						mb_strtoupper(trim($row['patient_name'])),
						trim($row['diagnosis']),
						trim($admission_date),
						trim($discharged_date),
						trim($row['age']),
						strtoupper(trim($row['sex'])),
						trim($row['icd']),
						trim($row['icd_desc']),
						mb_strtoupper(trim($row['address'])),
						trim($row['if_dengue']),
						trim($fatality)
					);

					$i++;
				}
		}/*
		else {
			print_r($sql);
			print_r($db->ErrorMsg());
			exit;
			# Error
		}*/
	}
}

$rep = new RepGen_SentinelSurveillance($from, $to, $code,$_GET['sclass']);
$rep->AliasNbPages();
$rep->FetchData();
ini_set('memory_limit', '2048M');
$rep->Report();
?>
