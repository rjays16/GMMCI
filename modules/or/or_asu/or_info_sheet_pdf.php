<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/billing/class_ops.php'); //load the SegOps class
require_once($root_path.'include/care_api_classes/class_department.php'); //load the department class
require_once($root_path.'include/care_api_classes/class_person.php');
require_once($root_path . 'include/care_api_classes/class_ward.php'); //load the ward class
require_once($root_path.'include/care_api_classes/class_personell.php');

/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

		class OR_ASU_info_sheet extends RepGen {

		 var $pid;
		 var $encounter_nr;
		 var $discount_id;
		 var $date;
		 var $or_refno;

		function OR_ASU_info_sheet($refno='', $pid)
		{
				global $db;
				$this->RepGen("INFORMATION SHEET");
				$this->colored = TRUE;
				#$this->ColumnWidth = array(36,20,60,50,15,20);
				$this->RowHeight = 6;
				#$this->Alignment = array('L','L','L','L','R','C');
				$this->PageOrientation = "P";
				if ($this->colored)    $this->SetDrawColor(0xDD);
				$this->or_refno = $refno;
				$this->pid = $pid;
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
					$row['hosp_name']    = "SOUTHERN PHILIPPINES MEDICAL CENTER";
					$row['hosp_addr1']   = "Bajada, Davao City";
				}

				$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',40,4,12);
				$this->SetFont("Arial","I","9");
				$total_w = 165;
				$this->Cell(17,4);
				$this->Cell($total_w,4, $row['hosp_country'],$border2,1,'C');
				$this->Cell(17,4);
				$this->Cell($total_w,4, $row['hosp_agency'],$border2,1,'C');
				$this->Ln(2);
				$this->SetFont("Arial","B","10");
				$this->Cell(17,4);
				$this->Cell($total_w,4, $row['hosp_name'],$border2,1,'C');
				$this->SetFont("Arial","","9");
				//$this->Cell(17,4);
				//$this->Cell($total_w,4, $row['hosp_addr1'],$border2,1,'C');
				//$this->Ln(4);
				$this->SetFont('Arial','B',12);
				$this->Cell(17,5);
				$this->Cell($total_w,4,"OUTPATIENT PREVENTIVE HEALTH CARE CENTER",$border2,1,'C');
				$this->Cell(17, 5);
				$this->Cell($total_w, 4, "AMBULATORY SURGERY UNIT", $border2, 1, 'C');
				$this->Ln(8);
				$this->Cell(17, 5);
				$this->Cell($total_w, 4, "INFORMATION SHEET", $border2, 1, 'C');
				#$this->SetFont('Arial','B',9);
				#$this->Cell(17,4);
				$this->Ln(10);
				$this->SetTextColor(0);

				$this->PrintData();
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

				/*if (!$this->_count) {
						$this->SetFont('Arial','B',9);
						$this->SetFillColor(255);
						$this->SetTextColor(0);
						$this->Cell(201, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
				}

				$cols = array(); */
		}

		function PrintData()
		{
			global $db;
			$seg_ops = new SegOps();
			$seg_department = new Department();
			$seg_personell = new Personell();
			$nr = $seg_ops->getOpRequestNrByRefNo($this->or_refno);
			if ($seg_ops->encOpsNrHasOpsServ($nr)) {
				#echo "churvaloo!";
				$basic_info = $seg_ops->getAllEncounterOpsServiceInfo($nr);
				$or_main_info = $seg_ops->get_or_main_basic_info($this->or_refno);
			}
			//print_r($basic_info);
			//	echo "pid= ".$this->pid;
			/*
			//print case no
			$sql = "select os.encounter_nr, os.pid from seg_ops_serv as os join seg_or_main as om on os.refno=om.ceo_refno".
			" and om.ceo_refno=".$db->qstr($_GET['refno']);
			$result=$db->Execute($sql);
			$row=$result->FetchRow();
			$this->SetFont('Arial','B',12);
			$this->Cell(20, 4, "Case no. ", "", 0, 'L');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->SetFont('Arial','',12);
			$this->Cell(2, 4, $row['encounter_nr'], "", 0, '');
			$this->Line(30, 45, 70, 45);

			//print date of operation
			$date_of_operation = $seg_ops->get_date_of_operation($this->or_refno);
			$this->Cell(100, 4);
			$this->SetFont('Arial','B',12);
			$this->Cell(40, 4, "Date of Operation ", "", 0, 'L');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->SetFont('Arial','',12);
			$this->Cell(2, 4, $date_of_operation, "", 1, '');
			$this->Line(175, 45, 210, 45);
			$this->Ln();    */

			//print patient info
			//$seg_person = new Person($or_main_info['pid']);
			$seg_person = new Person($this->pid);
			$person_info = $seg_person->getAllInfoArray();
			$person_gender = (strnatcasecmp($person_info['sex'], 'm') == 0) ? 'Male' : 'Female';
			$person_age = (int)$seg_person->getAge(date('m/d/Y', strtotime($person_info['date_birth'])));
			$person_age = is_int($person_age) ? $person_age : '-Not specified-';
			$person_name = $person_info['name_first']." ".$person_info['name_last'];
			$person_address = implode(", ",array_filter(array($person_info['street_name'], $person_info["brgy_name"], $person_info["mun_name"])));
			#print_r($person_info);

			$this->SetFont('Arial','B',12);
			$this->Cell(16, 4, "NAME:",0, 0, 'L');
			$x = $this->GetX();
			$y = $this->GetY();
			//$this->Cell(1, 4, ":", "", 0, 'R'); //space
			$this->SetFont('Arial','',12);
			$this->Cell(75, 4, strtoupper($person_name), "", 0, '');
			//$this->Line(47, 53, 88, 53);
			$this->Line($x, $y+3.5, $x+75, $y+3.5);
			//$this->Cell($this->GetStringWidth($person_name)+10, 4);
			$this->SetFont('Arial','B',12);
			$this->Cell(12, 4, "AGE:",0, 0, 'L');
			$x = $this->GetX();
			$y = $this->GetY();
			$this->SetFont('Arial','',12);
			$this->Cell(10, 4, $person_age, "", 0, '');
			$this->Line($x, $y+3.5, $x+10, $y+3.5);
			$this->SetFont('Arial','B',12);
			$this->Cell(12, 4, "SEX:", "", 0, 'L');
			$x = $this->GetX();
			$y = $this->GetY();
			$this->SetFont('Arial','',12);
			$this->Cell(17, 4, $person_gender, "", 0, '');
			$this->Line($x, $y+3.5, $x+17, $y+3.5);
			$this->SetFont('Arial', 'B', 12);
			$this->Cell(31, 4, "CIVIL STATUS:", "", 0, 'L');
			$x = $this->GetX();
			$y = $this->GetY();
			$this->SetFont('Arial', '', 12);
			$this->Cell(25, 4, $person_info['civil_status'], "", 0, 'L');
			$this->Line($x, $y+3.5, $x+25, $y+3.5);
			$this->Ln(8);
			$this->SetFont('Arial', 'B', 12);
			$this->Cell(24, 4, "ADDRESS:", "", 0, 'L');
			$x = $this->GetX();
			$y = $this->GetY();
			$this->SetFont('Arial', '', 10);
			$this->Cell(65, 4, $person_address, "", 0, 'L');
			$this->Line($x, $y+3.5, $x+65, $y+3.5);
			$this->SetFont('Arial', 'B', 12);
			$this->Cell(19, 4, "B-DATE:", "", 0, 'L');
			$x = $this->GetX();
			$y = $this->GetY();
			$this->SetFont('Arial', '', 11);
			$this->Cell(35, 4, date('F j, Y', strtotime($person_info['date_birth'])));
			$this->Line($x, $y+3.5, $x+35, $y+3.5);
			$this->SetFont('Arial', 'B', 12);
			$this->Cell(25, 4, "B-PLACE:", "", 0, 'L');
			$x = $this->GetX();
			$y = $this->GetY();
			$this->SetFont('Arial', '', 11);
			$this->Cell(30, 4, $person_info['place_birth'], "", 0, 'L');
			$this->Line($x, $y+3.5, $x+30, $y+3.5);
			$this->Ln(12);

			//query for chief complaint, diagnosis, plan operation
			$query1 = "SELECT encounter_nr, or_procedure, dr_nr FROM seg_or_main WHERE ceo_refno='$this->or_refno'";
			#echo $query1;
			$result1=$db->Execute($query1);
			$row1=$result1->FetchRow();
			$this->encounter_nr = $row1['encounter_nr'];
			#echo $this->encounter_nr;

			$query2 = "SELECT er_opd_diagnosis, chief_complaint FROM care_encounter WHERE encounter_nr='$this->encounter_nr'";
			$result2=$db->Execute($query2);
			$row2=$result2->FetchRow();

			//chief complaint
			$this->SetFont('Arial', 'B', 12);
			$this->Cell(35, 4, "CHIEF COMPLAINT:", "", 1, 'L');
			$this->SetFont('Arial', '', 12);
			$this->MultiCell(198, 5, $row2['chief_complaint'], "", 'L'); //put chief complaint here

			$this->SetY(120);
			$this->SetFont('Arial', 'B', 12);
			$this->Cell(35, 4, "DIAGNOSIS:", "", 1, 'L');
			$this->SetFont('Arial', '', 12);
			$this->MultiCell(198, 5, $row2['er_opd_diagnosis'], "", 'L'); //put diagnosis here

			$this->SetY(180);
			$this->SetFont('Arial', 'B', 12);
			$this->Cell(35, 4, "PLAN/OPERATION:", "", 1, 'L');
			$this->SetFont('Arial', '', 12);
			$this->MultiCell(198, 5, $row1['or_procedure'], "", 'L'); //put plan/operation here

			//attending physician
			$doctor = $seg_personell->get_Person_name($row1['dr_nr']);
			//print_r($doctor);
			$this->SetXY(133, 215);
			$this->SetFont('Arial', '', 12);
			$this->Cell(65, 4, $doctor['dr_name'].", M.D.","", 1, "L");
			$this->SetX(133);
			$x = $this->GetX();
			$y = $this->GetY();
			$this->Line($x, $y-0.5, $x+65, $y-0.5);
			$this->Cell(65, 4, "ATTENDING PHYSICIAN", "", 1, "C");


			/*$anesthetic_intra_operative = $post_op_details['intra_operative'];
			$anesthetic_post_operative = $post_op_details['post_operative'];
			$anesthetic_patient_status = $post_op_details['or_status'];
			$or_technique = $post_op_details['or_technique'];
			$needle_count = $post_op_details['needle_count'];
			$instrument_count = $post_op_details['instrument_count'];
			$anesthesia_array = $seg_ops->get_or_main_anesthesia_as_array($or_main_info['or_main_refno']);
			$anesthesia = current($anesthesia_array);*/
		}
}
$rep =& new OR_ASU_info_sheet($_GET['refno'], $_GET['pid']);
$rep->AliasNbPages();
$rep->Report();

?>