<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');

/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

	class RepGen_Medocs_MasterInpatientIndex extends RepGen {
	var $from, $to;
	var $colored = TRUE;

	function RepGen_Medocs_MasterInpatientIndex ($from, $to) {
		global $db;
		$this->RepGen("MEDICAL RECORDS: MASTER INPATIENT INDEX");
		# 165
		$this->ColumnWidth = array(22,20,53,18,18,38,7,20);
		$this->RowHeight = 5.5;
		$this->Alignment = array('L','L','L','C','C','L','C','L');
		$this->PageOrientation = "P";
		if ($from) $this->from=date("Y-m-d",strtotime($from));
		if ($to) $this->to=date("Y-m-d",strtotime($to));		
		$this->SetFillColor(0xFF);
		if ($this->colored) $this->SetDrawColor(0xDD);
	}
	
	function Header() {
		global $root_path, $db;
		
		$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',70,8,15);
		$this->SetFont("Arial","I","9");
		$total_w = 165;
		$this->Cell(17,4);
  	$this->Cell($total_w,4,'Republic of the Philippines',$border2,1,'C');
		$this->Cell(17,4);
	  $this->Cell($total_w,4,'DEPARTMENT OF HEALTH',$border2,1,'C');
  	$this->Ln(2);
		$this->SetFont("Arial","B","10");
		$this->Cell(17,4);
  	$this->Cell($total_w,4,'Gonzales Maranan Medical Center Incorporated',$border2,1,'C');
		$this->SetFont("Arial","","9");
		$this->Cell(17,4);
  	$this->Cell($total_w,4,'Digos City',$border2,1,'C');
  	$this->Ln(4);
	  $this->SetFont('Arial','B',12);
		$this->Cell(17,5);
  	$this->Cell($total_w,4,'MASTER INPATIENT INDEX',$border2,1,'C');
	  $this->SetFont('Arial','B',9);
		$this->Cell(17,5);
		if ($this->from || $this->to) {
			$text = "From ".date("F j, Y",strtotime($this->from))." to ".date("F j, Y",strtotime($this->to));
		}
		else
	  	$text = "Full History";
			
  	$this->Cell($total_w,4,$text,$border2,1,'C');
		$this->Ln(5);
		/*
		$from_dt=strtotime($this->from_date);
		$to_dt=strtotime($this->to_date);
		$this->SetFont("Arial","","9");
		if (!empty($this->from_date) && !empty($this->to_date))
			$this->Cell(0,5,
				sprintf('%s-%s',date("F j, Y",$from_dt),date("F j, Y",$to_dt)),
				$border2,1,'C');
		*/
		# Print table header
		
    $this->SetFont('Arial','B',8);
		if ($this->colored) $this->SetFillColor(0xED);
		$this->SetTextColor(0);
		$row=6;
		$this->Cell($this->ColumnWidth[0],$row,'PID',1,0,'C',1);
		$this->Cell($this->ColumnWidth[1],$row,'Case #',1,0,'C',1);
		$this->Cell($this->ColumnWidth[2],$row,'Patient Name',1,0,'C',1);
		$this->Cell($this->ColumnWidth[3],$row,'Admitted',1,0,'C',1);
		$this->Cell($this->ColumnWidth[4],$row,"Discharged",1,0,'C',1);
		$this->Cell($this->ColumnWidth[5],$row,'Department/Serv',1,0,'C',1);
		$this->Cell($this->ColumnWidth[6],$row,'Sex',1,0,'C',1);
		$this->Cell($this->ColumnWidth[7],$row,'Result',1,0,'C',1);
		$this->Ln();
	}
	
	function Footer()
	{
		$this->SetY(-23);
		$this->SetFont('Arial','I',8);
		$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
	}
	
	function BeforeData() {
		if ($this->colored) {
			$this->DrawColor = array(0xDD,0xDD,0xDD);
		}
	}
	
	function BeforeCellRender() {
		$this->FONTSIZE = 8;
		if ($this->colored) {
			if (($this->RENDERPAGEROWNUM%2)>0) 
				$this->RENDERCELL->FillColor=array(0xee, 0xef, 0xf4);
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
	
	function FetchData() {		
		global $db;

		if ($this->from) {
			$where[]="DATE(e.discharge_date) BETWEEN '$this->from' AND '$this->to'";
		}

		if ($where)
			$whereSQL = "AND (".implode(") AND (",$where).")";

		$sql = "
SELECT 
CONCAT(IFNULL(p.name_last,''),IFNULL(CONCAT(', ', p.name_first),''),IFNULL(CONCAT(', ', p.name_middle),'')) AS patient_name,p.sex,
e.pid, e.encounter_nr, e.admission_dt AS admission_date, e.discharge_date,
d.name_formal AS department_name,
r.result_desc
FROM care_encounter AS e
LEFT JOIN care_person AS p ON p.pid=e.pid
LEFT JOIN care_department AS d ON d.nr=e.current_dept_nr
LEFT JOIN seg_encounter_result AS er ON er.encounter_nr=e.encounter_nr
LEFT JOIN seg_results AS r ON r.result_code=er.result_code
WHERE (e.encounter_type=3 OR e.encounter_type=4) AND e.discharge_date IS NOT NULL $whereSQL\n";
		
		$sql .= "ORDER BY patient_name,discharge_date";
		$result=$db->Execute($sql);
		if ($result) {
			$this->_count = $result->RecordCount();
			$this->Data=array();
			while ($row=$result->FetchRow()) {
				$this->Data[]=array(
					$row['pid'],
					$row['encounter_nr'],
					$row['patient_name'],
					date("m/d/Y",strtotime($row['admission_date'])),
					date("m/d/Y",strtotime($row['discharge_date'])),
					$row['department_name'],
					strtoupper($row['sex']),
					$row['result_desc']);
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

$rep = new RepGen_Medocs_MasterInpatientIndex($_GET['from'], $_GET['to']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>