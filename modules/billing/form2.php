<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

define(INFO_INDENT, 10);

class PhilhealthForm2 extends FPDF {
	var $encounter_nr = '';
	var $hcare_id = 0;
	var $fontfamily_label = "Arial";
	var $fontfamily_answer = "Arial";
	var $fontstyle_label = "";
	var $fontstyle_answer = 'B';
	var $fontstyle_label2 = '';
	var $fontsize_label = 7.5;
	var $fontsize_answer = 11;
	var $fontsize_philhealth = 18;
	var $fontsize_claim = 12;
	var $fontsize_label2 = 10;
	var $fontsize_servperform = 8.5;
	var $fontsize_revised = 8;
	var $fontsize_label3 = 8;
	var $fontsize_answer2 = 9;
	var $fontsize_note = 7.5;
	var $fontsize_philuse = 7.5;
	var $fontsize_table_note = 7;
	var $fontsize_hospname = 14;

	var $fontfamily_admdiagnosis = "Arial";
	var $fontstyle_admdiagnosis = 'B';
	var $fontsize_admdiagnosis = 9;

	var $AdjustedRowHeight = 6;

	var $half = 100;
	var $third = 165;
	var $x1 = 5;
	var $y1 = 5;
	var $lastypos;
//    var $x1 = 8;
//    var $y1 = 8;
	var $tot_width = 201;

	var $auth_rep;            // Authorized representative of hospital.
	var $rep_capacity;        // Official capacity of authorized representative.

	var $hospnum;             // Hospital Number (HRN or PID) of patient.
	var $hospaccnum;          // Accreditation No. of Hospital

	var $meds_array;          // Array of class Medicine.
	var $confinement_array;   // Array of class Confinement.
	var $hospserv_array;      // Array of class HospServices.
	var $diagnosis_array;     // Array of class Diagnosis.
	var $healthperson_array;  // Array of class Health Personnel.
	var $surgeon_array;       // Array of class Surgeon.
	var $anesth_array;				// Array of Anaesthesiologists.
	var $lab_array;           // Array of class X-Ray/Laboratory Charges.
	var $sup_array;           // Array of object supplies
	var $others_array;        // Array of object other charges.
//    var $hospnum_array;       // Array of class HospitalNumber.
//    var $accnum_array;        // Array of class AccreditationNumber.

	var $total_rlo_charges  = 0;    // Total charges of x-ray, lab and others
	var $total_rlo_hospital = 0;    // Total hospital claims in x-ray, lab and others
	var $total_rlo_patient  = 0;    // Total patient claims in x-ray, lab and others

	var $x2;
	var $y2;
	var $boxwidth = 3;
	var $boxheight = 3;
//    var $boxheight = 4.5;
	var $RowHeight = 4;
//    var $RowHeight = 6;
	var $tablewidth = 201;
	var $tableheader = 8;
	var $h_cell = 2;
	var $h_multi = 3;
//    var $h_cell = 4;
//    var $h_multi = 5;
	var $diag_space = 153;
	var $serv_space = 75;

	var $bNoPageBreak = false;

	var $total_hosp_charge = 0;
	var $total_pf_charge   = 0;
	var $total_out_claim   = 0;

	var $form_part = '';

	function PhilhealthForm2() {
		$pg_array = array('215.9','330.2');
		$this->FPDF('P', 'mm', $pg_array);
		$this->SetDrawColor(0,0,0);
		$this->SetMargins(5,5,1);
		$this->SetAutoPageBreak(true, 0.25);

		$this->setToLongSize();

		$this->x1 = $this->lMargin;
		$this->y1 = $this->tMargin;
	}

	function setToLongSize() {
		$this->SetLeftMargin(8);
		$this->SetTopMargin(10);

		$this->fontsize_label = 7.5;
		$this->fontsize_answer = 11;
		$this->fontsize_philhealth = 18;
		$this->fontsize_claim = 12;
		$this->fontsize_label2 = 10;
		$this->fontsize_servperform = 8.5;
		$this->fontsize_revised = 8;
		$this->fontsize_label3 = 8;
		$this->fontsize_answer2 = 9;
		$this->fontsize_note = 7.5;
		$this->fontsize_philuse = 7.5;
		$this->fontsize_table_note = 7;
		$this->half = 100;
		$this->third = 165;
		$this->x1 = 5;
		$this->y1 = 5;
		$this->tot_width = 201;

		$this->boxwidth = 3;
		$this->boxheight = 4.5;
		$this->RowHeight = 4.5;
		$this->tablewidth = 201;
		$this->tableheader = 8;
		$this->h_cell = $this->RowHeight;
		$this->h_multi = $this->RowHeight;
		$this->diag_space = 153;
		$this->serv_space = 75;
	}

	function checkifmember(){
		global $db;
		$sql = "SELECT i.is_principal AS Member FROM care_person_insurance AS i
				LEFT JOIN care_encounter e ON e.pid = i.pid
				WHERE e.encounter_nr = $this->encounter_nr";
		$result = $db->Execute($sql);
		$row = $result->FetchRow();
		return $row['Member'];
	}

	function checkdependence(){
		global $db;

		$sql = "SELECT d.parent_pid AS Parent
				FROM seg_dependents AS d
				LEFT JOIN care_encounter AS e ON e.pid = d.dependent_pid
				where e.encounter_nr = $this->encounter_nr AND d.status = 'member'";

		if ($result = $db->Execute($sql)) {
			if ($row = $result->FetchRow())
				return $row['Parent'];
		}
		return false;
	}

	function addHead($name, $formnum, $revised, $note, $hcare, $cert, $acc){
		$this->Rect($this->x1, $this->y1-2, 160, 18);
		//Philhealth
		 $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_philhealth);
		$this->SetXY($this->x1, $this->y1);
		$length = $this->GetStringWidth($name);
		$this->Cell($length, $this->h_cell, $name);
		//Claim form 2
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_claim);
		$this->SetXY($this->x1,$this->y1+4);
		$length = $this->GetStringWidth($formnum);
		$this->Cell($length, $this->h_cell, $formnum);
		//Revised May 2000
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_revised);
		$this->SetXY($this->x1,$this->y1+8);
		$length = $this->GetStringWidth($revised);
		$this->Cell($length, $this->h_cell, $revised);
		//Note
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_note);
		$this->SetXY($this->x1, $this->y1+12);
		$length = $this->GetStringWidth($note);
		$this->Cell($length, $this->h_cell, $note);
		//Health Care
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_claim);
		$this->SetXY($this->x1+83, $this->y1);
		$length = $this->GetStringWidth($hcare);
		$this->Cell($length, $this->h_cell, $hcare);
		//Certification
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_claim);
		$this->SetXY($this->x1+70, $this->y1+4);
		$length = $this->GetStringWidth($cert);
		$this->Cell($length, $this->h_cell, $cert,0,0);
		//Accreditation
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label2);
		$this->SetXY($this->x1+70, $this->y1+8);
		$length = $this->GetStringWidth($acc);
//      $this->Cell($length, $this->h_cell, $acc);
		$this->Cell($length, $this->h_cell, " ");
		$l_pacc = $length +2;
//      if(!empty($this->accnum_array)){
//                foreach($this->accnum_array as $objaccnum){
//                    $this->SetX(($this->x1+70)+ $l_pacc);
//                    $length = $this->GetStringWidth($this->hospaccnum);
//                    $this->Cell($length, $this->h_cell, $this->hospaccnum, 0,0,'L');
//                }
//       }
	}

	function addDateReceived($date){
		$this->Rect($this->x1+160, $this->y1-2, 41, 18);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label2);
		$this->SetXY($this->x1+163, $this->y1);
		$length = $this->GetStringWidth($date);
		$this->Cell($length , $this->h_cell, $date,0,0);
	}

	function addPart1($title){
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label2);
		$this->SetXY($this->x1+33, $this->y1+17);
		$length = $this->GetStringWidth($title);
		$this->Cell($length, $this->h_cell, $title);
	}

	function addBillNum($billnum){
//     $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontfamily_label);
//     $this->SetXY($this->x1+160, $this->y1+17);
//     $length = $this->GetStringWidth($billnum);
//     $this->Cell($length, $this->h_cell, $billnum,0,1);
		$this->Ln();
	}

	function addHospitalInfo($hnum, $cat, $name, $add, $street, $city, $barangay, $prov, $zip){
	global $db;

	$ans="X";
	$noans = " ";
	$prim = "Primary";
	$sec = "Secondary";
	$ter = "Tertiary";
	$Amb = "Ambulatory";
	$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);

	$this->y1 = $this->GetY();

	$this->Rect($this->x1, $this->y1, 201, $this->RowHeight);
	$objInfo = new Hospital_Admin();
	if ($row = $objInfo->getAllHospitalInfo()) {
		$row['hosp_type'] = strtoupper($row['hosp_type']);
		$row['hosp_name']   = strtoupper($row['hosp_name']);
		$row['addr_no_street'] = strtoupper($row['addr_no_street']);
		$row['brgy_name'] = strtoupper($row['brgy_name']);
		$row['mun_name'] = strtoupper($row['mun_name']);
		$row['prov_name'] = strtoupper($row['prov_name']);
	}

	//Hospital Accreditation No. (per BPH format) ....
//    $this->SetXY($this->x1, $this->y1+21);
	$length = $this->GetStringWidth($hnum);
	$this->Cell($length, $this->RowHeight, $hnum);
	$l_hnum = $length + 3;

//      if(!empty($this->hospnum_array)){
//                foreach($this->hospnum_array as $objnum){

	$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
	$this->SetX($this->x1 + $l_hnum);
	$length = $this->GetStringWidth($this->hospaccnum);
	$this->Cell($length, $this->RowHeight, $this->hospaccnum, 0,0,'L');

//                }
//       }

		 $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		//Accreditation Category
		$this->Line($this->x1+75,$this->y1,$this->x1+75, $this->y1);
		$this->SetX($this->x1+75);
		$length = $this->GetStringWidth($cat);
		$this->Cell($length, $this->RowHeight, $cat);

		$ans = array('','','','');
		switch ($row["hosp_type"]) {
			case "PH":
			$ans[0] = 'X';
			break;

			case "SH":
			$ans[1] = 'X';
			break;

			case "TH":
			$ans[2] = 'X';
			break;
		}

		$this->SetX($this->x1+120);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->Cell($this->boxwidth,$this->RowHeight,$ans[0],0,0,'C');
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		$this->Rect($this->GetX()-3, $this->GetY()+1, 3, $this->RowHeight-2);
		$this->SetX($this->x1+123);
		$length = $this->GetStringWidth($prim);
		$this->Cell($length, $this->RowHeight, $prim);
		$this->SetX($this->x1+137);

		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->Cell($this->boxwidth,$this->RowHeight,$ans[1],0,0,'C');
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		$this->Rect($this->GetX()-3, $this->GetY()+1, 3, $this->RowHeight-2);
		$this->SetX($this->x1+140);
		$length = $this->GetStringWidth($sec);
		$this->Cell($length, $this->RowHeight, $sec);

		$this->SetX($this->x1+159);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->Cell($this->boxwidth,$this->RowHeight,$ans[2],0,0,'C');
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		$this->Rect($this->GetX()-3, $this->GetY()+1, 3, $this->RowHeight-2);
		$this->SetX($this->x1+162);
		$length = $this->GetStringWidth($ter);
		$this->Cell($length, $this->RowHeight, $ter);
		$this->SetX($this->x1+177);

		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->Cell($this->boxwidth,$this->RowHeight,$ans[3],0,0,'C');
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		$this->Rect($this->GetX()-3, $this->GetY()+1, 3, $this->RowHeight-2);
		$this->SetX($this->x1+180);
		$this->Cell($length, $this->RowHeight, $Amb,0,1);

		$this->y1 = $this->GetY();

		//Name of Hospital
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		$this->Rect($this->x1, $this->y1, 201, ($this->RowHeight * 2) - 1);
		$length = $this->GetStringWidth($name);
		$this->Cell($length, $this->RowHeight, $name,0,0);
		$this->Ln($this->RowHeight-1);
		$this->SetX($this->x1+INFO_INDENT);
//		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_hospname);
		$this->Cell($this->tot_width, $this->RowHeight, $row['hosp_name'],0,1,'C');

		//Address of Hospital
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		$this->Rect($this->x1, $this->y1, 201, ($this->RowHeight * 2) - 1);
//      $this->SetXY($this->x1, $this->y1+33);
		$length = $this->GetStringWidth($add);
		$this->Cell($length, $this->RowHeight, $add,0,0);

		$this->Ln($this->RowHeight-2);

		//Street
		$this->y1 = $this->GetY();
		$this->SetX($this->x1 + $this->GetStringWidth("4. "));

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		$length = $this->GetStringWidth($street);
		$this->Cell($length, $this->RowHeight, $street);
		$this->Ln($this->RowHeight-1);
		$this->SetX($this->x1+INFO_INDENT);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$length = $this->GetStringWidth($row['addr_no_street']);
		$this->Cell($length, $this->RowHeight, $row['addr_no_street'],0,1);

		$this->Rect($this->x1, $this->y1 - $this->RowHeight+2, 201, ($this->RowHeight * 2) + 1.5);

		//City
		$this->SetX($this->x1 + $this->GetStringWidth("4. "));
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		$length = $this->GetStringWidth($city);
		$this->Cell($length, $this->RowHeight, $city);
		$this->Ln($this->RowHeight-1);
		$this->SetX($this->x1+INFO_INDENT);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$length = $this->GetStringWidth($row['mun_name']);
		$this->Cell($length, $this->RowHeight, $row['mun_name']);

		$this->Rect($this->x1, $this->y1 + ($this->RowHeight * 2)-1, 201, ($this->RowHeight * 2) - 1);

		//Barangay
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		$this->SetXY($this->x1+100, $this->y1);
		$length = $this->GetStringWidth($barangay);
		$this->Cell($length, $this->RowHeight, $barangay);
		$this->Ln($this->RowHeight-1);
		$this->SetX($this->x1+100+INFO_INDENT);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$length = $this->GetStringWidth($row['brgy_name']);
		$this->Cell($length, $this->RowHeight, $row['brgy_name'],0,1);

		//Province
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		$this->SetX($this->x1+100);
		$length = $this->GetStringWidth($prov);
		$this->Cell($length, $this->RowHeight, $prov);
		$this->Ln($this->RowHeight-1);
		$this->SetX($this->x1+100+INFO_INDENT);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$length = $this->GetStringWidth($row['prov_name']);
		$this->Cell($length, $this->RowHeight, $row['prov_name'],0,1);

		$ypos = $this->GetY();

		//Zip Code
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		$this->SetXY($this->x1+165, $this->y1);
		$length = $this->GetStringWidth($zip);
		$this->Cell($length, $this->RowHeight, $zip);
		$this->Ln($this->RowHeight-1);
		$this->SetX($this->x1+165+INFO_INDENT);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$length = $this->GetStringWidth($row['zip_code']);
		$this->Cell($length, $this->RowHeight, $row['zip_code']);

		$this->SetY($ypos);
	}

	function getPrincipalNm($pid) {
		global $db;

		$strSQL = "SELECT p.name_last AS LastName, p.name_first AS FirstName, p.name_2 AS SecondName, \n
						p.name_3 AS ThirdName, p.name_middle AS MiddleName, i.insurance_nr AS IdNum     \n
					 FROM care_person AS p LEFT JOIN care_person_insurance AS i ON i.pid = p.pid        \n
					 WHERE i.hcare_id = $this->hcare_id AND i.is_principal = 1 AND p.pid = '$pid'";

		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				return $result;
			}
		}

		return false;
	}

	function getPrincipalNmFromTmp($enc_nr) {
		global $db;

		$strSQL = "select member_lname as LastName, member_fname as FirstName, '' as SecondName, \n
						 '' as ThirdName, member_mname as MiddleName, mi.insurance_nr as IdNum   \n
						from seg_insurance_member_info as mi inner join care_encounter as ce       \n
						 on mi.pid = ce.pid                                                      \n
						where ce.encounter_nr = '$enc_nr'";

		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				return $result;
			}
		}

		return false;
	}

	function getPrincipalAddr($pid) {
		global $db;

		$strSQL = "SELECT p.street_name AS Street, sb.brgy_name AS Barangay,                        \n
						sg.mun_name AS Municity, sg.zipcode AS Zipcode, sp.prov_name AS Province    \n
						FROM care_person AS p                                                         \n
							LEFT JOIN seg_barangays AS sb ON sb.brgy_nr = p.brgy_nr                   \n
							LEFT JOIN seg_municity AS sg ON sg.mun_nr = p.mun_nr                     \n
							LEFT JOIN seg_provinces AS sp ON sp.prov_nr = sg.prov_nr                  \n
						WHERE p.pid = '$pid'";

		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				return $result;
			}
		}

		return false;
	}

	function getPrincipalAddrFromTmp($enc_nr) {
		global $db;

		$strSQL = "select street_name as Street, brgy_name as Barangay, mun_name as Municity,                 \n
						 zipcode as Zipcode, prov_name as Province                                            \n
						from (seg_insurance_member_info as mi                                                   \n
						 left join ((seg_barangays as b inner join seg_municity as m on m.mun_nr = b.mun_nr)  \n
							inner join seg_provinces as p on m.prov_nr = p.prov_nr)                           \n
							on mi.brgy_nr = b.brgy_nr) inner join care_encounter as ce on mi.pid = ce.pid     \n
						where ce.encounter_nr = '$enc_nr'                                                       \n
						order by create_dt desc limit 1";

		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				return $result;
			}
		}

		return false;
	}

	function addMemberName($title, $lname, $mname, $fname, $id) {
		global $db;

		$this->y1 = $this->GetY();

		$this->Rect($this->x1, $this->y1, 201, ($this->RowHeight * 5)-2);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		//Name of Member and Identification
		$length = $this->GetStringWidth($title);
		$this->Cell($length, $this->RowHeight, $title);
		$this->Ln($this->RowHeight-2);

		$this->y1 = $this->GetY();
		//Last Name
		$this->SetX($this->x1 + $this->GetStringWidth("5. "));
		$length = $this->GetStringWidth($lname);
		$this->Cell($length, $this->RowHeight, $lname,0,1);
		$this->Ln();
		//Middle Name
//      $this->SetXY($this->x1+5, $this->y1+65);
		$this->SetX($this->x1 + $this->GetStringWidth("5. "));
		$length = $this->GetStringWidth($mname);
		$this->Cell($length, $this->RowHeight, $mname,0,0);
		//First Name
//      $this->SetXY($this->x1+100, $this->y1);
		$this->SetXY($this->x1+100, $this->y1);
		$length = $this->GetStringWidth($fname);
		$this->Cell($length, $this->RowHeight, $fname,0,1);
		$this->Ln();
		//Identification
		$this->SetX($this->x1+100);
		$length = $this->GetStringWidth($id);
		$id_length = $length + 2;
		$this->Cell($length, $this->RowHeight, $id);
		$this->is_member = $this->checkifmember();

		if($this->is_member == 1){
			$sql_1 = "SELECT p.name_last AS LastName, p.name_first AS FirstName, p.name_2 AS SecondName,
						p.name_3 AS ThirdName, p.name_middle AS MiddleName,
						i.insurance_nr AS IdNum
						FROM care_person AS p
						LEFT JOIN care_encounter AS e ON e.pid = p.pid
						LEFT JOIN care_person_insurance AS i ON i.pid = p.pid
						WHERE i.hcare_id = $this->hcare_id AND i.is_principal = 1 AND e.encounter_nr = $this->encounter_nr";

			$result = $db->Execute($sql_1);
		}
		else{
			if ($member_encounter = $this->checkdependence()) {
	//            $sql_2 = "SELECT p.name_last AS LastName, p.name_first AS FirstName, p.name_2 AS SecondName,
	//                      p.name_3 AS ThirdName, p.name_middle AS MiddleName,
	//                      i.insurance_nr AS IdNum
	//                      FROM care_person AS p
	//                      LEFT JOIN care_encounter AS e ON e.pid = p.pid
	//                      LEFT JOIN care_person_insurance AS i ON i.pid = p.pid
	//                      WHERE i.hcare_id = $this->hcare_id AND i.is_principal = 1 AND e.encounter_nr = $member_encounter";

	//            $result = $db->Execute($sql_2);
				$result = $this->getPrincipalNm($member_encounter);                 // Modified by LST - 06.13.2009
			}
			else
				$result = $this->getPrincipalNmFromTmp($this->encounter_nr);
		}
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		if ($result) {
			$this->_count = $result->RecordCount();
			$row = $result->FetchRow();


			$lastname  = $row['LastName'];
			$firstname = $row['FirstName']." ".$row['SecondName']." ".$row['ThirdName'];
			$midname   = $row['MiddleName'];
			$idnum     = $row['IdNum'];
		}
		else {
			$lastname  = " ";
			$firstname = " ";
			$midname   = " ";
			$idnum     = " ";
		}

		$lastname = strtoupper($lastname);
		$firstname = strtoupper($firstname);
		$midname = strtoupper($midname);

		//Last Name
		$this->SetXY($this->x1+INFO_INDENT, $this->y1+$this->RowHeight);
		$length = $this->GetStringWidth($lastname);
		$this->Cell($length, $this->RowHeight, $lastname);
		//First Name
		$this->SetX($this->x1+$this->half+INFO_INDENT);
		$length = $this->GetStringWidth($firstname);
		$this->Cell($length, $this->RowHeight, $firstname,0,1);
		$this->Ln();
		//Middle Name
		$this->SetX($this->x1+INFO_INDENT);
		$length = $this->GetStringWidth($midname);
		$this->Cell($length, $this->RowHeight, $midname);
		//Identification No.
		$this->SetX($this->x1+$this->half+INFO_INDENT);
		$length = $this->GetStringWidth($idnum);
		$this->Cell($length, $this->RowHeight, $idnum,0,1);
	}

	function addMemberAddress($title, $street, $city, $barangay, $prov, $zip) {
		global $db;
		$this->y1 = $this->GetY();

		$this->Rect($this->x1, $this->y1, 201, ($this->RowHeight * 5)-2);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		//Address of Hospital
		$this->SetX($this->x1);
		$length = $this->GetStringWidth($title);
		$this->Cell($length, $this->RowHeight, $title);
		$this->Ln($this->RowHeight-2);

		$this->y1 = $this->GetY();

		//Street
		$this->SetX($this->x1 + $this->GetStringWidth("6. "));
		$length = $this->GetStringWidth($street);
		$this->Cell($length, $this->RowHeight, $street,0,1);
		$this->Ln($this->RowHeight-1);
		//City
		$this->SetX($this->x1 + $this->GetStringWidth("6. "));
		$length = $this->GetStringWidth($city);
		$this->Cell($length, $this->RowHeight, $city);
		//Barangay
		$this->SetXY($this->x1+$this->half, $this->y1);
		$length = $this->GetStringWidth($barangay);
		$this->Cell($length, $this->RowHeight, $barangay,0,1);
		$this->Ln($this->RowHeight-1);
		//Province
		$this->SetX($this->x1+$this->half);
		$length = $this->GetStringWidth($prov);
		$this->Cell($length, $this->RowHeight, $prov);
		//Zip Code
		$this->SetXY($this->x1+165, $this->y1);
		$length = $this->GetStringWidth($zip);
		$this->Cell($length, $this->RowHeight, $zip);

		if($this->is_member == 1){
			$sql_1 = "SELECT p.street_name AS Street, sb.brgy_name AS Barangay,
								sg.mun_name AS Municity, sg.zipcode AS Zipcode, sp.prov_name AS Province
							FROM care_person AS p
								LEFT JOIN seg_barangays AS sb ON sb.brgy_nr = p.brgy_nr
								LEFT JOIN seg_municity AS sg ON sg.mun_nr = sb.mun_nr
								LEFT JOIN seg_provinces AS sp ON sp.prov_nr = sg.prov_nr
								LEFT JOIN care_encounter AS e ON e.pid = p.pid
							WHERE e.encounter_nr = $this->encounter_nr";
			$result = $db->Execute($sql_1);
		}
		else{
			if ($member_encounter = $this->checkdependence()) {
//              $sql_2 = "SELECT p.street_name AS Street, sb.brgy_name AS Barangay,
//                          sg.mun_name AS Municity, sg.zipcode AS Zipcode, sp.prov_name AS Province
//                          FROM care_person AS p
//                          LEFT JOIN seg_barangays AS sb ON sb.brgy_nr = p.brgy_nr
//                          LEFT JOIN seg_municity AS sg ON sg.mun_nr = sb.mun_nr
//                          LEFT JOIN seg_provinces AS sp ON sp.prov_nr = sg.prov_nr
//                          LEFT JOIN care_encounter AS e ON e.pid = p.pid
//                          WHERE e.encounter_nr = $member_encounter";
//
//              $result = $db->Execute($sql_2);
				$result = $this->getPrincipalAddr($member_encounter);
			}
			else
				$result = $this->getPrincipalAddrFromTmp($this->encounter_nr);
		}

		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		if ($result) {
				$this->_count = $result->RecordCount();
				$row = $result->FetchRow();

				$sstreet   = $row['Street'];
				$sbrgyname = $row['Barangay'];
				$smunname  = $row['Municity'];
				$sprovname = $row['Province'];
				$szipcode  = $row['Zipcode'];
		}
		else {
				$sstreet   = "";
				$sbrgyname = "";
				$smunname  = "";
				$sprovname = "";
				$szipcode  = "";
		}

		//Street
		$this->SetXY($this->x1+INFO_INDENT, $this->y1+$this->RowHeight);
		$length = $this->GetStringWidth($sstreet);
		$this->Cell($length, $this->RowHeight, $sstreet);

		//Barangay
		$this->SetX($this->x1 + $this->half+INFO_INDENT);
		$length = $this->GetStringWidth($sbrgyname);
		$this->Cell($length, $this->RowHeight, $sbrgyname,0,1);
		$this->Ln($this->RowHeight-1);

		//City
		$this->SetX($this->x1+INFO_INDENT);
		$length = $this->GetStringWidth($smunname);
		$this->Cell($length, $this->RowHeight, $smunname);

		//Province
		$this->SetX($this->x1+$this->half+INFO_INDENT);
		$length = $this->GetStringWidth($sprovname);
		$this->Cell($length, $this->RowHeight, $sprovname,0,1);

		$ypos = $this->GetY();

		//Zip Code
		$this->SetXY($this->x1 + $this->third+INFO_INDENT, $this->y1+$this->RowHeight);
		$length = $this->GetStringWidth($szipcode);
		$this->Cell($length, $this->RowHeight, $szipcode,0,1);

		$this->SetY($ypos+1);
	}

	function addNamePatient($title, $lname, $fname, $mname, $age, $sex) {
		global $db;

		$pcolwidths = array(95, 115-95, $this->tot_width - 115);

		$ans="X";
		$noans = " ";
		$m = "Male";
		$f = "Female";

		$lastname  = '';
		$firstname = '';
		$midname   = '';
		$n_age     = '';
		$sex_arr   = array('','');

		$sql= "SELECT p.name_last AS LastName, p.name_first AS FirstName, p.name_2 AS SecondName,
				p.name_3 AS ThirdName, p.name_middle AS MiddleName, p.age AS Age, p.sex AS Sex
				FROM care_person AS p
				LEFT JOIN care_encounter AS e ON e.pid = p.pid
				LEFT JOIN care_person_insurance AS pi ON pi.pid = p.pid
				LEFT JOIN seg_encounter_insurance AS i ON i.encounter_nr = e.encounter_nr
				WHERE i.hcare_id = $this->hcare_id AND e.encounter_nr = $this->encounter_nr";

		if ($result=$db->Execute($sql)) {
			$this->_count = $result->RecordCount();
			if ($row = $result->FetchRow()) {
				$lastname  = $row['LastName'];
				$firstname = $row['FirstName']." ".$row['SecondName']." ".$row['ThirdName'];
				$midname   = $row['MiddleName'];
				$n_age     = $row['Age'];

				$sex_arr   = array('','');
				switch ($row['Sex']) {
					case 'm':
						$sex_arr[0] = 'X';
						break;

					case 'f':
						$sex_arr[1] = 'X';
						break;
				}
			}
		}

		$this->y1 = $this->GetY();
		$ypos = $this->y1;

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		$this->Rect($this->x1, $this->y1, $pcolwidths[0], ($this->RowHeight * 6)-2.5);

		//Name of Patient
		$this->SetX($this->x1);
		$length = $this->GetStringWidth($title);
		$this->Cell($length, $this->RowHeight, $title);
		$this->Ln($this->RowHeight-2);

		$this->y1 = $this->GetY();

		// Last Name
		$this->SetX($this->x1 + $this->GetStringWidth("7. "));
		$length = $this->GetStringWidth($lname);
		$this->Cell($length, $this->RowHeight, $lname);

		$this->Ln($this->RowHeight-2);
//        $this->Ln(4);
		$this->SetX($this->x1+INFO_INDENT);
		$length = $this->GetStringWidth($lastname);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->Cell($length, $this->RowHeight, strtoupper($lastname),0,1);

		//First Name
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		$this->SetX($this->x1 + $this->GetStringWidth("7. "));
		$length = $this->GetStringWidth($fname);
		$this->Cell($length, $this->RowHeight, $fname);

		$this->Ln($this->RowHeight-2);

		$this->SetX($this->x1+INFO_INDENT);
		$length = $this->GetStringWidth($firstname);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->Cell($length, $this->RowHeight, strtoupper($firstname),0,1);
		//Middle Name
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
//        $this->SetXY($this->x1+5, $this->y1+112);
		$this->SetX($this->x1 + $this->GetStringWidth("7. "));
		$length = $this->GetStringWidth($mname);
		$this->Cell($length, $this->RowHeight, $mname);

		$this->Ln($this->RowHeight-2);

		$this->SetX($this->x1+INFO_INDENT);
		$length = $this->GetStringWidth($midname);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->Cell($length, $this->RowHeight, strtoupper($midname));
		//Age
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		$this->Rect($this->x1+$pcolwidths[0], $this->y1-($this->RowHeight-2), $pcolwidths[1], ($this->RowHeight * 3)-($this->RowHeight * 0.9));
		$this->SetXY($this->x1+$pcolwidths[0], $this->y1);
//        $length = $this->GetStringWidth($age);
		$this->Cell($pcolwidths[1], $this->RowHeight, $age, 0,0,"L");

		$this->Ln($this->RowHeight-1);

		$this->SetX($this->x1+101);
		$length = $this->GetStringWidth($n_age);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->Cell($length, $this->RowHeight, $n_age,0,1);
//        $this->Ln($this->RowHeight-1);

		//Sex
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
//        $this->Rect($this->x1 + $pcolwidths[0], $this->y1+($this->RowHeight * 1.55), 20, ($this->RowHeight * 6)-2 - (($this->RowHeight * 3)-($this->RowHeight * 0.7)));
		$this->SetX($this->x1 + $pcolwidths[0]);
//        $length = $this->GetStringWidth($sex);
		$this->Cell($pcolwidths[1], $this->RowHeight, $sex,0,0,"L");

		$this->Ln($this->RowHeight);

		$this->SetX($this->x1 + 98);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->Cell($this->boxwidth,$this->RowHeight - 2,$sex_arr[0], 0, 0, 'C');
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		$this->Rect($this->GetX()-3, $this->GetY(), 3, $this->RowHeight-2);
		$this->SetX($this->x1+101);
		$this->Cell($this->GetStringWidth($f), $this->RowHeight-2, $m, 0, 1);
		$this->Ln($this->RowHeight-2);

		$this->SetX($this->x1+98);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->Cell($this->boxwidth, $this->RowHeight - 2, $sex_arr[1], 0,0, 'C');
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		$this->Rect($this->GetX()-3, $this->GetY(), 3, $this->RowHeight-2);
		$this->SetX($this->x1+101);
		$this->Cell($this->GetStringWidth($f), $this->RowHeight-2, $f);

		$this->lastypos = $this->GetY();
		$this->SetY($ypos);
	}

	function addDiagnosis($diagnosis){
		global $db;

		$this->y1 = $this->GetY();

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		$this->Rect($this->x1+115, $this->y1, 86, ($this->RowHeight * 6)-2.5);
		//Diagnosis
		$sql = "SELECT e.pid AS pid, er_opd_diagnosis AS Diagnosis
			 FROM care_encounter AS e where e.encounter_nr = $this->encounter_nr";
		$result=$db->Execute($sql);
		$this->_count = $result->RecordCount();
		$row = $result->FetchRow();
		$this->SetX($this->x1+115);
		$length = $this->GetStringWidth($diagnosis);
		$this->Cell($length, $this->RowHeight, $diagnosis);
		$this->Ln($this->RowHeight);
		$this->SetX($this->x1+115);
		$length = $this->GetStringWidth($row['Diagnosis']);
		$this->SetFont($this->fontfamily_admdiagnosis, $this->fontstyle_admdiagnosis, $this->fontsize_admdiagnosis);
//		 $this->SetFontSize(8);
		if ($length > $this->tot_width - 115) {
			$this->SetFontSize(8);
			$this->MultiCell($this->tot_width - 115, $this->RowHeight-1.5, $row['Diagnosis'],0,"L");
		}
		else
			$this->MultiCell($this->tot_width - 115, $this->RowHeight, $row['Diagnosis'],0,"L");
		$this->SetY($this->lastypos + $this->RowHeight);
	}

	function addConfinement($title, $dateA, $timeA, $dateD, $timeD, $numdays, $death_date) {
		 $this->y1 = $this->GetY();
		 $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		 $this->Rect($this->x1, $this->y1, 201, $this->RowHeight * 3);
		 //Confinement Period
		 $this->SetX($this->x1);
		 $length = $this->GetStringWidth($title);
		 $this->Cell($length, $this->RowHeight, $title);
		 $this->Ln($this->RowHeight-2);

		 $this->y1 = $this->GetY();

		 //Date Admitted
		 $this->SetX($this->x1+$this->GetStringWidth("11. "));
		 $length = $this->GetStringWidth($dateA);
		 $this->Cell($length, $this->RowHeight, $dateA);
		 $l_admitdt = $length + 2;
		 //Date Discharged
		 $this->SetX($this->x1+80);
		 $length = $this->GetStringWidth($dateD);
		 $this->Cell($length, $this->RowHeight, $dateD);
		 $l_dischdt = $length + 2;
		 //Claimed No. of Days
		 $this->SetX($this->x1+140);
		 $length = $this->GetStringWidth($numdays);
		 $this->Cell($length, $this->RowHeight, $numdays);
		 $height2 = $height1 + 8;
		 $l_claimdays = $length + 2;

		 $this->Ln($this->RowHeight);

		 //Time Admitted
		 $this->SetX($this->x1+$this->GetStringWidth("11. "));
		 $length = $this->GetStringWidth($timeA);
		 $this->Cell($length, $this->h_cell, $timeA);
		 $l_admittm = $length + 2;
		 //Time Discharged
		 $this->SetX($this->x1+80);
		 $length = $this->GetStringWidth($timeD);
		 $this->Cell($length, $this->RowHeight, $timeD);
		 $l_dischtm = $length + 2;
		 //Date of Death
		 $height3 = $height2 - 5;
		 $this->SetX($this->x1+140);
		 $length = $this->GetStringWidth($death_date);
		 $this->MultiCell($length, $this->RowHeight-2, $death_date);
		 $l_death_dt = 25;

		 $this->SetY($this->y1);

		if(!empty($this->confinement_array)){
			foreach($this->confinement_array as $objconf){
				$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
				//Date Admitted
				$this->SetX($this->x1+($l_admitdt + 10));
				$length = $this->GetStringWidth($objconf->admit_dt);
				$this->Cell($length, $this->RowHeight, $objconf->admit_dt, 0, 0, 'L');
				//Date Discharged
				$this->SetX($this->x1+($l_dischdt + 80));
				$length = $this->GetStringWidth($objconf->discharge_dt);
				$this->Cell($length, $this->RowHeight, $objconf->discharge_dt, 0, 0, 'L');
				//Claimed No. of Days
				$this->SetX($this->x1+($l_claimdays + 140));
				$length = $this->GetStringWidth($objconf->claim_days);
				$this->Cell($length, $this->RowHeight, $objconf->claim_days, 0, 0, 'L');

				$this->Ln($this->RowHeight);

				//Time Admitted
				$this->SetX($this->x1 + ($l_admittm + 10));
				$length = $this->GetStringWidth($objconf->admit_tm);
				$this->Cell($length, $this->RowHeight, $objconf->admit_tm, 0, 0, 'L');
				//Time Discharged
				$this->SetX($this->x1 + ($l_dischtm + 80));
				$length = $this->GetStringWidth($objconf->discharge_tm);
				$this->Cell($length, $this->RowHeight, $objconf->discharge_tm, 0, 0, 'L');
				//Date of Death
				if ($objconf->death_dt != '00-00-0000') {
					$this->SetX($this->x1 + ($l_death_dt + 140));
					$length = $this->GetStringWidth($objconf->death_dt);
					$this->Cell($length, $this->RowHeight, $objconf->death_dt, 0, 0, 'L');
				}
			}

			$this->SetY($this->GetY() + $this->RowHeight);
		}
		else
			$this->SetY($this->GetY() + ($this->RowHeight * 2));
	}

	function addHospAmbServ($title, $ch_a, $ch_b, $ch_c, $ch_d, $ch_e, $tot){

		 $xcoord = $this->x1+63;

		 $this->y1 = $this->GetY()+2;
		 $this->lastypos = $this->y1;

		 $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);

		 //Hospital/Ambulatory Services
		 $this->SetXY($this->x1, $this->y1);
		 $length = $this->GetStringWidth($title);
		 $this->Cell($length, $this->RowHeight, $title, 0, 0);

		//TABLE
		 $this->SetX($this->x1+63);
		 $length = $this->GetStringWidth("ACTUAL HOSPITAL");
		 $this->Cell($length+10, $this->RowHeight, "ACTUAL HOSPITAL/", 0, 0, 'C');
		 $this->Cell(70, $this->RowHeight,"BENEFIT CLAIM", 0, 0, 'C');
		 $this->Cell(30, $this->RowHeight, "REDUCTION", 0, 0, 'C');
		 $this->Ln($this->RowHeight - 1);
		 $this->SetX($this->x1+63);
		 $this->Cell(37, $this->RowHeight, "AMBULATORY CHARGES", 0, 0 ,'C');
		 $this->SetX($this->x1+$this->half);
		 $this->Cell(35, $this->RowHeight, "HOSPITAL", 0, 0, 'C');
		 $this->Cell(35, $this->RowHeight, "PATIENT", 0, 0, 'C');
		 $this->Cell(32, $this->RowHeight, "CODE", 0, 0, "C");
		 $this->Rect($this->x1+63, $this->lastypos, 38, ($this->RowHeight * 2)-2);
		 $this->Rect($this->x1+101, $this->lastypos, 68, $this->RowHeight - 1);
		 $this->Rect($this->x1+101, $this->lastypos + ($this->RowHeight - 1), 34, $this->RowHeight-1);
		 $this->Rect($this->x1+135, $this->lastypos + ($this->RowHeight - 1), 34, $this->RowHeight-1);
		 $this->Rect($this->x1+169, $this->lastypos, 32, ($this->RowHeight * 2)-2);

		 $this->y1 = $this->GetY() + $this->RowHeight - 2;
		 $this->SetY($this->y1);

		 $this->ColumnWidth = array(38, 34, 34, 32);
	 /*  //for loop for rows
		 $ycoord = $this->y1+142;
		 $xcoord = $this->x1+63;
		 for($count = 0; $count<7; $count++){
		 $this->SetXY($xcoord, $ycoord);
		 $this->Cell(38, 4, "", 1, 0);
		 $this->Cell(34, 4, "", 1, 0);
		 $this->Cell(34, 4, "", 1, 0);
		 $this->Cell(32, 4, "", 1, 0);
		 $ycoord = $ycoord+4;
		 }
		 */

		 //a.Room and Board
//       $this->SetX($this->x1);
//       $length = $this->GetStringWidth($ch_a);
//       $this->Cell($length, $this->RowHeight, $ch_a);
//       $this->Ln($this->RowHeight-1);
//
		 //b. Drugs and Medicines
//       $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label3);
//       $this->SetX($this->x1);
//       $length = $this->GetStringWidth($ch_b);
//       $this->Cell($length, $this->RowHeight, $ch_b);
//       $this->Ln($this->RowHeight-1);
//
		 //c. X-ray/Lab. Test/Others
//       $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label3);
//       $this->SetX($this->x1);
//       $length = $this->GetStringWidth($ch_c);
//       $this->Cell($length, $this->RowHeight, $ch_c);
//       $this->Ln($this->RowHeight-1);
//
		 //d.Operating Room Fee
//       $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label3);
//       $this->SetX($this->x1);
//       $length = $this->GetStringWidth($ch_d);
//       $this->Cell($length, $this->RowHeight, $ch_d);
//       $this->Ln($this->RowHeight);
//
		 //e. Medicines bought
//       $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label3);
//       $this->SetX($this->x1);
//       $length = $this->GetStringWidth($ch_e);
//       $this->MultiCell($length, $this->RowHeight-2, $ch_e);
//       $this->Ln();
//
		 // Total
//       $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label3);
//       $this->SetX($this->x1+49);
//       $length = $this->GetStringWidth($tot);
//       $this->Cell($length, $this->RowHeight - 2, $tot);

			$row2 = $this->RowHeight-1;
			$this->SetY($this->y1);

			$row_desc[0] = array("a. Room and Board", 0, "L");
			$row_desc[1] = array("b. Drugs and Medicine (Part III of details)", 0, "L");
			$row_desc[2] = array("c. X-ray/Lab. Test/Others (Part IV of details)", 0, "L");
			$row_desc[3] = array("d. Operating Room Fee", 0, "L");
			$row_desc[4] = array("e. Medicines Bought & Lab. Performed\n    outside hosp. during confinement period", 0, "L");
			$row_desc[5] = array("TOTAL", 0, "C");

			$oldRowHeight = $this->RowHeight;
			$this->RowHeight = $this->AdjustedRowHeight;

			if(!empty($this->hospserv_array)){
				$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer2);
				$ycoord = $this->y1 + 1; //142

				$h_charges        = 0;
				$h_hospital_claim = 0;
				$h_patient_claim  = 0;

				$i = 0;

				$this->SetY($ycoord);
				foreach ($this->hospserv_array as $objroom){
					$this->SetX($this->x1);

					$row2 = ((count($this->hospserv_array)-1) == $i) ? $row2 = ($row2 * 2) - 2 : $this->RowHeight-1;

					$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label3);
					if (($n = strpos($row_desc[$i][0], "\n")) === false)
						$this->Cell($this->tot_width - ($this->ColumnWidth[0]+$this->ColumnWidth[1]+$this->ColumnWidth[2]+$this->ColumnWidth[3]), $row2, $row_desc[$i][0], 0, 0, $row_desc[$i][2]);
					else {
						$ypos = $this->GetY();
						$this->MultiCell($this->tot_width - ($this->ColumnWidth[0]+$this->ColumnWidth[1]+$this->ColumnWidth[2]+$this->ColumnWidth[3]), $this->RowHeight-2, $row_desc[$i][0], 0, $row_desc[$i][2]);
						$this->SetXY($this->x1 + ($this->tot_width - ($this->ColumnWidth[0]+$this->ColumnWidth[1]+$this->ColumnWidth[2]+$this->ColumnWidth[3])), $ypos);
					}

					$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer2);
					$this->Cell($this->ColumnWidth[0], $row2, number_format($objroom->charges,2,'.',','), 1, 0, 'R');
					if ($i == 4)
						$this->Cell($this->ColumnWidth[1], $row2, "",1, 0,'R');
					else
						$this->Cell($this->ColumnWidth[1], $row2, number_format($objroom->claim_hospital,2,'.',','),1, 0,'R');
					if (($objroom->claim_patient == 0) && ($i != 4))
						$this->Cell($this->ColumnWidth[2], $row2, "", 1, 0, 'R');
					else
						$this->Cell($this->ColumnWidth[2], $row2, number_format($objroom->claim_patient,2,'.',','), 1, 0, 'R');
					$this->Cell($this->ColumnWidth[3], $row2, "", 1, 0, 'C');
					$ycoord = $ycoord + $row2;
	//                  $this->Ln();
					$this->Ln($this->RowHeight-1);

					$h_charges += $objroom->charges;
					$h_hospital_claim += $objroom->claim_hospital;
					$h_patient_claim += $objroom->claim_patient;

					// Take note of medicinies/services paid for by patient outside hospital.
					if ($i == 4) $this->total_out_claim = $objroom->claim_patient;

					$i++;
				}

				// Take note of total hospital charges.
				$this->total_hosp_charge = $h_hospital_claim;

				$row2 = $this->RowHeight-1;

				$this->SetXY($this->x1, $ycoord);
				$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label3);
				$this->Cell($this->tot_width - ($this->ColumnWidth[0]+$this->ColumnWidth[1]+$this->ColumnWidth[2]+$this->ColumnWidth[3]), $row2, $row_desc[5][0], 0, 0, $row_desc[5][2]);

				$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer2);
				$this->Cell($this->ColumnWidth[0], $row2, number_format($h_charges,2,'.',','), 1, 0, 'R');
				$this->Cell($this->ColumnWidth[1], $row2, number_format($h_hospital_claim,2,'.',','),1, 0,'R');
				$this->Cell($this->ColumnWidth[2], $row2, number_format($h_patient_claim,2,'.',','), 1, 0, 'R');
				$this->Cell($this->ColumnWidth[3], $row2, "", 1, 0, 'C');

				$this->RowHeight = $oldRowHeight;

				$this->Ln($this->RowHeight);
			}

			$this->Rect($this->x1, $this->lastypos, 63, ($ycoord + $this->RowHeight + 0.5) - $this->lastypos);
			$this->SetY($ycoord + $this->RowHeight + ($this->AdjustedRowHeight - $oldRowHeight));
	}

	function addCertification($cert, $sig, $date, $cap){
//      global $x1, $y1;

		// ... added by LST - 04.21.2009 ... by adjusting values of these two variables, the whole certification part is adjusted.
		$xbasis = 46;
		$sigpos = 2;
		$cert_height = ($this->RowHeight * 6) + 2;  //adjust this variable if there are changes made in $sigpos

		$this->y1 = $this->GetY();

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label3);
		//$this->Rect($this->x1, $this->y1+170, 201, 14);
		$this->Rect($this->x1, $this->y1-1, 201, $cert_height);
		//Certification
		$this->SetX($this->x1);
		$length = $this->GetStringWidth($cert);
		$this->MultiCell($length, 3, $cert);

		$this->Ln(($this->RowHeight - 1) * ($this->RowHeight + 0.25));

		$this->y1 = $this->GetY();

		//Signature
		//$this->SetFontSize(8);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		$length = $this->GetStringWidth($this->auth_rep)+2;             // Authorized Representative -- added by LST - 04.21.2009
		$this->SetX($this->x1+$xbasis-($length/2));
		$this->Cell($length, $this->RowHeight * 0.25, $this->auth_rep);

		$xadj = (($this->GetStringWidth($this->auth_rep) > $this->GetStringWidth($sig) ? $this->GetStringWidth($this->auth_rep) : $this->GetStringWidth($sig))/2);
		$this->Line($this->x1+$xbasis-$xadj, $this->y1+$sigpos, $this->x1+$xbasis+$xadj, $this->y1+$sigpos);

		//Date Signed
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		$rundate = strftime("%B %d, %Y");
		$length = $this->GetStringWidth($rundate)+2;                    // Date Signed -- added by LST - 04.21.2009
		$this->SetX($this->x1+($xbasis+78)-($length/2));
		$this->Cell($length, $this->RowHeight * 0.25, $rundate);

		$this->Line($this->x1+($xbasis+78)-($length/2), $this->y1+$sigpos, $this->x1+($xbasis+78)+($length/2), $this->y1+$sigpos);

		//Official Capacity
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		$length = $this->GetStringWidth((($this->rep_capacity == '') ? $cap : $this->rep_capacity)) + 2;             // Official Capacity -- added by LST - 04.21.2009
		$this->SetX($this->x1+($xbasis+126)-($length/2));
		$this->Cell($length, $this->RowHeight * 0.25, $this->rep_capacity);

		$this->Line($this->x1+($xbasis+126)-($length/2), $this->y1+$sigpos, $this->x1+($xbasis+126)+($length/2), $this->y1+$sigpos);

		$this->Ln($this->RowHeight - 3);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_answer, $this->fontsize_label3);
		$length = $this->GetStringWidth($sig);
		$this->SetX($this->x1+$xbasis-(($length+2)/2));
		$this->Cell($length, $this->RowHeight, $sig);

		//Date Signed
//      $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
//      $rundate = strftime("%B %d, %Y");
//      $length = $this->GetStringWidth($rundate)+2;                    // Date Signed -- added by LST - 04.21.2009
//      $this->SetX($this->x1+($xbasis+78)-($length/2));
//      $this->Cell($length, $this->RowHeight-1, $rundate);

//      $this->Line($this->x1+($xbasis+78)-($length/2), $this->y1+$sigpos, $this->x1+($xbasis+78)+($length/2), $this->y1+$sigpos);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_answer, $this->fontsize_label3);
		$length = $this->GetStringWidth($date)+1;
		$this->SetX($this->x1+($xbasis+78)-($length/2));
		$this->Cell($length, $this->RowHeight, $date);

//      //Official Capacity
//      $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
//      $length = $this->GetStringWidth((($this->rep_capacity == '') ? $cap : $this->rep_capacity)) + 2;             // Official Capacity -- added by LST - 04.21.2009
//      $this->SetX($this->x1+($xbasis+126)-($length/2));
//      $this->Cell($length, $this->RowHeight-1, $this->rep_capacity);

//      $this->Line($this->x1+($xbasis+126)-($length/2), $this->y1+$sigpos, $this->x1+($xbasis+126)+($length/2), $this->y1+$sigpos);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_answer, $this->fontsize_label3);
		$length = $this->GetStringWidth($cap);
		$this->SetX($this->x1+($xbasis+126)-($length/2));
		$this->Cell($length, $this->RowHeight, $cap, 0, 0);

		$this->Ln($this->RowHeight*0.1);
	 }

	 function addPart2($str){
		 //modified by Cherry 04-22-09
		 $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		 $ypos = $this->GetY()+1;
		 //$this->SetXY($this->x1+40, $this->y1+185);
		 $this->SetXY($this->x1+40, $ypos+3);
		 $length = $this->GetStringWidth($str);
		 $this->Cell($length, $this->RowHeight, $str);
		 $this->Ln($this->RowHeight-2);
	 }

	 function addFinDiagnosis($fdiag){
//       global $x1, $y1;
		//modified by Cherry 04-22-09
		 $height = ($this->RowHeight * 5) + 2;
		 $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		 //$this->Rect($this->x1, $this->y1+188, 161, 9);
		 //$this->SetXY($this->x1, $this->y1+189);
		 $ypos = $this->GetY();
		 $this->Rect($this->x1, $ypos+2, 161, $height);
		 $this->SetXY($this->x1, $ypos+2);
		 $length = $this->GetStringWidth($fdiag);
		 $this->Cell($length, $this->RowHeight, $fdiag);
		 $this->Ln($this->RowHeight);

//       $ypos = $this->GetY();

		 //$row = 3;
		 $width = 161;

		if(!empty($this->diagnosis_array)) {
			$diagnosis = "";
			foreach($this->diagnosis_array as $objdiag) {
				$diagnosis .= (($diagnosis == "") ? "" : "; ").$objdiag->fin_diagnosis;
			}
			$this->SetX($this->x1 + $this->GetStringWidth("14. "));
			$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer2);
			$length = $this->GetStringWidth($diagnosis);
			if($length < $this->diag_space) {
				$this->Cell($length, $this->RowHeight-2, $diagnosis,0,0,'L');
			}
			else {
				$this->MultiCell($this->diag_space, $this->RowHeight-2, $diagnosis,0,'J');
			}

//			foreach($this->diagnosis_array as $objdiag) {
//				$this->Cell(($width * 0.8), $this->RowHeight-2, $objdiag->fin_diagnosis, 0, 0, 'L');
//				$this->Cell(($width * 0.2), $this->RowHeight-2, $objdiag->code, 0, 1, 'L');
//			}
		}

		$this->SetY($ypos-0.5);

		//$this->addPhilUse("Relative Unit Value", "Illness Code", "Reduction Code", 1, $this->y1+185+3);
		$this->addPhilUse("Relative Unit Value", "Illness Code", "Reduction Code", 1, $ypos+2);
		$this->addCaseType("15. Case Type",
					"A",
					"B",
					"C",
					"D",
					$height);
	 }

	 function addCaseType($ctype, $ord, $int, $cat, $catD, $prevheight){
//       global $x1, $y1;
		 # Modified by Cherry 04-22-09
		 $ans = "X";
		 $noans = " ";
		 $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		 $ypos = $this->GetY();
		 //$this->Rect($this->x1, $this->y1+197, 161, 4);
		 $this->Rect($this->x1, $ypos+($prevheight - 2), 161, $this->RowHeight);
		 //15.Case Type
		 //$this->SetXY($this->x1, $this->y1+198);
		 $this->SetXY($this->x1, $ypos+($prevheight - 1));
		 $length = $this->GetStringWidth($ctype);
		 $this->Cell($length, $this->RowHeight-2, $ctype);

		if(!empty($this->diagnosis_array)) {
			$objdiag = $this->diagnosis_array[0];

			$case_arr = array('','','','');
			if (($objdiag->case_type >= 1) && ($objdiag->case_type <= 4)) $case_arr[$objdiag->case_type-1] = 'X';
		}

		//Ordinary
		 $this->SetX($this->x1+40);
		 $this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		 $this->Cell($this->boxwidth, $this->RowHeight-2, $case_arr[0], 0, 0, 'C');
		 $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		 $this->Rect($this->GetX()-3, $this->GetY(), 3, $this->RowHeight-2);
		 $this->SetX($this->x1+44);
		 $length = $this->GetStringWidth($ord);
		 $this->Cell($length, $this->RowHeight-2, $ord);
		 //Intensive
		 $this->SetX($this->x1+70);
		 $this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		 $this->Cell($this->boxwidth, $this->RowHeight-2, $case_arr[1], 0, 0, 'C');
		 $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		 $this->Rect($this->GetX()-3, $this->GetY(), 3, $this->RowHeight-2);
		 $this->SetX($this->x1+74);
		 $length = $this->GetStringWidth($int);
		 $this->Cell($length, $this->RowHeight-2, $int);
		 //Catastrophic
		 $this->SetX($this->x1+100);
		 $this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		 $this->Cell($this->boxwidth, $this->RowHeight-2, $case_arr[2], 0, 0, 'C');
		 $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		 $this->Rect($this->GetX()-3, $this->GetY(), 3, $this->RowHeight-2);
		 $this->SetX($this->x1+104);
		 $length = $this->GetStringWidth($cat);
		 $this->Cell($length, $this->RowHeight-2, $cat);
		 //Category D
		 $this->SetX($this->x1+130);
		 $this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		 $this->Cell($this->boxwidth, $this->RowHeight-2, $case_arr[3], 0, 0, 'C');
		 $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		 $this->Rect($this->GetX()-3, $this->GetY(), 3, $this->RowHeight-2);
		 $this->SetX($this->x1+134);
		 $length = $this->GetStringWidth($catD);
		 $this->Cell($length, $this->RowHeight-2, $catD);

		 $this->Ln(2);
	 }

	function addAttPhysician($name, $sig, $acc, $bir, $serv){
		#Modified by Cherry 04-22-09
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		$ypos = $this->GetY();

		//$this->Rect($this->x1, $this->y1+201, 161, 8);
		$this->Rect($this->x1, $ypos+1.5, 161, ($this->RowHeight * 2.5)-0.1);
		$this->Ln($this->RowHeight-2);
		//16. Name of Attending Physician
		//$this->SetXY($this->x1, $this->y1+202);
		$this->SetX($this->x1);
		//$this->Line(4);
		$length = $this->GetStringWidth($name);
		$this->Cell($length, $this->RowHeight-2, $name);

		//Signature & Date Signed
		$this->SetX($this->x1+110);
		$new_y = $this->GetY();

		$new_y += ($this->RowHeight * 2);

		//$this->Line($this->x1+108, $this->y1+208.5, $this->x1+155, $this->y1+208.5);
		$this->Line($this->x1+108, $new_y, $this->x1+155, $new_y);
		$length = $this->GetStringWidth($sig);
		$this->Cell($length, $this->RowHeight-2, $sig, 0, 1);

		$this->lastypos = $this->GetY();
		$this->Ln($this->RowHeight-1);
		$new_y = $this->GetY()+4.2;

		$this->SetY($new_y);

		//PHIC Accreditation No.
		//$this->Rect($this->x1, $this->y1+209, 80, 4);
		//$this->SetXY($this->x1, $this->y1+210);
		$this->Rect($this->x1, $new_y, 80, $this->RowHeight);
		$this->SetX($this->x1);
		$length = $this->GetStringWidth($acc);
		$this->Cell($length, $this->RowHeight, $acc);
//		$l_accnum = $length + 2;

		//BIR/TIN No.
		//$this->Rect($this->x1+80, $this->y1+209, 81, 4);
		$this->Rect($this->x1+80, $new_y, 81, $this->RowHeight);
		$this->SetX($this->x1+80);
		$length = $this->GetStringWidth($bir);
		$this->Cell($length, $this->RowHeight, $bir);

		$this->Ln($this->RowHeight);
		$new_y = $this->GetY();

		//Services Performed
		$new_y2 = $this->GetY();
		$this->SetFontSize($this->fontsize_servperform);
		//$this->Rect($this->x1, $this->y1+213, 80, 10);
		//$this->SetXY($this->x1, $this->y1+214);
		$this->Rect($this->x1, $new_y2, 80, ($this->RowHeight * 2));
		$this->SetX($this->x1);
		$length = $this->GetStringWidth($serv);
		$this->Cell($length, $this->RowHeight, $serv);
		$lserv = 80;
		//Table
		$this->SetFontSize($this->fontsize_table_note);
		$this->SetX($this->x1+80);
		$this->Cell(27, $this->RowHeight, "20.    Actual", "R", 0, 'L');
		$this->Cell(54, $this->RowHeight * 0.75, "Benefit Claim", "B", 0, 'C');
		$this->Ln($this->RowHeight - 1);
		$table_y = $this->GetY();
		$this->SetX($this->x1+80);
		$this->Cell(27, $this->RowHeight * 0.5, "Professional Charges", "BR", 0, 'L');
		$this->SetX($this->x1+107);
		$this->Cell(27, $this->RowHeight * 0.5, "Physician", "BR", 0, 'C');
		$this->Cell(27, $this->RowHeight * 0.5, "Patient", "BR", 0, 'C');

//        $this->Rect($this->x1+107, $this->GetY()-0.5, 27, $this->RowHeight * 0.575);

		$this->ColumnWidth = 27;
		$rowtable = ($this->RowHeight * 0.75)-0.1;
		$row = 2;
		$row_multi = 3;

		$bdetails_added = false;

		$this->total_pf_charge = 0;
		if(!empty($this->healthperson_array)){
			$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);

			$this->Ln($this->RowHeight * 0.5);

			$services = "";
			foreach($this->healthperson_array as $objphysician){
				if ($objphysician->role_area != 'D4') {
					$services .= (($services != '') ? " " : "").$objphysician->servperformance;
					//Table
					$this->SetX($this->x1+80);
					$this->Cell($this->ColumnWidth, $rowtable, number_format($objphysician->profcharges,2,'.',','), 1, 0, 'R');
					$this->Cell($this->ColumnWidth, $rowtable, (($objphysician->claim_physician != 0) ? number_format($objphysician->claim_physician,2,'.',',') : ""), 1, 0, 'R');
					$this->Cell($this->ColumnWidth, $rowtable, (($objphysician->claim_patient != 0) ? number_format($objphysician->claim_patient,2,'.',',') : ""), 1, 0, 'R');

					$this->total_pf_charge += $objphysician->claim_physician;

					$bdetails_added = true;

					break;
				}
			}

			if ($objphysician->role_area != 'D4') {
//               $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
				 $this->SetY($this->lastypos);

				 //Name of Attending Physician
				 $this->SetX($this->x1 + $this->GetStringWidth("16. "));
				 $length_name = $this->GetStringWidth($objphysician->name);
				 $this->Cell($length_name, $this->RowHeight+2, $objphysician->name, 0, 0, 'L');

				 $this->Ln($this->RowHeight * 1.75);

				 //PHIC accreditaion No.
				 $this->SetX($this->GetStringWidth("17. PHIC Accreditation No. "));
				 $length_acc = $this->GetStringWidth($objphysician->accnum);
				 $this->Cell($length_acc, $this->RowHeight, $objphysician->accnum, 0, 0, 'L');

				 //BIR/TIN No.
				 $this->SetX(($this->x1+81)+$this->GetStringWidth("18. BIR/TIN No. "));
				 $length_bir = $this->GetStringWidth($objphysician->bir_tin_num);
				 $this->Cell($length_bir, $this->RowHeight, $objphysician->bir_tin_num, 0, 0, 'L');

				 $this->Ln(1);
				 $this->Ln(($this->RowHeight * 1.5));

				//Services Performed
				 $length = $this->GetStringWidth($services);
				 $this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
				 if($length <= $this->serv_space){
					 $this->Cell($length, $this->RowHeight, $services, 0, 0, 'L');
				 }
				 else{
					 $this->MultiCell($this->serv_space, $this->RowHeight-1, $services, 0,0, 'L');
				 }
			}
		}

		if (!$bdetails_added) {
			$this->Rect($this->x1+80, $table_y+($this->RowHeight * 0.5), $this->ColumnWidth, $rowtable);
			$this->Rect($this->x1+107, $table_y+($this->RowHeight * 0.5), $this->ColumnWidth, $rowtable);
			$this->Rect($this->x1+134, $table_y+($this->RowHeight * 0.5), $this->ColumnWidth, $rowtable);

			$this->SetY($this->lastypos);
			$this->Ln($this->RowHeight+1);
			$this->Ln(($this->RowHeight * 2.25));
			$this->Ln($this->RowHeight-2.25);
		}

		$this->Ln();

		$this->addSurgeon("21. Name of Surgeon",
				 "Signature & Date Signed",
				 "22. PHIC Accreditation No.",
				 "23. BIR/TIN No.",
				 "24. Services Performed:",
				 "Date of Operation",
				 10);
	 }

	function addSurgeon($name, $sig, $acc, $bir, $serv, $op, $height){
		if ($this->CheckPageBreak($this->RowHeight * 8))
			$this->SetY($this->tMargin);
		else
			$this->SetY($this->GetY()-0.39);

		$this->y1 = $this->GetY();
		$ypos = $this->y1;

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);

		 //$this->Rect($this->x1, $this->y1+201, 161, 8);
		$this->Rect($this->x1, $ypos+1.5, 161, ($this->RowHeight * 2.5));
		$this->Ln($this->RowHeight-2);
		//16. Name of Attending Physician
		//$this->SetXY($this->x1, $this->y1+202);
		$this->SetX($this->x1);
		//$this->Line(4);
		$length = $this->GetStringWidth($name);
		$this->Cell($length, $this->RowHeight-2, $name);

		//Signature & Date Signed
		$this->SetX($this->x1+110);
		$new_y = $this->GetY();

		$new_y += ($this->RowHeight * 2);

		//$this->Line($this->x1+108, $this->y1+208.5, $this->x1+155, $this->y1+208.5);
		$this->Line($this->x1+108, $new_y, $this->x1+155, $new_y);
		$length = $this->GetStringWidth($sig);
		$this->Cell($length, $this->RowHeight-2, $sig, 0, 1);

		$this->lastypos = $this->GetY();
		$this->Ln($this->RowHeight-1);
		$new_y = $this->GetY()+4.2;

		$this->SetY($new_y);

		//PHIC Accreditation No.
		//$this->Rect($this->x1, $this->y1+209, 80, 4);
		//$this->SetXY($this->x1, $this->y1+210);
		$this->Rect($this->x1, $new_y, 80, $this->RowHeight);
		$this->SetX($this->x1);
		$length = $this->GetStringWidth($acc);
		$this->Cell($length, $this->RowHeight, $acc);
		$l_accnum = $length + 1;

		//BIR/TIN No.
		//$this->Rect($this->x1+80, $this->y1+209, 81, 4);
		$this->Rect($this->x1+80, $new_y, 81, $this->RowHeight);
		$this->SetX($this->x1+80);
		$length = $this->GetStringWidth($bir);
		$this->Cell($length, $this->RowHeight, $bir);

		$this->Ln($this->RowHeight);
		$new_y = $this->GetY();

		//Services Performed
		$new_y2 = $this->GetY();
		$this->SetFontSize($this->fontsize_servperform);
		//$this->Rect($this->x1, $this->y1+213, 80, 10);
		//$this->SetXY($this->x1, $this->y1+214);
//        $this->Rect($this->x1, $new_y2, 80, ($this->RowHeight * 2));
		$this->SetX($this->x1);
		$length = $this->GetStringWidth($serv);
		$this->Cell($length, $this->RowHeight, $serv);
		$lserv = 80;
		//Table
		$this->SetFontSize($this->fontsize_table_note);
		$this->SetX($this->x1+80);
		$this->Cell(27, $this->RowHeight, "25.    Actual", "R", 0, 'L');
		$this->Cell(54, $this->RowHeight * 0.75, "Benefit Claim", "B", 0, 'C');
		$this->Ln($this->RowHeight - 1);
		$table_y = $this->GetY();
		$this->SetX($this->x1+80);
		$this->Cell(27, $this->RowHeight * 0.5, "Professional Charges", "BR", 0, 'L');
		$this->SetX($this->x1+107);
		$this->Cell(27, $this->RowHeight * 0.5, "Physician", "BR", 0, 'C');
		$this->Cell(27, $this->RowHeight * 0.5, "Patient", "BR", 0, 'C');

//        $this->Rect($this->x1+107, $this->GetY()-0.5, 27, $this->RowHeight * 0.575);

		$this->ColumnWidth = 27;
		$rowtable = $this->RowHeight * 0.75;

		$bhasinfo = false;

		if(!empty($this->surgeon_array)) {
			$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);

			$this->Ln($this->RowHeight * 0.5);

			$services = "";
//			foreach($this->surgeon_array as $objsurgeon) {
//				$services .= (($services != '') ? " " : "").$objsurgeon->servperformance;
//			}

//			$length = $this->GetStringWidth($services);

			//-------            if($length > $this->serv_space) $yadj = ceil(ceil($length)/$this->serv_space) + 1;
//			if($length > $this->serv_space) {
			//-------				$yadj = ceil(ceil($length)/$this->serv_space);
//				$nb = $this->NbLines($this->serv_space, $services);
//				$yadj = $nb;
//			}
//			else {
//				$yadj = 1;
//				$nb = 1;
//			}

//			reset($this->surgeon_array);
			$this->SetX($this->x1+80);
			$yadj = 1;
			foreach($this->surgeon_array as $objsurgeon) {
				$bRemoveObj = true;
				//Table
//				$this->Cell($this->ColumnWidth, $rowtable+($yadj * $this->RowHeight), number_format($objsurgeon->profcharges,2,'.',','), 1, 0, 'R');
//				$this->Cell($this->ColumnWidth, $rowtable+($yadj * $this->RowHeight), number_format($objsurgeon->claim_physician,2,'.',','), 1, 0, 'R');
//				$this->Cell($this->ColumnWidth, $rowtable+($yadj * $this->RowHeight), number_format($objsurgeon->cliam_patient,2,'.',','), 1, 0, 'R');

				$services .= (($services != '') ? " " : "").$objsurgeon->servperformance;
				$length = $this->GetStringWidth($services);
				if($length > $this->serv_space) {
//					$nb = $this->NbLines($this->serv_space, $services);
					$yadj = $this->NbLines($this->serv_space, $services);
					if ($yadj >= 5) {
						$services = "(Please refer to attached page.)";
						$yadj = 1;
						$bRemoveObj = false;
					}
				}
				else {
					$yadj = 1;
//					$nb = 1;
				}

				$this->Cell($this->ColumnWidth, $rowtable + ($yadj + 1.5), number_format($objsurgeon->profcharges,2,'.',','), 1, 0, 'R');
				$this->Cell($this->ColumnWidth, $rowtable + ($yadj + 1.5), number_format($objsurgeon->claim_physician,2,'.',','), 1, 0, 'R');
				$this->Cell($this->ColumnWidth, $rowtable + ($yadj + 1.5), number_format($objsurgeon->claim_patient,2,'.',','), 1, 0, 'R');

				$this->total_pf_charge += $objsurgeon->claim_physician;
				if ($bRemoveObj) array_shift($this->surgeon_array);		// Remove surgeon instance already printed in form.

				break;
			}

			$this->SetY($this->lastypos);

			//Name of Surgeon
			$this->SetX($this->x1 + $this->GetStringWidth("21. "));
			$length_name = $this->GetStringWidth($objsurgeon->name);
			$this->Cell($length_name, $this->RowHeight+1, $objsurgeon->name, 0, 0, 'L');
			//$this->Ln(4);
			//PHIC accreditaion No.
			$this->Ln($this->RowHeight * 1.75);

			$this->SetX($this->x1 + $l_accnum);
			$length_acc = $this->GetStringWidth($objsurgeon->accnum);
			$this->Cell($length_acc, $this->RowHeight, $objsurgeon->accnum, 0, 0, 'L');
			//BIR/TIN No.

			$this->SetX(($this->x1+81) + $this->GetStringWidth($bir));
			$length_bir = $this->GetStringWidth($objsurgeon->bir_tin_num);
			$this->Cell($length_bir, $this->RowHeight, $objsurgeon->bir_tin_num, 0, 0, 'L');
//            $this->Ln(8);
			//Services Performed

			$this->Ln(1);
			$this->Ln(($this->RowHeight * 1.5));

//            $this->Ln(($this->RowHeight * 2)-1);

			$this->SetFontSize(7);
			$this->SetX($this->x1 + $this->GetStringWidth("24. "));
//            if($length <= $this->serv_space){
//                $this->Cell($length, $this->RowHeight-2, $services, 0, 0, 'L');
//            }
//            else{
			$this->MultiCell($this->serv_space, $this->RowHeight * 0.6, $services, 0,'L');
//			$this->MultiCell($this->serv_space, $nb * 1, $services, 0,'L');
//            }
			$this->SetFontSize($this->fontsize_table_note);

			//Date of Operation
			//$this->SetXY($this->x1, $this->y1+242+($yadj * $this->h_cell));
//			$this->SetXY($this->x1, $new_y +($yadj * $this->RowHeight) + 2);
			$this->SetXY($this->x1, $new_y2 + ($yadj + $this->RowHeight + 1));
			$length = $this->GetStringWidth($op);
			$this->Cell($length, $this->RowHeight, $op);
			$l_opdate = $length + 2;

			//$this->SetXY($this->x1+$l_opdate, $this->y1+242+($yadj * $this->h_cell));
//            $this->SetXY($this->x1+$l_opdate, ($new_y+12)+($yadj * $this->h_cell));
			$this->SetX($this->x1+$l_opdate);
			$this->Cell($length, $this->RowHeight, $objsurgeon->operation_dt, 0, 0, 'L');

			$this->Rect($this->x1, $new_y+0.1, 80, ($yadj + $this->RowHeight + 1) + (($this->RowHeight * 0.6) - 2) + ($this->RowHeight * 1.5) - 2.4);

			if ($yadj == 0) $this->SetXY($this->GetX(), $this->GetY()-2);
//			$yadj += (($this->RowHeight * 0.6) - 2);

			if ($yadj <= 2) $yadj = 3;
			if ($yadj > 3)
				$yadj = (($this->RowHeight * 0.6) - 2)  + ($this->RowHeight * 1.5) - 2;
			else
				$yadj += 2;

			$bhasinfo = true;
		}
		else {
			$this->Rect($this->x1, $new_y2+0.1, 80, ($this->RowHeight * 2)+0.1);

			$this->Rect($this->x1+80, $table_y+($this->RowHeight * 0.5), $this->ColumnWidth, $rowtable);
			$this->Rect($this->x1+107, $table_y+($this->RowHeight * 0.5), $this->ColumnWidth, $rowtable);
			$this->Rect($this->x1+134, $table_y+($this->RowHeight * 0.5), $this->ColumnWidth, $rowtable);

			$this->SetY($this->lastypos);
			$this->Ln($this->RowHeight+1);
			$this->Ln(($this->RowHeight * 2.4));
			$this->Ln($this->RowHeight - 2.25);

			$yadj = $this->RowHeight + 1;
		}

		$this->Ln();

		$this->addPhilUse("Relative Unit Value", "Illness Code", "Reduction Code", 2, $ypos+1.5, $yadj, $bhasinfo);
	}

	function addAnesthesiologist($name, $sig, $acc, $bir, $serv) {
//        if ($this->CheckPageBreak($this->RowHeight * 8))
//            $this->SetY($this->tMargin);
//        else
			$this->SetY($this->GetY()-0.9);

		$this->y1 = $this->GetY();
		$ypos = $this->y1;

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);

		 //$this->Rect($this->x1, $this->y1+201, 161, 8);
		$this->Rect($this->x1, $ypos+1.5, 161, ($this->RowHeight * 2.5));
		$this->Ln($this->RowHeight-2);
		//16. Name of Attending Physician
		//$this->SetXY($this->x1, $this->y1+202);
		$this->SetX($this->x1);
		//$this->Line(4);
		$length = $this->GetStringWidth($name);
		$this->Cell($length, $this->RowHeight-2, $name);

		//Signature & Date Signed
		$this->SetX($this->x1+110);
		$new_y = $this->GetY();

		$new_y += ($this->RowHeight * 2);

		//$this->Line($this->x1+108, $this->y1+208.5, $this->x1+155, $this->y1+208.5);
		$this->Line($this->x1+108, $new_y, $this->x1+155, $new_y);
		$length = $this->GetStringWidth($sig);
		$this->Cell($length, $this->RowHeight-2, $sig, 0, 1);

		$this->lastypos = $this->GetY();
		$this->Ln($this->RowHeight-1);
		$new_y = $this->GetY()+4.2;

		$this->SetY($new_y);

		//PHIC Accreditation No.
		//$this->Rect($this->x1, $this->y1+209, 80, 4);
		//$this->SetXY($this->x1, $this->y1+210);
		$this->Rect($this->x1, $new_y, 80, $this->RowHeight);
		$this->SetX($this->x1);
		$length = $this->GetStringWidth($acc);
		$this->Cell($length, $this->RowHeight, $acc);
		$l_accnum = $length + 1;

		//BIR/TIN No.
		//$this->Rect($this->x1+80, $this->y1+209, 81, 4);
		$this->Rect($this->x1+80, $new_y, 81, $this->RowHeight);
		$this->SetX($this->x1+80);
		$length = $this->GetStringWidth($bir);
		$this->Cell($length, $this->RowHeight, $bir);

		$this->Ln($this->RowHeight);
		$new_y = $this->GetY();

		//Services Performed
		$new_y2 = $this->GetY();
		$this->SetFontSize($this->fontsize_servperform);
		//$this->Rect($this->x1, $this->y1+213, 80, 10);
		//$this->SetXY($this->x1, $this->y1+214);
//        $this->Rect($this->x1, $new_y2, 80, ($this->RowHeight * 2));
		$this->SetX($this->x1);
		$length = $this->GetStringWidth($serv);
		$this->Cell($length, $this->RowHeight, $serv);
		$lserv = 80;
		//Table
		$this->SetFontSize($this->fontsize_table_note);
		$this->SetX($this->x1+80);
		$this->Cell(27, $this->RowHeight, "30.    Actual", "R", 0, 'L');
		$this->Cell(54, $this->RowHeight * 0.75, "Benefit Claim", "B", 0, 'C');
		$this->Ln($this->RowHeight - 1);
		$table_y = $this->GetY();
		$this->SetX($this->x1+80);
		$this->Cell(27, $this->RowHeight * 0.5, "Professional Charges", "BR", 0, 'L');
		$this->SetX($this->x1+107);
		$this->Cell(27, $this->RowHeight * 0.5, "Physician", "BR", 0, 'C');
		$this->Cell(27, $this->RowHeight * 0.5, "Patient", "BR", 0, 'C');

//        $this->Rect($this->x1+107, $this->GetY()-0.5, 27, $this->RowHeight * 0.575);

		$columnwidth = 27;
		$row = 2;
		$row_multi = 3;
		$rowtable = $this->RowHeight * 0.75;

		$bdetails_added = false;
		$bhasinfo = false;
		if(!empty($this->anesth_array)) {
			$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
			$this->Ln($this->RowHeight * 0.5);
			$services = "";
//			foreach($this->healthperson_array as $objsurgeon){
//				if ($objperson->role_area == 'D4') $services .= (($services != '') ? " " : "").$objsurgeon->servperformance;
//			}

//			$length = $this->GetStringWidth($services);
//			$yadj = 0;
//			if($length > $this->serv_space) $yadj = ceil(ceil($length)/$this->serv_space);

//            $this->SetXY($this->x1+80, $this->y1+18);
			$yadj = 1;
			$this->SetX($this->x1 + 80);
			foreach($this->anesth_array as $objperson) {
				$bRemoveObj = true;
//				if ($objperson->role_area == 'D4') {
					$services .= (($services != '') ? " " : "").$objperson->servperformance;
					$length = $this->GetStringWidth($services);
//					$yadj = 0;
//					if($length > $this->serv_space) $yadj = ceil(ceil($length)/$this->serv_space);

					if($length > $this->serv_space) {
//						$nb = $this->NbLines($this->serv_space, $services);
						$yadj = $this->NbLines($this->serv_space, $services);
						if ($yadj >= 5) {
							$services = "(Please refer to attached page.)";
							$yadj = 1;
							$bRemoveObj = false;
						}
					}
					else {
						$yadj = 1;
//						$nb = 1;
					}

					$this->bNoPageBreak = true;

					//Table
//					$this->Cell($columnwidth, $rowtable+($yadj * $this->RowHeight), number_format($objperson->profcharges,2,'.',','), 1, 0, 'R');
//					$this->Cell($columnwidth, $rowtable+($yadj * $this->RowHeight), number_format($objperson->claim_physician,2,'.',','), 1, 0, 'R');
//					$this->Cell($columnwidth, $rowtable+($yadj * $this->RowHeight), number_format($objperson->cliam_patient,2,'.',','), 1, 0, 'R');

					$this->Cell($columnwidth, $rowtable+($yadj + 1.5), number_format($objperson->profcharges,2,'.',','), 1, 0, 'R');
					$this->Cell($columnwidth, $rowtable+($yadj + 1.5), number_format($objperson->claim_physician,2,'.',','), 1, 0, 'R');
					$this->Cell($columnwidth, $rowtable+($yadj + 1.5), number_format($objperson->cliam_patient,2,'.',','), 1, 0, 'R');

					$this->total_pf_charge += $objperson->claim_physician;

					$bdetails_added = true;

					$this->bNoPageBreak = false;

					if ($bRemoveObj) array_shift($this->anesth_array);		// Remove surgeon instance already printed in form.

					break;
//				}
			}

//			die('No. of lines'.$this->NbLines($this->serv_space, $services));

//			if ($objperson->role_area == 'D4') {
				$this->SetY($this->lastypos);

				//Name of Anesthesiologist
//                $this->SetXY($this->x1, $this->y1+5);
				$this->SetX($this->x1 + $this->GetStringWidth("26. "));
				$length_name = $this->GetStringWidth($objperson->name);
				$this->Cell($length_name, $this->RowHeight+1, $objperson->name, 0, 0, 'L');

				$this->Ln($this->RowHeight * 1.75);

				//PHIC accreditaion No.
				$this->SetX($this->x1 + $l_accnum);
				$length_acc = $this->GetStringWidth($objperson->accnum);
				$this->Cell($length_acc, $this->RowHeight, $objperson->accnum, 0, 0, 'L');
				//BIR/TIN No.
				$this->SetX(($this->x1+81)+$this->GetStringWidth($bir));
				$length_bir = $this->GetStringWidth($objperson->bir_tin_num);
				$this->Cell($length_bir, $this->RowHeight, $objperson->bir_tin_num, 0, 0, 'L');

				$this->Ln(1);
				$this->Ln(($this->RowHeight * 1.5));

				//Services Performed
				$this->SetFontSize(7);
				$this->SetX($this->x1 + $this->GetStringWidth("29. "));
//				$length = $this->GetStringWidth($services);
//				if($length <= $this->serv_space){
//					$this->Cell($length, $this->RowHeight-2, $services,0,0,'L');
//				}
//				else{
					$this->MultiCell($this->serv_space, $this->RowHeight * 0.6, $services, 0, 'L');
//				}

				$this->SetFontSize($this->fontsize_table_note);

				$this->SetXY($this->x1, $new_y +($yadj * $this->RowHeight) + 2);
//				$this->Rect($this->x1, $new_y, 80, (($yadj + 2) * $this->RowHeight)+0.1);
				$this->Rect($this->x1, $new_y+0.1, 80, ($yadj + $this->RowHeight + 1) + (($this->RowHeight * 0.6) - 2) + ($this->RowHeight * 1.5) - 2.4);

				if ($yadj == 0) $this->SetXY($this->GetX(), $this->GetY()-2);
//			$yadj += (($this->RowHeight * 0.6) - 2);

				if ($yadj <= 2) $yadj = 3;
				if ($yadj > 3)
					$yadj = (($this->RowHeight * 0.6) - 2)  + ($this->RowHeight * 1.5) - 2;
				else {
					$yadj += 2;
				}

//				$yadj += 3;
//				$this->Ln($this->RowHeight-1);

				$bhasinfo = true;
//			}
		}

		if (!$bdetails_added) {
			$this->Rect($this->x1, $new_y2, 80, ($this->RowHeight * 2)+0.1);

			$this->Rect($this->x1+80, $table_y+($this->RowHeight * 0.5), $this->ColumnWidth, $rowtable);
			$this->Rect($this->x1+107, $table_y+($this->RowHeight * 0.5), $this->ColumnWidth, $rowtable);
			$this->Rect($this->x1+134, $table_y+($this->RowHeight * 0.5), $this->ColumnWidth, $rowtable);

			$this->SetY($this->lastypos);
			$this->Ln($this->RowHeight+1);
			$this->Ln(($this->RowHeight * 2.25));
			$this->Ln($this->RowHeight-2.25);

			$yadj = $this->RowHeight + 1;
//			$yadj = 3;
		}

		$this->addPhilUse("Relative Unit Value", "Illness Code", "Reduction Code", 3, $ypos+1.5, $yadj, $bhasinfo);
	}

	function addNote($note) {
		//modified by Cherry 04-22-09
		$this->bNoPageBreak = true;
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_table_note);
		//NOTE
		//$this->SetY($this->y1+267);
		$ypos = $this->GetY()+4;
		$this->SetY($ypos);
		$length = $this->GetStringWidth($note);
		$this->MultiCell(201, 2, $note);
		$this->bNoPageBreak = false;
	}

	function AcceptPageBreak() {
		return (!$this->bNoPageBreak);
	}

	function addPhilUse($rel_unit, $icode, $rcode, $section = 0, $ypos, $adj = 0, $bwithinfo = false, $addedAdj = 0){
		$origx = $this->GetX();
		$origy = $this->GetY();

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_philuse);

		//For Philhealth Use
		switch ($section) {
			case 1:
				$this->SetXY($this->x1+161, $ypos);
				$this->Cell(40, $this->RowHeight, "FOR PHILHEALTH", "TR", 0, 'C');
				$this->Ln($this->RowHeight-2);
				$this->SetX($this->x1+161);
				$this->Cell(40, $this->RowHeight, "USE", "BR", 0, 'C');

				$this->Ln($this->RowHeight);
				$ypos = $this->GetY();

				//Relative Unit Value
				$this->SetX($this->x1+161);
				$this->Cell(40, $this->RowHeight, $rel_unit, 0, 0,'L');
				$this->Ln(($this->RowHeight * 4)-1);
				$this->Rect($this->x1+161, $ypos, 40, ($this->RowHeight * 4)-0.5);

				$ypos = $this->GetY()+0.5;

				//Illness Code
				$this->SetX($this->x1+161);
				$this->Cell(40, $this->RowHeight, $icode, "R", 1, 'L');
				$this->Ln($this->RowHeight * 1.5);
				$this->Rect($this->x1+161, $ypos, 40, ($this->RowHeight * 2.5)-0.1);

				$ypos += ($this->RowHeight * 2.5)-0.1;

				//Reduction Code for Attending Physician
				$this->SetX($this->x1+161);
				$this->Cell(40, $this->RowHeight, $rcode, "R", 1, 'L');
//              $this->Rect($this->x1+161, $ypos, 40, ($this->RowHeight - 1) + ($this->RowHeight));
				$this->Rect($this->x1+161, $ypos, 40, ($this->RowHeight * 1.7)+0.1);

				$ypos += ($this->RowHeight * 1.7)+0.1;

				$this->Rect($this->x1+161, $ypos, 40, ($this->RowHeight * 0.75) + ($this->RowHeight * 0.5)+0.1);
//              $this->Rect($this->x1+161, $ypos, 40, ($this->RowHeight * 1.3));
				break;

		case 2:
				$n = 0;

				$this->SetXY($this->x1+161, $ypos);
				$this->Cell(40, $this->RowHeight, $rcode, 0,0, 'L');
				$this->Line($this->x1+161, $ypos+($this->RowHeight * 2.5), $this->x1+201, $ypos+($this->RowHeight * 2.5));

				$this->Rect($this->x1+161, $ypos, 40, ($bwithinfo ? ($this->RowHeight * 2.5) : 0) + ($this->RowHeight * $adj)+$n);
				break;

		case 3:
				$n = 0.1;
				//Reduction Code for Surgeon and Anesthesiologist
//              $this->Line($this->x1+161, $ypos, $this->x1+201, $ypos);
				$this->SetXY($this->x1+161, $ypos);
				$this->Cell(40, $this->RowHeight, $rcode, 0,0, 'L');
				$this->Line($this->x1+161, $ypos+($this->RowHeight * 2.5), $this->x1+201, $ypos+($this->RowHeight * 2.5));

				$this->Rect($this->x1+161, $ypos, 40, ($bwithinfo ? (($this->RowHeight * 1) + 0.2 + $addedAdj) : 0) + ($this->RowHeight * $adj)+$n);
//				$this->Rect($this->x1+161, $ypos, 40, ($bwithinfo ? ($this->RowHeight * 1.5) : 0) + ($this->RowHeight * $adj)+$n);
				if ($bwithinfo) $origy = $origy + 2;
				break;

//        case 3:
				//Reduction Code for Anesthesiologist
//              $this->Line($this->x1+161, $ypos, $this->x1+201, $ypos);
//              $this->SetXY($this->x1+161, $ypos+1);
//              $this->Cell(40, $this->h_cell, $rcode, 0,0, 'L');
//              $this->Line($this->x1+161, $ypos+($this->RowHeight * 2), $this->x1+201, $ypos+($this->RowHeight * 2));
//
//              $this->Rect($this->x1+161, $ypos, 40, $this->RowHeight * $adj);
//              break;
		}

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		$this->SetXY($origx, $origy);
	 }

	function addPart3($title) {
		$note = "NOTE: Official Receipts for drugs and medicines purchased by patient must be attached to this claim.";
		$total_actual = 0;
		$total_hospital = 0;
		$total_patient = 0;

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		$this->Cell(0, $this->RowHeight, $title, 0, 1, 'C');

		$this->ColumnWidth = array(43,40,20,10,17,31,40);
		$this->SetWidths(array(43,40,20,10,17,31,20,20));

		$length = $this->ColumnWidth[0] + $this->ColumnWidth[1] + $this->ColumnWidth[2] + $this->ColumnWidth[3];
		$length2 = $length + $this->ColumnWidth[4];
		$length = $length + $this->ColumnWidth[4] + $this->ColumnWidth[5] + $this->lMargin;

		$oldRowHeight = $this->RowHeight;
		$this->RowHeight = $this->AdjustedRowHeight;

//      $this->RowHeight = 5.5;
		# Print table header
		$rowtable = $this->RowHeight-1;
		$this->form_part = "Drugs and Medicines";
		$this->PrintPart3Header();

		if (!empty($this->meds_array)) {
//		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer2);
			foreach ($this->meds_array as $v) {
				$total_actual += $v->actual_charges;
				$total_hospital += $v->claim_hospital;
				$total_patient += $v->claim_patient;
			//$this->Row(array($test1, $v->brand, $v->preparation, $v->qty, $v->unit_price, $v->actual_charges, $v->claim_hospital, $v->claim_patient));
				$this->Row(array(strtoupper($v->gen_name), $v->brand, $v->preparation, $v->qty, $v->unit_price, $v->actual_charges, $v->claim_hospital, $v->claim_patient));
				//$this->Cell($this->ColumnWidth[0],$rowtable,$v->gen_name,1,0,'L');
				//$this->Cell($this->ColumnWidth[1],$rowtable,$v->brand,1,0,'L');
				//$this->Cell($this->ColumnWidth[2], $rowtable, $v->preparation,1,0,'C');
				//$this->Cell($this->ColumnWidth[3],$rowtable,$v->qty,1,0,'C');
				//$this->Cell($this->ColumnWidth[4],$rowtable,$v->unit_price,1,0,'C');
				//$this->Cell($this->ColumnWidth[5],$rowtable,$v->actual_charges,1,0,'C');
				//$this->Cell($this->ColumnWidth[6]/2, $rowtable, $v->claim_hospital,1,0,'C');
				//$this->Cell($this->ColumnWidth[6]/2, $rowtable, $v->claim_patient, 1, 0, 'C');
				//$this->Ln();
			}

			//Total
			$this->SetFont($this->fontfamily_label, $this->fontstyle_answer, $this->fontsize_label);
//			$this->Cell($length2, $rowtable, "Total", 1, 0, 'R');
			$this->Cell($length2, $rowtable, "T O T A L", 'T', 0, 'C');
			$this->SetFont($this->fontfamily_label, $this->fontstyle_answer, $this->fontsize_answer2);
			$this->Cell($this->ColumnWidth[5], $rowtable, number_format($total_actual,2,'.',','), 'T', 0, 'R');
			$this->Cell($this->ColumnWidth[6]/2, $rowtable,number_format($total_hospital,2,'.',','), 'T', 0, 'R');
			$this->Cell($this->ColumnWidth[6]/2, $rowtable,(($total_patient == 0) ? " " : number_format($total_patient,2,'.',',')) , 'T', 0, 'R');

			$this->Ln();
//			$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label3);
			$this->bNoPageBreak = true;
			$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_table_note);
			$this->Cell($this->tablewidth, $rowtable, $note, 1, 0, 'L');
			$this->bNoPageBreak = false;
			$this->Ln(5);
		}
		else{
			$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
			$this->SetFillColor(255);
			$this->SetTextColor(0);
			$this->Cell($this->tablewidth, $this->RowHeight, " ", 1, 0);
			$this->Ln(6);
		//$this->Cell(0, $this->RowHeight, "No records found for this report...", 0, 1, 'L');
		}

		$this->RowHeight = $oldRowHeight;
	}

	function addPart4($title){
		$this->total_rlo_charges  = 0;
		$this->total_rlo_hospital = 0;
		$this->total_rlo_patient  = 0;

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);

		$this->x2 = $this->GetX();
		$this->y2 = $this->GetY();
//        $this->SetXY($this->x2+($this->half/2), $this->y2+1);
//        $length = $this->GetStringWidth($title);
		$this->Cell(0, $this->h_cell, $title, 0, 0, "C");
		$this->Ln(3);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label2, $this->fontsize_label3);
		$partA = "A. X-ray/Laboratory";
//		$length = $this->GetStringWidth($partA);
//		$this->Cell($length, $this->RowHeight, $partA);
//		$this->Ln($this->RowHeight-1);

	 $this->ColumnWidth = array(88,20,20,33,40);
	 $this->SetWidths(array(88,20,20,33,20,20));
	 # Print table header

		$oldRowHeight = $this->RowHeight;
		$this->RowHeight = $this->AdjustedRowHeight;

		$rowtable = $this->RowHeight-1;

//		$this->SetFont($this->fontfamily_label,$this->fontstyle_label,$this->fontsize_label3);
//		if ($this->colored) $this->SetFillColor(0xED);
//		$this->SetTextColor(0);
//		$rowheader=8;
//		$rowtable = $this->RowHeight-1;
//		$length = $this->ColumnWidth[0] + $this->ColumnWidth[1] + $this->ColumnWidth[2] + $this->ColumnWidth[3]+ $this->lMargin;
//		$this->Cell($this->ColumnWidth[0],$rowheader,'Particulars',1,0,'C');
//		$this->Cell($this->ColumnWidth[1],$rowheader,'Qty',1,0,'C');
//		$this->Cell($this->ColumnWidth[2],$rowheader,'UNIT PRICE',1,0,'C');
//		$this->Cell($this->ColumnWidth[3],$rowheader,'ACTUAL CHARGES',1,0,'C');
//		$this->Cell($this->ColumnWidth[4], $rowheader/2, 'Benefit Claim',1,0,'C');
//		$this->Ln();
//		$this->SetX($length);
//		$this->Cell($this->ColumnWidth[4]/2, $rowheader/2, 'Hospital',1,0,'C');
//		$this->Cell($this->ColumnWidth[4]/2, $rowheader/2, 'Patient', 1, 0, 'C');
//		$this->Ln();

//		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer2);

		$this->form_part = $partA;
		$this->CheckPageBreak($this->RowHeight + 8);
		$length = $this->GetStringWidth($partA);
		$this->Cell($length, $this->RowHeight, $partA);
		$this->Ln($this->RowHeight-1);
		$this->PrintPart4Header();

		if (!empty($this->lab_array)) {
			foreach ($this->lab_array as $objlab) {
				$this->RowPart4(array(strtoupper($objlab->particulars), $objlab->qty, $objlab->unit_price,$objlab->actual_charges,$objlab->claim_hospital,$objlab->claim_patient));
				//$this->Cell($this->ColumnWidth[0],$rowtable,$objlab->particulars,1,0,'C');
				//$this->Cell($this->ColumnWidth[1],$rowtable,$objlab->qty,1,0,'C');
				//$this->Cell($this->ColumnWidth[2],$rowtable,$objlab->unit_price,1,0,'C');
				//$this->Cell($this->ColumnWidth[3],$rowtable,$objlab->actual_charges,1,0,'C');
				//$this->Cell($this->ColumnWidth[4]/2, $rowtable, $objlab->claim_hospital,1,0,'C');
				//$this->Cell($this->ColumnWidth[4]/2, $rowtable, $objlab->claim_patient, 1, 0, 'C');
				//$this->Ln();

				$this->total_rlo_charges  += $objlab->actual_charges;
				$this->total_rlo_hospital += $objlab->claim_hospital;
				$this->total_rlo_patient  += $objlab->claim_patient;
			}
		}

		 else{
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		$this->SetFillColor(255);
		$this->SetTextColor(0);
		$this->Cell($this->tablewidth, $this->RowHeight, " ", 1, 0);
		$this->Ln(6);
		//$this->Cell(0, $this->RowHeight, "No records found for this report...", 0, 1, 'L');
		 }

		 $this->RowHeight = $oldRowHeight;
	 }

	function PrintPart3Header($part = '') {
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label3);
		if ($part != '') $this->Cell(0, $this->RowHeight, $part." (continued)", 0, 1, "L");
		if ($this->colored) $this->SetFillColor(0xED);
		$this->SetTextColor(0);
		$rowheader=8;
		$length = $this->ColumnWidth[0] + $this->ColumnWidth[1] + $this->ColumnWidth[2] + $this->ColumnWidth[3];
		$length2 = $length + $this->ColumnWidth[4];
		$length = $length + $this->ColumnWidth[4] + $this->ColumnWidth[5] + $this->lMargin;

		$this->Cell($this->ColumnWidth[0],$rowheader,'Generic Name',1,0,'C');
		$this->Cell($this->ColumnWidth[1],$rowheader,'Brand',1,0,'C');
		$this->Cell($this->ColumnWidth[2],$rowheader, 'Preparation',1,0,'C');
		$this->Cell($this->ColumnWidth[3],$rowheader,'Qty',1,0,'C');
		$this->Cell($this->ColumnWidth[4],$rowheader,'UNIT PRICE',1,0,'C');
		$this->Cell($this->ColumnWidth[5],$rowheader,'ACTUAL CHARGES',1,0,'C');
		$this->Cell($this->ColumnWidth[6], $rowheader/2, 'Benefit Claim',1,0,'C');
		$this->Ln();
		$this->SetX($length);
		$this->Cell($this->ColumnWidth[6]/2, $rowheader/2, 'Hospital',1,0,'C');
		$this->Cell($this->ColumnWidth[6]/2, $rowheader/2, 'Patient', 1, 0, 'C');
		$this->Ln();
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer2);
	}

	function PrintPart4Header($part = '') {
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label3);
		if ($part != '') $this->Cell(0, $this->RowHeight, $part." (continued)", 0, 1, "L");
		if ($this->colored) $this->SetFillColor(0xED);
		$this->SetTextColor(0);
		$rowheader=8;
//		$rowtable = $this->RowHeight-1;
		$length = $this->ColumnWidth[0] + $this->ColumnWidth[1] + $this->ColumnWidth[2] + $this->ColumnWidth[3]+ $this->lMargin;
		$this->Cell($this->ColumnWidth[0],$rowheader,'Particulars',1,0,'C');
		$this->Cell($this->ColumnWidth[1],$rowheader,'Qty',1,0,'C');
		$this->Cell($this->ColumnWidth[2],$rowheader,'UNIT PRICE',1,0,'C');
		$this->Cell($this->ColumnWidth[3],$rowheader,'ACTUAL CHARGES',1,0,'C');
		$this->Cell($this->ColumnWidth[4], $rowheader/2, 'Benefit Claim',1,0,'C');
		$this->Ln();
		$this->SetX($length);
		$this->Cell($this->ColumnWidth[4]/2, $rowheader/2, 'Hospital',1,0,'C');
		$this->Cell($this->ColumnWidth[4]/2, $rowheader/2, 'Patient', 1, 0, 'C');
		$this->Ln();
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer2);
	}

	function addSupplies(){
//     $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		$this->x2 = $this->GetX();
		$this->y2 = $this->GetY();
		$this->SetXY($this->x2, $this->y2+1);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label2, $this->fontsize_label3);
		$partB = "B. Supplies";
//		$length = $this->GetStringWidth($partB);
//		$this->Cell($length, $this->RowHeight, $partB);
//		$this->Ln($this->RowHeight-1);

		$this->ColumnWidth = array(88,20,20,33,40);
		$this->SetWidths(array(88,20,20,33,20,20));
		# Print table header
		$oldRowHeight = $this->RowHeight;
		$this->RowHeight = $this->AdjustedRowHeight;

		$rowtable = $this->RowHeight-1;
		$this->form_part = $partB;
		$this->CheckPageBreak($this->RowHeight + 8);
		$length = $this->GetStringWidth($partB);
		$this->Cell($length, $this->RowHeight, $partB);
		$this->Ln($this->RowHeight-1);
		$this->PrintPart4Header();

		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer2);
		if (!empty($this->sup_array)) {
			foreach ($this->sup_array as $objsup) {
				$this->RowPart4(array(strtoupper($objsup->particulars), $objsup->qty, $objsup->unit_price,$objsup->actual_charges,$objsup->claim_hospital,$objsup->claim_patient));
				//$this->Cell($this->ColumnWidth[0],$rowtable,$objlab->particulars,1,0,'C');
				//$this->Cell($this->ColumnWidth[1],$rowtable,$objlab->qty,1,0,'C');
				//$this->Cell($this->ColumnWidth[2],$rowtable,$objlab->unit_price,1,0,'C');
				//$this->Cell($this->ColumnWidth[3],$rowtable,$objlab->actual_charges,1,0,'C');
				//$this->Cell($this->ColumnWidth[4]/2, $rowtable, $objlab->claim_hospital,1,0,'C');
				//$this->Cell($this->ColumnWidth[4]/2, $rowtable, $objlab->claim_patient, 1, 0, 'C');
				//$this->Ln();
				$this->total_rlo_charges  += $objsup->actual_charges;
				$this->total_rlo_hospital += $objsup->claim_hospital;
				$this->total_rlo_patient  += $objsup->claim_patient;
			}
		}
		else{
			$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
			$this->SetFillColor(255);
			$this->SetTextColor(0);
			$this->Cell($this->tablewidth, $this->RowHeight, " ", 1, 0);
			$this->Ln(6);
		//$this->Cell(0, $this->RowHeight, "No records found for this report...", 0, 1, 'L');
		}
		$this->RowHeight = $oldRowHeight;
	}

	function addOthers(){
	 $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
	 $note = "NOTE: O.R. for laboratory procedures performed outside the hospital during this confinement period must be attached to this claim.";
	 $this->x2 = $this->GetX();
	 $this->y2 = $this->GetY();
	 $this->SetXY($this->x2, $this->y2+1);
	 $this->SetFont($this->fontfamily_label, $this->fontstyle_label2, $this->fontsize_label3);
	 $partC = "C. Others";
//	 $length = $this->GetStringWidth($partC);
//	 $this->Cell($length, $this->RowHeight, $partC);
//	 $this->Ln($this->RowHeight-1);

		$this->ColumnWidth = array(88,20,20,33,40);
	 $this->SetWidths(array(88,20,20,33,20,20));

		$oldRowHeight = $this->RowHeight;
		$this->RowHeight = $this->AdjustedRowHeight;

	 $length2 = $this->ColumnWidth[0] + $this->ColumnWidth[1] + $this->ColumnWidth[2];
	 $rowtable = $this->RowHeight-1;
	 # Print table header

//		$this->SetFont($this->fontfamily_label, $this->fontstyle_label,$this->fontsize_label3);
//		if ($this->colored) $this->SetFillColor(0xED);
//		$this->SetTextColor(0);
//		$rowheader=8;
//		$rowtable = $this->RowHeight-1;
//		$length = $this->ColumnWidth[0] + $this->ColumnWidth[1] + $this->ColumnWidth[2] + $this->ColumnWidth[3] + $this->lMargin;
//		$this->Cell($this->ColumnWidth[0],$rowheader,'Particulars',1,0,'C');
//		$this->Cell($this->ColumnWidth[1],$rowheader,'Qty',1,0,'C');
//		$this->Cell($this->ColumnWidth[2],$rowheader,'UNIT PRICE',1,0,'C');
//		$this->Cell($this->ColumnWidth[3],$rowheader,'ACTUAL CHARGES',1,0,'C');
//		$this->Cell($this->ColumnWidth[4], $rowheader/2, 'Benefit Claim',1,0,'C');
//		$this->Ln();
//		$this->SetX($length);
//		$this->Cell($this->ColumnWidth[4]/2, $rowheader/2, 'Hospital',1,0,'C');
//		$this->Cell($this->ColumnWidth[4]/2, $rowheader/2, 'Patient', 1, 0, 'C');
//		$this->Ln();

//		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer2);

		$this->form_part = $partC;
		$this->CheckPageBreak($this->RowHeight + 8);
		$length = $this->GetStringWidth($partC);
		$this->Cell($length, $this->RowHeight, $partC);
		$this->Ln($this->RowHeight-1);
		$this->PrintPart4Header();

		if (!empty($this->others_array)) {
			foreach ($this->others_array as $objothers) {
				$this->RowPart4(array(strtoupper($objothers->particulars), $objothers->qty, $objothers->unit_price,$objothers->actual_charges,$objothers->claim_hospital,$objothers->claim_patient));
				//$this->Cell($this->ColumnWidth[0],$rowtable,$objlab->particulars,1,0,'C');
				//$this->Cell($this->ColumnWidth[1],$rowtable,$objlab->qty,1,0,'C');
				//$this->Cell($this->ColumnWidth[2],$rowtable,$objlab->unit_price,1,0,'C');
				//$this->Cell($this->ColumnWidth[3],$rowtable,$objlab->actual_charges,1,0,'C');
				//$this->Cell($this->ColumnWidth[4]/2, $rowtable, $objlab->claim_hospital,1,0,'C');
				//$this->Cell($this->ColumnWidth[4]/2, $rowtable, $objlab->claim_patient, 1, 0, 'C');
				//$this->Ln();
			$this->total_rlo_charges  += $objothers->actual_charges;
			$this->total_rlo_hospital += $objothers->claim_hospital;
			$this->total_rlo_patient  += $objothers->claim_patient;
			}
		}
		 else{
		$tablewidth = 188;
		$row = 4;
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		$this->SetFillColor(255);
		$this->SetTextColor(0);
		//$this->Cell(0, $this->RowHeight, "No records found for this report...", 1, 1, 'L');
		$this->Cell($this->tablewidth, $this->RowHeight, " ", 1, 0);
		$this->Ln(6);
		//$this->Cell($tablewidth, $row, "No records found for this report...",0,1,'L');
		 }

		 if (!empty($this->lab_array) || !empty($this->sup_array) || !empty($this->others_array)) {
			//Total
//			$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
			$this->SetFont($this->fontfamily_label, $this->fontstyle_answer, $this->fontsize_label);
//			$this->Cell($length2, $rowtable, "Total", 1, 0, 'R');
			$this->Cell($length2, $rowtable, "T O T A L", 'T', 0, 'C');
			$this->SetFont($this->fontfamily_label, $this->fontstyle_answer, $this->fontsize_answer2);
			$this->Cell($this->ColumnWidth[3], $rowtable, number_format($this->total_rlo_charges,2,'.',','), 'T', 0, 'R');
			$this->Cell($this->ColumnWidth[4]/2, $rowtable,number_format($this->total_rlo_hospital,2,'.',','), 'T', 0, 'R');
			$this->Cell($this->ColumnWidth[4]/2, $rowtable, (($this->total_rlo_patient == 0) ? " " : number_format($this->total_rlo_patient,2,'.',',')), 'T', 0, 'R');

			$this->Ln($this->RowHeight);
			$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_table_note);
			$this->bNoPageBreak = true;
			$this->Cell($this->tablewidth, $rowtable, $note, 1, 0, 'L');
			$this->bNoPageBreak = false;
			$this->Ln(5);
		 }

		 $this->RowHeight = $oldRowHeight;
	 }

	function addPart5($title, $i, $amt, $wdeduct, $hosp, $prof, $med, $ndeduct){
		if ($this->CheckPageBreak($this->RowHeight * 9))
			$this->SetY($this->tMargin);
		else
			$this->SetY($this->GetY());

		$row = $this->RowHeight;
		$space = 17;
		$receipt = "O.R. - Official Receipt";
		$date = "Date";
		$sig = "Signature Over Printed Name of Patient/Member";

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);

//    $this->x2 = $this->GetX();
//    $this->y2 = $this->GetY();
	//$this->Rect($this->x2, $this->y2, $this->tablewidth, 35);
//    $this->SetXY($this->x2+($this->half/2), $this->y2+1);
	//Part V - Certification of Patient/Member
//    $length = $this->GetStringWidth($title);
		$this->Cell(0, $row, $title, 0, 0, "C");
		$this->Ln($this->RowHeight);
		$this->x2 = $this->GetX();
		$this->y2 = $this->GetY();
		$this->Rect($this->x2, $this->y2, $this->tablewidth, $this->RowHeight * 9);
		$this->Ln($this->RowHeight-1);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label2, $this->fontsize_table_note);
		$length = $this->GetStringWidth($i);
		$this->Cell($length, $row, $i);
		$this->Ln($this->RowHeight);

		$this->Cell(1,$this->boxheight," ",0,0);
//    $this->Cell($this->boxwidth, $this->boxheight, " ",1, 0);
		$xpos = $this->GetX();
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->Cell($this->boxwidth, $this->RowHeight-1, ($this->total_hosp_charge > 0) ? "X" : "", 0, 0, 'C');
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label2, $this->fontsize_table_note);
		$this->SetX($xpos);

		$this->Rect($this->GetX(), $this->GetY(), 3, $this->RowHeight-1);
		$this->SetX($this->GetX() + 3);
		$length = $this->GetStringWidth($amt);
		$this->Cell($length, $row, $amt);

		$tmp = "P".number_format($this->total_hosp_charge, 2, '.', ',');
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->Cell($this->GetStringWidth($tmp." "), $row, $tmp." ", 0, 0);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label2, $this->fontsize_table_note);

		$length = $this->GetStringWidth($wdeduct.$hosp);
		$this->Cell($length, $this->h_cell, $wdeduct.$hosp);
		$this->Ln($this->RowHeight);

		$this->Cell(1,$this->boxheight," ",0,0);
//    $this->Cell($this->boxwidth, $this->boxheight, " ",1, 0);
		$xpos = $this->GetX();
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->Cell($this->boxwidth, $this->RowHeight-1, ($this->total_pf_charge > 0) ? "X" : "", 0, 0, 'C');
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label2, $this->fontsize_table_note);
		$this->SetX($xpos);

		$this->Rect($this->GetX(), $this->GetY(), 3, $this->RowHeight-1);
		$this->SetX($this->GetX() + 3);

		$length = $this->GetStringWidth($amt);
		$this->Cell($length, $row, $amt);

		$tmp = "P".number_format($this->total_pf_charge, 2, '.', ',');
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->Cell($this->GetStringWidth($tmp." "), $row, $tmp." ", 0, 0);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label2, $this->fontsize_table_note);

		$length = $this->GetStringWidth($wdeduct.$prof);
		$this->Cell($length, $this->h_cell, $wdeduct.$prof);
		$this->Ln($this->RowHeight);

		$this->Cell(1,$this->boxheight," ",0,0);
//    $this->Cell($this->boxwidth, $this->boxheight, " ",1, 0);
		$xpos = $this->GetX();
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->Cell($this->boxwidth, $this->RowHeight-1, ($this->total_out_claim > 0) ? "X" : "", 0, 0, 'C');
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label2, $this->fontsize_table_note);
		$this->SetX($xpos);

		$this->Rect($this->GetX(), $this->GetY(), 3, $this->RowHeight-1);
		$this->SetX($this->GetX() + 3);

		$length = $this->GetStringWidth($amt);
		$this->Cell($length, $row, $amt);

		$tmp = "P".number_format($this->total_out_claim, 2, '.', ',');
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->Cell($this->GetStringWidth($tmp." "), $row, $tmp." ", 0, 0);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label2, $this->fontsize_table_note);

		$length = $this->GetStringWidth($wdeduct.$med);
		$this->Cell($length, $this->h_cell, $med);
		$this->Ln($this->RowHeight);

		$this->Cell(1,$this->boxheight," ",0,0);
//    $this->Cell($this->boxwidth, $this->boxheight, " ",1, 0);
		$xpos = $this->GetX();
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->Cell($this->boxwidth, $this->RowHeight-1, ($this->total_hosp_charge == 0) ? "X" : "", 0, 0, 'C');
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label2, $this->fontsize_table_note);
		$this->SetX($xpos);

		$this->Rect($this->GetX(), $this->GetY(), 3, $this->RowHeight-1);
		$this->SetX($this->GetX() + 4);

		$length = $this->GetStringWidth($ndeduct.$hosp);
		$this->Cell($length, $row, $ndeduct." from the".$hosp);
		$this->Ln($this->RowHeight);

		$this->Cell(1,$this->boxheight," ",0,0);
//    $this->Cell($this->boxwidth, $this->boxheight, " ",1, 0);
		$xpos = $this->GetX();
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->Cell($this->boxwidth, $this->RowHeight-1, ($this->total_pf_charge == 0) ? "X" : "", 0, 0, 'C');
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label2, $this->fontsize_table_note);
		$this->SetX($xpos);

		$this->Rect($this->GetX(), $this->GetY(), 3, $this->RowHeight-1);
		$this->SetX($this->GetX() + 4);

		$length = $this->GetStringWidth($ndeduct.$prof);
		$this->Cell($length, $row, $ndeduct." from the".$prof);
		$this->Ln($this->RowHeight * 2.5);

		$y2 = $this->GetY()-0.75;
		$x2 = (($this->tablewidth/3)/2);

		$this->Line($x2 - ($this->GetStringWidth($receipt)/2) - 10, $y2, $x2 + ($this->GetStringWidth($receipt)/2) + 20, $y2);
		$this->Cell($this->tablewidth/3, 2, $receipt, 0, 0, 'C');

		$x3 = $this->tablewidth/3;
		$this->Line($x3 + ($x2 - ($this->GetStringWidth($date)/2) - 15), $y2, $x3 + (25 + $x2 + ($this->GetStringWidth($date)/2)), $y2);
		$this->Cell($this->tablewidth/3, 2, $date, 0, 0, 'C');

		$x3 *= 2;
		$this->Line($x3 + ($x2 - ($this->GetStringWidth($sig)/2)), $y2, $x3 + (10 + $x2 + ($this->GetStringWidth($sig)/2)), $y2);
		$this->Cell($this->tablewidth/3, 2, $sig, 0, 0, 'C');
	}

	 ///==========================================
	 function SetWidths($w) {
		//Set the array of column widths
		$this->widths=$w;
	 }

	function SetAligns($a) {
		//Set the array of column alignments
		$this->aligns=$a;
	}

	function Row($data) {
		$rowborder = 0;
		$row = $this->RowHeight-1;
		//Calculate the height of the row
		$nb=0;
		for ($i=0; $i<count($data); $i++)
			$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));

//		$nb2=$this->NbLines($this->widths[1],$data[1]);
//		$nb3=$this->NbLines($this->widths[0],$data[0]);
//			if($nb2>$nb3){
//				$nbdiff = $nb2 - $nb3;
//				 $nbdiff = $nbdiff*$row;
//				k == 1;
//			}
//			else if($nb3>$nb2){
//				$nbdiff = $nb3 - $nb2;
//				 $nbdiff = $nbdiff*$row;
//				k==0;
//			}
//			else{
//				$nbdiff = 0;
//			}

		//$nb3=max($nb,$this->NbLines($this->widths[0],$data[0]));
		//print_r($nb2, $nb3);

		//$nb = $nb*2;
		//print_r($nb);
		$h=$row*$nb;
	//Issue a page break first if needed
//	$this->CheckPageBreak($h*1.5);
		if ($this->CheckPageBreak($h*1.5)) {
			$this->PrintPart3Header($this->form_part);
		}

		//Draw the cells of the row
		for($i=0;$i<count($data);$i++) {
			$w=$this->widths[$i];
			$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
			//Save the current position
			$x=$this->GetX();
			$y=$this->GetY();
			//Draw the border
			//$this->Rect($x,$y,$w,$h);
			//Print the text
			//print_r($data[i]);
			//number_format($objroom->charges,2,'.',',')
			if($i==2){
				$length = $this->GetStringWidth($data[$i]);
				if($length <= $this->widths[$i]){
					$this->Cell($w, $this->RowHeight, $data[$i],$rowborder,0,'L');
				}
				else{
					$this->MultiCell($w, $row,$data[$i],$rowborder,'L');
				}
			}
			else if($i>2) {
				//print_r(i);
				if($i==3){
					$tmp = str_replace(',', '', $data[$i]);
					$this->Cell($w, ((is_numeric($tmp) || $tmp == '') ? $this->RowHeight : $h), $data[$i],$rowborder,0,'R');
				}
				else{
					$tmp = ($data[$i] == 0) ? " " : number_format($data[$i],2,'.',',');
					$this->Cell($w, $this->RowHeight, $tmp, $rowborder,0, 'R');
				}
			}
			else{
				$length = $this->GetStringWidth($data[$i]);
				if($length <= $this->widths[$i]) {
					$this->Cell($w, $this->RowHeight, $data[$i],$rowborder,0,'L');
				}
				else {
					$nbrow = 3;
					// $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
					$this->MultiCell($w, $row,$data[$i],$rowborder,'L');

					//$this->MultiCell($length, $row,$data[$i],1,'L');
				}
			}

			//Put the position to the right of the cell
			$this->SetXY($x+$w,$y);
		}
		//Go to the next line
		$this->Ln($h);
	}

	function CheckPageBreak($h) {
		//If the height h would cause an overflow, add a new page immediately
		if($this->GetY()+$h > $this->PageBreakTrigger) {
			$this->AddPage($this->CurOrientation);
			return true;
		}
		else
			return false;
	}

	function NbLines($w,$txt) {
		//Computes the number of lines a MultiCell of width w will take
		$cw=&$this->CurrentFont['cw'];
		if($w==0)
			$w=$this->w-$this->rMargin-$this->x+1;
		$wmax=($w-(2*$this->cMargin)+2)*1000/$this->FontSize;  	// adjusted by LST to limit to 1 line text which really don't exceed 1 line.
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

	function RowPart4($data) {
		$rowborder = 0;
		$row = $this->RowHeight-1;
		//Calculate the height of the row
		$nb=0;
		for($i=0;$i<count($data);$i++)
		//print_r($nb=max($nb,$this->NbLines($this->widths[$i],$data[$i])));
			$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));

		$h=$row*$nb;
		//Issue a page break first if needed
		if ($this->CheckPageBreak($h*1.5)) {
			$this->PrintPart4Header($this->form_part);
		}

		//Draw the cells of the row
		for($i=0;$i<count($data);$i++)
		{
		$w=$this->widths[$i];
		//$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
		//Save the current position
		$x=$this->GetX();
		$y=$this->GetY();
		//Draw the border
		//$this->Rect($x,$y,$w,$h);
		//Print the text

		if ($i==0) {
			$length = $this->GetStringWidth($data[0]);
			if($length < $this->widths[0]) {
				$this->Cell($w, $h, $data[0],$rowborder,0,'L');
			}
			else {
				$this->MultiCell($w,$row,$data[0],$rowborder,'L');
			}
		}
		else{
			if ($i==1) {
				$tmp = str_replace(',', '', $data[$i]);
				$this->Cell($w, ((is_numeric($tmp) || $tmp == '') ? $this->RowHeight : $h), $data[$i],$rowborder,0,'C');
			}
			else {
				$tmp = ($data[$i] == 0) ? " " : number_format($data[$i],2,'.',',');
				$this->Cell($w, $this->RowHeight, $tmp,$rowborder,0, 'R');
			}
		}
		//$this->MultiCell($w,5,$data[$i],1,$a);
		//Put the position to the right of the cell
		$this->SetXY($x+$w,$y);
		}
		//Go to the next line
		$this->Ln($h);
	}

	function PrintOtherPFHeader($part = '') {
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label3);
		if ($part != '') $this->Cell(0, $this->RowHeight, $part." (continued)", 0, 1, "L");
		if ($this->colored) $this->SetFillColor(0xED);
		$this->SetTextColor(0);
		$rowheader=8;

//		$length = $this->ColumnWidth[0] + $this->ColumnWidth[1] + $this->ColumnWidth[2] + $this->ColumnWidth[3];
//		$length2 = $length + $this->ColumnWidth[4];
//		$length = $length + $this->ColumnWidth[4] + $this->ColumnWidth[5] + $this->lMargin;

		$this->Cell($this->ColumnWidth[0],$rowheader,'DOCTOR\'S NAME',1,0,'C');
		$this->Cell($this->ColumnWidth[1],$rowheader,'ACCREDITATION NO.',1,0,'C');
		$this->Cell($this->ColumnWidth[2],$rowheader, 'TIN',1,0,'C');
		$this->Cell($this->ColumnWidth[3],$rowheader,'SERVICES PERFORMED',1,0,'C');
		$this->Cell($this->ColumnWidth[4],$rowheader,'ACTUAL CHARGES',1,0,'C');
		$this->Cell($this->ColumnWidth[5],$rowheader,'PHYSICIAN',1,0,'C');
		$this->Cell($this->ColumnWidth[6], $rowheader, 'PATIENT',1,0,'C');
		$this->Ln();
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer2);
	}

	/***
	* If there is more than 1 surgeon or anaesthesiologist, print remaining surgeons/anaesthesiologists in a separate page.
	*/
	function PrintOtherPFs() {
		$bPageAdded = false;
//		$this->ColumnWidth = array(35,30,15,60,20,20,20);
		$row1 = array(80,40,40);
		$row2 = array(10,90,30,30,30);

		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer2);
		if (is_array($this->surgeon_array) && (count($this->surgeon_array) > 0)) {
			$this->AddPage();
			$bPageAdded = true;
			$this->Cell(200,$this->RowHeight,'SURGEON(S)','B',1,'L');
//			$this->form_part = 'SURGEON(S)';
//			$this->PrintOtherPFHeader();    \
			reset($this->surgeon_array);
			foreach($this->surgeon_array as $v) {
				$this->SetWidths($row1);
				$this->PrintPFRow(array($v->name, $v->accnum, $v->bir_tin_num));
				$this->Ln(1);
				$this->SetWidths($row2);
				$this->PrintPFRow(array(" ", $v->servperformance, $v->profcharges, $v->claim_physician, $v->claim_patient), true);
				$this->Ln($this->RowHeight);
			}
			$this->Ln($this->RowHeight * 2);
		}

		if (is_array($this->anesth_array) && (count($this->anesth_array) > 0)) {
			if (!$bPageAdded) $this->AddPage();
			$this->Cell(200,$this->RowHeight,'ANAESTHESIOLOGIST(S)','B',1,'L');
//			$this->form_part = 'ANAESTHESIOLOGIST(S)';
//			$this->PrintOtherPFHeader();
			reset($this->anesth_array);
			foreach($this->anesth_array as $v) {
				$this->SetWidths($row1);
				$this->PrintPFRow(array($v->name, $v->accnum, $v->bir_tin_num));
				$this->Ln(1);
				$this->SetWidths($row2);
				$this->PrintPFRow(array(" ", $v->servperformance, $v->profcharges, $v->claim_physician, $v->claim_patient), true);
				$this->Ln($this->RowHeight);
			}
		}
	}

	function PrintPFRow($data, $bRow2 = false) {
		$rowborder = 0;
		$row = $this->RowHeight-1;
		//Calculate the height of the row
		$nb=0;
		for ($i=0; $i<count($data); $i++)
			$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));

		$h=$row*$nb;
	//Issue a page break first if needed
//	$this->CheckPageBreak($h*1.5);
//		if ($this->CheckPageBreak($h*1.5)) {
//			$this->PrintOtherPFHeader($this->form_part);
//		}

		//Draw the cells of the row
		for($i=0;$i<count($data);$i++) {
			$w=$this->widths[$i];
			$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
			//Save the current position
			$x=$this->GetX();
			$y=$this->GetY();
			//Draw the border
			//$this->Rect($x,$y,$w,$h);
			//Print the text
			//print_r($data[i]);
			//number_format($objroom->charges,2,'.',',')
//			if($i==2){
//				$length = $this->GetStringWidth($data[$i]);
//				if($length <= $this->widths[$i]){
//					$this->Cell($w, $this->RowHeight, $data[$i],$rowborder,0,'L');
//				}
//				else{
//					$this->MultiCell($w, $row,$data[$i],$rowborder,'L');
//				}
//			}
//			else if (($i>=2) && $bRow2) {
			if (($i>=2) && $bRow2) {
				//print_r(i);
//				if($i==2){
//					$tmp = str_replace(',', '', $data[$i]);
//					$this->Cell($w, ((is_numeric($tmp) || $tmp == '') ? $this->RowHeight : $h), $data[$i],$rowborder,0,'R');
//				}
//				else{
					$tmp = ($data[$i] == 0) ? " " : number_format($data[$i],2,'.',',');
					$this->Cell($w, $this->RowHeight, $tmp, $rowborder,0, 'R');
//				}
			}
			else{
				$length = $this->GetStringWidth($data[$i]);
				if($length <= $this->widths[$i]) {
					$this->Cell($w, $this->RowHeight, $data[$i],$rowborder,0,'L');
				}
				else {
					$nbrow = 3;
					// $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
					$this->MultiCell($w, $row,$data[$i],$rowborder,'L');

					//$this->MultiCell($length, $row,$data[$i],1,'L');
				}
			}

			//Put the position to the right of the cell
			$this->SetXY($x+$w,$y);
		}
		//Go to the next line
		$this->Ln($h);
	}
}

class Medicine {
	var $gen_name;
	var $brand;
	var $preparation;
	var $qty;
	var $unit_price;
	var $actual_charges;
	var $claim_hospital;
	var $claim_patient;
}

class Laboratories{
	var $particulars;
	var $qty;
	var $unit_price;
	var $actual_charges;
	var $claim_hospital;
	var $claim_patient;
}

class Confinement{
	var $admit_dt;
	var$discharge_dt;
	var $death_dt;
	var $claim_days;
	var $admit_tm;
	var $discharge_tm;
}

class Diagnosis {
	var $code;
	var $fin_diagnosis;
	var $case_type;
}

class HospServices {
	var $charges;
	var $claim_hospital;
	var $claim_patient;
	var $reduction;
}

class HealthPersonnel {
	var $name;
	var $accnum;
	var $bir_tin_num;
	var $servperformance;
	var $profcharges;
	var $claim_physician;
	var $claim_patient;
	var $role_area;
}

class Surgeon {
	var $name;
	var $accnum;
	var $bir_tin_num;
	var $servperformance;
	var $profcharges;
	var $claim_physician;
	var $claim_patient;
	var $operation_dt;
}

//  class HospitalNumber{
//        var $hospnum;
//  }
//
//  class AccreditationNumber{
//        var $philhealthacc;
//  }

$pdf = new PhilhealthForm2();
include_once($root_path.'modules/billing/form2_data.php');
$pdf->Open();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->addHead("PHILHEALTH",
				"CLAIM FORM 2",
				"Revised May 2000",
				"Note: This form together with claim form 1 should be filed with Philhealth ".
				"within 60 calendar days from date of discharge",
				"HEALTH CARE",
				"PROVIDER'S CERTIFICATION",
				"Philhealth Accreditation #");
$pdf->addDateReceived("(DATE RECEIVED)");
$pdf->addPart1("PART I - HOSPITAL DATA AND CHARGES (Hospital to Fill in All items)");
$pdf->addBillNum("BILL #");
$pdf->addHospitalInfo("1. Philhealth Accreditation No.",
						"2. Accreditation Category",
						"3. Name of Hospital/Ambulatory Clinic",
						"4. Address of Hospital/Ambulatory Clinic",
						"No., Street",
						"Municipality/City",
						"Barangay",
						"Province",
						"Zip Code");
$pdf->addMemberName("5. Name of Member and Identification",
					"Last Name",
					"Middle Name",
					"First Name",
					"Identification No.");
$pdf->addMemberAddress("6. Address of Member",
						"No., Street",
						"Municipality/City",
						"Barangay",
						"Province",
						"Zip Code");
$pdf->addNamePatient("7. Name of Patient",
					"Last Name",
					"First Name",
					"Middle Name",
					"8. Age",
					"9. Sex");
$pdf->addDiagnosis("10. Admission Diagnosis");
$pdf->addConfinement("11. Confinement Period",
					"a. Date Admitted",
					"b. Time Admitted",
					"c. Date Discharged",
					"d. Time Discharged",
					"e. Claimed No. of Days",
					"f. Date of Death\n   (If Applicable)");
$pdf->addHospAmbServ("12. Hospital/Ambulatory Services",
						"a. Room and Board",
						"b. Drugs and Medicines (Part III for details)",
						"c. X-ray/Lab. Test/Others (Part IV for details)",
						"d. Operating Room Fee",
						"e. Medicines bought & laboratory performed\n".
						"   outside hospital during confinement period",
						"TOTAL");
$pdf->addCertification("13. CERTIFICATION of HOSPITAL/AMBULATORY CLINIC: I certify that ".
						"the services rendered are duly recorded in the patient's chart and\n".
						"that the information given in this form are true and correct.",
						"Signature Over Printed Name of Authorized Representative",
						"Date Signed",
						"Official Capacity");
$pdf->addPart2("PART II - PROFESSIONAL DATA AND CHARGES (Doctor/s to Fill in Respective Portions)");
$pdf->addFinDiagnosis("14. Complete Final Diagnosis");
/*$pdf->addCaseType("15. Case Type",
					"Ordinary",
					"Intensive",
					"Catastrophic",
					"Category D");       */ //commented by Cherry 04-22-09

$pdf->addAttPhysician("16. Name of Attending Physician",
						"Signature & Date Signed",
						"17. PHIC Accreditation No.",
						"18. BIR/TIN No.",
						"19. Services Performed:");

/*$pdf->addSurgeon("21. Name of Surgeon",
				 "Signature & Date Signed",
				 "22. PHIC Accreditation No.",
				 "23. BIR/TIN No.",
				 "24. Services Performed:",
				 "Date of Operation");    */

$pdf->addAnesthesiologist("26. Name of Anesthesiologist",
							"Signature &  Date Signed",
							"27. PHIC Accreditation No.",
							"28. BIR/TIN No.",
							"29. Services Performed");

//$pdf->addPhilUse("Relative Unit Value",
//                "Illness Code",
//                "Reduction Code");

$pdf->addNote("NOTE: Anyone who supplies false or incorrect information requested by this or a related form".
				" or commits misrepresentation shall be subject to criminal, civil".
				" or administrative prosecution.");

$pdf->AddPage();
$pdf->SetLeftMargin($pdf->x1);

$pdf->addPart3("PART III: DRUGS AND MEDICINES");

$pdf->addPart4("PART IV - X-RAY, LABORATORIES AND OTHERS");

//$pdf->addSupplies("B. Supplies");
$pdf->addSupplies();

//$pdf->addOthers("C. Others");
$pdf->addOthers();

$pdf->addPart5("PART V - CERTIFICATION of PATIENT/MEMBER",
				 "I hereby certify that",
				 " The amount of ",
				 "was deducted from the",
				 " hospital charges.",
				 " professional fee charges.",
				 "was paid for medicines/laboratory acquired outside the hospital during this confinement. (O.R. attached)",
				 "No deduction was made");

$pdf->PrintOtherPFs();
$pdf->Output();
?>