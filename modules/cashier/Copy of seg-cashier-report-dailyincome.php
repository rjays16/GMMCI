<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');

/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

	class RepGen_Cashier_DailyIncome extends RepGen {
	var $encoder;
	var $date;
	var $detailed;

	function RepGen_Cashier_DailyIncome ($encoder, $date=FALSE, $detailed=FALSE) {
		global $db;
		$this->RepGen("CASHIER");
		#$this->ColumnWidth = array(25,60,20,18,18,20,100,21);
		$this->ColumnWidth = array(35,110,20);
		$this->RowHeight = 6;
		$this->Alignment = array('L','L','L');
		$this->PageOrientation = "P";
		if ($date) $this->date=date("Ymd",strtotime($date));
		$this->encoder=$encoder;
		$this->detailed=$detailed;
	}
	
	function Header() {
		global $root_path;
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
	  $this->SetFont('Arial','B',11);
		$this->Cell(17,5);
  	$this->Cell($total_w,5,'Cashier Daily Income',$border2,1,'C');
		$this->Ln(3);
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
		
    $this->SetFont('Arial','B',9 );
		$this->SetFillColor(150);
		$this->SetTextColor(255);
		$row=7;
		$this->Cell($this->ColumnWidth[0],$row,'Item Code',1,0,'C',1);
		$this->Cell($this->ColumnWidth[1],$row,'Item Description',1,0,'C',1);
		$this->Cell($this->ColumnWidth[2],$row,'Quantity',1,0,'C',1);
		$this->Cell($this->ColumnWidth[3],$row,'Total Amount',1,0,'C',1);
		$this->Ln();
	}
	
	function BeforeRow() {
		if (($this->ROWNUM%2)==0) 
			$this->FILLCOLOR=array(0xde, 0xdf, 0xe4);
		else
			$this->FILLCOLOR=array(255,255,255);
	}
	
	function FetchData() {		
		global $db;
		$sql = "SELECT i.bestellnum, p.artikelname, COUNT(*)\n".
			"FROM seg_pharma_orders AS o\n".
			"INNER JOIN seg_pharma_order_items AS i ON i.refno=o.refno\n".
			"LEFT JOIN care_pharma_products_main AS p ON p.bestellnum=i.bestellnum\n";
		$where = 	"i.serve_status='S'"; 
		if ($this->area)
			$where.= " AND o.pharma_area='$this->area'";
		if ($this->date) {
			$where.= " AND DATE(o.orderdate)='$this->date'";
		}
		$sql .= "WHERE $where GROUP BY bestellnum ORDER BY artikelname";
		$result=$db->Execute($sql);
		if ($result) {
			$this->Data=array();
			while ($row=$result->FetchRow()) {
				$this->Data[]=array($row[0],$row[1],$row[2]);
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

$iss = new RepGen_Cashier_DailyIncome($_GET['area'],$_GET['date']);
$iss->FetchData();
$iss->Report();
?>