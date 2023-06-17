<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);

require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');

/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

	class RepGen_Cashier_DailyEndorsement extends RepGen {
	var $encoder;
	var $date;
	var $shift_start;
	var $shift_end;
	var $detailed;

	function RepGen_Cashier_DailyEndorsement ($encoder="", $date=FALSE, $shift_start=FALSE, $shift_end=FALSE, $from, $to, $detailed=FALSE) {
		global $db;
//		$this->RepGen("CASHIER",'P',array(108,355.6));
		$this->RepGen("CASHIER",'P','Legal');
		$this->ColumnWidth = array(20,30,20,20,45,0);
		$this->Alignment = array('C','L','R','R','L','L');
		$this->PageOrientation = "P";
		$this->TOPMARGIN = 8;
		if ($date) $this->date=date("Y-m-d",strtotime($date));
		$this->encoder=$encoder;
		$this->detailed=$detailed;
		$this->shift_start=$shift_start;
		$this->shift_end=$shift_end;
		$this->or_from = $from;
		$this->or_to = $to;
		$this->RowHeight = 6;
		#$this->SetDrawColor(0xDD);
	}
	
	function Header() {
	}
	
	function BeforeData() {
		global $root_path, $db;
		
		if ($this->encoder) {
			$sql = "SELECT name FROM care_users WHERE login_id=".$db->qstr($this->encoder);
			$this->encoderName = $db->GetOne($sql);
		}
		$total_w = 196;
		
		$image_offset = -12;
		$image_width = 12;
		$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',$total_w/2-$image_width+$image_offset,6,$image_width);
		//$total_w = 88;
		/*
		$this->Cell($total_w,3,'',1,1);
		$this->Cell(0,3,'',1,1);
		*/
		
		$pad=4;
		$this->SetFont("Arial","I","7");
		$this->Cell($pad,3);
  	$this->Cell($total_w,3,'Republic of the Philippines',$border2,1,'C');
		$this->Cell($pad,3);
	  $this->Cell($total_w,3,'DEPARTMENT OF HEALTH',$border2,1,'C');
		$this->SetFont("Arial","B","9");
		$this->Cell($pad,4);
  	$this->Cell($total_w,4,'Gonzales Maranan Medical Center Incorporated',$border2,1,'C');
		$this->SetFont("Arial","B","7");
		$this->Cell($pad,5);
  	$this->Cell($total_w,3,'CASHIER\'S OFFICE',$border2,1,'C');
		$this->Ln(0);
		
	  $this->SetFont('Arial','B',10);
		$this->Cell($pad,6);
  	$this->Cell($total_w,4,'DAILY ENDORSEMENT REPORT',$border2,1,'C');
		
	  $this->SetFont('Arial','B',8);
		$this->Cell($pad,3);
  	$this->Cell($total_w,3,date("F j, Y",strtotime($this->date)),$border2,1,'C');
		$this->Ln(2);
		
		# Print table header
		
		$box_width = $total_w*0.7;
		$pad = $total_w/2 - $box_width/2+0.1;
		$rowh=4;
    $this->SetFont('Arial','B',8);
		$this->Cell($pad,$rowh);
		$this->Cell(12,$rowh,'NAME',0,0,'L');
    $this->SetFont('Arial','B',9);
		$this->Cell($box_width-12,$rowh,$this->encoderName ? strtoupper($this->encoderName)  : 'N/A','B',1,'L');
		
    $this->SetFont('Arial','B',8);
		$this->Cell($pad,$rowh);
		$this->Cell(12,6,"OR #s",0,0,'L');
    $this->SetFont('Arial','',8);
		$this->Cell($box_width-12,8,'','B',0,'L');
		/*
    $this->SetFont('Arial','B',8);
		$this->Cell(10,4,'SHIFT',0,0,'L');
    $this->SetFont('Arial','',8);
		$this->Cell(0,4,'','B',1,'L');
		*/
		$this->Ln(12);

		$accounts = array(
			'HOI'=>'HOSPITAL INCOME (HOI)',
			'DRUGS'=>'DRUGS AND MEDICINE',
			'CMEDS'=>'CONSIGNED MEDS',
			'PAY'=>'PAYWARD',
			'AFF'=>'AFFILIATION',
			'CTSCAN'=>'CT SCAN',
			'HC' => 'HEART CENTER',
			'BC' => 'BLOOD CENTER',
			'OTHERS'=>'OTHERS'
		);
		$totals = array_flip(array_keys($accounts));
		$memos = array_flip(array_keys($accounts));
		foreach ($totals as $i=>$v) {
			$totals[$i] = 0;
			$memos[$i] = 0;
		}
		
		global $db;
		$sql = "SELECT r.amount_due, r.or_no, pay.discount_tendered,
fn_get_account_type(r.ref_no,r.ref_source, r.service_code,'S') AS acc_type
FROM seg_pay_request AS r
INNER JOIN seg_pay AS pay ON pay.or_no=r.or_no
";
		
		$sqlCM = "SELECT d.price*d.quantity AS `refund_amount`,
fn_get_account_type(d.ref_no,d.ref_source,d.service_code,'S') AS acc_type
FROM seg_credit_memo_details AS d
INNER JOIN seg_credit_memos AS m ON m.memo_nr=d.memo_nr
INNER JOIN seg_pay AS pay ON pay.or_no=d.or_no
";
		
		$where = array();
		$whereCM = array();
		
		if ($this->date) {
			$datesql="DATE(pay.or_date)=".$db->qstr($this->date);
			$cm_datesql = "DATE(m.issue_date)=".$db->qstr($this->date);
			$dTime = strtotime($this->date);

			if (is_numeric($this->shift_start) && is_numeric($this->shift_end)) {
				if ($this->shift_start < $this->shift_end) {
					$start_time = $dTime + $this->shift_start*3600;
					$end_time = $dTime + $this->shift_end*3600;
				}
				else {
					$start_time = $dTime + $this->shift_start*3600;
					$end_time = $dTime + $this->shift_end*3600+86400;
				}
				$datesql = "pay.or_date BETWEEN CAST(".$db->qstr(date("Y-m-d H:i:s",$start_time))." AS DATETIME) AND CAST(".$db->qstr(date("Y-m-d H:i:s",$end_time))." AS DATETIME)";
				$cm_datesql = "m.issue_date BETWEEN CAST(".$db->qstr(date("Y-m-d H:i:s",$start_time))." AS DATETIME) AND CAST(".$db->qstr(date("Y-m-d H:i:s",$end_time))." AS DATETIME)";
			}
			elseif (is_numeric($this->shift_start)) {
				$start_time = $dTime + $this->shift_start*3600;
				$datesql = "pay.or_date BETWEEN CAST(".$db->qstr(date("Y-m-d H:i:s",$start_time))." AS DATETIME) AND NOW()";
				$cm_datesql = "m.issue_date BETWEEN CAST(".$db->qstr(date("Y-m-d H:i:s",$start_time))." AS DATETIME) AND NOW()";
			}
			$where[] = $datesql;
			$whereCM[] = $cm_datesql;
		}
		if ($this->encoder) {
			$where[]="create_id=".$db->qstr($this->encoder);
			$whereCM[] = "personnel=".$db->qstr($this->encoder);
		}
		
		if ($this->or_from) {
			$where[] = "CAST(pay.or_no AS UNSIGNED) >= ".$db->qstr($this->or_from);
			$whereCM[] = "CAST(pay.or_no AS UNSIGNED) >= ".$db->qstr($this->or_from);
		}
			
		if ($this->or_to) {
			$where[] = "CAST(pay.or_no AS UNSIGNED) <= ".$db->qstr($this->or_to);
			$whereCM[] = "CAST(pay.or_no AS UNSIGNED) <= ".$db->qstr($this->or_to);
		}

		
		# Get OR Series used
		$orSQL = "SELECT or_no FROM seg_pay AS pay WHERE (".implode(") AND (",$where).") ORDER BY pay.or_no";
		$result=$db->Execute($orSQL);
		if ($result) {
			$series = array();
			$i=0;
			$prev_item = "";
			$series_start = "";
			while ($row=$result->FetchRow()) {
				if (!$series_start) $series_start = $row['or_no'];
				if ( ((float)$prev_item+1) != ((float)$row['or_no']) && $prev_item) {
					if ($series_start==$prev_item)
						$series[] = $prev_item;
					else
						$series[] = $series_start."-".$prev_item;
					$series_start = $row['or_no'];
				}
				else {
#					echo $row['or_no']."<br>";
				}
				$prev_item = $row['or_no'];
			}
			if ($series_start==$prev_item)
				$series[] = $prev_item;
			else
				$series[] = $series_start."-".$prev_item;
		}
		
		# Print OR Series
	  $this->SetFont('Arial','',8);
		$series = array_chunk($series, 5);
		$box_width = 120;
		$x = 52;
		$y = 37;
		foreach ($series as $i=>$v) {
			$this->Text($x, $y+3*$i, implode(', ',$v));
		}
		
		
		
		$where[] = "pay.cancel_date IS NULL";
		$whereCM[] = "pay.cancel_date IS NULL";
		if ($where) {			
			$sql .= "WHERE (".implode(") AND (",$where).") ORDER BY pay.or_date";
			$sqlCM .= "WHERE (".implode(") AND (",$whereCM).") ORDER BY m.issue_date";
		}
		//echo $sql;
		$result=$db->Execute($sql);
		if ($result) {
			while ($row=$result->FetchRow()) {
				$type_arr = explode('|',$row['acc_type']);
				if (count($type_arr) == 1) $type_arr[1] = $type_arr[0];
				$acc = strtoupper($type_arr[0]);
				if(!array_key_exists($row['or_no'], $discount)){
					$discount[$row['or_no']] = $row['discount_tendered'];
					$row['amount_due'] -= $discount[$row['or_no']];
				}
				if (array_key_exists($acc,$totals)) $totals[$acc] += (float)$row['amount_due'];
				else $totals['OTHERS'] += (float)$row['amount_due'];
			}
		}
		else {
			print_r($sql);
			print_r($db->ErrorMsg());
			exit;
			# Error
		}
		
		$resultCM = $db->Execute($sqlCM);
		if ($resultCM) {
			while ($row=$resultCM->FetchRow()) {
				$type_arr = explode('|',$row['acc_type']);
				if (count($type_arr) == 1) $type_arr[1] = $type_arr[0];
				$acc = strtoupper($type_arr[0]);
				if (array_key_exists($acc,$memos)) $memos[$acc] += (float)$row['refund_amount'];
				else $memos['OTHERS'] += (float)$row['refund_amount'];
			}
		}
		else {
			print_r($sqlCM);
			print_r($db->ErrorMsg());
			exit;
			# Error
		}


		$i=1;
		$left = 60;
		$right = 43;
		$pad = $total_w/2-($left+$right)/2;
		$rowh = 5;
		$total_or = 0;
		$font_size = 10;
		foreach ($accounts as $s=>$account) {
			$net = (float)$totals[$s] - (float)$memos[$s];
			$total_or += $net;
			$this->Cell($pad,$rowh);
			$this->SetFont('Arial','B',$font_size);
			$this->Cell(46,$rowh-1,"$i. $account",0,1,'L');
			$this->SetFont('Arial','',$font_size);
			$this->Cell($pad,$rowh);
			$this->Cell($left,$rowh,'Collection',1,0,'L');
			$this->Cell($right,$rowh,number_format((float)$totals[$s],2),1,1,'R');
			$this->Cell($pad,$rowh);
			$this->Cell($left,$rowh,'Credit Memo',1,0,'L');
			$this->Cell($right,$rowh,"-".number_format((float)$memos[$s],2),1,1,'R');
			$this->Cell($pad,$rowh);
			$this->SetFont('Arial','B',$font_size);
			$this->Cell($left,$rowh,'Total',1,0,'L');
			$this->Cell($right,$rowh,number_format((float)$net,2),1,1,'R');
			$this->Ln(1);
			$i++;
		}

		$this->Ln(2);
		$this->SetFillColor(0xEE);
		$this->SetFont('Arial','B',9);
		$this->Cell($pad,$rowh);
		$this->Cell($left,$rowh,"TOTAL O.R. COLLECTION",1,0,'L',0);
		$this->Cell($right,$rowh,number_format((float)$total_or,2),1,1,'R',0);

		$this->Ln(2);
		$denominations = array('1,000','500','100','50','20','10','(Coins) 10','(Coins) 5','(Coins) 1');

		$rowh = 4;
		$this->SetFont('Arial','B',8);
		$this->Cell($pad,$rowh);
		$this->Cell(0,3,"BREAKDOWN OF DENOMINATION",0,1,'L');
		
		
		$adjust = 20;
		$rowh = 4;
		$this->Cell($pad,$rowh);
		$this->Cell($left-$adjust,$rowh,"No. of Pcs",1,0,'C');
		$this->Cell($adjust,$rowh,"Denomination",1,0,'C');
		$this->Cell($right,$rowh,'AMOUNT',1,1,'C');
		
		foreach ($denominations as $i=>$denom) {
			$this->Cell($pad,$rowh);
			$this->Cell($left-$adjust,$rowh,"",1,0,'L');
			$this->Cell($adjust,$rowh,$denom,1,0,'R');
			$this->Cell($right,$rowh,'',1,1,'R');
		}

		$this->Cell($pad,$rowh);
		$this->Cell($left-$adjust,$rowh,"OTHERS",1,0,'L');
		$this->Cell($adjust,$rowh,"",1,0,'L');
		$this->Cell($right,$rowh,'',1,1,'C');
		$this->Cell($pad,$rowh);
		$this->Cell($left-$adjust,$rowh,"CHEQUES",1,0,'L');
		$this->Cell($adjust,$rowh,"",1,0,'C');
		$this->Cell($right,$rowh,'',1,1,'C');
		
		for ($i=0;$i<2;$i++) {
			$this->Cell($pad,$rowh);
			$this->Cell($left-$adjust,$rowh,"",1,0,'C');
			$this->Cell($adjust,$rowh,"",1,0,'C');
			$this->Cell($right,$rowh,'',1,1,'C');
		}

		$this->Cell($pad,$rowh);
		$this->Cell($left-$adjust,$rowh,"OVERAGE",1,0,'L');
		$this->Cell($adjust,$rowh,"",1,0,'C');
		$this->Cell($right,$rowh,'',1,1,'C');
		
		$this->Cell($pad,$rowh);
		$this->Cell($left-$adjust,$rowh,"SHORTAGE",1,0,'L');
		$this->Cell($adjust,$rowh,"",1,0,'C');
		$this->Cell($right,$rowh,'',1,1,'C');
		
		$rowh = 4;
		$this->SetFont('Arial','B',8);
		$this->Cell($pad,$rowh);
		$this->Cell($left,$rowh,"TOTAL CASH ENDORSEMENT",1,0,'L',0);
		$this->Cell($right,$rowh,"",1,1,'R',0);
		
		$rowh = 3;		
		$box_width = 130;
		$pad = $total_w/2 - $box_width/2;
		$this->Ln(3);
		$this->SetFont('Arial','',8);
		$this->Cell($pad,$rowh);
		$this->Cell($box_width/2,$rowh+0.5," Certified Correct:",'TL',0,'L');
		$this->Cell($box_width/2,$rowh+0.5,'Endorsed and submitted by:  ','TR',1,'R');
		
		$this->Cell($pad,$rowh);
		$this->Cell(2.5,$rowh,"",'L',0,'L');
		$this->Cell($box_width/2-5,$rowh,"",'B',0,'L');
		$this->Cell(5,$rowh-1,"",0,0,'L');
		$this->Cell($box_width/2-5,$rowh,'','B',0,'R');
		$this->Cell(2.5,$rowh,"",'R',1,'L');
		
		$this->SetFont('Arial','B',8);		
		$this->Cell($pad,$rowh);
		$this->Cell($box_width/2,$rowh+0.5,"LINO DALISAY - CASH CLERK I",'L',0,'C');
		$this->Cell($box_width/2,$rowh+0.5,strtoupper($this->encoderName),'R',1,'C');
		
		$this->Cell($pad,$rowh);
		$this->Cell($box_width,1,"",'LR',1,'L');
		
		$this->SetFont('Arial','',7);
		$this->Cell($pad,$rowh);
		$this->Cell($box_width,$rowh," Noted by:",'LR',1,'L');
		
		$this->Cell($pad,$rowh);
		$this->Cell(2.5,$rowh,"",'L',0,'L');
		$this->Cell($box_width/2-5,$rowh,"",'B',0,'L');
		$this->Cell($box_width/2+2.5,$rowh,"",'R',1,'L');

		$this->SetFont('Arial','B',8);
		$this->Cell($pad,$rowh);
		$this->Cell($box_width/2,$rowh+0.5,"ARTEMIO GALICIA - CASHIER III",'L',0,'C');
		$this->Cell($box_width/2,$rowh+0.5,"",'R',1,'C');
		
		$this->Cell($pad,$rowh);
		$this->Cell($box_width,$rowh,"",'LR',1,'L');
		
		$this->Cell($pad,$rowh);
		$this->Cell($box_width,$rowh,"",'LBR',1,'L');
	}
	
	function BeforeRow() {
		if (($this->ROWNUM%2)>0) 
			$this->FILLCOLOR=array(0xee, 0xef, 0xf4);
		else
			$this->FILLCOLOR=array(255,255,255);
		$this->DRAWCOLOR = array(0xDD,0xDD,0xDD);
	}
	
	function BeforeCell() {
		if ($this->COLNUM==1) {
			$this->FONTSTYLE='B';
		}
		else {
			$this->FONTSTYLE='';
		}
		$this->FONTSIZE=7;
	}
	
	function AfterData() {
		global $db;
		
		/*
		if (!$this->_count) {
			$this->SetFont('Arial','B',9);
			$this->SetFillColor(255);
			$this->SetTextColor(0);
			$this->Cell(0, $this->RowHeight, "No records found for this encoder...", 1, 1, 'L', 1);
		}
		
		$this->SetFont('Arial','B',8);
		$this->SetFillColor(0xEE);
		$this->SetTextColor(0);
		$this->Cell($this->ColumnWidth[0], $this->RowHeight, "TOTAL", 1, 0, 'L', 1);
		$this->Cell($this->ColumnWidth[1]+$this->ColumnWidth[2], $this->RowHeight, "Total No. of O.R. Used - $this->_count", 1, 0, 'C', 1);
		$this->Cell($this->ColumnWidth[3], $this->RowHeight, number_format($this->_total,2), 1, 0, 'R', 1);
		$this->Cell($this->ColumnWidth[4], $this->RowHeight, "", 1, 0, '', 1);
		$this->Cell($this->ColumnWidth[5], $this->RowHeight, "", 1, 0, '', 1);
		*/
	}
	
}
$iss = new RepGen_Cashier_DailyEndorsement($_GET['encoder'],$_GET['date'],$_GET['shiftstart'],$_GET['shiftend'], $_GET['orfrom'], $_GET['orto']);
$iss->Report();

?>