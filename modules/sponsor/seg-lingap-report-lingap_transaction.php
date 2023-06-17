<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');

/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

class RepGen_Lingap_Transaction extends RepGen {

	var $pid;
	var $pname;
	var $date_from;
	var $date_to;
	var $total_amount;

	function RepGen_Lingap_Transaction($pid='', $pname='', $datefrom, $dateto)
	{
		global $db;
		$this->RepGen("LINGAP PATIENT TRANSACTION HISTORY");
		$this->Headers = array(
				'Date', 'Control Nr',
				'Transaction Type', 'Amount', 'Remarks'
		);
		$this->colored = TRUE;
		$this->ColumnWidth = array(36,22,60,30,20,20);
		$this->RowHeight = 6;
		$this->Alignment = array('L','L','C','R','L');
		$this->PageOrientation = "P";
		if ($datefrom) $this->date_from=date("Y-m-d",strtotime($datefrom));
		else $this->date_from=date("Y-m-d");
		if ($dateto) $this->date_to=date("Y-m-d",strtotime($dateto));
		else $this->date_to=date("Y-m-d");
		$this->pid=$pid;
		$this->pname=$pname;
		if ($this->colored)    $this->SetDrawColor(0xDD);
	}

	function Header()
	{
		global $root_path, $db;
		$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',70,6,15);
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
		$this->Cell($total_w,4,'Quezon Ave., Digos City',$border2,1,'C');
		$this->Ln(6);
		$this->SetFont('Arial','B',12);
		$this->Cell(17,5);
		$this->Cell($total_w,4,'LINGAP PATIENT TRANSACTION HISTORY',$border2,1,'C');
		$this->SetFont('Arial','B',9);
		$this->Cell(17,5);

		$this->Ln(2);
		$this->Cell(17,5);
		$this->Cell($total_w,4,$this->pname,$border2,1,'C');
		$this->Cell(17,5);
		if($this->date_from && $this->date_to)
		{
				$this->Cell($total_w,4,date("F j, Y",strtotime($this->date_from))." to ".date("F j, Y",strtotime($this->date_to)),$border2,1,'C');
		}
		$this->Ln();
		$this->SetTextColor(0);
		$row=5;
		$this->SetFont('Arial','B',9);
		$this->Cell($this->ColumnWidth[0],$this->RowHeight,$this->Headers[0],1,0,'C',1);
		$this->Cell($this->ColumnWidth[1],$this->RowHeight,$this->Headers[1],1,0,'C',1);
		$this->Cell($this->ColumnWidth[2],$this->RowHeight,$this->Headers[2],1,0,'C',1);
		$this->Cell($this->ColumnWidth[3],$this->RowHeight,$this->Headers[3],1,0,'C',1);
		$this->Cell($this->ColumnWidth[4],$this->RowHeight,$this->Headers[4],1,0,'C',1);
		$this->Ln();

	}

	function Footer()
	{
		$this->SetY(-23);
		$this->SetFont('Arial','I',8);
		$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
	}

	function BeforeData()
	{
		if ($this->colored) {
				$this->DrawColor = array(0xDD,0xDD,0xDD);
		}
		$this->ColumnFontSize = 9;
	}

	function BeforeCellRender()
	{
		$this->FONTSIZE = 8;
		if ($this->colored) {
			if (($this->RENDERPAGEROWNUM%2)>0)
				$this->RENDERCELL->FillColor=array(0xee, 0xef, 0xf4);
			else
				$this->RENDERCELL->FillColor=array(255,255,255);
		}
	}

	function AfterData()
	{
		global $db;

		if (!$this->_count) {
			$this->SetFont('Arial','B',9);
			$this->SetFillColor(255);
			$this->SetTextColor(0);
			$this->Cell(205, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
		}

		$cols = array();
	}

	function FetchData()
	{
		$this->SetFont('Arial','',8);
		global $db;
		$where_date="";
		if($this->date_from!=$this->date_to)
		{
			$where_date=" and (lp.entry_date between '".$this->date_from." 00:00:00' and '".$this->date_to." 23:59:59') ";
		}
		else if($this->date_from==$this->date_to)
		{
			$where_date=" and (lp.entry_date like '".$this->date_from." %') ";
		}
		$sql="select lp.control_nr, lp.entry_date, lp.entry_type, lp.amount, lp.remarks".
		" from seg_lingap_ledger_patient as lp where lp.pid=".$db->qstr($this->pid).
		$where_date.
		" order by lp.control_nr";
		#echo "query: ".$sql;
		$result = $db->Execute($sql);

		if($result->RecordCount()) {
			$this->_count = $result->RecordCount();
			$this->Data=array();
			while($row=$result->FetchRow()) {
				$this->Data[]=array(
						$row['entry_date'],$row['control_nr'],
						$row['entry_type'], $row['amount'], $row['remarks']
				);
					$this->total_amount+=($row['amount']);
			}
			$this->Data[]=array(
				"TOTAL AMOUNT","","",number_format($this->total_amount,2),""
			);
		}
		else
		{
			print_r($sql);
			print_r($db->ErrorMsg());
			exit;
			# Error
		}
	}

}
$rep =& new RepGen_Lingap_Transaction($_GET['pid'], $_GET['pname'], $_GET['datefrom'], $_GET['dateto']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();

?>