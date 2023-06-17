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

		$this->ColumnWidth = array(8,50, 65, 18, 10, 35, 20,29, 35);
		$this->RowHeight = 5;
		$this->TextHeight = 5;
		$this->TextPadding = 0.2;
		#$this->Alignment = array('L', 'L', 'C', 'C', 'C', 'C', 'C', 'C');
		$this->Alignment = array('C' ,'L', 'L', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
		$this->TableLabel = array('NAME OF PATIENT', 'ADDRESS', 'AGE', 'SEX', 'BIRTH DATE', 'CIVIL STATUS', 'CONTROL NUMBER','DATE OF APPLICATION');
		$this->PageOrientation = "L";
		$this->NoWrap = FALSE;
		$this->LEFTMARGIN = 5;

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
		$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',50,8,20);
		$this->SetFont("Arial","I","9");
		$total_w = 165;
		$this->Cell(50,4);
			#$this->Cell($total_w,4,'Republic of the Philippines',$border2,1,'C');
		$this->Cell($total_w,4,$row['hosp_country'],$border2,1,'C');
		$this->Cell(50,4);
			#$this->Cell($total_w,4,'DEPARTMENT OF HEALTH',$border2,1,'C');
		$this->Cell($total_w,4,$row['hosp_agency'],$border2,1,'C');
			$this->Ln(2);
		$this->SetFont("Arial","B","10");
		$this->Cell(50,4);
			#$this->Cell($total_w,4,'Gonzales Maranan Medical Center Incorporated',$border2,1,'C');
		$this->Cell($total_w,4,$row['hosp_name'],$border2,1,'C');
		$this->SetFont("Arial","","9");
		$this->Cell(50,4);
			#$this->Cell($total_w,4,'Bajada, Davao City',$border2,1,'C');
		$this->Cell($total_w,4,$row['hosp_addr1'],$border2,1,'C');
			$this->Ln(4);
			$this->SetFont('Arial','B',12);
		$this->Cell(50,5);

		$this->Cell($total_w,4,'LINGAP STATISTICS',$border2,1,'C');
		 $this->SetFont('Arial','B',9);
		$this->Cell(50,5);

		if ($this->from==$this->to)
			$text = "For ".date("F j, Y",strtotime($this->from));
		else
				#$text = "Full History";
			$text = "From ".date("F j, Y",strtotime($this->from))." To ".date("F j, Y",strtotime($this->to));

			$this->Cell($total_w,4,$text,$border2,1,'C');
		$this->Ln(5);

		# Print table header
		$this->Cell(30,4,'No. of Records : ',$border2,0,'L');
		$this->SetFont('Arial','B',9);
		$this->Cell(20,4,$this->_count,$border2,1,'L');

			$this->SetFont('Arial','B',8);
		#if ($this->colored) $this->SetFillColor(0xED);
		if ($this->colored) $this->SetFillColor(255);
		$this->SetTextColor(0);
		$row=6;
		$this->Cell($this->ColumnWidth[0], $row, "", 1, 0, 'C');
		$this->Cell($this->ColumnWidth[1], $row, $this->TableLabel[0], 1, 0,'C');
		$this->Cell($this->ColumnWidth[2], $row, $this->TableLabel[1], 1, 0, 'C');
		$this->Cell($this->ColumnWidth[3], $row, $this->TableLabel[2], 1, 0, 'C');
		$this->Cell($this->ColumnWidth[4], $row, $this->TableLabel[3], 1, 0, 'C');
		$this->Cell($this->ColumnWidth[5], $row, $this->TableLabel[4], 1, 0, 'C');
		$this->Cell($this->ColumnWidth[6], $row, $this->TableLabel[5], 1, 0, 'C');
		$this->Cell($this->ColumnWidth[7], $row, $this->TableLabel[6], 1, 0, 'C');
		$this->Cell($this->ColumnWidth[8], $row, $this->TableLabel[7], 1, 0, 'C');
		#$this->Cell($this->ColumnWidth[8], $row, $this->TableLabel[8], 1, 0, 'C');
		#$this->Cell($this->ColumnWidth[9], $row, $this->TableLabel[9], 1, 0, 'C');
		#$this->Cell($this->ColumnWidth[10], $row, $this->TableLabel[10], 1, 0, 'C');
		#$this->Cell($this->ColumnWidth[11], $row, $this->TableLabel[11], 1, 0, 'C');
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
			$total_width = $this->ColumnWidth[0] + $this->ColumnWidth[1] + $this->ColumnWidth[2] + $this->ColumnWidth[3] + $this->ColumnWidth[4] +$this->ColumnWidth[5] +$this->ColumnWidth[6] + $this->ColumnWidth[7];
			$this->Cell($total_width, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
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
				;";        */
		 $sql = "SELECT CONCAT(IF (trim(p.name_last) IS NULL,'',trim(p.name_last)),', ',IF(trim(p.name_first) IS NULL ,'',trim(p.name_first)),' ', IF(trim(p.name_middle) IS NULL,'',trim(p.name_middle))) AS Patient_Name,
			IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age) AS Age, upper(p.sex) AS Sex,
			p.date_birth, p.civil_status,
			CONCAT(IF (trim(p.street_name) IS NULL,'',trim(p.street_name)),' ',
				IF (trim(sb.brgy_name) IS NULL,'',trim(sb.brgy_name)),' ',
				IF (trim(sm.mun_name) IS NULL,'',trim(sm.mun_name)),' ',
				IF (trim(sm.zipcode) IS NULL,'',trim(sm.zipcode)),' ',
				IF (trim(sp.prov_name) IS NULL,'',trim(sp.prov_name)),' ',
				IF (trim(sr.region_name) IS NULL,'',trim(sr.region_name))) AS Address,
			sl.control_nr,
			sl.date_generated AS date_application
			/*sle.entry_date AS date_approved*/
			FROM seg_social_lingap AS sl
			/*LEFT JOIN seg_lingap_entries AS sle ON sle.pid = sl.pid*/
			INNER JOIN care_person AS p ON p.pid = sl.pid
			LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=p.brgy_nr
			LEFT JOIN seg_municity AS sm ON sm.mun_nr=p.mun_nr
			LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
			LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
			WHERE DATE(sl.date_generated) BETWEEN '2010-07-01' AND '2010-07-22'
			ORDER BY Patient_Name ASC;
			";

			#echo "".$sql;
			$result=$db->Execute($sql);
				if ($result) {

					$this->_count = $result->RecordCount();
						$this->Data=array();
					$i = 1;
					while ($row=$result->FetchRow()) {
										$this->Data[]=array(
												$i,
												$row['Patient_Name'],
												$row['Address'],
												$row['Age'],
												$row['Sex'],
												date("m/d/Y", strtotime($row['date_birth'])),
												$row['civil_status'],
												$row['control_nr'],
												date("m/d/Y",strtotime($row['date_application']))
										 );
										 $i++;
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

$rep = new RepGen_LingapStatistics($_GET['from'], $_GET['to']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>