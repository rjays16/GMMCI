<?php
#created by Cherry 07-30-09
#Cancer/Tumor Monitoring Report
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

class RepGen_LingapStatistics extends RepGen{
var $colored = TRUE;
var $from, $to;

	 function RepGen_LingapStatistics ($from, $to) {
		global $db;
		$this->RepGen("MEDICAL RECORDS: LINGAP STATISTICS");

		$this->ColumnWidth = array(15,15,15,40,8,15,15,40,20,12,30,30,30,10,10,40,40, 40);
		$this->RowHeight = 5;
		$this->TextHeight = 5;
		$this->TextPadding = 0.2;
		$this->Alignment = array('L', 'L', 'C', 'C', 'C', 'C', 'C', 'C');
		$this->TableLabel = array('NAME OF PATIENT', 'ADDRESS', 'AGE', 'SEX', 'CONTROL NUMBER','DATE OF APPLICATION', 'DATE APPROVED', 'AMOUNT GRANTED');
		$this->PageOrientation = "L";
		$this->FPDF("L", 'mm', 'Legal');
		$this->NoWrap = FALSE;
		$this->LEFTMARGIN = 2;

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
		#$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',50,8,20);
		$this->SetFont("Arial","I","9");
		$total_w = 165;
		#$this->Cell(50,4);
			#$this->Cell($total_w,4,'Republic of the Philippines',$border2,1,'C');
		$this->Cell(0,4,$row['hosp_country'],$border2,1,'C');
		#$this->Cell(50,4);
			#$this->Cell($total_w,4,'DEPARTMENT OF HEALTH',$border2,1,'C');
		$this->Cell(0,4,$row['hosp_agency'],$border2,1,'C');
			$this->Ln(2);
		$this->SetFont("Arial","B","10");
		#$this->Cell(50,4);
			#$this->Cell($total_w,4,'Gonzales Maranan Medical Center Incorporated',$border2,1,'C');
		$this->Cell(0,4,$row['hosp_name'],$border2,1,'C');
		$this->SetFont("Arial","","9");
		#$this->Cell(50,4);
			#$this->Cell($total_w,4,'Bajada, Davao City',$border2,1,'C');
		$this->Cell(0,4,$row['hosp_addr1'],$border2,1,'C');
			$this->Ln(4);
			$this->SetFont('Arial','B',12);
		#$this->Cell(50,5);

		$this->Cell(0,4,'DELIVERY ROOM',$border2,1,'C');
		 $this->SetFont('Arial','B',9);
		#$this->Cell(50,5);

		if ($this->from==$this->to)
			$text = "For ".date("F j, Y",strtotime($this->from));
		else
				#$text = "Full History";
			$text = "From ".date("F j, Y",strtotime($this->from))." To ".date("F j, Y",strtotime($this->to));

			$this->Cell(0,4,$text,$border2,1,'C');
		$this->Ln(5);

		# Print table header
			$this->SetFont('Arial','B',8);
		#if ($this->colored) $this->SetFillColor(0xED);
		if ($this->colored) $this->SetFillColor(255);
		$this->SetTextColor(0);
		$row=6;
		$this->Cell($this->ColumnWidth[0], $row, "DATE/TIME", 1, 0, 'C');
		$this->Cell($this->ColumnWidth[1], $row, "HOSP #", 1, 0, 'C');
		$this->Cell($this->ColumnWidth[2], $row, "CASE #", 1, 0, 'C');
		$this->Cell($this->ColumnWidth[3], $row, "PTS. NAME", 1, 0, 'C');
		$this->Cell($this->ColumnWidth[4], $row, "AGE", 1, 0, 'C');
		$this->Cell($this->ColumnWidth[5], $row, "STATUS", 1, 0, 'C');
		$this->Cell($this->ColumnWidth[6], $row, "B-DAY", 1, 0, 'C');
		$this->Cell($this->ColumnWidth[7], $row, "ADDRESS", 1, 0, 'C');
		$this->Cell($this->ColumnWidth[8], $row, "PHILHEALTH #", 1, 0, 'C');
		$this->Cell($this->ColumnWidth[9], $row, "SEX Bb.", 1, 0, 'C');
		$this->Cell($this->ColumnWidth[10], $row, "DATE DELIVERY", 1, 0, 'C');
		$this->Cell($this->ColumnWidth[11], $row, "ADMITTING DX.", 1, 0, 'C');
		$this->Cell($this->ColumnWidth[12], $row, "OB ON DUTY", 1, 0, 'C');
		$this->Cell($this->ColumnWidth[13], $row, "ROD", 1, 0, 'C');
		$this->Cell($this->ColumnWidth[14], $row, "NOD", 1, 0, 'C');
		$this->Cell($this->ColumnWidth[15], $row, "REMARKS", 1, 0, 'C');
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
			$this->Cell(275, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
		}

		$cols = array();
	}

	function FetchData(){
		global $db;
 /*
	 $sql ="SELECT CONCAT(IF (trim(p.name_last) IS NULL,'',trim(p.name_last)),', ',IF(trim(p.name_first) IS NULL ,'',trim(p.name_first)),' ', IF(trim(p.name_middle) IS NULL,'',trim(p.name_middle))) AS Patient_Name,
			IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age) AS Age, upper(p.sex) AS Sex,
			CONCAT(IF (trim(p.street_name) IS NULL,'',trim(p.street_name)),' ',
				IF (trim(sb.brgy_name) IS NULL,'',trim(sb.brgy_name)),' ',
				IF (trim(sm.mun_name) IS NULL,'',trim(sm.mun_name)),' ',
				IF (trim(sm.zipcode) IS NULL,'',trim(sm.zipcode)),' ',
				IF (trim(sp.prov_name) IS NULL,'',trim(sp.prov_name)),' ',
				IF (trim(sr.region_name) IS NULL,'',trim(sr.region_name))) AS Address,
			sle.control_nr,
			sl.date_generated AS date_application,
			sle.entry_date AS date_approved
			FROM seg_social_lingap AS sl
			INNER JOIN seg_lingap_entries AS sle ON sle.pid = sl.pid
			INNER JOIN care_person AS p ON p.pid = sl.pid
			LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=p.brgy_nr
			LEFT JOIN seg_municity AS sm ON sm.mun_nr=p.mun_nr
			LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
			LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
			WHERE DATE(sle.entry_date) BETWEEN '".$this->from."' AND '".$this->to."'
			ORDER BY DATE(sle.entry_date) ASC
				;";
			#echo "".$sql;
			$result=$db->Execute($sql);
				if ($result) {

					$this->_count = $result->RecordCount();
						$this->Data=array();

					while ($row=$result->FetchRow()) {
										$this->Data[]=array(
												$row['Patient_Name'],
												$row['Age'],
												$row['Sex'],
												$row['Address'],
												$row['control_nr'],
												date("Y-m-d ",strtotime($row['date_application'])),
												date("Y-m-d ", strtotime($row['date_approved']))
										 );
					}

			}
			else {
				print_r($sql);
				print_r($db->ErrorMsg());
				exit;
				# Error
			} */
	}
}

$rep = new RepGen_LingapStatistics($_GET['from'], $_GET['to']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>