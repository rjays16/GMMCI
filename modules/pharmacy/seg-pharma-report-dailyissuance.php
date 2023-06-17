<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');

/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

	class RepGen_Pharma_Issuance extends RepGen {
	var $area;
	var $date;
	var $encoder;
	var $shift_start;
	var $shift_end;

	function RepGen_Pharma_Issuance ($area, $date, $encoder, $shift_start, $shift_end, $full) {
		global $db;
		$this->RepGen("PHARMACY DAILY ISSUANCE");
		#$this->ColumnWidth = array(25,60,20,18,18,20,100,21);
		# 165
		$this->colored = FALSE;
		$this->ColumnWidth = array(28,68,18,22,22,18,20);
		$this->RowHeight = 6;
		$this->Alignment = array('L','L','R','R','R','R','R');
		$this->PageOrientation = "P";
		if ($date) $this->date=date("Ymd",strtotime($date));
		$this->encoder=$encoder;
		$this->shift_start=$shift_start;
		$this->shift_end=$shift_end;
		$this->area=$area;
		$this->full=$full;
		if ($this->colored)	$this->SetDrawColor(0xDD);
	}
	
	function Header() {
		global $root_path, $db;
		
		if ($this->encoder) {
			$sql = "SELECT name FROM care_users WHERE login_id=".$db->qstr($this->encoder);
			$this->encoderName = $db->GetOne($sql);
		}
		
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
  	$this->Cell($total_w,4,'Quezon Ave., Digos City',$border2,1,'C');
  	$this->Ln(4);
	  $this->SetFont('Arial','B',12);
		$this->Cell(17,5);
  	$this->Cell($total_w,4,'PHARMACY DAILY ISSUANCE',$border2,1,'C');
	  $this->SetFont('Arial','B',9);
		$this->Cell(17,5);
		if ($this->date) {
			$text = "For ".date("F j, Y",strtotime($this->date));
#			print_r($this->shift_start);
#			print_r($this->shift_end);
#			print_r($_GET);
#			exit;
			
			if ($this->shift_start != $this->shift_end) {
				$time1 = (int)$this->shift_start;
				if ($time1 == 0) $time1 = "12:00mn";
				elseif ($time1 == 12) $time1 = "12:00nn";
				else $time1 = ($time1%12).":00".($time1/12>0 ? "am" : "pm");
				
				$time2 = (int)$this->shift_end;
				if ($time2 == 0) $time2 = "12:00mn";
				elseif ($time2 == 12) $time2 = "12:00nn";
				else $time2 = ($time2%12).":00".($time2/12>0 ? "am" : "pm");
				
				$text .= " ($time1 to $time2)";
			}
		}
		else
	  	$text = "All issuances";
			
  	$this->Cell($total_w,4,$text,$border2,1,'C');
		$this->Ln(5);

		# Print table header
    $this->SetFont('Arial','B',9);
		if ($this->colored) $this->SetFillColor(0xED);
		$this->SetTextColor(0);
		$row=7;
		$this->Cell($this->ColumnWidth[0],$row,'Item Code',1,0,'C',1);
		$this->Cell($this->ColumnWidth[1],$row,'Item Description',1,0,'C',1);
		$this->Cell($this->ColumnWidth[2],$row,'Beg Bal.',1,0,'C',1);
		$this->Cell($this->ColumnWidth[3],$row,'Total Issued',1,0,'C',1);
		$this->Cell($this->ColumnWidth[4],$row,"Ward Stocks",1,0,'C',1);
		$this->Cell($this->ColumnWidth[5],$row,'Returns',1,0,'C',1);
		$this->Cell($this->ColumnWidth[6],$row,'End Bal.',1,0,'C',1);
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
		$this->ColumnFontSize = 9;
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

		if ($this->date) {
			$whereOrder[]="DATE(o.orderdate)='$this->date'";
			$whereWard[]="DATE(w.stock_date)='$this->date'";
			$whereReturn[]="DATE(r.return_date)='$this->date'";
			
			$dTime = strtotime($this->date);
			if (is_numeric($this->shift_start) && is_numeric($this->shift_end)) {
				if ($this->shift_start != $this->shift_end) {
					if ($this->shift_start >= $this->shift_end) {
						$start_time = $dTime + $this->shift_start*3600;
						$end_time = $dTime + $this->shift_end*3600;
					}
					else {
						$start_time = $dTime + $this->shift_start*3600;
						$end_time = $dTime + $this->shift_end*3600+86400;
					}
					$whereOrder[] = "o.orderdate>='".date("YmdHis",$start_time)."' AND o.orderdate<='".date("YmdHis",$end_time)."'";
					$whereWard[] = "w.stock_date>='".date("YmdHis",$start_time)."' AND w.stock_date<='".date("YmdHis",$end_time)."'";
					$whereReturn[] = "r.return_date>='".date("YmdHis",$start_time)."' AND r.return_date<='".date("YmdHis",$end_time)."'";
				}
			}
			elseif (is_numeric($this->shift_start)) {
				$start_time = $dTime + $this->shift_start*3600;
				$whereOrder[] = "o.orderdate>='".date("YmdHis",$start_time)."' AND o.orderdate<=NOW()";
				$whereWard[] = "w.stock_date>='".date("YmdHis",$start_time)."' AND w.stock_date<=NOW()";
				$whereReturn[] = "r.return_date>='".date("YmdHis",$start_time)."' AND r.return_date<=NOW()";
			}
		}
		if ($this->encoder) {
			$whereOrder[]="o.create_id=".$db->qstr($this->encoder);
			$whereWard[]="w.create_id=".$db->qstr($this->encoder);
			$whereReturn[]="r.create_id=".$db->qstr($this->encoder);
		}

		if ($whereOrder)
			$sqlOrder = "AND (".implode(") AND (",$whereOrder).")";
		if ($whereReturn)
			$sqlReturn = "AND (".implode(") AND (",$whereReturn).")";
		if ($whereWard)
			$sqlWard = "AND (".implode(") AND (",$whereWard).")";
		

		
		$sql = "
SELECT
p.bestellnum,p.artikelname,
(SELECT COUNT(*) FROM seg_pharma_order_items AS oi LEFT JOIN seg_pharma_orders AS o ON o.refno=oi.refno
WHERE oi.serve_status='S' AND oi.bestellnum=p.bestellnum $sqlOrder) AS total_issued,
(SELECT COUNT(*) FROM seg_pharma_ward_stock_items AS wi LEFT JOIN seg_pharma_ward_stocks AS w ON w.stock_nr=wi.stock_nr
WHERE wi.bestellnum=p.bestellnum $sqlWard) AS total_wardstocks,
(SELECT COUNT(*) FROM seg_pharma_return_items AS ri LEFT JOIN seg_pharma_returns AS r ON r.return_nr=ri.return_nr
WHERE ri.bestellnum=p.bestellnum $sqlReturn) AS total_returns
FROM care_pharma_products_main AS p\n";
		
		if (strtolower($this->full) != "yes") $sql.= "HAVING total_issued>0 OR total_returns>0 OR total_wardstocks>0\n";
		$sql .= "ORDER BY p.artikelname";
		$result=$db->Execute($sql);
		if ($result) {
			$this->_count = $result->RecordCount();
			$this->Data=array();
			while ($row=$result->FetchRow()) {
				$this->Data[]=array($row['bestellnum'],$row['artikelname'],'',$row['total_issued'],$row['total_wardstocks'],$row['total_returns'],'');
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

$rep = new RepGen_Pharma_Issuance($_GET['area'],$_GET['date'],$_GET['encoder'],$_GET['shiftstart'], $_GET['shiftend'],$_GET['full']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>