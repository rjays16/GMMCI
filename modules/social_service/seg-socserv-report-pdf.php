<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path."/classes/fpdf/pdf.class.php");
require($root_path.'/modules/repgen/repgen.inc.php');
include_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/class_personell.php');


/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

	class Socserv_Patients extends FPDF {
	var $date;
	var $colored = TRUE;
	var $encoder, $fromdate, $todate;
	var $total_width;


	function Socserv_Patients ($class, $encoder, $fromdate, $todate) {
		global $db;
		#$this->RepGen("PATIENT'S LIST","L","Legal");
		#$this->RepGen("CLASSIFICATION LIST","L","A4");
		$this->SetAutoPageBreak(FALSE);

		# 165
		$this->ColumnWidth = array(10,20,22,60,20,90,20,16,10,20,23,30);
		#$this->RowHeight = 7;
		$this->RowHeight = 4.5;
		$this->TextHeight = 5;
		$this->total_width = $this->ColumnWidth[0]+$this->ColumnWidth[1]+$this->ColumnWidth[2]+$this->ColumnWidth[3]+$this->ColumnWidth[4]+$this->ColumnWidth[5]+$this->ColumnWidth[6]+$this->ColumnWidth[7]+$this->ColumnWidth[8]+$this->ColumnWidth[9]+$this->ColumnWidth[10]+$this->ColumnWidth[11];

		$this->Alignment = array('L','L','L','L','L','L','L','L','C','L','L','L');
		#$this->PageOrientation = "L";
		#$this->PageFormat = "Legal";
		$this->LEFTMARGIN=5;
		$this->DEFAULT_TOPMARGIN = 5;
		$this->NoWrap = false;
		$this->FPDF("L", 'mm', 'Legal');
		$this->encoder = $encoder;

		#if ($encoder!='all'){
			#$this->encoder = mb_strtoupper($encoder);
		#}else
		#	$this->encoder_name = "ALL SOCIAL WORKERS";

		if (($fromdate)&&($fromdate!='0000-00-00'))
			$this->fromdate = mb_strtoupper(date("F d, Y",strtotime($fromdate)));

		if (($todate)&&($todate!='0000-00-00'))
			$this->todate = mb_strtoupper(date("F d, Y",strtotime($todate)));


		$this->SetFillColor(0xFF);
		if ($this->colored) $this->SetDrawColor(0xDD);
	}

	function Header() {
		global $root_path, $db;
		$objInfo = new Hospital_Admin();
		$pers_obj=new Personell;

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

		$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',20,8,30,40);
		$this->SetFont("Arial","I","9");
		$total_w = 0;
		$this->Cell(17,4);
		$this->Cell($total_w,4,$row['hosp_country'],$border2,1,'C');
		$this->Cell(17,4);
		$this->Cell($total_w,4,$row['hosp_agency'],$border2,1,'C');
		$this->Ln(2);
		$this->SetFont("Arial","B","10");
		$this->Cell(17,4);
		$this->Cell($total_w,4,$row['hosp_name'],$border2,1,'C');
		$this->SetFont("Arial","","9");
		$this->Cell(17,4);
		$this->Cell($total_w,4,$row['hosp_addr1'],$border2,1,'C');
		$this->Ln(2);

		$this->SetFont("Arial","B","10");

		$this->Cell(17,4);
		$this->Cell($total_w,4,'SOCIAL SERVICE DEPARTMENT',$border2,1,'C');
		$this->Ln(2);
		$this->SetFont("Arial","B","13");

		$this->SetFont('Arial','B',12);
		$this->Cell(17,5);
		$this->Cell($total_w,4,'PATIENTS\' LIST REPORT',$border2,1,'C');
		$this->Ln(2);
		$this->Cell(17,5);
		#echo "enc = ".$this->encoder_name;
		if ($this->encoder!='all'){
			$row = $pers_obj->getPersonellInfo($this->encoder);
			$this->encoder_fullname = mb_strtoupper($row["name_last"]).", ".mb_strtoupper($row["name_first"])." ".mb_strtoupper($row["name_middle"]);
			$this->Cell($total_w,4,'SOCIAL WORKER : '.$this->encoder_fullname,$border2,1,'C');
		}else{
			$this->Cell($total_w,4,'ALL SOCIAL WORKERS',$border2,1,'C');
		}


		$this->Ln(2);

		$this->Cell(5,5);
		$this->SetFont('Arial','',10);
		if ($this->fromdate==$this->todate)
				$text = "For ".date("F j, Y",strtotime($this->fromdate));
		else
				$text = "From ".date("F j, Y",strtotime($this->fromdate))." To ".date("F j, Y",strtotime($this->todate));

		$this->Cell($total_w,4,$text,$border2,1,'C');
		$this->Ln(5);

		# Print table header
		#echo "count = ".$this->_count;
		/*$this->Cell(30,4,'No. of Records : ',$border2,0,'L');
		$this->SetFont('Arial','B',10);
		$this->Cell(20,4,$this->_count,$border2,1,'L');

		$this->SetFont('ARIAL','B',8);
		#if ($this->colored) $this->SetFillColor(0xED);
		if ($this->colored) $this->SetFillColor(255);
		$this->SetTextColor(0);
		$row=6;
		$this->Cell($this->ColumnWidth[0],$row,'',1,0,'C',1);
		$this->Cell($this->ColumnWidth[1],$row,'GRANT DATE',1,0,'C',1);
		$this->Cell($this->ColumnWidth[2],$row,'MSS NO.',1,0,'C',1);
		$this->Cell($this->ColumnWidth[3],$row,'PATIENT\'S NAME',1,0,'C',1);
		$this->Cell($this->ColumnWidth[4],$row,'HRN',1,0,'C',1);
		$this->Cell($this->ColumnWidth[5],$row,'ADDRESS',1,0,'C',1);
		$this->Cell($this->ColumnWidth[6],$row,"BIRTHDATE",1,0,'C',1);
		$this->Cell($this->ColumnWidth[7],$row,'AGE',1,0,'C',1);
		$this->Cell($this->ColumnWidth[8],$row,'SEX',1,0,'C',1);
		$this->Cell($this->ColumnWidth[9],$row,'CIVIL STATUS',1,0,'C',1);
		$this->Cell($this->ColumnWidth[10],$row,'RELIGION',1,0,'C',1);
		$this->Cell($this->ColumnWidth[11],$row,'CLASSIFICATION',1,0,'C',1);  */
		$this->Ln();
	}

	function Footer()
	{
		$this->SetY(-7);
		$this->SetFont('Arial','I',8);
		$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:s A"),0,0,'R');
	}

	function BeforeRow() {
		$this->FONTSIZE = 10;
		if ($this->colored) {
			if (($this->ROWNUM%2)>0)
				#$this->FILLCOLOR=array(0xee, 0xef, 0xf4);
				$this->FILLCOLOR=array(255,255,255);
			else
				$this->FILLCOLOR=array(255,255,255);
			$this->DRAWCOLOR = array(0xDD,0xDD,0xDD);
		}
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
				#$this->RENDERCELL->FillColor=array(0xee, 0xef, 0xf4);
				$this->RENDERCELL->FillColor=array(255,255,255);
			else
				$this->RENDERCELL->FillColor=array(255,255,255);
		}
	}

	function AfterData() {
		global $db;

		if (!$this->_count) {
			$this->SetFont('Arial','B',10);
			$this->SetFillColor(255);
			$this->SetTextColor(0);
			$this->Cell(287, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
		}

		$cols = array();
	}

	function FetchData($class, $encoder, $fromdate, $todate) {
		global $db;

//------------------------QUERIES--------------
		#OUTPATIENTS
		$sql_2 = "SELECT DISTINCT SQL_CALC_FOUND_ROWS s.mss_no,
							CONCAT(IF (trim(p.name_last) IS NULL,' ',trim(p.name_last)),', ',
								IF(trim(p.name_first) IS NULL ,' ',trim(p.name_first)),' ',
								IF(trim(p.name_middle) IS NULL,' ',trim(p.name_middle))) AS patient_name,
							p.date_birth,
							IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age) AS age,
							p.sex,p.pid,p.religion,
							p.street_name, sb.brgy_name, sm.mun_name, sp.prov_name,
						r.religion_name, p.civil_status,
						SUBSTRING(MAX(CONCAT(grant_dte,cgp.discountid)),20) AS discountid,
						SUBSTRING(MAX(CONCAT(grant_dte,cgp.discount)),20) AS discount,
						SUBSTRING(MAX(CONCAT(grant_dte,grant_dte)),20) AS grant_dte,
						SUBSTRING(MAX(CONCAT(grant_dte,sw_nr)),20) AS sw_nr,
						fn_get_personell_name(SUBSTRING(MAX(CONCAT(grant_dte,sw_nr)),20)) AS social_worker,
						SUBSTRING(MAX(CONCAT(grant_dte,other_name)),20) AS other_name,
						SUBSTRING(MAX(CONCAT(grant_dte,id_number)),20) AS id_number

						FROM care_person AS p
						INNER JOIN seg_charity_grants_pid AS cgp ON cgp.pid=p.pid
						INNER JOIN seg_social_patient AS s ON s.pid=cgp.pid
						LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=p.brgy_nr
						LEFT JOIN seg_municity AS sm ON sm.mun_nr=p.mun_nr
						LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
						LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
						LEFT JOIN seg_religion AS r ON r.religion_nr=p.religion

						WHERE (DATE(cgp.grant_dte) BETWEEN '".$fromdate."' AND '".$todate."')
						AND cgp.discountid NOT IN ('LINGAP')
						GROUP BY cgp.pid
						ORDER BY (SUBSTRING(MAX(CONCAT(grant_dte,sw_nr)),20)), p.name_last, p.name_first, p.name_middle";
		 $result_2=$db->Execute($sql_2);
		 if ($result_2) {
				$this->_count_outpatient = $result_2->RecordCount();
		 }

			#INPATIENTS
		$sql_1 = "SELECT DISTINCT s.mss_no,name_last,name_first,name_middle,
							CONCAT(IF (trim(p.name_last) IS NULL,'',trim(p.name_last)),', ',
								IF(trim(p.name_first) IS NULL ,'',trim(p.name_first)),' ',
								IF(trim(p.name_middle) IS NULL,'',trim(p.name_middle))) AS patient_name,
							p.date_birth,
							IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age) AS age,
							p.sex,p.pid,p.religion,
							p.street_name, sb.brgy_name, sm.mun_name, sp.prov_name,
						r.religion_name, p.civil_status,
						SUBSTRING(MAX(CONCAT(grant_dte,cgp.discountid)),20) AS discountid,
						SUBSTRING(MAX(CONCAT(grant_dte,cgp.discount)),20) AS discount,
						d.parentid,
						SUBSTRING(MAX(CONCAT(grant_dte,grant_dte)),20) AS grant_dte,
						SUBSTRING(MAX(CONCAT(grant_dte,sw_nr)),20) AS sw_nr,
						fn_get_personell_name(SUBSTRING(MAX(CONCAT(grant_dte,sw_nr)),20)) AS social_worker,
						SUBSTRING(MAX(CONCAT(grant_dte,other_name)),20) AS other_name,
						SUBSTRING(MAX(CONCAT(grant_dte,id_number)),20) AS id_number

						FROM seg_social_patient AS s
						INNER JOIN care_encounter AS e ON e.pid=e.encounter_nr
						INNER JOIN seg_charity_grants AS cgp ON cgp.encounter_nr=e.encounter_nr
						INNER JOIN seg_discount AS d ON d.discountid=cgp.discountid
						INNER JOIN care_person AS p ON p.pid=s.pid
						LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=p.brgy_nr
						LEFT JOIN seg_municity AS sm ON sm.mun_nr=p.mun_nr
						LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
						LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
						LEFT JOIN seg_religion AS r ON r.religion_nr=p.religion

						WHERE (DATE(cgp.grant_dte) BETWEEN '".$fromdate."' AND '".$todate."')
						AND cgp.discountid NOT IN ('LINGAP')
						GROUP BY cgp.encounter_nr;
						ORDER BY name_last, name_first, name_middle";
		 $result_1=$db->Execute($sql_1);
		 if($result_1){
			$this->_count_inpatient = $result_1->RecordCount();
		 }

//----------------------------------
		$this->SetFont('Arial', 'B', 9);
		$this->Cell(30,4,'No. of Records : ',$border2,0,'L');
		$xrec = $this->GetX();
		$yrec = $this->GetY();

		$this->Cell(20,4,$this->_count_inpatient + $this->_count_outpatient,$border2,1,'L');
		$this->Ln();
		#if ($this->colored) $this->SetFillColor(0xED);
		if ($this->colored) $this->SetFillColor(255);
		$this->SetTextColor(0);
		$row=6;

		$this->Ln();
		$this->SetFont('Arial','B',8);
		$this->Cell(50, $row, "OUTPATIENT", 0,1, 'L');
		$this->Cell($this->ColumnWidth[0],$row,'',1,0,'C',1);
		$this->Cell($this->ColumnWidth[1],$row,'GRANT DATE',1,0,'C',1);
		$this->Cell($this->ColumnWidth[2],$row,'MSS NO.',1,0,'C',1);
		$this->Cell($this->ColumnWidth[3],$row,'PATIENT\'S NAME',1,0,'C',1);
		$this->Cell($this->ColumnWidth[4],$row,'HRN',1,0,'C',1);
		$this->Cell($this->ColumnWidth[5],$row,'ADDRESS',1,0,'C',1);
		$this->Cell($this->ColumnWidth[6],$row,"BIRTHDATE",1,0,'C',1);
		$this->Cell($this->ColumnWidth[7],$row,'AGE',1,0,'C',1);
		$this->Cell($this->ColumnWidth[8],$row,'SEX',1,0,'C',1);
		$this->Cell($this->ColumnWidth[9],$row,'CIVIL STATUS',1,0,'C',1);
		$this->Cell($this->ColumnWidth[10],$row,'RELIGION',1,0,'C',1);
		$this->Cell($this->ColumnWidth[11],$row,'CLASSIFICATION',1,0,'C',1);
		$this->Ln();

		#echo "s = ".$sql;

		if ($result_2) {
			//$this->_count_outpatient = $result_2->RecordCount();
			//echo "outpatient= ".$this->_count_outpatient;
			$this->Data=array();
			$i=1;
			$total_gross_amount = 0;
			while ($row2=$result_2->FetchRow()) {
				#if ($row["sex"]=='f')
					#$sex = "FEMALE";
				#elseif ($row["sex"]=='m')
					#$sex = "MALE";

				if ($row2['street_name']){
					if ($row2['brgy_name']!="NOT PROVIDED")
						$street_name = $row2['street_name'].", ";
					else
						$street_name = $row2['street_name'].", ";
				}else
					$street_name = "";

					if ((!($row2['brgy_name'])) || ($row2['brgy_name']=="NOT PROVIDED"))
						$brgy_name = "";
					else
						$brgy_name  = $row2['brgy_name'].", ";

					if ((!($row2['mun_name'])) || ($row2['mun_name']=="NOT PROVIDED"))
						$mun_name = "";
					else{
						if ($row2['brgy_name'])
							$mun_name = $row2['mun_name'];
						else
							$mun_name = $row2['mun_name'];
					}

					if ((!($row2['prov_name'])) || ($row2['prov_name']=="NOT PROVIDED"))
						$prov_name = "";
					else
						$prov_name = $row2['prov_name'];

					if(stristr(trim($row2['mun_name']), 'city') === FALSE){
						if ((!empty($row2['mun_name']))&&(!empty($row2['prov_name']))){
							if ($row2['prov_name']!="NOT PROVIDED")
								$prov_name = ", ".trim($row2['prov_name']);
							else
								$prov_name = "";
						}else{
							#$province = trim($prov_name);
							$prov_name = "";
						}
					}else
						$prov_name = " ";

					$address = $street_name.$brgy_name.$mun_name.$prov_name;

				if ($row2["religion"]!= 1)
					$religion = $row2["religion_name"];
				else
					$religion = "";

				$sqld = "SELECT * FROM seg_discount WHERE discountid='".$row2['discountid']."'";
				$rsD2 = $db->Execute($sqld);
				$rowD2 = $rsD2->FetchRow();

				if (empty($rowD2['parentid']))
					$discountid = $row2['discountid'];
				else
					$discountid = $rowD2['parentid']." (".$row2['discountid'].")";
				/*
				$this->Data[]=array(
						$i,
						date("m/d/Y",strtotime($row["grant_dte"])),
						$row["mss_no"],
						$row["patient_name"],
						$row["pid"],
						$address,
						date("m/d/Y",strtotime($row["date_birth"])),
						$row["age"],
						mb_strtoupper($row["sex"]),
						ucfirst($row["civil_status"]),
						ucfirst($religion),
						mb_strtoupper($discountid)

				);   */
				$mul = $this->NbLines($this->ColumnWidth[5], $address);
				$this->Cell($this->ColumnWidth[0], $row*$mul, $i, 1, 0, $this->Alignment[0]);
				$this->Cell($this->ColumnWidth[1], $row*$mul, date("m/d/Y",strtotime($row2["grant_dte"])), 1, 0, $this->Alignment[1]);
				$this->Cell($this->ColumnWidth[2], $row*$mul, $row2["mss_no"], 1, 0, $this->Alignment[2]);
				$this->Cell($this->ColumnWidth[3], $row*$mul, strtoupper($row2["patient_name"]), 1, 0, $this->Alignment[3]);
				$this->Cell($this->ColumnWidth[4], $row*$mul, $row2["pid"], 1, 0, $this->Alignment[4]);
				$x = $this->GetX();
				$y = $this->GetY();
				$this->MultiCell($this->ColumnWidth[5], $row, $address, 1, $this->Alignment[5]);
				//$this->Cell($this->ColumnWidth[5], $row, $address, 1, 0, $this->Alignment[5]);
				$this->SetXY($x+$this->ColumnWidth[5], $y);
				$this->Cell($this->ColumnWidth[6], $row*$mul, date("m/d/Y",strtotime($row2["date_birth"])), 1, 0, $this->Alignment[6]);
				$this->Cell($this->ColumnWidth[7], $row*$mul, $row2["age"], 1, 0, $this->Alignment[7]);
				$this->Cell($this->ColumnWidth[8], $row*$mul, mb_strtoupper($row2["sex"]), 1, 0, $this->Alignment[8]);
				$this->Cell($this->ColumnWidth[9], $row*$mul, ucfirst($row2["civil_status"]), 1, 0, $this->Alignment[9]);
				$this->Cell($this->ColumnWidth[10], $row*$mul, ucfirst($religion), 1, 0, $this->Alignment[10]);
				$this->Cell($this->ColumnWidth[11], $row*$mul, mb_strtoupper($discountid), 1, 0, $this->Alignment[11]);
				$this->Ln();
				//$mul = $this->NbLines($this->ColumnWidth[5], $address);
				//echo $mul;

				//$this->Row(1, date("m/d/Y",strtotime($row2["grant_dte"])), $row2["mss_no"], $row2["patient_name"],$row2["pid"],$address,date("m/d/Y",strtotime($row2["date_birth"])),$row2["age"],mb_strtoupper($row2["sex"]),ucfirst($row2["civil_status"]),ucfirst($religion),mb_strtoupper($discountid));
				$i++;
			}
		}
		else {
			$this->Cell($this->total_width, $this->RowHeight, "No records found for this table...", 1, 1, 'L', 1);
		}

//------------------------
		$this->Ln();
		$this->Cell(50, $row, "INPATIENT / ER", 0,1, 'L');
		$this->SetFont('ARIAL','B',8);
		$this->Cell($this->ColumnWidth[0],$row,'',1,0,'C',1);
		$this->Cell($this->ColumnWidth[1],$row,'GRANT DATE',1,0,'C',1);
		$this->Cell($this->ColumnWidth[2],$row,'MSS NO.',1,0,'C',1);
		$this->Cell($this->ColumnWidth[3],$row,'PATIENT\'S NAME',1,0,'C',1);
		$this->Cell($this->ColumnWidth[4],$row,'HRN',1,0,'C',1);
		$this->Cell($this->ColumnWidth[5],$row,'ADDRESS',1,0,'C',1);
		$this->Cell($this->ColumnWidth[6],$row,"BIRTHDATE",1,0,'C',1);
		$this->Cell($this->ColumnWidth[7],$row,'AGE',1,0,'C',1);
		$this->Cell($this->ColumnWidth[8],$row,'SEX',1,0,'C',1);
		$this->Cell($this->ColumnWidth[9],$row,'CIVIL STATUS',1,0,'C',1);
		$this->Cell($this->ColumnWidth[10],$row,'RELIGION',1,0,'C',1);
		$this->Cell($this->ColumnWidth[11],$row,'CLASSIFICATION',1,0,'C',1);
		$this->Ln();

		 if($result_1){
			//$this->_count_inpatient = $result_1->RecordCount();
			#echo "inpatient= ".$this->_count_inpatient;
			$i=1;
			$total_gross_amount = 0;
			$this->SetFont('Arial', '', 8);
				while ($row=$result_1->FetchRow()) {
				#if ($row["sex"]=='f')
					#$sex = "FEMALE";
				#elseif ($row["sex"]=='m')
					#$sex = "MALE";

				if ($row['street_name']){
					if ($row['brgy_name']!="NOT PROVIDED")
						$street_name = $row['street_name'].", ";
					else
						$street_name = $row['street_name'].", ";
				}else
					$street_name = "";

					if ((!($row['brgy_name'])) || ($row['brgy_name']=="NOT PROVIDED"))
						$brgy_name = "";
					else
						$brgy_name  = $row['brgy_name'].", ";

					if ((!($row['mun_name'])) || ($row['mun_name']=="NOT PROVIDED"))
						$mun_name = "";
					else{
						if ($row['brgy_name'])
							$mun_name = $row['mun_name'];
						else
							$mun_name = $row['mun_name'];
					}

					if ((!($row['prov_name'])) || ($row['prov_name']=="NOT PROVIDED"))
						$prov_name = "";
					else
						$prov_name = $row['prov_name'];

					if(stristr(trim($row['mun_name']), 'city') === FALSE){
						if ((!empty($row['mun_name']))&&(!empty($row['prov_name']))){
							if ($row['prov_name']!="NOT PROVIDED")
								$prov_name = ", ".trim($row['prov_name']);
							else
								$prov_name = "";
						}else{
							#$province = trim($prov_name);
							$prov_name = "";
						}
					}else
						$prov_name = " ";

					$address = $street_name.$brgy_name.$mun_name.$prov_name;

				if ($row["religion"]!= 1)
					$religion = $row["religion_name"];
				else
					$religion = "";

				$sqld = "SELECT * FROM seg_discount WHERE discountid='".$row['discountid']."'";
				$rsD = $db->Execute($sqld);
				$rowD = $rsD->FetchRow();

				if (empty($rowD['parentid']))
					$discountid = $row['discountid'];
				else
					$discountid = $rowD['parentid']." (".$row['discountid'].")";
				/*
				$this->Data[]=array(
						$i,
						date("m/d/Y",strtotime($row["grant_dte"])),
						$row["mss_no"],
						$row["patient_name"],
						$row["pid"],
						$address,
						date("m/d/Y",strtotime($row["date_birth"])),
						$row["age"],
						mb_strtoupper($row["sex"]),
						ucfirst($row["civil_status"]),
						ucfirst($religion),
						mb_strtoupper($discountid)

				);*/
				$mul = $this->NbLines($this->ColumnWidth[5], $address);
				$this->Cell($this->ColumnWidth[0], $row*$mul, $i, 1, 0, $this->Alignment[0]);
				$this->Cell($this->ColumnWidth[1], $row*$mul, date("m/d/Y",strtotime($row["grant_dte"])), 1, 0, $this->Alignment[1]);
				$this->Cell($this->ColumnWidth[2], $row*$mul, $row["mss_no"], 1, 0, $this->Alignment[2]);
				$this->Cell($this->ColumnWidth[3], $row*$mul, strtoupper($row["patient_name"]), 1, 0, $this->Alignment[3]);
				$this->Cell($this->ColumnWidth[4], $row*$mul, $row["pid"], 1, 0, $this->Alignment[4]);
				$x = $this->GetX();
				$y = $this->GetY();
				$this->MultiCell($this->ColumnWidth[5], $row, $address, 1, $this->Alignment[5]);
				//$this->Cell($this->ColumnWidth[5], $row, $address, 1, 0, $this->Alignment[5]);
				$this->SetXY($x+$this->ColumnWidth[5], $y);
				$this->Cell($this->ColumnWidth[6], $row*$mul, date("m/d/Y",strtotime($row["date_birth"])), 1, 0, $this->Alignment[6]);
				$this->Cell($this->ColumnWidth[7], $row*$mul, $row2["age"], 1, 0, $this->Alignment[7]);
				$this->Cell($this->ColumnWidth[8], $row*$mul, mb_strtoupper($row["sex"]), 1, 0, $this->Alignment[8]);
				$this->Cell($this->ColumnWidth[9], $row*$mul, ucfirst($row["civil_status"]), 1, 0, $this->Alignment[9]);
				$this->Cell($this->ColumnWidth[10], $row*$mul, ucfirst($religion), 1, 0, $this->Alignment[10]);
				$this->Cell($this->ColumnWidth[11], $row*$mul, mb_strtoupper($discountid), 1, 0, $this->Alignment[11]);
				$this->Ln();
				$i++;
			}
		 }else {
			$this->Cell($this->total_width, $this->RowHeight, "No records found for this table...", 1, 1, 'L', 1);
		}

		$this->Ln();
//---------------------------

		if($this->_count_inpatient && $this->_count_outpatient){
			$record = $this->_count_inpatient + $this->_count_outpatient;
		}elseif(!$this->_count_inpatient){
			$record = $this->_count_outpatient;
		}elseif(!$this->_count_outpatient){
			$record = $this->_count_inpatient;
		}
		//$this->SetXY($xrec, $yrec);
		//$this->Cell(20,4,$record,$border2,1,'L');
	}

	//-------------------------------------
	 function SetWidths($w)
	 {
			//Set the array of column widths
			$this->widths=$w;
	 }

	 function SetAligns($a)
	 {
			//Set the array of column alignments
			$this->aligns=$a;
	 }

	 function Row($data)
	 {
		$row = 4;
			//Calculate the height of the row
			$nb=0;
			for($i=0;$i<count($data);$i++)
					$nb=max($nb,$this->NbLines($this->ColumnWidth[$i],$data[$i]));
					$nb2=$this->NbLines($this->ColumnWidth[0],$data[0]);
					$nb3=$this->NbLines($this->ColumnWidth[11],$data[11]);
					if($nb2>$nb3){
						$nbdiff = $nb2 - $nb3;
						 $nbdiff = $nbdiff*$row;
						k == 1;
					}
					else if($nb3>$nb2){
						$nbdiff = $nb3 - $nb2;
						 $nbdiff = $nbdiff*$row;
						k==0;
					}
					else{
						$nbdiff = 0;
					}

					//$nb3=max($nb,$this->NbLines($this->widths[0],$data[0]));
					//print_r($nb2, $nb3);

					//$nb = $nb*2;
					//print_r($nb);
			$h=$row*$nb;
			//Issue a page break first if needed
			$this->CheckPageBreak($h);
			//Draw the cells of the row

			for($i=0;$i<count($data);$i++)
			{
					$w=$this->ColumnWidth[$i];
					$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
					//$a = isset($this->Alignment[$i]) ? $this->Alignment[$i] : 'L';
					//Save the current position

					$x=$this->GetX();
					$y=$this->GetY();
					//Draw the border

							$length = $this->GetStringWidth($data[$i]);
							if($length < $this->ColumnWidth[$i]){
								//$this->Cell($w, $h, $data[$i],1,0,'L');
								$this->Cell($w, $h, $data[$i], 1, 0, $this->Alignment[$i]);
							}
							else{
								$nbrow = 3;
								// $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
								//$this->MultiCell($w, $row,$data[$i],1,'L');
								$this->MultiCell($w, $row, $data[$i], 1,$this->Alignment[$i]);

								//$this->MultiCell($length, $row,$data[$i],1,'L');

							}

					//Put the position to the right of the cell
					$this->SetXY($x+$w,$y);
			}
			//Go to the next line
			$this->Ln($h);
		}

		function CheckPageBreak($h) {
				//If the height h would cause an overflow, add a new page immediately
				if($this->GetY()+$h>$this->PageBreakTrigger)
						$this->AddPage($this->CurOrientation);
		}

		function NbLines($w,$txt) {
				//Computes the number of lines a MultiCell of width w will take
				$cw=&$this->CurrentFont['cw'];
				if($w==0)
						$w=$this->w-$this->rMargin-$this->x;
				$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
				$s=str_replace("\r",'',$txt);
				$nb=strlen($s);
				if($nb>0 and $s[$nb-1]=="\n")
						$nb--;
				$sep=-1;
				$i=0;
				$j=0;
				$l=0;
				$nl=1;
				while($i<$nb)
				{
						$c=$s[$i];
						if($c=="\n")
						{
								$i++;
								$sep=-1;
								$j=$i;
								$l=0;
								$nl++;
								continue;
						}
						if($c==' ')
								$sep=$i;
						$l+=$cw[$c];
						if($l>$wmax)
						{
								if($sep==-1)
								{
										if($i==$j)
												$i++;
								}
								else
										$i=$sep+1;
								$sep=-1;
								$j=$i;
								$l=0;
								$nl++;
						}
						else
								$i++;
				}
				return $nl;
		}
		//-----------------------

}

$fromdate = $_GET['from'];
$todate = $_GET['to'];
$encoder = $_GET['encoder'];
$class = $_GET['class'];

#echo "fromdate = ".$fromdate;
#echo "<br>todate = ".$todate;
#echo "<br>encoder_name = ".$encoder_name;
#echo "<br>class = ".$class;

$iss = new Socserv_Patients($class, $encoder, $fromdate, $todate);
$iss->Open();
$iss->AliasNbPages();
$iss->AddPage();
$iss->FetchData($class, $encoder, $fromdate, $todate);
#$iss->Report();
$iss->Output();
?>