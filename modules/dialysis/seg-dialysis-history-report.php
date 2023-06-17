<?php
//created by CHA, July 30, 2010
//history report for dialysis transaction
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
//require($root_path.'/modules/repgen/repgen.inc.php');
require($root_path.'/modules/repgen/repgenclass.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/class_department.php');
require_once($root_path.'include/care_api_classes/dialysis/class_dialysis.php');

class Dialysis_History_Report extends RepGen {

	var $pid;
	var $total_width=0;

	function Dialysis_History_Report($pid)
	{
		global $db;
		$this->RepGen("DIALYSIS HISTORY REPORT");
		$this->ColumnLabels = array(
				'Reference No.',
				'Case No.',
				'Date Visited',
				'Requesting Doctor',
				'Attending Nurse',
				'Dialysis',
				'Request Status'
			);
		$this->colored = FALSE;
		$this->ColumnWidth = array(25,20,20,35,35,20,40);
		$this->Columns = 7;
		$this->RowHeight = 6;
		$this->TextHeight = 6;
		$this->Alignment = array('C','C','C','L','L','C','C');
		$this->PageOrientation = "P";
		$this->SetMargins(1,1,1);
		$this->total_width = array_sum($this->ColumnWidth);
		$this->NoWrap=FALSE;
		//$this->LEFTMARGIN = 35;

		$this->pid = $pid;

		if ($this->colored) $this->SetDrawColor(0xDD);
	}

	function Header()
	{
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
			$row['hosp_addr1']   = "Quezon Ave., Digos City, Davao del Sur";
		}

		$this->SetFont("Arial","I","9");
		$total_w = 100;
		$this->Cell(45,4);
		$this->Cell($total_w,4,$row['hosp_country'],$border2,1,'C');
		$this->Cell(45,4);
		$this->Cell($total_w,4,$row['hosp_agency'],$border2,1,'C');
		$this->Ln(2);
		$this->SetFont("Arial","B","10");
		$this->Cell(45,4);
		$this->Cell($total_w,4,$row['hosp_name'],$border2,1,'C');
		$this->SetFont("Arial","","9");
		$this->Cell(45,4);
		$this->Cell($total_w,4,$row['hosp_addr1'],$border2,1,'C');
		$this->Ln(4);

		$this->Cell(45,5);
		$this->Cell($total_w,4,'HISTORY OF DIALYSIS TRANSACTIONS',$border2,1,'C');
		$this->SetFont('Arial','B',9);
		$this->Cell(45,5);

		$this->Ln();
		$dialysis_obj = new SegDialysis();
		$data = $dialysis_obj->getTransactionByPid($this->pid);
		$this->Cell(20,4,'Patient Name : '.$data["patient_name"],$border2,1,'L');
		$this->Cell(20,4,'PID : '.$this->pid,$border2,1,'L');
		$sql = "SELECT count(refno) FROM seg_dialysis_transaction WHERE pid=".$db->qstr($this->pid);
		$visit_no = $db->GetOne($sql);
		$this->Cell(20,4,'No.of Visits : '.$visit_no,$border2,1,'L');
		$this->Ln(2);

		parent::Header();
	}

	function BeforeData()
	{
		if ($this->colored) {
			$this->DrawColor = array(0xDD,0xDD,0xDD);
		}
		$this->ColumnFontSize = 9;
	}

	/*function BeforeCellRender()
	{
			$this->FONTSIZE = 8;
			if ($this->colored) {
					if (($this->RENDERPAGEROWNUM%2)>0)
							$this->RENDERCELL->FillColor=array(0xee, 0xef, 0xf4);
					else
							$this->RENDERCELL->FillColor=array(255,255,255);
			}
	}*/
	function Footer()
	{
		parent::Footer();
	}

	function AfterData()
	{
			global $db;
			if (!$this->_count) {
					$this->SetFont('Arial','B',9);
					$this->SetFillColor(255);
					$this->SetTextColor(0);
					$this->Cell($this->total_width, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
			}

			$cols = array();
	}

	function FetchData()
	{
		global $db;

		$sql = "SELECT dt.*, fn_get_person_name(dt.pid) AS `patient_name`, \n".
					"(SELECT fn_get_person_name(pe.pid) \n".
						"	FROM care_personell AS pe INNER JOIN care_person AS cp ON pe.pid=cp.pid WHERE pe.nr=dt.requesting_doctor) AS `requesting_doctor`, \n".
					"(SELECT fn_get_person_name(pe.pid) \n".
						"	FROM care_personell AS pe INNER JOIN care_person AS cp ON cp.pid=pe.pid WHERE pe.nr=dt.attending_nurse) AS `attending_nurse` \n".
				"FROM seg_dialysis_transaction AS dt \n".
				"INNER JOIN care_encounter AS ce ON dt.encounter_nr=ce.encounter_nr \n".
				"INNER JOIN care_person AS cp ON ce.pid=cp.pid WHERE dt.pid=".$db->qstr($this->pid);
		$result=$db->Execute($sql);
		$this->_count = $result->RecordCount();

		if ($result!==FALSE) {
			$this->Data=array();
			 while ($row=$result->FetchRow())
			 {
					$this->Data[]=array(
						 $row["refno"],
						 $row["encounter_nr"],
						 date('d-M-Y h:i a',strtotime($row["transaction_date"])),
						 $row["requesting_doctor"],
						 $row["attending_nurse"],
						 $row["dialysis_type"],
						 $row["status"].($row["is_deleted"]?' (DELETED)':'')
					);
			}
		}
		else {
			echo "error:".$db->ErrorMsg();
		}
	}
}

$rep = new Dialysis_History_Report($_GET["pid"]);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>
