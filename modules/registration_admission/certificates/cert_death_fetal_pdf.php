<?php
# created by pet for fetal death certificate, patterned from birth & death certificates; june 11, 2008
function seg_ucwords($str) {
	$words = preg_split("/([\s,.-]+)/", mb_strtolower($str), -1, PREG_SPLIT_DELIM_CAPTURE);
	$words = @array_map('ucwords',$words);
	return implode($words);
}

include("roots.php");
include_once($root_path."/classes/fpdf/fpdf.php");
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');

if (isset($_GET['id']) && $_GET['id']){
	$pid = $_GET['id'];
}

include_once($root_path.'include/care_api_classes/class_person.php');
$person_obj=new Person($pid);

require_once($root_path.'include/care_api_classes/class_personell.php');
$pers_obj=new Personell;

include_once($root_path.'include/care_api_classes/class_address.php');
$address_country = new Address('country');
$address_brgy = new Address('barangay');

require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
$objInfo = new Hospital_Admin();

if ($row = $objInfo->getAllHospitalInfo()) {
		$row['hosp_agency'] = strtoupper($row['hosp_agency']);
		$row['hosp_name']   = strtoupper($row['hosp_name']);
}
else {
		$row['hosp_country'] = "Republic of the Philippines";
		$row['hosp_agency']  = "DEPARTMENT OF HEALTH";
		$row['hosp_name']    = "BUKIDNON PROVINCIAL HOSPITAL - MALAYBALAY";
		$row['hosp_addr1']   = "Malaybalay, Bukidnon";
		$row['mun_name']     = "Malaybalay";
		$row['prov_name']     = "Bukidnon";
		$row['region_name']     = "Region X";
}

if ($pid){
	if (!($basicInfo=$person_obj->BasicDataArray($pid))){
		echo '<em class="warn"> Sorry, the page cannot be displayed!</em>';
		exit();
	}
	extract($basicInfo);
	$brgy_info = $address_brgy->getAddressInfo($brgy_nr,TRUE);
	if($brgy_info){
		$brgy_row = $brgy_info->FetchRow();
	}
}else{
	echo '<em class="warn">Sorry, the page cannot be displayed! <br> Invalid PID!</em>';
	exit();
}
/*
$birthYear = intval(substr($date_birth, 0, 4));
$birthMonth = intval(substr($date_birth, 5, 7));
$birthDay = intval(substr($date_birth, 8, 10));
*/

$birthYear = date("Y",strtotime($date_birth));
$birthMonth = date("F",strtotime($date_birth));
$birthDay = date("d",strtotime($date_birth));

include_once($root_path.'include/care_api_classes/class_cert_death_fetal.php');
$obj_fetalDeathCert = new FetalDeathCertificate($pid);

$fetalDeathCertInfo = $obj_fetalDeathCert->getFetalDeathCertRecord($pid);

if ($fetalDeathCertInfo){
	extract($fetalDeathCertInfo);
	$delivery_method_tmp= substr(trim($fetalDeathCertInfo['delivery_method']),0,1);
	$delivery_method_info = substr(trim($fetalDeathCertInfo['delivery_method']),4);
	$attendant_type_tmp = substr(trim($fetalDeathCertInfo['attendant_type']),0,1);
	$attendant_type_others = substr(trim($fetalDeathCertInfo['attendant_type']),4);
	$death_occurrence = substr(trim($fetalDeathCertInfo['death_occurrence']),0,1);
	$corpse_disposal_tmp= substr(trim($fetalDeathCertInfo['corpse_disposal']),0,1);
	$corpse_disposal_others = substr(trim($fetalDeathCertInfo['corpse_disposal']),4);
	$is_autopsy = substr(trim($fetalDeathCertInfo['is_autopsy']),0,1);
	$tmp_death_cause = unserialize($fetalDeathCertInfo['death_cause']);
}

	$border="1";
	$border2="0";
	$space=2;
	$fontSizeInput=11;
	$fontSizeHeading=14;

	$pdf = new FPDF("P","mm","Legal");
	$pdf->AddPage("P");

	$pdf->SetDisplayMode(real,'default');

	$x = $pdf->GetX();
	#$y = $pdf->GetY();
	#$pdf->Line($x, $y, $x+200, $y);
	#left margin 10mm
	#top margin 30mm

	$y=($x*2.8)-4;

	$pdf->SetXY($x,$y);
	$pdf->SetFont("Arial","",$fontSizeInput);

	$pdf->SetY(-0.5);
	$z = $pdf->GetY();

	#$pdf->Text($x+32, $y-5, "Davao del Sur");
	$pdf->Text($x+32, $y-1, $row['mun_name']);
	$pdf->Text($x+110, $y, $registry_nr);

// FETUS
 // 1. NAME

	#$pdf->SetXY($x+30, $y+8);
	$pdf->SetXY($x+10, $y+8);
	#$pdf->MultiCell(42, 4,mb_strtoupper($name_first), '', 'L','0');
	$pdf->Cell(60, 4,mb_strtoupper($name_first),'', '0','C');

	#$pdf->SetXY($x+72, $y+8);
	$pdf->SetXY($x+70, $y+8);
	#$pdf->MultiCell(40, 4,mb_strtoupper($name_middle), '', 'L','0');
	$pdf->Cell(40, 4,mb_strtoupper($name_middle),'', '0','C');

	#$pdf->SetXY($x+115, $y+8);
	$pdf->SetXY($x+110, $y+8);
	#$pdf->MultiCell(40, 4,mb_strtoupper($name_last), '', 'L','0');
	$pdf->Cell(43, 4,mb_strtoupper($name_last),'', '0','C');

 // 2. SEX

	if ($sex=='m')
		$pdf->Text($x+13, $y+21.5, "X");
	if ($sex=='f')
		$pdf->Text($x+40, $y+21.5, "X");
	if ($sex=='u')
		$pdf->Text($x+13, $y+25, "X");

 // 3. DATE OF DELIVERY
	#if ($birthMonth)
		#$birthMonth = date("F",strtotime($birthMonth));
	$arrayMonth = array ("January","February","March","April","May","June","July","August","September","October","November","December");
	$pdf->Text($x+110, $y+25, $birthDay."    ".$birthMonth."    ".$birthYear);

 // 4. PLACE OF DELIVERY
	$pdf->SetFont("Arial","",$fontSizeInput+1);

	if ($birth_place_basic)
		$birth_place = mb_strtoupper(trim($birth_place_basic)).", ";
	else
		$birth_place = trim($row['hosp_name']);

	$pdf->Text($x+20, $y+37, $birth_place." ".mb_strtoupper(trim($birth_place_mun)));
	$pdf->SetFont("Arial","",$fontSizeInput);

 // 5a. TYPE OF DELIVERY

	if ($birth_type=="1")
		$pdf->Text($x+13, $y+44, "X");
	if ($birth_type=="2")
		$pdf->Text($x+40, $y+44, "X");
	if (($birth_type!="")&&($birth_type!="1")&&($birth_type!="2"))
		$pdf->Text($x+23, $y+47.5, "X");

 // 5b. IF MULTIPLE DELIVERY, FETUS WAS

	if ($birth_rank == 'first')
		$pdf->Text($x+79, $y+44, "X");
	if ($birth_rank == 'second')
		$pdf->Text($x+120, $y+44, "X");
	else{
		$pdf->Text($x+99, $y+47.5, "X");
		#$birth_rank = "birth_rank";
		$pdf->Text($x+132, $y+47.5, $birth_rank);
	}

// MOTHER
 // 5c. METHOD OF DELIVERY

	if ($delivery_method == '1')
		$pdf->Text($x+13, $y+56.75, "X");
	else{
		$pdf->Text($x+13, $y+59.75, "X");

		$pdf->Text($x+43, $y+59.60, $delivery_method_info);
	}

 // 5d.BIRTH ORDER
	$pdf->Text($x+72, $y+59.60, $birth_order);

 // 5e. WEIGHT OF FETUS
	$pdf->Text($x+136, $y+59.75, $birth_weight);

 // 6. MAIDEN NAME (MOTHER)
	#$pdf->SetFont("Arial","",$fontSizeInput-1);
	$pdf->SetFont("Arial","",$fontSizeInput);
	#$pdf->SetXY($x+30, $y+64);
	$pdf->SetXY($x+25, $y+65);
	#$pdf->MultiCell(42, 4,mb_strtoupper($m_name_first), '', 'L','0');
	$pdf->Cell(50, 4,mb_strtoupper($m_name_first),'', '0','C');

	#$pdf->SetXY($x+72, $y+64);
	$pdf->SetXY($x+75, $y+65);
	#$pdf->MultiCell(40, 4,mb_strtoupper($m_name_middle), '', 'L','0');
	$pdf->Cell(39, 4,mb_strtoupper($m_name_middle),'', '0','C');

	#$pdf->SetXY($x+115, $y+64);
	$pdf->SetXY($x+114, $y+65);
	#$pdf->MultiCell(40, 4,mb_strtoupper($m_name_last), '', 'L','0');
	$pdf->Cell(40, 4,mb_strtoupper($m_name_last),'', '0','C');

	$pdf->SetFont("Arial","",$fontSizeInput);

 // 7.CITIZENSHIP (MOTHER)
	if ($m_citizenship=='PH')
		$m_citizenship = "FILIPINO";
	$pdf->Text($x+16, $y+80, strtoupper($m_citizenship));

 // 8. RELIGION (MOTHER)
		$religion_obj = $obj_fetalDeathCert->getMReligion($m_religion);
	if ($religion_obj['religion_name']=="Not Applicable")
		$religion_obj['religion_name'] = "";
	#$pdf->SetFont("Arial","",$fontSizeInput-2);
	$pdf->SetFont("Arial","",$fontSizeInput-1);
	$pdf->SetXY($x+38, $y+76);
	$pdf->MultiCell(20, 4, $religion_obj['religion_name'], '', 'C','0');
	$pdf->SetFont("Arial","",$fontSizeInput);

 // 9. OCCUPATION (MOTHER)
		$occupation_obj = $obj_fetalDeathCert->getMOccupation($m_occupation);
	if ($occupation_obj['occupation_name']=="Not Applicable" || $occupation_obj['occupation_name']=="None")
		$occupation_obj['occupation_name'] = "";
	#$pdf->SetFont("Arial","",$fontSizeInput-2.5);
	$pdf->SetFont("Arial","",$fontSizeInput-1);
	$pdf->SetXY($x+67, $y+76);
	$pdf->MultiCell(35, 4,mb_strtoupper($occupation_obj['occupation_name']), '', 'C','0');
	$pdf->SetFont("Arial","",$fontSizeInput);
	//$pdf->Text($x+69, $y+80, $occupation_obj['occupation_name']);

 // 10. AGE AT THE TIME OF THIS BIRTH (MOTHER)
	$pdf->Text($x+137, $y+80, $m_age);

 // 11a. Total number of children born alive
	$pdf->Text($x+35, $y+92, $m_total_alive);

 // 11b. Number of children still living
	$pdf->Text($x+68, $y+92, $m_still_living);

 // 11c. Number of children born alive but are now dead
	$pdf->Text($x+130, $y+92, $m_now_dead);

 // 12. RESIDENCE (MOTHER)
	$m_address = $m_residence_basic;
	#echo "s= ".$m_residence_basic;
	$brgy = $address_country->getMunicityByBrgy($m_residence_brgy);
	$mun = $address_country->getProvinceByBrgy($m_residence_mun);
	$prov = $address_country->getProvinceInfo($m_residence_prov);
	/*
	if (!empty($m_address) && !empty($m_residence_mun)){
		$m_address = $m_address.", ".strtoupper($brgy['brgy_name'])." ".strtoupper(seg_ucwords($mun['mun_name']));
	}else{
		$m_address = $m_address." ".strtoupper($brgy['brgy_name'])." ".strtoupper(seg_ucwords($mun['mun_name']));
	}

	#added by VAN 08-05-08
	#if ($mun['mun_name']!='Davao City'){
	if(stristr($brgy_row['mun_name'], 'city') === FALSE){
		if (!empty($m_address)){
			$m_address = $m_address.", ".seg_ucwords(trim($prov['prov_name']));
		}else{
			$m_address = $m_address." ".seg_ucwords(trim($prov['prov_name']));
		}
	}
	*/
	if ($m_address){
		if ($brgy_name!="NOT PROVIDED")
			$street_name = trim($m_address).", ";
		else
			$street_name = trim($m_address).", ";
	}else
		$street_name = "";



	if ((!($brgy['brgy_name'])) || ($brgy['brgy_name']=="NOT PROVIDED"))
		$brgy_name = "";
	else
		$brgy_name  = trim($brgy['brgy_name']).", ";

	if ((!($mun['mun_name'])) || ($mun['mun_name']=="NOT PROVIDED"))
		$mun_name = "";
	else{
		if ($brgy_name)
			$mun_name = trim($mun['mun_name']);
		#else
			#$mun_name = $mun_name;
	}

	if ((!($prov['prov_name'])) || ($prov['prov_name']=="NOT PROVIDED"))
		$prov_name = "";
	else
		$prov_name = trim($prov['prov_name']);

	if(stristr(trim($mun_name), 'city') === FALSE){
		if ((!empty($mun_name))&&(!empty($prov_name))){
			if ($prov_name!="NOT PROVIDED")
				$prov_name = ", ".trim($prov_name);
			else
				$prov_name = "";
		}else{
			#$province = trim($prov_name);
			$prov_name = "";
		}
	}else
		$prov_name = " ";

	$m_address = $street_name.$brgy_name.$mun_name.$prov_name;

	$pdf->SetFont("Arial","",$fontSizeInput-1.0);
	$pdf->SetXY($x+20, $y+100);
	#$pdf->MultiCell(140, 4,mb_strtoupper($m_address), '', 'L','0');
	$pdf->MultiCell(133, 4,$m_address, '', 'L','0');
	$pdf->SetFont("Arial","",$fontSizeInput);

// FATHER
 //13. NAME
	#$pdf->SetFont("Arial","",$fontSizeInput-1);
	$pdf->SetFont("Arial","",$fontSizeInput);
	if ((($f_name_first=="N/A") || ($f_name_first=="n/a"))&&(($f_name_middle=="N/A") || ($f_name_middle=="n/a"))&&(($f_name_last=="N/A") || ($f_name_last=="n/a"))){
		$pdf->SetXY($x+70, $y+108);
		$pdf->MultiCell(10, 4,"N/A", '', 'C','0');
	}else{
		#$pdf->SetXY($x+30, $y+108);
		$pdf->SetXY($x+10, $y+109);
		#$pdf->MultiCell(42, 4,mb_strtoupper($f_name_first), '', 'L','0');
		$pdf->Cell(60, 4,mb_strtoupper($f_name_first),'', '0','C');

		#$pdf->SetXY($x+72, $y+108);
		$pdf->SetXY($x+70, $y+109);
		#$pdf->MultiCell(40, 4, mb_strtoupper($f_name_middle), '', 'L','0');
		$pdf->Cell(40, 4, mb_strtoupper($f_name_middle),'', '0','C');

		#$pdf->SetXY($x+115, $y+108);
		$pdf->SetXY($x+110, $y+109);
		#$pdf->MultiCell(40, 4,mb_strtoupper($f_name_last), '', 'L','0');
		$pdf->Cell(43, 4,mb_strtoupper($f_name_last),'', '0','C');
	}
	$pdf->SetFont("Arial","",$fontSizeInput);

 //14.CITIZENSHIP (FATHER)
	if ($f_citizenship=='PH')
		$f_citizenship = "FILIPINO";

	if (($f_citizenship=="n/a")||($f_citizenship=="N/A")||((($f_name_first=="N/A") || ($f_name_first=="n/a"))&&(($f_name_middle=="N/A") || ($f_name_middle=="n/a"))&&(($f_name_last=="N/A") || ($f_name_last=="n/a"))))
		$f_citizenship = "";
	$pdf->Text($x+16, $y+124, strtoupper($f_citizenship));

 //15. RELIGION (FATHER)
		$religion_obj = $obj_fetalDeathCert->getFReligion($f_religion);
	if ($religion_obj['religion_name']=="Not Applicable")
		$religion_obj['religion_name'] = "";
	$pdf->SetFont("Arial","",$fontSizeInput-1);
	$pdf->SetXY($x+38, $y+120);
	$pdf->MultiCell(20, 4,$religion_obj['religion_name'], '', 'C','0');
	$pdf->SetFont("Arial","",$fontSizeInput);

 //16. OCCUPATION (FATHER)
		$occupation_obj = $obj_fetalDeathCert->getFOccupation($f_occupation);
	if ($occupation_obj['occupation_name']=="Not Applicable" || $occupation_obj['occupation_name']=="None")
		$occupation_obj['occupation_name'] = "";
	$pdf->SetFont("Arial","",$fontSizeInput-1);
	$pdf->SetXY($x+67, $y+120);
	$pdf->MultiCell(35, 4,mb_strtoupper($occupation_obj['occupation_name']), '', 'C','0');
	$pdf->SetFont("Arial","",$fontSizeInput);
	//$pdf->Text($x+69, $y+124, $occupation_obj['occupation_name']);

 //17. AGE AT THE TIME OF THIS BIRTH (FATHER)
	if ($f_age==0)
		$f_age = "";

	$pdf->Text($x+137, $y+124, $f_age);

//18. DATE AND PLACE OF MARRIAGE OF PARENTS

	if (($parent_marriage_date!='0000-00-00') && (!empty($parent_marriage_date))){
		#if ($parent_marriage_date){
		if (($parent_marriage_place)||($parent_marriage_place!='N/A'))
			$parent_marriage_info_tmp = date("F d, Y",strtotime($parent_marriage_date))." at ".$parent_marriage_place;
		else
			$parent_marriage_info_tmp = date("F d, Y",strtotime($parent_marriage_date));
	}else{

		$parent_marriage_info_tmp = " ";
	}



	$pdf->Text($x+16, $y+135, $parent_marriage_info_tmp);

// MEDICAL CERTIFICATE --- --- --- --- ---
// 19. CAUSES OF FETAL DEATH
		$pdf->Text($x+48, $y+150, $tmp_death_cause['cause1']);   # Main disease/condition of fetus
		$pdf->Text($x+52, $y+154, $tmp_death_cause['cause2']);   # Other diseases/conditions of fetus
		$pdf->Text($x+66, $y+159, $tmp_death_cause['cause3']);   # Main maternal disease/condition affecting fetus
		$pdf->Text($x+67, $y+164, $tmp_death_cause['cause4']);   # Other maternal disease/condition affecting fetus
		$pdf->Text($x+46, $y+169, $tmp_death_cause['cause5']);   # Other relevant circumstances

 // 20. FETUS DIED

	if ($death_occurrence=="1")
		$pdf->Text($x+45, $y+174, "X");
	if ($death_occurrence=="2")
		$pdf->Text($x+75, $y+174.5, "X");
	if ($death_occurrence=="3")
		$pdf->Text($x+120, $y+174.5, "X");

 // 21. LENGTH OF PREGNANCY
		$pdf->Text($x+75, $y+179, $pregnancy_length);

 // 22a. ATTENDANT

	if ($attendant_type=='1')
		$pdf->Text($x+45, $y+184, "X");
	if ($attendant_type=='2')
		$pdf->Text($x+69, $y+184, "X");
	if ($attendant_type=='3')
		$pdf->Text($x+88, $y+184, "X");
	if ($attendant_type=='4')
		$pdf->Text($x+112, $y+184, "X");
	if ($attendant_type=='5'){
		$pdf->Text($x+8, $y+187.5, "X");
		$attendant_type_others = "other";
		$pdf->Text($x+45, $y+187.5, $attendant_type_others);
	}
	if ($attendant_type=='6')
		$pdf->Text($x+112, $y+187.5, "X");

 //22b. CERTIFICATION
	#if (($death_time !='00:00:00') && ($death_time!=""))
		if ($death_time!="")
		$death_time = convert24HourTo12HourLocal($death_time);
	else
		$death_time = '';
	if (($attendant_date_sign!='0000-00-00') && ($attendant_date_sign!="")){
		$tempYear = date("Y",strtotime($attendant_date_sign));
		$tempMonth = date("F",strtotime($attendant_date_sign));
		$tempDay = date("d",strtotime($attendant_date_sign));

		$attendant_date_sign =$tempDay." ".$tempMonth." ".$tempYear;
	}else{
		$attendant_date_sign = '';
	}

		$pdf->Text($x+65, $y+200, $death_time);
		$doctor = $pers_obj->get_Person_name($attendant_name);

		$middleInitial = "";
		if (trim($doctor['name_middle'])!=""){
			$thisMI=split(" ",$doctor['name_middle']);
			foreach($thisMI as $value){
				if (!trim($value)=="")
				$middleInitial .= $value[0];
			}
			if (trim($middleInitial)!="")
			$middleInitial .= ". ";
		}
		$doctor_name = $doctor["name_first"]." ".$doctor["name_2"]." ".$middleInitial.$doctor["name_last"];
		if (!empty($attendant_name))
			#$doctor_name = "Dr. ".ucwords(mb_strtolower($doctor_name));
			$doctor_name = mb_strtoupper($doctor_name).", MD";

		#$pdf->Text($x+26, $y+213.5, $doctor_name);
		$pdf->Text($x+22, $y+213.5, $doctor_name);

		#$pdf->Text($x+27.5, $y+217, $attendant_title);
		$pdf->Text($x+22, $y+217, $attendant_title);

		$pdf->SetXY($x+17, $y+218);
		#$pdf->MultiCell(60, 4,mb_strtoupper($attendant_address), '', 'L','0');
		#$pdf->MultiCell(60, 4,$attendant_address, '', 'L','0');
		$attendant_address = substr_replace(trim($attendant_address)," ",20,1);
		$pdf->MultiCell(55, 4,seg_ucwords($attendant_address), '0', 'L','0');

		$pdf->Text($x+17, $y+230, $attendant_date_sign);

 // 23. CORPSE DISPOSAL

	if ($corpse_disposal_tmp=='1')
		$pdf->Text($x+11, $y+240.5, "X");
	if ($corpse_disposal_tmp=='2')
		$pdf->Text($x+39, $y+240.5, "X");
	if ($corpse_disposal_tmp=='3'){
		$pdf->Text($x+11, $y+244.5, "X");

		$corpse_disposal_others = "0";
		$pdf->Text($x+44, $y+244.5, $corpse_disposal_others);
	}

 // 24. BURIAL/CREMATION PERMIT
 #$burial_permit = '000000';
	$pdf->Text($x+95, $y+240.5, $burial_permit);
	if ($burial_date_issued = "0000-00-00")
		$burial_date_issued = "";
	$pdf->Text($x+95, $y+244.5, $burial_date_issued);

 // 25. AUTOPSY

	if ($is_autopsy=='1')
		$pdf->Text($x+135, $y+241, "X");
	if ($is_autopsy=='2')
		$pdf->Text($x+135, $y+245, "X");

 // 26. NAME AND ADDRESS OF CEMETERY OR CREMATORY
 #$cemetery_name_address = 'cemetery';
	$pdf->Text($x+16, $y+253.5, $cemetery_name_address);

 // 27. INFORMANT
	if (($informant_date_sign!='0000-00-00') && ($informant_date_sign!="")){
		$tempYear = date("Y",strtotime($informant_date_sign));
		$tempMonth = date("F",strtotime($informant_date_sign));
		$tempDay = date("d",strtotime($informant_date_sign));

		$informant_date_sign =$tempDay." ".$tempMonth." ".$tempYear;
	}else{
		$informant_date_sign = '';
	}
		$pdf->Text($x+22, $y+267, mb_strtoupper($informant_name));
		$pdf->Text($x+35, $y+271, $informant_relation);
		$pdf->SetXY($x+96, $y+260);
		#$pdf->MultiCell(60, 4,mb_strtoupper($informant_address), '', 'L','0');
		$pdf->MultiCell(60, 4,$informant_address, '', 'L','0');
		$pdf->Text($x+98, $y+271.5, $informant_date_sign);

 // 28. PREPARED BY
	if (($encoder_date_sign!='0000-00-00') && ($encoder_date_sign!="")){
		$tempYear = date("Y",strtotime($encoder_date_sign));
		$tempMonth = date("F",strtotime($encoder_date_sign));
		$tempDay = date("d",strtotime($encoder_date_sign));

		$encoder_date_sign =$tempDay." ".$tempMonth." ".$tempYear;
	}else{
		$encoder_date_sign = '';
	}
		$pdf->Text($x+24, $y+288, mb_strtoupper($encoder_name));
		$pdf->Text($x+24, $y+292, $encoder_title);
		$pdf->Text($x+18, $y+296, $encoder_date_sign);

	$pdf->Output();
?>