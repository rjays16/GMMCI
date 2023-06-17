<?php
//created by CHA, July 29, 2010
//income report for dialysis transaction
//same with Laboratory Income report
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

class Dialysis_Income_Report extends RepGen {

	var $colored = FALSE;
	var $fromdate;
	var $todate;
	var $SUM_AMT_BILLED;
	var $SUM_AMT_PAID;
	var $SUM_LAB;
	var $SUM_BLOOD;
	var $SUM_OTHER;

	var $no_of_services;
	var $no_of_patients;
	var $no_of_requests;

	var $pat_type;
	var $enctype;
	var $patient_type;
	var $servgroup;
	var $group_cond;

	var $total_width=0;

	function Dialysis_Income_Report($fromdate, $todate) {
		global $db;
		$this->RepGen("INCOME REPORT: DIALYSIS");
		$this->ColumnWidth = array(12,30,20,20,20,20,20);
		$this->RowHeight = 5.5;
		$this->LEFTMARGIN = 35;
		$this->Alignment = array('C','C','R','R','R','R','R');
		$this->PageOrientation = "P";
		$this->total_width = array_sum($this->ColumnWidth);

		if ($fromdate) $this->fromdate=date("Y-m-d",strtotime($fromdate));
		if ($todate) $this->todate=date("Y-m-d",strtotime($todate));

		$this->pat_type=5;
		$this->enctype = " AND (encounter_type IN (5) OR encounter_type IS NULL AND is_rdu = 0) ";
		$this->patient_type = "DIALYSIS PATIENT";

		$this->SetFillColor(0xFF);
		if ($this->colored) $this->SetDrawColor(0xDD);
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
			$row['hosp_addr1']   = "Quezon Ave., Digos City, Davao del Sur";
		}

		$this->SetFont("Arial","I","9");
		$total_w = 50;
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

		$this->SetFont("Arial","B","8");
		$this->Cell(50,4);
		$this->Cell($total_w,4,'DEPARTMENT OF DIALYSIS',$border2,1,'C');
		$this->Ln(2);

		$this->Cell(45,5);
		$this->Cell($total_w,4,$this->patient_type.' INCOME REPORT',$border2,1,'C');
		$this->SetFont('Arial','B',9);
		$this->Cell(45,5);


		if ($this->fromdate==$this->todate)
			$text = "For ".date("F j, Y",strtotime($this->fromdate));
		else
			$text = "From ".date("F j, Y",strtotime($this->fromdate))." To ".date("F j, Y",strtotime($this->todate));

		$this->Cell($total_w,4,$text,$border2,1,'C');
		$this->Ln(5);

	}

	function Footer()
	{
		$this->SetY(-23);
		$this->SetFont('Arial','I',8);
		$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
	}

	function BeforeRow()
	{
		$this->FONTSIZE = 8;
		if ($this->colored) {
			if (($this->ROWNUM%2)>0)
				$this->FILLCOLOR=array(0xee, 0xef, 0xf4);
			else
				$this->FILLCOLOR=array(255,255,255);
			$this->DRAWCOLOR = array(0xDD,0xDD,0xDD);
		}
	}

	function BeforeData()
	{
		if ($this->colored) {
			$this->DrawColor = array(0xDD,0xDD,0xDD);
		}
		# Print table header
		$this->SetFont('Arial','B',8);
		if ($this->colored) $this->SetFillColor(0xED);
		$this->SetTextColor(0);
		$row=6;
		$this->Cell($this->ColumnWidth[0],$row,'DATE',1,0,'C',1);
		$this->Cell($this->ColumnWidth[1],$row,'SHIFT',1,0,'C',1);
		$this->Cell($this->ColumnWidth[2],$row,'AMT BILLED',1,0,'C',1);
		$this->Cell($this->ColumnWidth[3],$row,'AMT PAID',1,0,'C',1);
		$this->Cell($this->ColumnWidth[4],$row,'LAB',1,0,'C',1);
		$this->Cell($this->ColumnWidth[5],$row,'BLOOD',1,0,'C',1);
		$this->Cell($this->ColumnWidth[6],$row,'OTHERS',1,0,'C',1);
		$this->Ln();
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

	function AfterData()
	{
		global $db;

		if (!$this->_count) {
				$this->SetFont('Arial','B',9);
				$this->SetFillColor(255);
				$this->SetTextColor(0);
				$this->Cell($this->total_width, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
		}else{
				$this->SetFont('Arial','B',12);
				$this->Ln(4);
				$this->Cell(80, $this->RowHeight, 'AMOUNT BILLED', 0, 0, 'L', 1);
				$this->Cell(15, $this->RowHeight, '= Php', 0, 0, 'L', 1);
				$this->Cell(30, $this->RowHeight, number_format($this->SUM_AMT_BILLED,2,'.',','), 0, 1, 'R', 1);
				$this->Cell(80, $this->RowHeight, 'AMOUNT PAID ', 0, 0, 'L', 0);
				$this->Cell(15, $this->RowHeight, '= Php', 0, 0, 'L', 1);
				$this->Cell(30, $this->RowHeight, number_format($this->SUM_AMT_PAID,2,'.',','), 0, 1, 'R', 1);

				$this->Cell(80, $this->RowHeight, 'LABORATORY', 0, 0, 'L', 0);
				$this->Cell(15, $this->RowHeight, '= Php', 0, 0, 'L', 1);
				$this->Cell(30, $this->RowHeight, number_format($this->SUM_LAB,2,'.',','), 0, 1, 'R', 1);
				$this->Cell(80, $this->RowHeight, 'BLOOD BANK', 0, 0, 'L', 0);
				$this->Cell(15, $this->RowHeight, '= Php', 0, 0, 'L', 1);
				$this->Cell(30, $this->RowHeight, number_format($this->SUM_BLOOD,2,'.',','), 0, 1, 'R', 1);
				$this->Cell(80, $this->RowHeight, 'OTHER CHARGES', 0, 0, 'L', 0);
				$this->Cell(15, $this->RowHeight, '= Php', 0, 0, 'L', 1);
				$this->Cell(30, $this->RowHeight, number_format($this->SUM_OTHER,2,'.',','), 0, 1, 'R', 1);

				$this->Ln(5);
				$this->Cell(80, $this->RowHeight, 'NUMBER OF PATIENTS SERVED', 0, 0, 'L', 0);
				$this->Cell(15, $this->RowHeight, '=', 0, 0, 'L', 1);
				$this->Cell(30, $this->RowHeight, $this->no_of_patients, 0, 1, 'R', 1);

				$this->Cell(80, $this->RowHeight, 'NUMBER OF REQUESTS', 0, 0, 'L', 0);
				$this->Cell(15, $this->RowHeight, '=', 0, 0, 'L', 1);
				$this->Cell(30, $this->RowHeight, $this->no_of_requests, 0, 1, 'R', 1);

				$this->Cell(80, $this->RowHeight, 'NUMBER OF SERVICES REQUESTED', 0, 0, 'L', 0);
				$this->Cell(15, $this->RowHeight, '=', 0, 0, 'L', 1);
				$this->Cell(30, $this->RowHeight, $this->no_of_services, 0, 1, 'R', 1);

	}

		$cols = array();
	}

	function getTotalAmount($fromtime, $totime, $date)
	{
		global $db;

		unset($result2);
		unset($row2);

		$sql2 = "SELECT SQL_CALC_FOUND_ROWS
							DATE_,
							SHIFT,
							SUM(CASE WHEN (AMT_PAID) then AMT_PAID else 0 end) AS AMT_PAID,
							SUM(CASE WHEN (LAB) then LAB else 0 end) AS LAB,
							SUM(CASE WHEN (BLOOD) then BLOOD else 0 end) AS BLOOD,
							SUM(CASE WHEN (OTHER) then OTHER else 0 end) AS OTHER
							FROM seg_rep_lab_income_tbl WHERE DATE_='".$date."'
							AND SHIFT BETWEEN '".$fromtime."' AND '".$totime."'
						 ".$this->group_cond."
						 ".$this->enctype;

		$result2=$db->Execute($sql2);
		$row2 = $result2->FetchRow();
		$row2['AMT_BILLED']  = $row2['AMT_PAID']  + $row2['LAB'] + $row2['BLOOD'] + $row2['OTHER'];

		return $row2;
	}



	function FetchData()
	{
		global $db;
		$db->Execute("CALL sp_populate_lab_income('".$this->fromdate."','".$this->todate."')");

		$sql = "SELECT SQL_CALC_FOUND_ROWS
							DATE_,
							SHIFT,
							SUM(CASE WHEN (AMT_PAID) then AMT_PAID else 0 end) AS AMT_PAID,
							SUM(CASE WHEN (LAB) then LAB else 0 end) AS LAB,
							SUM(CASE WHEN (BLOOD) then BLOOD else 0 end) AS BLOOD,
							SUM(CASE WHEN (OTHER) then OTHER else 0 end) AS OTHER

						FROM seg_rep_lab_income_tbl
						WHERE DATE_ BETWEEN '".$this->fromdate."' AND '".$this->todate."'
						 ".$this->group_cond."
						 ".$this->enctype.
						"GROUP BY DATE_, SHIFT";
		$result=$db->Execute($sql);

		$sql_serv = "SELECT SQL_CALC_FOUND_ROWS
								count(service_code) AS no_of_services
								FROM seg_rep_lab_income_tbl
								WHERE DATE_ BETWEEN '".$this->fromdate."' AND '".$this->todate."'
								".$this->group_cond."
								".$this->enctype.
								"GROUP BY service_code";

		$result_serv = $db->Execute($sql_serv);

		if (is_object($result_serv))
			$this->no_of_services = $result_serv->RecordCount();
		if (!$this->no_of_services)
			$this->no_of_services = 0;

		$sql_pat = "SELECT SQL_CALC_FOUND_ROWS
								count(pid) AS no_of_services
								FROM seg_rep_lab_income_tbl
								WHERE DATE_ BETWEEN '".$this->fromdate."' AND '".$this->todate."'
								".$this->group_cond."
								".$this->enctype.
								"GROUP BY pid";

		$result_pat=$db->Execute($sql_pat);
		if (is_object($result_pat))
			$this->no_of_patients = $result_pat->RecordCount();

		if (!$this->no_of_patients)
			$this->no_of_patients = 0;

		$sql_ref = "SELECT SQL_CALC_FOUND_ROWS
								count(refno) AS no_of_services
								FROM seg_rep_lab_income_tbl
								WHERE DATE_ BETWEEN '".$this->fromdate."' AND '".$this->todate."'
								".$this->group_cond."
								".$this->enctype.
								"GROUP BY refno";

		$result_ref=$db->Execute($sql_ref);
		if (is_object($result_ref))
			$this->no_of_requests = $result_ref->RecordCount();

		if (!$this->no_of_requests)
			$this->no_of_requests = 0;

		$SUM_AMT_BILLED = 0;
		$SUM_AMT_PAID = 0;
		$SUM_AMT_LAB = 0;
		$SUM_AMT_BLOOD = 0;
		$SUM_OTHER = 0;

		$count_7_8am = 0;
		$count_8_9am = 0;
		$count_9_10am = 0;
		$count_10_11am = 0;
		$count_11_12nn = 0;
		$count_12_1pm = 0;
		$count_1_2pm = 0;
		$count_2_3pm = 0;
		$count_3_4pm = 0;
		$count_4_5pm = 0;
		$count_5_6pm = 0;
		$count_6_7pm = 0;
		$count_7_8pm = 0;
		$count_8_9pm = 0;
		$count_9_10pm = 0;
		$count_10_11pm = 0;
		$count_11_12mn = 0;
		$count_12_1am = 0;
		$count_1_2am = 0;
		$count_2_3am = 0;
		$count_3_4am = 0;
		$count_4_5am = 0;
		$count_5_6am = 0;
		$count_6_7am = 0;

		$total_amt_billed = 0;
		$total_amt_paid = 0;
		$total_amt_lab = 0;
		$total_amt_blood = 0;
		$total_amt_other = 0;

		$prev_date = "";
		$first = TRUE;


		if ($result) {
			$this->_count = $result->RecordCount();
			$this->Data=array();

			while ($row=$result->FetchRow())
			{
					$timeframe = $row['SHIFT'];
					$DATE = date("m/d",strtotime($row['DATE_']));
					$prev_date = $DATE;

					if(($timeframe >= '00:00:00') && ($timeframe <= '00:59:59')){
							$shift = "12:00 AM - 01:00 AM";
							$row2 = $this->getTotalAmount('00:00:00', '00:59:59', $row['DATE_']);

					}elseif(($timeframe >= '01:00:00') && ($timeframe <= '01:59:59')){
							$shift = "01:00 AM - 02:00 AM";
							$row2 = $this->getTotalAmount('01:00:00', '01:59:59', $row['DATE_']);

					}elseif(($timeframe >= '02:00:00') && ($timeframe <= '02:59:59')){
							$shift = "02:00 AM - 03:00 AM";
							$row2 = $this->getTotalAmount('02:00:00', '02:59:59', $row['DATE_']);

					}elseif(($timeframe >= '03:00:00') && ($timeframe <= '03:59:59')){
							$shift = "03:00 AM - 04:00 AM";
							$row2 = $this->getTotalAmount('03:00:00', '03:59:59', $row['DATE_']);

					}elseif(($timeframe >= '04:00:00') && ($timeframe <= '04:59:59')){
							$shift = "04:00 AM - 05:00 AM";
							$row2 = $this->getTotalAmount('04:00:00', '04:59:59', $row['DATE_']);

					}elseif(($timeframe >= '05:00:00') && ($timeframe <= '05:59:59')){
							$shift = "05:00 AM - 06:00 AM";
							$row2 = $this->getTotalAmount('05:00:00', '05:59:59', $row['DATE_']);

					}elseif(($timeframe >= '06:00:00') && ($timeframe <= '06:59:59')){
							$shift = "06:00 AM - 07:00 AM";
							$row2 = $this->getTotalAmount('06:00:00', '06:59:59', $row['DATE_']);

					}elseif(($timeframe >= '07:00:00') && ($timeframe <= '07:59:59')){
							$shift = "07:00 AM - 08:00 AM";
							$row2 = $this->getTotalAmount('07:00:00', '07:59:59', $row['DATE_']);

					}elseif(($timeframe >= '08:00:00') && ($timeframe <= '08:59:59')){
							$shift = "08:00 AM - 09:00 AM";
							$row2 = $this->getTotalAmount('08:00:00', '08:59:59', $row['DATE_']);

					}elseif(($timeframe >= '09:00:00') && ($timeframe <= '09:59:59')){
							$shift = "09:00 AM - 10:00 AM";
							$row2 = $this->getTotalAmount('09:00:00', '09:59:59', $row['DATE_']);

					}elseif(($timeframe >= '10:00:00') && ($timeframe <= '10:59:59')){
							$shift = "10:00 AM - 11:00 AM";
							$row2 = $this->getTotalAmount('10:00:00', '10:59:59', $row['DATE_']);

					}elseif(($timeframe >= '11:00:00') && ($timeframe <= '11:59:59')){
							$shift = "11:00 AM - 12:00 PM";
							$row2 = $this->getTotalAmount('11:00:00', '11:59:59', $row['DATE_']);

					}elseif(($timeframe >= '12:00:00') && ($timeframe <= '12:59:59')){
							$shift = "12:00 PM - 01:00 PM";
							$row2 = $this->getTotalAmount('12:00:00', '12:59:59', $row['DATE_']);

					}elseif(($timeframe >= '13:00:00') && ($timeframe <= '13:59:59')){
							$shift = "01:00 PM - 02:00 PM";
							$row2 = $this->getTotalAmount('13:00:00', '13:59:59', $row['DATE_']);

					}elseif(($timeframe >= '14:00:00') && ($timeframe <= '14:59:59')){
							$shift = "02:00 PM - 03:00 PM";
							$row2 = $this->getTotalAmount('14:00:00', '14:59:59', $row['DATE_']);

					}elseif(($timeframe >= '15:00:00') && ($timeframe <= '15:59:59')){
							$shift = "03:00 PM - 04:00 PM";
							$row2 = $this->getTotalAmount('15:00:00', '15:59:59', $row['DATE_']);

					}elseif(($timeframe >= '16:00:00') && ($timeframe <= '16:59:59')){
							$shift = "04:00 PM - 05:00 PM";
							$row2 = $this->getTotalAmount('16:00:00', '16:59:59', $row['DATE_']);

					}elseif(($timeframe >= '17:00:00') && ($timeframe <= '17:59:59')){
							$shift = "05:00 PM - 06:00 PM";
							$row2 = $this->getTotalAmount('17:00:00', '17:59:59', $row['DATE_']);

					}elseif(($timeframe >= '18:00:00') && ($timeframe <= '18:59:59')){
							$shift = "06:00 PM - 07:00 PM";
							$row2 = $this->getTotalAmount('18:00:00', '19:59:59', $row['DATE_']);

					}elseif(($timeframe >= '19:00:00') && ($timeframe <= '19:59:59')){
							$shift = "07:00 PM - 08:00 PM";
							$row2 = $this->getTotalAmount('19:00:00', '19:59:59', $row['DATE_']);

					}elseif(($timeframe >= '20:00:00') && ($timeframe <= '20:59:59')){
							$shift = "08:00 PM - 09:00 PM";
							$row2 = $this->getTotalAmount('20:00:00', '20:59:59', $row['DATE_']);

					}elseif(($timeframe >= '21:00:00') && ($timeframe <= '21:59:59')){
							$shift = "09:00 PM - 10:00 PM";
							$row2 = $this->getTotalAmount('21:00:00', '21:59:59', $row['DATE_']);

					}elseif(($timeframe >= '22:00:00') && ($timeframe <= '22:59:59')){
							$shift = "10:00 PM - 11:00 PM";
							$row2 = $this->getTotalAmount('22:00:00', '22:59:59', $row['DATE_']);

					}elseif(($timeframe >= '23:00:00') && ($timeframe <= '23:59:59')){
							$shift = "11:00 PM - 12:00 AM";
							$row2 = $this->getTotalAmount('23:00:00', '23:59:59', $row['DATE_']);

					}

					$total_amt_billed += $row2['AMT_BILLED'];
					$total_amt_paid += $row2['AMT_PAID'];
					$total_amt_lab += $row2['LAB'];
					$total_amt_blood += $row2['BLOOD'];
					$total_amt_other += $row2['OTHER'];

					if($shift!=$old_shift){
								 $this->Data[]=array(
										$DATE,
										$shift,
										number_format($row2['AMT_BILLED'],2,'.',','),
										number_format($row2['AMT_PAID'],2,'.',','),
										number_format($row2['LAB'],2,'.',','),
										number_format($row2['BLOOD'],2,'.',','),
										number_format($row2['OTHER'],2,'.',',')

									);
						}


						 $old_shift = $shift;
			}

				$this->SUM_AMT_BILLED = $total_amt_billed;
				$this->SUM_AMT_PAID = $total_amt_paid;
				$this->SUM_LAB = $total_amt_lab;
				$this->SUM_BLOOD = $total_amt_blood;
				$this->SUM_OTHER = $total_amt_other;

		}
		else {
			 print_r($sql);
			 print_r($db->ErrorMsg());
			 exit;
			 # Error
		}
	}
}

$rep = new Dialysis_Income_Report($_GET['date_from'], $_GET["date_to"]);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>
