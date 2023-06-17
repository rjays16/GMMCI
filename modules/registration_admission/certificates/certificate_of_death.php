<?php
/*		
		$this->sql ="SELECT cp.pid, enc.encounter_nr, 
							cp.name_last, cp.name_first, cp.name_2, cp.name_3, cp.name_middle,
							enc.encounter_date AS er_opd_datetime, 
							dept.name_formal,
							cp.street_name, sb.brgy_name, sm.zipcode, sm.mun_name, sp.prov_name, sr.region_name,
							cp.phone_1_nr, cp.phone_2_nr, cp.cellphone_1_nr, cp.cellphone_2_nr, cp.sex, cp.civil_status,
							fn_get_age(enc.encounter_date,cp.date_birth) AS age,
							cp.date_birth, cp.place_birth,
							sc.country_name AS citizenship, 
							sreli.religion_name AS religion, 
							so.occupation_name AS occupation, 
							cp.mother_name, cp.father_name, cp.spouse_name, cp.guardian_name,							
							enc.informant_name, enc.info_address, enc.relation_informant, 
							enc.encounter_type, 
							enc.referrer_dr AS opd_admitting_physician,
							enc.current_dept_nr,							
							enc.consulting_dr AS admitting_physician,
							enc.modify_id AS admitting_clerk,
							enc.create_id AS admitting_clerk_er_opd,
							enc.referrer_diagnosis AS admitting_diagnosis
						FROM $this->tb_person AS cp, $this->tb_enc AS enc, 
							$this->tb_dept AS dept,
							$this->tb_barangays AS sb, $this->tb_municity AS sm, 
							$this->tb_provinces AS sp, $this->tb_regions AS sr, 
							$this->tb_country AS sc, $this->tb_religion AS sreli, $this->tb_occupation AS so
						WHERE enc.encounter_nr='$encounter_nr'
							AND cp.pid=enc.pid AND dept.nr=enc.current_dept_nr
							AND sr.region_nr=sp.region_nr AND sp.prov_nr=sm.prov_nr 
							AND sm.mun_nr=sb.mun_nr AND sb.brgy_nr=cp.brgy_nr
							AND sc.country_code=cp.citizenship AND sreli.religion_nr = cp.religion 
							AND so.occupation_nr = cp.occupation " ;
*/							
							
	include("roots.php");
	include_once($root_path."/classes/fpdf/fpdf.php");
	require_once($root_path.'include/inc_environment_global.php');
	include_once($root_path.'include/inc_date_format_functions.php');

	require_once($root_path.'/include/care_api_classes/class_drg.php');
	$objDRG= new DRG;

	include_once($root_path.'include/care_api_classes/class_encounter.php');
	$enc_obj=new Encounter;


$_GET['encounter_nr'] = 2007500031;

	if ($_GET['encounter_nr']) {
		if (!($enc_info = $enc_obj->getEncounterInfo($_GET['encounter_nr']))){
			echo '<em class="warn">Sorry but the page cannot be displayed!</em>';
			exit();
		}
		#echo "enc_obj->sql = '".$enc_obj->sql."' <br> \n";		
		extract($enc_info);
	}else{
			echo '<em class="warn">Sorry but the page cannot be displayed! <br>Invalid Case Number! </em>';
			exit();	
	}


	$border="1";
	$border2="0";
	$space=2;
	$fontSizeLabel=9;
	$fontSizeInput=14;
	$fontSizeHeading=15;

	//Cell(float w [, float h [, string txt [, mixed border [, int ln [, string align [, int fill [, mixed link]]]]]]])
	$pdf = new FPDF();
	$pdf->AddPage("P");
	
	$pdf->SetLeftMargin(0);


	$pdf->Ln(14);
	$pdf->SetFont("Arial","",$fontSizeLabel-2);
    $pdf->Cell(158,4,'',"",'L');	
	
	$pdf->SetFont("Arial","B",$fontSizeLabel-2);
	$pdf->Cell(40,34,'',"",'L');	
	
	$pdf->SetFont("Arial","B",$fontSizeLabel);
	
	

	
/* $pdf->SetXY(100,140);
 $pdf->MultiCell(50,20, "This is a test This is a testThis is a test This is a testThis is a testThis is a test This is a testThis is a test This is a testThis is a testThis is a test This is a testThis is a test This is a testThis is a testThis is a test This is a testThis is a test This is a testThis is a testThis is a test This is a testThis is a test This is a testThis is a testThis is a test This is a testThis is a test This is a testThis is a testThis is a test This is a testThis is a test This is a testThis is a testThis is a test This is a testThis is a test This is a testThis is a testThis is a test This is a testThis is a test This is a testThis is a testThis is a test This is a testThis is a test This is a testThis is a testThis is a test This is a testThis is a test This is a testThis is a test", 1, 'L');
*/	
	
	$pdf->Text(35,52, "Province");
	$pdf->Text(35, 57.5, "City/Municipality");
	$pdf->Text(110, 58, "Registry No.");


//1. NAME
	$pdf->Text(33, 69,$name_first);
	$pdf->Text(73, 69,$name_middle);
	$pdf->Text(118, 69, $name_last);
	




//2. SEX
	$pdf->Text(0, 79, "x");
	$pdf->Text(0, 84, "x");
	
	
//3. RELIGION
	$pdf->Text(30, 82, "Religion");
	
//4. AGE
		$pdf->Text(57, 83, "00");
		$pdf->Text(80, 83, "00 yr");
		$pdf->Text(102, 83, "month");
		$pdf->Text(117, 83,"days");
		$pdf->Text(127.5, 83, "Hrs/min/sec");	

//5. PLACE OF DEATH
	$pdf->Text(83, 97, "City/Municipality");
	$pdf->Text(122, 97, "Province");	

//5. DATE OF DEATH
	$pdf->Text(32.5, 107, "Day");
	$pdf->Text(50, 107, "Month");
	$pdf->Text(70, 107, "year");
	
//7. CITIZENSHIP
$pdf->Text(125, 107, "Citizenship");

//8. RESIDENCE
	$pdf->Text(25, 116, $address);
	$pdf->Text(25, 116, "Baranggay");
	$pdf->Text(75, 116, "City/Municipality");
	$pdf->Text(122, 116, "Province");
	
	
	
//2. CIVIL STATUS
	$pdf->Text(0, 124.5, "s");
	$pdf->Text(0, 128, "m");
	$pdf->Text(29, 124.5, "w");
	$pdf->Text(29, 128, "o");
	$pdf->Text(61, 124.5, "u");

//7. OCCUPATION
	$pdf->Text(97, 127, "Occupation");


//17. CAUSES OF DEATH
	$pdf->Text(33, 148, "immediate cause");
	$pdf->Text(33, 156.7, "antecedent cause");
	$pdf->Text(33, 165.5, "underlying cause");	
	$pdf->Text(37, 174, "other cause");		

//18. DEATH BY NON NATURAL CAUSES
	//A. Manner of death
	$pdf->Text(0, 193, "H");
	$pdf->Text(27, 193,"S");
	$pdf->Text(59, 193, "A");
	$pdf->Text(92, 193, "O");
	$pdf->Text(124, 193, "Specify");				

	//B. Place of occurence
	$pdf->Text(84, 197.7, "Place of occurence");	

//19.ATTENDANT
	$pdf->Text(0, 207.5, "X");
	$pdf->Text(0, 213, "X");	
	$pdf->Text(0, 217, "X");
	$pdf->Text(50, 207.5, "X");
	$pdf->Text(50, 213, "X");
	$pdf->Text(50, 217, "X");
	
	//If attended, state duration:
	$pdf->Text(100, 207.5, "from");	
	$pdf->Text(129, 207.5, "xxxx");
	$pdf->Text(100, 213, "To");	
	$pdf->Text(129, 213, "xxxx");

//20. CERTIFICATION OF DEATH
	$pdf->Text(0,232.5, "x");
	$pdf->Text(0,237, "x");
	$pdf->Text(82,237, "time");	
	
//21. CORPSE DISPOSAL
	$pdf->Text(0, 285.5, "B");
	$pdf->Text(0, 289.5, "C");	
	$pdf->Text(24, 285.5, "O");
	$pdf->Text(25, 289.5, "Specify");
	
//22. BURIAL CREMATION
	$pdf->Text(84.5, 285.5, "number");
	$pdf->Text(84.5, 289.5, "Date issued");

//23. AUTOPSY
	$pdf->Text(136, 285.5, "Y");
	$pdf->Text(136, 289.5, "N");
	
// NAME AND ADDRESS OF CEMETERY OR CREMATORY
	$pdf->Text(5,300, "NAME AND ADDRESS OF CEMETERY OR CREMATORY");
	

/*
//3. DATE OF BIRTH
	$pdf->Text(110, 90, "Day");
	$pdf->Text(125, 90, "Month");
	$pdf->Text(140, 90, "year");
	
//4. PLACE OF BIRTH
	$pdf->Text(83, 105, "City/Municipality");
	$pdf->Text(122, 105, "Province");

//5a TYPE OF BIRTH
	$pdf->Text(10, 118, "sinlge");
	$pdf->Text(30, 118, "twin");
	$pdf->Text(20, 121.5, "Triplet");
	
//5b IF MULTIPLE BIRTH, CHILD WAS
	$pdf->Text(75, 118, "1st");
	$pdf->Text(103, 118, "2nd");
	$pdf->Text(85, 121.5, "Others");
	$pdf->Text(124, 121.5, "Specify");
	
//5c.BIRTH ORDER
    $pdf->Text(15, 134, "live Birth");

//5d. WEIGHT AT BIRTH	
	$pdf->Text(96, 134, " X grams");
	
//6. MAIDEN NAME
	$pdf->Text(32, 146, "MAIDEN_Fname");
	$pdf->Text(73, 146, "MAIDEN middle");
	$pdf->Text(117, 146, "MAIDEN last");
	
// 7.CITIZENSHIP
	$pdf->Text(35, 157, "Citizenship");

//8. RELIGION
	$pdf->Text(115, 157, "Religion");
	
//9a
	$pdf->Text(25, 172, "No.");
	$pdf->Text(83, 172, "No.");
	$pdf->Text(138, 172, "No.");

//10. OCCUPATION (MOTHER)
	$pdf->Text(45, 183, "Occupation");
	
//11. AGE AT THE TIME OF THIS BIRTH (MOTHER)
	$pdf->Text(127, 184.5, "age");
	
//12. RESIDENCE
	$pdf->Text(30, 196, "Baranggay");
	$pdf->Text(85, 196, "City/Municipality");
	$pdf->Text(120, 196, "Province");

//13. FATHER'S NAME
	$pdf->Text(35, 208.5, "FATHER_Fname");
	$pdf->Text(75, 208.5, "FATHER_middle");
	$pdf->Text(116, 208.5, "FATHER_last");
	
//14.CITIZENSHIP (FATHER)
	$pdf->Text(35, 218, "F_Citizenship");

//15. RELIGION
	$pdf->Text(115, 218, "F_Religion");
	
//16. OCCUPATION (FATHER)
	$pdf->Text(45, 228, "Occupation");
	
//11. AGE AT THE TIME OF THIS BIRTH (FATHER)
	$pdf->Text(128, 230, "age");
	
*/
	$pdf->Output();	
	
	
?>