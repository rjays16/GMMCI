<?php
#created by Cherry 07-30-09
#Cancer/Tumor Monitoring Report
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

class OperatingRoomCensus extends FPDF{
var $colored = TRUE;
var $from, $to;

	 function OperatingRoomCensus ($from, $to) {
		global $db;
		//$this->RepGen("MEDICAL RECORDS: LINGAP STATISTICS");

		$this->ColumnWidth = array(10, 30,30,30,30,30, 30, 15, 15, 15, 15, 15);
		$this->RowHeight = 5;
		$this->TextHeight = 5;
		$this->TextPadding = 0.2;
		$this->Alignment = array('C','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R');
		$this->TableLabel = array('NAME OF PATIENT', 'ADDRESS', 'AGE', 'SEX', 'CONTROL NUMBER','DATE OF APPLICATION', 'DATE APPROVED', 'AMOUNT GRANTED');
		$this->PageOrientation = "L";
		$this->NoWrap = FALSE;
		$this->LEFTMARGIN = 7;
		$this->FPDF('L', 'mm', 'Letter');

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
		//$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',50,8,20);
		$this->SetFont("Arial","I","9");
		$total_w = 0;
		#$this->Cell(50,4);
			#$this->Cell($total_w,4,'Republic of the Philippines',$border2,1,'C');
		$this->Cell($total_w,4,$row['hosp_country'],$border2,1,'C');
		#$this->Cell(50,4);
			#$this->Cell($total_w,4,'DEPARTMENT OF HEALTH',$border2,1,'C');
		$this->Cell($total_w,4,$row['hosp_agency'],$border2,1,'C');
			$this->Ln(2);
		$this->SetFont("Arial","B","10");
		#$this->Cell(50,4);
			#$this->Cell($total_w,4,'Gonzales Maranan Medical Center Incorporated',$border2,1,'C');
		$this->Cell($total_w,4,$row['hosp_name'],$border2,1,'C');
		$this->SetFont("Arial","","9");
		#$this->Cell(50,4);
			#$this->Cell($total_w,4,'Bajada, Davao City',$border2,1,'C');
		$this->Cell($total_w,4,$row['hosp_addr1'],$border2,1,'C');
			$this->Ln(4);
			$this->SetFont('Arial','B',12);
		//$this->Cell(50,5);

		$this->Cell($total_w,4,'OPERATING ROOM CENSUS',$border2,1,'C');
		 $this->SetFont('Arial','B',9);
		#$this->Cell(50,5);

		if ($this->from==$this->to)
			$text = "For ".date("F j, Y",strtotime($this->from));
		else
				#$text = "Full History";
			$text = "From ".date("F j, Y",strtotime($this->from))." To ".date("F j, Y",strtotime($this->to));

			$this->Cell($total_w,4,$text,$border2,1,'C');
		$this->Ln(5);

		# Print table header
			$this->SetFont('Arial','B',8);
		#if ($this->colored) $this->SetFillColor(0xED);
		if ($this->colored) $this->SetFillColor(255);
		$this->SetTextColor(0);
		$row=6;
		$this->Cell($this->ColumnWidth[0], $row, '', "TLR", 0);
		$this->Cell($this->ColumnWidth[1]+$this->ColumnWidth[2], $row, 'MAJOR (Excl. C/S)', 1, 0, 'C');
		$this->Cell($this->ColumnWidth[3]+$this->ColumnWidth[4], $row, 'MINOR', 1, 0, 'C');
		$this->Cell($this->ColumnWidth[5]+$this->ColumnWidth[6], $row, 'Cesarean Section', 1, 0, 'C');
		$this->Cell($this->ColumnWidth[7]+$this->ColumnWidth[8], $row, 'ELECTIVE', 1, 0, 'C');
		$this->Cell($this->ColumnWidth[9]+$this->ColumnWidth[10], $row, 'EMERGENCY', 1, 0, 'C');
		$this->Cell($this->ColumnWidth[11], $row, 'DAY', "TLR", 1, 'C');
		$this->Cell($this->ColumnWidth[0], $row, '', "LR", 0);
		$this->Cell($this->ColumnWidth[1], $row, 'PAYWARD', 1, 0, 'C');
		$this->Cell($this->ColumnWidth[2], $row, 'CHARITY', 1, 0, 'C');
		$this->Cell($this->ColumnWidth[3], $row, 'PAYWARD', 1, 0, 'C');
		$this->Cell($this->ColumnWidth[4], $row, 'CHARITY', 1, 0, 'C');
		$this->Cell($this->ColumnWidth[5], $row, 'PAYWARD', 1, 0, 'C');
		$this->Cell($this->ColumnWidth[6], $row, 'CHARITY', 1, 0, 'C');
		$this->Cell($this->ColumnWidth[7], $row, 'PAY', 1, 0, 'C');
		$this->Cell($this->ColumnWidth[8], $row, 'CHAR', 1, 0, 'C');
		$this->Cell($this->ColumnWidth[9], $row, 'PAY', 1, 0, 'C');
		$this->Cell($this->ColumnWidth[10], $row, 'CHAR', 1, 0, 'C');
		$this->Cell($this->ColumnWidth[11], $row, 'TOTAL', "LR", 1, 'C');
		$this->Cell($this->ColumnWidth[0], $row, "", "BLR", 0);
		$this->Cell($this->ColumnWidth[1]/2, $row, '<=13', 1, 0, 'C');
		$this->Cell($this->ColumnWidth[1]/2, $row, '14 up', 1, 0, 'C');
		$this->Cell($this->ColumnWidth[2]/2, $row, '<=13>', 1, 0, 'C');
		$this->Cell($this->ColumnWidth[2]/2, $row, '14 up', 1, 0, 'C');
		$this->Cell($this->ColumnWidth[3]/2, $row, '<=13', 1, 0, 'C');
		$this->Cell($this->ColumnWidth[3]/2, $row, '14 up', 1, 0, 'C');
		$this->Cell($this->ColumnWidth[4]/2, $row, '<=13', 1, 0, 'C');
		$this->Cell($this->ColumnWidth[4]/2, $row, '14 up', 1, 0, 'C');
		$this->Cell($this->ColumnWidth[5]/2, $row, '<=13>', 1, 0, 'C');
		$this->Cell($this->ColumnWidth[5]/2, $row, '14 up', 1, 0, 'C');
		$this->Cell($this->ColumnWidth[6]/2, $row, '<=13', 1, 0, 'C');
		$this->Cell($this->ColumnWidth[6]/2, $row, '14 up', 1, 0, 'C');
		$this->Cell($this->ColumnWidth[7], $row, '', 1, 0, 'C');
		$this->Cell($this->ColumnWidth[8], $row, '', 1, 0, 'C');
		$this->Cell($this->ColumnWidth[9], $row, '', 1, 0, 'C');
		$this->Cell($this->ColumnWidth[10], $row, '', 1, 0, 'C');
		$this->Cell($this->ColumnWidth[11], $row, '', "BLR", 0, 'C');
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
			$tot_width = $this->ColumnWidth[0]+$this->ColumnWidth[1]+$this->ColumnWidth[2]+$this->ColumnWidth[3]+$this->ColumnWidth[4]+$this->ColumnWidth[5]+$this->ColumnWidth[6]+$this->ColumnWidth[7]+$this->ColumnWidth[8];
			$tot_width = $tot_width + $this->ColumnWidth[9] + $this->ColumnWidth[10] + $this->ColumnWidth[11];
			$this->Cell($tot_width, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
		}

		$cols = array();
	}

	function FetchData(){
		global $db;
		$rowheight = 4;

		$sql_first = "SELECT
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '01' then 1 else 0 end) AS maj_pay_below13_1,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '01' then 1 else 0 end) AS maj_pay_14up_1,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '01' then 1 else 0 end) AS maj_cha_below13_1,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '01' then 1 else 0 end) AS maj_cha_14up_1,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '01' then 1 else 0 end) AS min_pay_below13_1,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '01' then 1 else 0 end) AS min_pay_14up_1,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '01' then 1 else 0 end) AS min_cha_below13_1,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '01' then 1 else 0 end) AS min_cha_14up_1,

						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '02' then 1 else 0 end) AS maj_pay_below13_2,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '02' then 1 else 0 end) AS maj_pay_14up_2,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '02' then 1 else 0 end) AS maj_cha_below13_2,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '02' then 1 else 0 end) AS maj_cha_14up_2,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '02' then 1 else 0 end) AS min_pay_below13_2,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '02' then 1 else 0 end) AS min_pay_14up_2,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '02' then 1 else 0 end) AS min_cha_below13_2,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '02' then 1 else 0 end) AS min_cha_14up_2,

						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '03' then 1 else 0 end) AS maj_pay_below13_3,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '03' then 1 else 0 end) AS maj_pay_14up_3,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '03' then 1 else 0 end) AS maj_cha_below13_3,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '03' then 1 else 0 end) AS maj_cha_14up_3,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '03' then 1 else 0 end) AS min_pay_below13_3,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '03' then 1 else 0 end) AS min_pay_14up_3,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '03' then 1 else 0 end) AS min_cha_below13_3,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '03' then 1 else 0 end) AS min_cha_14up_3,

						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '04' then 1 else 0 end) AS maj_pay_below13_4,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '04' then 1 else 0 end) AS maj_pay_14up_4,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '04' then 1 else 0 end) AS maj_cha_below13_4,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '04' then 1 else 0 end) AS maj_cha_14up_4,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '04' then 1 else 0 end) AS min_pay_below13_4,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '04' then 1 else 0 end) AS min_pay_14up_4,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '04' then 1 else 0 end) AS min_cha_below13_4,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '04' then 1 else 0 end) AS min_cha_14up_4,

						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '05' then 1 else 0 end) AS maj_pay_below13_5,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '05' then 1 else 0 end) AS maj_pay_14up_5,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '05' then 1 else 0 end) AS maj_cha_below13_5,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '05' then 1 else 0 end) AS maj_cha_14up_5,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '05' then 1 else 0 end) AS min_pay_below13_5,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '05' then 1 else 0 end) AS min_pay_14up_5,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '05' then 1 else 0 end) AS min_cha_below13_5,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '05' then 1 else 0 end) AS min_cha_14up_5,

						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '06' then 1 else 0 end) AS maj_pay_below13_6,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '06' then 1 else 0 end) AS maj_pay_14up_6,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '06' then 1 else 0 end) AS maj_cha_below13_6,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '06' then 1 else 0 end) AS maj_cha_14up_6,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '06' then 1 else 0 end) AS min_pay_below13_6,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '06' then 1 else 0 end) AS min_pay_14up_6,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '06' then 1 else 0 end) AS min_cha_below13_6,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '06' then 1 else 0 end) AS min_cha_14up_6,

						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '07' then 1 else 0 end) AS maj_pay_below13_7,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '07' then 1 else 0 end) AS maj_pay_14up_7,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '07' then 1 else 0 end) AS maj_cha_below13_7,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '07' then 1 else 0 end) AS maj_cha_14up_7,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '07' then 1 else 0 end) AS min_pay_below13_7,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '07' then 1 else 0 end) AS min_pay_14up_7,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '07' then 1 else 0 end) AS min_cha_below13_7,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '07' then 1 else 0 end) AS min_cha_14up_7,

						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '08' then 1 else 0 end) AS maj_pay_below13_8,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '08' then 1 else 0 end) AS maj_pay_14up_8,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '08' then 1 else 0 end) AS maj_cha_below13_8,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '08' then 1 else 0 end) AS maj_cha_14up_8,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '08' then 1 else 0 end) AS min_pay_below13_8,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '08' then 1 else 0 end) AS min_pay_14up_8,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '08' then 1 else 0 end) AS min_cha_below13_8,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '08' then 1 else 0 end) AS min_cha_14up_8,

						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '09' then 1 else 0 end) AS maj_pay_below13_9,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '09' then 1 else 0 end) AS maj_pay_14up_9,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '09' then 1 else 0 end) AS maj_cha_below13_9,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '09' then 1 else 0 end) AS maj_cha_14up_9,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '09' then 1 else 0 end) AS min_pay_below13_9,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '09' then 1 else 0 end) AS min_pay_14up_9,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '09' then 1 else 0 end) AS min_cha_below13_9,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '09' then 1 else 0 end) AS min_cha_14up_9,

						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '10' then 1 else 0 end) AS maj_pay_below13_10,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '10' then 1 else 0 end) AS maj_pay_14up_10,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '10' then 1 else 0 end) AS maj_cha_below13_10,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '10' then 1 else 0 end) AS maj_cha_14up_10,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '10' then 1 else 0 end) AS min_pay_below13_10,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '10' then 1 else 0 end) AS min_pay_14up_10,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '10' then 1 else 0 end) AS min_cha_below13_10,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '10' then 1 else 0 end) AS min_cha_14up_10,

						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '11' then 1 else 0 end) AS maj_pay_below13_11,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '11' then 1 else 0 end) AS maj_pay_14up_11,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '11' then 1 else 0 end) AS maj_cha_below13_11,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '11' then 1 else 0 end) AS maj_cha_14up_11,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '11' then 1 else 0 end) AS min_pay_below13_11,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '11' then 1 else 0 end) AS min_pay_14up_11,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '11' then 1 else 0 end) AS min_cha_below13_11,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '11' then 1 else 0 end) AS min_cha_14up_11,

						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '12' then 1 else 0 end) AS maj_pay_below13_12,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '12' then 1 else 0 end) AS maj_pay_14up_12,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '12' then 1 else 0 end) AS maj_cha_below13_12,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '12' then 1 else 0 end) AS maj_cha_14up_12,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '12' then 1 else 0 end) AS min_pay_below13_12,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '12' then 1 else 0 end) AS min_pay_14up_12,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '12' then 1 else 0 end) AS min_cha_below13_12,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '12' then 1 else 0 end) AS min_cha_14up_12,

						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '13' then 1 else 0 end) AS maj_pay_below13_13,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '13' then 1 else 0 end) AS maj_pay_14up_13,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '13' then 1 else 0 end) AS maj_cha_below13_13,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '13' then 1 else 0 end) AS maj_cha_14up_13,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '13' then 1 else 0 end) AS min_pay_below13_13,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '13' then 1 else 0 end) AS min_pay_14up_13,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '13' then 1 else 0 end) AS min_cha_below13_13,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '13' then 1 else 0 end) AS min_cha_14up_13,

						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '14' then 1 else 0 end) AS maj_pay_below13_14,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '14' then 1 else 0 end) AS maj_pay_14up_14,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '14' then 1 else 0 end) AS maj_cha_below13_14,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '14' then 1 else 0 end) AS maj_cha_14up_14,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '14' then 1 else 0 end) AS min_pay_below13_14,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '14' then 1 else 0 end) AS min_pay_14up_14,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '14' then 1 else 0 end) AS min_cha_below13_14,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '14' then 1 else 0 end) AS min_cha_14up_14,

						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '15' then 1 else 0 end) AS maj_pay_below13_15,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '15' then 1 else 0 end) AS maj_pay_14up_15,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '15' then 1 else 0 end) AS maj_cha_below13_15,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '15' then 1 else 0 end) AS maj_cha_14up_15,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '15' then 1 else 0 end) AS min_pay_below13_15,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '15' then 1 else 0 end) AS min_pay_14up_15,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '15' then 1 else 0 end) AS min_cha_below13_15,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '15' then 1 else 0 end) AS min_cha_14up_15,

						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '16' then 1 else 0 end) AS maj_pay_below13_16,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '16' then 1 else 0 end) AS maj_pay_14up_16,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '16' then 1 else 0 end) AS maj_cha_below13_16,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '16' then 1 else 0 end) AS maj_cha_14up_16,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '16' then 1 else 0 end) AS min_pay_below13_16,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '16' then 1 else 0 end) AS min_pay_14up_16,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '16' then 1 else 0 end) AS min_cha_below13_16,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '16' then 1 else 0 end) AS min_cha_14up_16,

						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '17' then 1 else 0 end) AS maj_pay_below13_17,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '17' then 1 else 0 end) AS maj_pay_14up_17,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '17' then 1 else 0 end) AS maj_cha_below13_17,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '17' then 1 else 0 end) AS maj_cha_14up_17,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '17' then 1 else 0 end) AS min_pay_below13_17,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '17' then 1 else 0 end) AS min_pay_14up_17,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '17' then 1 else 0 end) AS min_cha_below13_17,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '17' then 1 else 0 end) AS min_cha_14up_17,

						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '18' then 1 else 0 end) AS maj_pay_below13_18,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '18' then 1 else 0 end) AS maj_pay_14up_18,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '18' then 1 else 0 end) AS maj_cha_below13_18,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '18' then 1 else 0 end) AS maj_cha_14up_18,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '18' then 1 else 0 end) AS min_pay_below13_18,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '18' then 1 else 0 end) AS min_pay_14up_18,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '18' then 1 else 0 end) AS min_cha_below13_18,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '18' then 1 else 0 end) AS min_cha_14up_18,

						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '19' then 1 else 0 end) AS maj_pay_below13_19,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '19' then 1 else 0 end) AS maj_pay_14up_19,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '19' then 1 else 0 end) AS maj_cha_below13_19,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '19' then 1 else 0 end) AS maj_cha_14up_19,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '19' then 1 else 0 end) AS min_pay_below13_19,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '19' then 1 else 0 end) AS min_pay_14up_19,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '19' then 1 else 0 end) AS min_cha_below13_19,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '19' then 1 else 0 end) AS min_cha_14up_19,

						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '20' then 1 else 0 end) AS maj_pay_below13_20,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '20' then 1 else 0 end) AS maj_pay_14up_20,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '20' then 1 else 0 end) AS maj_cha_below13_20,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '20' then 1 else 0 end) AS maj_cha_14up_20,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '20' then 1 else 0 end) AS min_pay_below13_20,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '20' then 1 else 0 end) AS min_pay_14up_20,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '20' then 1 else 0 end) AS min_cha_below13_20,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '20' then 1 else 0 end) AS min_cha_14up_20,

						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '21' then 1 else 0 end) AS maj_pay_below13_21,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '21' then 1 else 0 end) AS maj_pay_14up_21,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '21' then 1 else 0 end) AS maj_cha_below13_21,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '21' then 1 else 0 end) AS maj_cha_14up_21,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '21' then 1 else 0 end) AS min_pay_below13_21,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '21' then 1 else 0 end) AS min_pay_14up_21,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '21' then 1 else 0 end) AS min_cha_below13_21,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '21' then 1 else 0 end) AS min_cha_14up_21,

						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '22' then 1 else 0 end) AS maj_pay_below13_22,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '22' then 1 else 0 end) AS maj_pay_14up_22,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '22' then 1 else 0 end) AS maj_cha_below13_22,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '22' then 1 else 0 end) AS maj_cha_14up_22,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '22' then 1 else 0 end) AS min_pay_below13_22,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '22' then 1 else 0 end) AS min_pay_14up_22,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '22' then 1 else 0 end) AS min_cha_below13_22,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '22' then 1 else 0 end) AS min_cha_14up_22,

						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '23' then 1 else 0 end) AS maj_pay_below13_23,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '23' then 1 else 0 end) AS maj_pay_14up_23,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '23' then 1 else 0 end) AS maj_cha_below13_23,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '23' then 1 else 0 end) AS maj_cha_14up_23,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '23' then 1 else 0 end) AS min_pay_below13_23,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '23' then 1 else 0 end) AS min_pay_14up_23,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '23' then 1 else 0 end) AS min_cha_below13_23,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '23' then 1 else 0 end) AS min_cha_14up_23,

						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '24' then 1 else 0 end) AS maj_pay_below13_24,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '24' then 1 else 0 end) AS maj_pay_14up_24,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '24' then 1 else 0 end) AS maj_cha_below13_24,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '24' then 1 else 0 end) AS maj_cha_14up_24,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '24' then 1 else 0 end) AS min_pay_below13_24,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '24' then 1 else 0 end) AS min_pay_14up_24,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '24' then 1 else 0 end) AS min_cha_below13_24,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '24' then 1 else 0 end) AS min_cha_14up_24,

						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '25' then 1 else 0 end) AS maj_pay_below13_25,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '25' then 1 else 0 end) AS maj_pay_14up_25,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '25' then 1 else 0 end) AS maj_cha_below13_25,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '25' then 1 else 0 end) AS maj_cha_14up_25,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '25' then 1 else 0 end) AS min_pay_below13_25,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '25' then 1 else 0 end) AS min_pay_14up_25,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '25' then 1 else 0 end) AS min_cha_below13_25,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '25' then 1 else 0 end) AS min_cha_14up_25,

						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '26' then 1 else 0 end) AS maj_pay_below13_26,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '26' then 1 else 0 end) AS maj_pay_14up_26,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '26' then 1 else 0 end) AS maj_cha_below13_26,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '26' then 1 else 0 end) AS maj_cha_14up_26,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '26' then 1 else 0 end) AS min_pay_below13_26,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '26' then 1 else 0 end) AS min_pay_14up_26,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '26' then 1 else 0 end) AS min_cha_below13_26,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '26' then 1 else 0 end) AS min_cha_14up_26,

						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '27' then 1 else 0 end) AS maj_pay_below13_27,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '27' then 1 else 0 end) AS maj_pay_14up_27,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '27' then 1 else 0 end) AS maj_cha_below13_27,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '27' then 1 else 0 end) AS maj_cha_14up_27,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '27' then 1 else 0 end) AS min_pay_below13_27,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '27' then 1 else 0 end) AS min_pay_14up_27,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '27' then 1 else 0 end) AS min_cha_below13_27,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '27' then 1 else 0 end) AS min_cha_14up_27,

						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '28' then 1 else 0 end) AS maj_pay_below13_28,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '28' then 1 else 0 end) AS maj_pay_14up_28,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '28' then 1 else 0 end) AS maj_cha_below13_28,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '28' then 1 else 0 end) AS maj_cha_14up_28,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '28' then 1 else 0 end) AS min_pay_below13_28,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '28' then 1 else 0 end) AS min_pay_14up_28,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '28' then 1 else 0 end) AS min_cha_below13_28,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '28' then 1 else 0 end) AS min_cha_14up_28,

						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '29' then 1 else 0 end) AS maj_pay_below13_29,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '29' then 1 else 0 end) AS maj_pay_14up_29,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '29' then 1 else 0 end) AS maj_cha_below13_29,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '29' then 1 else 0 end) AS maj_cha_14up_29,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '29' then 1 else 0 end) AS min_pay_below13_29,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '29' then 1 else 0 end) AS min_pay_14up_29,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '29' then 1 else 0 end) AS min_cha_below13_29,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '29' then 1 else 0 end) AS min_cha_14up_29,

						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '30' then 1 else 0 end) AS maj_pay_below13_30,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '30' then 1 else 0 end) AS maj_pay_14up_30,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '30' then 1 else 0 end) AS maj_cha_below13_30,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '30' then 1 else 0 end) AS maj_cha_14up_30,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '30' then 1 else 0 end) AS min_pay_below13_30,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '30' then 1 else 0 end) AS min_pay_14up_30,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '30' then 1 else 0 end) AS min_cha_below13_30,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '30' then 1 else 0 end) AS min_cha_14up_30,

						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '31' then 1 else 0 end) AS maj_pay_below13_31,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '31' then 1 else 0 end) AS maj_pay_14up_31,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '31' then 1 else 0 end) AS maj_cha_below13_31,
						SUM(CASE WHEN ops.rvu >=30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '31' then 1 else 0 end) AS maj_cha_14up_31,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='2' AND DAY(cep.date) = '31' then 1 else 0 end) AS min_pay_below13_31,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='2' AND DAY(cep.date) = '31' then 1 else 0 end) AS min_pay_14up_31,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) <=13 AND cw.accomodation_type='1' AND DAY(cep.date) = '31' then 1 else 0 end) AS min_cha_below13_31,
						SUM(CASE WHEN ops.rvu <30 AND fn_calculate_age(p.date_birth, NOW()) >13 AND cw.accomodation_type='1' AND DAY(cep.date) = '31' then 1 else 0 end) AS min_cha_14up_31

						FROM care_encounter_procedure AS cep
						INNER JOIN care_encounter AS e ON e.encounter_nr = cep.encounter_nr
						INNER JOIN care_person AS p ON p.pid = e.pid
						LEFT JOIN care_ops301_en AS ops ON ops.code = cep.code
						LEFT JOIN care_ward AS cw ON cw.nr = e.current_ward_nr
						WHERE DATE(cep.date) BETWEEN '2010-01-01' AND '2010-08-11'
						AND cep.status NOT IN('deleted', 'inactive', 'hidden', 'void')
						AND e.status NOT IN('deleted', 'inactive', 'hidden', 'void');";
		$result_first = $db->Execute($sql_first);

		$sql_second = "SELECT
						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '01' then 1 else 0 end) AS cea_pay_below13_1,
						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '01' then 1 else 0 end) AS cea_pay_14up_1,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '01' then 1 else 0 end) AS cea_cha_below13_1,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '01' then 1 else 0 end) AS cea_cha_14up_1,

						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '02' then 1 else 0 end) AS cea_pay_below13_2,
						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '02' then 1 else 0 end) AS cea_pay_14up_2,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '02' then 1 else 0 end) AS cea_cha_below13_2,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '02' then 1 else 0 end) AS cea_cha_14up_2,

						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '03' then 1 else 0 end) AS cea_pay_below13_3,
						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '03' then 1 else 0 end) AS cea_pay_14up_3,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '03' then 1 else 0 end) AS cea_cha_below13_3,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '03' then 1 else 0 end) AS cea_cha_14up_3,

						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '04' then 1 else 0 end) AS cea_pay_below13_4,
						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '04' then 1 else 0 end) AS cea_pay_14up_4,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '04' then 1 else 0 end) AS cea_cha_below13_4,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '04' then 1 else 0 end) AS cea_cha_14up_4,

						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '05' then 1 else 0 end) AS cea_pay_below13_5,
						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '05' then 1 else 0 end) AS cea_pay_14up_5,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '05' then 1 else 0 end) AS cea_cha_below13_5,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '05' then 1 else 0 end) AS cea_cha_14up_5,

						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '06' then 1 else 0 end) AS cea_pay_below13_6,
						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '06' then 1 else 0 end) AS cea_pay_14up_6,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '06' then 1 else 0 end) AS cea_cha_below13_6,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '06' then 1 else 0 end) AS cea_cha_14up_6,

						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '07' then 1 else 0 end) AS cea_pay_below13_7,
						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '07' then 1 else 0 end) AS cea_pay_14up_7,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '07' then 1 else 0 end) AS cea_cha_below13_7,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '07' then 1 else 0 end) AS cea_cha_14up_7,

						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '08' then 1 else 0 end) AS cea_pay_below13_8,
						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '08' then 1 else 0 end) AS cea_pay_14up_8,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '08' then 1 else 0 end) AS cea_cha_below13_8,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '08' then 1 else 0 end) AS cea_cha_14up_8,

						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '09' then 1 else 0 end) AS cea_pay_below13_9,
						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '09' then 1 else 0 end) AS cea_pay_14up_9,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '09' then 1 else 0 end) AS cea_cha_below13_9,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '09' then 1 else 0 end) AS cea_cha_14up_9,

						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '10' then 1 else 0 end) AS cea_pay_below13_10,
						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '10' then 1 else 0 end) AS cea_pay_14up_10,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '10' then 1 else 0 end) AS cea_cha_below13_10,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '10' then 1 else 0 end) AS cea_cha_14up_10,

						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '11' then 1 else 0 end) AS cea_pay_below13_11,
						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '11' then 1 else 0 end) AS cea_pay_14up_11,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '11' then 1 else 0 end) AS cea_cha_below13_11,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '11' then 1 else 0 end) AS cea_cha_14up_11,

						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '12' then 1 else 0 end) AS cea_pay_below13_12,
						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '12' then 1 else 0 end) AS cea_pay_14up_12,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '12' then 1 else 0 end) AS cea_cha_below13_12,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '12' then 1 else 0 end) AS cea_cha_14up_12,

						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '13' then 1 else 0 end) AS cea_pay_below13_13,
						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '13' then 1 else 0 end) AS cea_pay_14up_13,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '13' then 1 else 0 end) AS cea_cha_below13_13,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '13' then 1 else 0 end) AS cea_cha_14up_13,

						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '14' then 1 else 0 end) AS cea_pay_below13_14,
						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '14' then 1 else 0 end) AS cea_pay_14up_14,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '14' then 1 else 0 end) AS cea_cha_below13_14,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '14' then 1 else 0 end) AS cea_cha_14up_14,

						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '15' then 1 else 0 end) AS cea_pay_below13_15,
						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '15' then 1 else 0 end) AS cea_pay_14up_15,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '15' then 1 else 0 end) AS cea_cha_below13_15,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '15' then 1 else 0 end) AS cea_cha_14up_15,

						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '16' then 1 else 0 end) AS cea_pay_below13_16,
						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '16' then 1 else 0 end) AS cea_pay_14up_16,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '16' then 1 else 0 end) AS cea_cha_below13_16,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '16' then 1 else 0 end) AS cea_cha_14up_16,

						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '17' then 1 else 0 end) AS cea_pay_below13_17,
						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '17' then 1 else 0 end) AS cea_pay_14up_17,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '17' then 1 else 0 end) AS cea_cha_below13_17,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '17' then 1 else 0 end) AS cea_cha_14up_17,

						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '18' then 1 else 0 end) AS cea_pay_below13_18,
						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '18' then 1 else 0 end) AS cea_pay_14up_18,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '18' then 1 else 0 end) AS cea_cha_below13_18,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '18' then 1 else 0 end) AS cea_cha_14up_18,

						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '19' then 1 else 0 end) AS cea_pay_below13_19,
						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '19' then 1 else 0 end) AS cea_pay_14up_19,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '19' then 1 else 0 end) AS cea_cha_below13_19,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '19' then 1 else 0 end) AS cea_cha_14up_19,

						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '20' then 1 else 0 end) AS cea_pay_below13_20,
						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '20' then 1 else 0 end) AS cea_pay_14up_20,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '20' then 1 else 0 end) AS cea_cha_below13_20,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '20' then 1 else 0 end) AS cea_cha_14up_20,

						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '21' then 1 else 0 end) AS cea_pay_below13_21,
						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '21' then 1 else 0 end) AS cea_pay_14up_21,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '21' then 1 else 0 end) AS cea_cha_below13_21,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '21' then 1 else 0 end) AS cea_cha_14up_21,

						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '22' then 1 else 0 end) AS cea_pay_below13_22,
						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '22' then 1 else 0 end) AS cea_pay_14up_22,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '22' then 1 else 0 end) AS cea_cha_below13_22,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '22' then 1 else 0 end) AS cea_cha_14up_22,

						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '23' then 1 else 0 end) AS cea_pay_below13_23,
						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '23' then 1 else 0 end) AS cea_pay_14up_23,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '23' then 1 else 0 end) AS cea_cha_below13_23,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '23' then 1 else 0 end) AS cea_cha_14up_23,

						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '24' then 1 else 0 end) AS cea_pay_below13_24,
						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '24' then 1 else 0 end) AS cea_pay_14up_24,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '24' then 1 else 0 end) AS cea_cha_below13_24,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '24' then 1 else 0 end) AS cea_cha_14up_24,

						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '25' then 1 else 0 end) AS cea_pay_below13_25,
						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '25' then 1 else 0 end) AS cea_pay_14up_25,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '25' then 1 else 0 end) AS cea_cha_below13_25,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '25' then 1 else 0 end) AS cea_cha_14up_25,

						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '26' then 1 else 0 end) AS cea_pay_below13_26,
						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '26' then 1 else 0 end) AS cea_pay_14up_26,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '26' then 1 else 0 end) AS cea_cha_below13_26,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '26' then 1 else 0 end) AS cea_cha_14up_26,

						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '27' then 1 else 0 end) AS cea_pay_below13_27,
						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '27' then 1 else 0 end) AS cea_pay_14up_27,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '27' then 1 else 0 end) AS cea_cha_below13_27,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '27' then 1 else 0 end) AS cea_cha_14up_27,

						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '28' then 1 else 0 end) AS cea_pay_below13_28,
						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '28' then 1 else 0 end) AS cea_pay_14up_28,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '28' then 1 else 0 end) AS cea_cha_below13_28,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '28' then 1 else 0 end) AS cea_cha_14up_28,

						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '29' then 1 else 0 end) AS cea_pay_below13_29,
						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '29' then 1 else 0 end) AS cea_pay_14up_29,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '29' then 1 else 0 end) AS cea_cha_below13_29,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '29' then 1 else 0 end) AS cea_cha_14up_29,

						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '30' then 1 else 0 end) AS cea_pay_below13_30,
						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '30' then 1 else 0 end) AS cea_pay_14up_30,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '30' then 1 else 0 end) AS cea_cha_below13_30,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '30' then 1 else 0 end) AS cea_cha_14up_30,

						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '31' then 1 else 0 end) AS cea_pay_below13_31,
						SUM(CASE WHEN cw.accomodation_type='2' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '31' then 1 else 0 end) AS cea_pay_14up_31,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '31' then 1 else 0 end) AS cea_cha_below13_31,
						SUM(CASE WHEN cw.accomodation_type='1' AND fn_calculate_age(p.date_birth, NOW()) AND  icd.diagnosis_code IN ('O75.7', 'O82', 'O82.0', 'O82.1', 'O82.2', 'O82.8', 'O82.9', 'O84.2', 'P03.4') AND DAY(cep.date) = '31' then 1 else 0 end) AS cea_cha_14up_31

						FROM care_encounter_procedure AS cep
						INNER JOIN care_encounter AS e ON e.encounter_nr = cep.encounter_nr
						INNER JOIN care_person AS p ON p.pid = e.pid
						LEFT JOIN care_ward AS cw ON cw.nr = e.current_ward_nr
						LEFT JOIN care_encounter_diagnosis AS ced ON ced.encounter_nr = e.encounter_nr
						LEFT JOIN care_icd10_en AS icd ON icd.diagnosis_code = ced.code
						WHERE DATE(cep.date) BETWEEN '2010-01-01' AND '2010-08-11'
						AND cep.status NOT IN ('deleted', 'hidden', 'inactive', 'void')
						";
	 $result_second = $db->Execute($sql_second);

	 $sql_third = "SELECT
						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Elective' AND DAY(som.date_operation) = '01' then 1 else 0 end) AS elective_pay_1,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Elective' AND DAY(som.date_operation) = '01' then 1 else 0 end) AS elective_cha_1,
						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '01' then 1 else 0 end) AS emergency_pay_1,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '01' then 1 else 0 end) AS emergency_cha_1,

						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Elective' AND DAY(som.date_operation) = '02' then 1 else 0 end) AS elective_pay_2,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Elective' AND DAY(som.date_operation) = '02' then 1 else 0 end) AS elective_cha_2,
						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '02' then 1 else 0 end) AS emergency_pay_2,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '02' then 1 else 0 end) AS emergency_cha_2,

						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Elective' AND DAY(som.date_operation) = '03' then 1 else 0 end) AS elective_pay_3,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Elective' AND DAY(som.date_operation) = '03' then 1 else 0 end) AS elective_cha_3,
						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '03' then 1 else 0 end) AS emergency_pay_3,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '03' then 1 else 0 end) AS emergency_cha_3,

						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Elective' AND DAY(som.date_operation) = '04' then 1 else 0 end) AS elective_pay_4,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Elective' AND DAY(som.date_operation) = '04' then 1 else 0 end) AS elective_cha_4,
						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '04' then 1 else 0 end) AS emergency_pay_4,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '04' then 1 else 0 end) AS emergency_cha_4,

						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Elective' AND DAY(som.date_operation) = '05' then 1 else 0 end) AS elective_pay_5,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Elective' AND DAY(som.date_operation) = '05' then 1 else 0 end) AS elective_cha_5,
						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '05' then 1 else 0 end) AS emergency_pay_5,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '05' then 1 else 0 end) AS emergency_cha_5,

						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Elective' AND DAY(som.date_operation) = '06' then 1 else 0 end) AS elective_pay_6,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Elective' AND DAY(som.date_operation) = '06' then 1 else 0 end) AS elective_cha_6,
						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '06' then 1 else 0 end) AS emergency_pay_6,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '06' then 1 else 0 end) AS emergency_cha_6,

						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Elective' AND DAY(som.date_operation) = '07' then 1 else 0 end) AS elective_pay_7,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Elective' AND DAY(som.date_operation) = '07' then 1 else 0 end) AS elective_cha_7,
						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '07' then 1 else 0 end) AS emergency_pay_7,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '07' then 1 else 0 end) AS emergency_cha_7,

						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Elective' AND DAY(som.date_operation) = '08' then 1 else 0 end) AS elective_pay_8,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Elective' AND DAY(som.date_operation) = '08' then 1 else 0 end) AS elective_cha_8,
						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '08' then 1 else 0 end) AS emergency_pay_8,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '08' then 1 else 0 end) AS emergency_cha_8,

						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Elective' AND DAY(som.date_operation) = '09' then 1 else 0 end) AS elective_pay_9,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Elective' AND DAY(som.date_operation) = '09' then 1 else 0 end) AS elective_cha_9,
						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '09' then 1 else 0 end) AS emergency_pay_9,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '09' then 1 else 0 end) AS emergency_cha_9,

						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Elective' AND DAY(som.date_operation) = '10' then 1 else 0 end) AS elective_pay_10,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Elective' AND DAY(som.date_operation) = '10' then 1 else 0 end) AS elective_cha_10,
						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '10' then 1 else 0 end) AS emergency_pay_10,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '10' then 1 else 0 end) AS emergency_cha_10,

						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Elective' AND DAY(som.date_operation) = '11' then 1 else 0 end) AS elective_pay_11,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Elective' AND DAY(som.date_operation) = '11' then 1 else 0 end) AS elective_cha_11,
						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '11' then 1 else 0 end) AS emergency_pay_11,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '11' then 1 else 0 end) AS emergency_cha_11,

						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Elective' AND DAY(som.date_operation) = '12' then 1 else 0 end) AS elective_pay_12,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Elective' AND DAY(som.date_operation) = '12' then 1 else 0 end) AS elective_cha_12,
						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '12' then 1 else 0 end) AS emergency_pay_12,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '12' then 1 else 0 end) AS emergency_cha_12,

						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Elective' AND DAY(som.date_operation) = '13' then 1 else 0 end) AS elective_pay_13,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Elective' AND DAY(som.date_operation) = '13' then 1 else 0 end) AS elective_cha_13,
						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '13' then 1 else 0 end) AS emergency_pay_13,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '13' then 1 else 0 end) AS emergency_cha_13,

						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Elective' AND DAY(som.date_operation) = '14' then 1 else 0 end) AS elective_pay_14,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Elective' AND DAY(som.date_operation) = '14' then 1 else 0 end) AS elective_cha_14,
						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '14' then 1 else 0 end) AS emergency_pay_14,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '14' then 1 else 0 end) AS emergency_cha_14,

						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Elective' AND DAY(som.date_operation) = '15' then 1 else 0 end) AS elective_pay_15,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Elective' AND DAY(som.date_operation) = '15' then 1 else 0 end) AS elective_cha_15,
						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '15' then 1 else 0 end) AS emergency_pay_15,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '15' then 1 else 0 end) AS emergency_cha_15,

						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Elective' AND DAY(som.date_operation) = '16' then 1 else 0 end) AS elective_pay_16,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Elective' AND DAY(som.date_operation) = '16' then 1 else 0 end) AS elective_cha_16,
						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '16' then 1 else 0 end) AS emergency_pay_16,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '16' then 1 else 0 end) AS emergency_cha_16,

						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Elective' AND DAY(som.date_operation) = '17' then 1 else 0 end) AS elective_pay_17,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Elective' AND DAY(som.date_operation) = '17' then 1 else 0 end) AS elective_cha_17,
						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '17' then 1 else 0 end) AS emergency_pay_17,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '17' then 1 else 0 end) AS emergency_cha_17,

						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Elective' AND DAY(som.date_operation) = '18' then 1 else 0 end) AS elective_pay_18,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Elective' AND DAY(som.date_operation) = '18' then 1 else 0 end) AS elective_cha_18,
						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '18' then 1 else 0 end) AS emergency_pay_18,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '18' then 1 else 0 end) AS emergency_cha_18,

						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Elective' AND DAY(som.date_operation) = '19' then 1 else 0 end) AS elective_pay_19,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Elective' AND DAY(som.date_operation) = '19' then 1 else 0 end) AS elective_cha_19,
						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '19' then 1 else 0 end) AS emergency_pay_19,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '19' then 1 else 0 end) AS emergency_cha_19,

						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Elective' AND DAY(som.date_operation) = '20' then 1 else 0 end) AS elective_pay_20,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Elective' AND DAY(som.date_operation) = '20' then 1 else 0 end) AS elective_cha_20,
						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '20' then 1 else 0 end) AS emergency_pay_20,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '20' then 1 else 0 end) AS emergency_cha_20,

						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Elective' AND DAY(som.date_operation) = '21' then 1 else 0 end) AS elective_pay_21,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Elective' AND DAY(som.date_operation) = '21' then 1 else 0 end) AS elective_cha_21,
						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '21' then 1 else 0 end) AS emergency_pay_21,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '21' then 1 else 0 end) AS emergency_cha_21,

						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Elective' AND DAY(som.date_operation) = '22' then 1 else 0 end) AS elective_pay_22,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Elective' AND DAY(som.date_operation) = '22' then 1 else 0 end) AS elective_cha_22,
						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '22' then 1 else 0 end) AS emergency_pay_22,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '22' then 1 else 0 end) AS emergency_cha_22,

						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Elective' AND DAY(som.date_operation) = '23' then 1 else 0 end) AS elective_pay_23,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Elective' AND DAY(som.date_operation) = '23' then 1 else 0 end) AS elective_cha_23,
						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '23' then 1 else 0 end) AS emergency_pay_23,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '23' then 1 else 0 end) AS emergency_cha_23,

						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Elective' AND DAY(som.date_operation) = '24' then 1 else 0 end) AS elective_pay_24,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Elective' AND DAY(som.date_operation) = '24' then 1 else 0 end) AS elective_cha_24,
						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '24' then 1 else 0 end) AS emergency_pay_24,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '24' then 1 else 0 end) AS emergency_cha_24,

						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Elective' AND DAY(som.date_operation) = '25' then 1 else 0 end) AS elective_pay_25,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Elective' AND DAY(som.date_operation) = '25' then 1 else 0 end) AS elective_cha_25,
						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '25' then 1 else 0 end) AS emergency_pay_25,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '25' then 1 else 0 end) AS emergency_cha_25,

						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Elective' AND DAY(som.date_operation) = '26' then 1 else 0 end) AS elective_pay_26,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Elective' AND DAY(som.date_operation) = '26' then 1 else 0 end) AS elective_cha_26,
						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '26' then 1 else 0 end) AS emergency_pay_26,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '26' then 1 else 0 end) AS emergency_cha_26,

						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Elective' AND DAY(som.date_operation) = '27' then 1 else 0 end) AS elective_pay_27,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Elective' AND DAY(som.date_operation) = '27' then 1 else 0 end) AS elective_cha_27,
						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '27' then 1 else 0 end) AS emergency_pay_27,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '27' then 1 else 0 end) AS emergency_cha_27,

						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Elective' AND DAY(som.date_operation) = '28' then 1 else 0 end) AS elective_pay_28,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Elective' AND DAY(som.date_operation) = '28' then 1 else 0 end) AS elective_cha_28,
						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '28' then 1 else 0 end) AS emergency_pay_28,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '28' then 1 else 0 end) AS emergency_cha_28,

						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Elective' AND DAY(som.date_operation) = '29' then 1 else 0 end) AS elective_pay_29,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Elective' AND DAY(som.date_operation) = '29' then 1 else 0 end) AS elective_cha_29,
						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '29' then 1 else 0 end) AS emergency_pay_29,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '29' then 1 else 0 end) AS emergency_cha_29,

						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Elective' AND DAY(som.date_operation) = '30' then 1 else 0 end) AS elective_pay_30,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Elective' AND DAY(som.date_operation) = '30' then 1 else 0 end) AS elective_cha_30,
						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '30' then 1 else 0 end) AS emergency_pay_30,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '30' then 1 else 0 end) AS emergency_cha_30,

						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Elective' AND DAY(som.date_operation) = '31' then 1 else 0 end) AS elective_pay_31,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Elective' AND DAY(som.date_operation) = '31' then 1 else 0 end) AS elective_cha_31,
						SUM(CASE WHEN cw.accomodation_type='2' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '31' then 1 else 0 end) AS emergency_pay_31,
						SUM(CASE WHEN cw.accomodation_type='1' AND som.request_priority='Emergency' AND DAY(som.date_operation) = '31' then 1 else 0 end) AS emergency_cha_31

						FROM seg_or_main AS som
						INNER JOIN care_encounter AS e ON e.encounter_nr = som.encounter_nr
						LEFT JOIN care_ward AS cw ON cw.nr = e.current_ward_nr
						WHERE DATE(som.date_operation) BETWEEN '2010-01-01' AND '2010-08-11'";
		$result_third = $db->Execute($sql_third);

		$maj_pay_below13 = 0;
		$maj_pay_14up = 0;
		$maj_cha_below13 = 0;
		$maj_cha_14up = 0;
		$min_pay_below13 = 0;
		$min_pay_14up = 0;
		$min_cha_below13 = 0;
		$min_cha_14up = 0;
		$ces_pay_below13 = 0;
		$ces_pay_14up = 0;
		$ces_cha_below13 = 0;
		$ces_cha_14up = 0;
		$elec_pay = 0;
		$elec_cha = 0;
		$emer_pay = 0;
		$emer_cha = 0;

		$row1=$result_first->FetchRow();
		$row2=$result_second->FetchRow();
		$row3=$result_third->FetchRow();

		for($i=1; $i<=31; $i++){

			 $str1a = "maj_pay_below13_".$i;
			 $str1b = "maj_pay_14up_".$i;
			 $str1c = "maj_cha_below13_".$i;
			 $str1d = "maj_cha_14up_".$i;
			 $str1e = "min_pay_below13_".$i;
			 $str1f = "min_pay_14up_".$i;
			 $str1g = "min_cha_below13_".$i;
			 $str1h = "min_cha_14up_".$i;
			 $str2i = "cea_pay_below13_".$i;
			 $str2j = "cea_pay_14up_".$i;
			 $str2k = "cea_cha_below13_".$i;
			 $str2l = "cea_cha_14up_".$i;
			 $str3m = "elective_pay_".$i;
			 $str3n = "elective_cha_".$i;
			 $str3o = "emergency_pay_".$i;
			 $str3p = "emergency_cha_".$i;

			 //compute total
			 $day_total = $row1[$str1a] + $row1[$str1b] + $row1[$str1c] + $row1[$str1d] + $row1[$str1e] + $row1[$str1f] +
										$row1[$str1g] + $row1[$str1h] + $row2[$str2i] + $row2[$str2j] + $row2[$str2k] + $row2[$str2l] +
										$row3[$str3m] + $row3[$str3n] + $row3[$str3o] + $row3[$str3p];

			 $maj_pay_below13 = $maj_pay_below13 + $row1[$str1a];
			 $maj_pay_14up = $maj_pay_14up + $row1[$str1b];
			 $maj_cha_below13 = $maj_cha_below13 + $row1[$str1c];
			 $maj_cha_14up = $maj_cha_14up + $row1[$str1d];
			 $min_pay_below13 = $min_pay_below13 + $row1[$str1e];
			 $min_pay_14up = $min_pay_14up + $row1[$str1f];
			 $min_cha_below13 = $min_cha_below13 + $row1[$str1g];
			 $min_cha_14up = $min_cha_14up + $row1[$str1h];
			 $ces_pay_below13 = $ces_pay_below13 + $row2[$str2i];
			 $ces_pay_14up = $ces_pay_14up + $row2[$str2j];
			 $ces_cha_below13 = $ces_cha_below13 + $row2[$str2k];
			 $ces_cha_14up = $ces_cha_14up + $row2[$str2l];
			 $elec_pay = $elec_pay + $row3[$str3m];
			 $elec_cha = $elec_cha + $row3[$str3n];
			 $emer_pay = $emer_pay + $row3[$str3o];
			 $emer_cha = $emer_cha + $row3[$str3p];
			 $grand_total = $grand_total + $day_total;

			$this->Cell($this->ColumnWidth[0], $rowheight, $i, 1, 0, $this->Alignment[0]);
			$this->Cell($this->ColumnWidth[1]/2, $rowheight, $row1[$str1a],1,0,$this->Alignment[1]);
			$this->Cell($this->ColumnWidth[1]/2, $rowheight, $row1[$str1b],1,0,$this->Alignment[2]);
			$this->Cell($this->ColumnWidth[2]/2, $rowheight, $row1[$str1c],1,0,$this->Alignment[3]);
			$this->Cell($this->ColumnWidth[2]/2, $rowheight, $row1[$str1d],1,0,$this->Alignment[4]);
			$this->Cell($this->ColumnWidth[3]/2, $rowheight, $row1[$str1e],1,0,$this->Alignment[5]);
			$this->Cell($this->ColumnWidth[3]/2, $rowheight, $row1[$str1f],1,0,$this->Alignment[6]);
			$this->Cell($this->ColumnWidth[4]/2, $rowheight, $row1[$str1g],1,0,$this->Alignment[7]);
			$this->Cell($this->ColumnWidth[4]/2, $rowheight, $row1[$str1h],1,0,$this->Alignment[8]);
			$this->Cell($this->ColumnWidth[5]/2, $rowheight, $row2[$str2i],1,0,$this->Alignment[9]);
			$this->Cell($this->ColumnWidth[5]/2, $rowheight, $row2[$str2j],1,0,$this->Alignment[10]);
			$this->Cell($this->ColumnWidth[6]/2, $rowheight, $row2[$str2k],1,0,$this->Alignment[11]);
			$this->Cell($this->ColumnWidth[6]/2, $rowheight, $row2[$str2l],1,0,$this->Alignment[12]);
			$this->Cell($this->ColumnWidth[7], $rowheight, $row3[$str3m],1,0,$this->Alignment[13]);
			$this->Cell($this->ColumnWidth[8], $rowheight, $row3[$str3n],1,0,$this->Alignment[14]);
			$this->Cell($this->ColumnWidth[9], $rowheight, $row3[$str3o],1,0,$this->Alignment[15]);
			$this->Cell($this->ColumnWidth[10], $rowheight, $row3[$str3p],1,0,$this->Alignment[16]);
			$this->Cell($this->ColumnWidth[11], $rowheight, $day_total,1,0,$this->Alignment[17]);
			$this->Ln();
		}

			$this->Cell($this->ColumnWidth[0], $rowheight, "total =>", 1, 0, $this->Alignment[0]);
			$this->Cell($this->ColumnWidth[1]/2, $rowheight, $maj_pay_below13,1,0,$this->Alignment[1]);
			$this->Cell($this->ColumnWidth[1]/2, $rowheight, $maj_pay_14up,1,0,$this->Alignment[2]);
			$this->Cell($this->ColumnWidth[2]/2, $rowheight, $maj_cha_below13,1,0,$this->Alignment[3]);
			$this->Cell($this->ColumnWidth[2]/2, $rowheight, $maj_cha_14up,1,0,$this->Alignment[4]);
			$this->Cell($this->ColumnWidth[3]/2, $rowheight, $min_pay_below13,1,0,$this->Alignment[5]);
			$this->Cell($this->ColumnWidth[3]/2, $rowheight, $min_pay_14up,1,0,$this->Alignment[6]);
			$this->Cell($this->ColumnWidth[4]/2, $rowheight, $min_cha_below13,1,0,$this->Alignment[7]);
			$this->Cell($this->ColumnWidth[4]/2, $rowheight, $min_cha_14up,1,0,$this->Alignment[8]);
			$this->Cell($this->ColumnWidth[5]/2, $rowheight, $ces_pay_below13,1,0,$this->Alignment[9]);
			$this->Cell($this->ColumnWidth[5]/2, $rowheight, $ces_pay_14up,1,0,$this->Alignment[10]);
			$this->Cell($this->ColumnWidth[6]/2, $rowheight, $ces_cha_below13,1,0,$this->Alignment[11]);
			$this->Cell($this->ColumnWidth[6]/2, $rowheight, $ces_cha_14up,1,0,$this->Alignment[12]);
			$this->Cell($this->ColumnWidth[7], $rowheight, $elec_pay,1,0,$this->Alignment[13]);
			$this->Cell($this->ColumnWidth[8], $rowheight, $elec_cha,1,0,$this->Alignment[14]);
			$this->Cell($this->ColumnWidth[9], $rowheight, $emer_pay,1,0,$this->Alignment[15]);
			$this->Cell($this->ColumnWidth[10], $rowheight, $emer_cha,1,0,$this->Alignment[16]);
			$this->Cell($this->ColumnWidth[11], $rowheight,  $grand_total,1,0,$this->Alignment[17]);

	}
}

$rep = new OperatingRoomCensus($_GET['from'], $_GET['to']);
$rep->AliasNbPages();
$rep->AddPage();
$rep->FetchData();
$rep->Output();
?>