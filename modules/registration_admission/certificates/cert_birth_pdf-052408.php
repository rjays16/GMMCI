<?php
#edited by VAN 05-20-08
include("roots.php");
include_once($root_path."/classes/fpdf/fpdf.php");
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');

if (isset($_GET['id']) && $_GET['id']){
	$pid = $_GET['id'];
}

include_once($root_path.'include/care_api_classes/class_person.php');
$person_obj=new Person($pid);

if ($pid){
	if (!($basicInfo=$person_obj->getAllInfoArray($pid))){
		echo '<em class="warn"> sorry byt the page cannot be displayed!</em>';
		exit();
	}
	extract($basicInfo);
}else{
	echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid PID!</em>';
	exit();
}

$birthYear = intval(substr($date_birth, 0, 4)); 
$birthMonth = intval(substr($date_birth, 5, 7)); 
$birthDay = intval(substr($date_birth, 8, 10)); 

include_once($root_path.'include/care_api_classes/class_cert_birth.php');
$obj_birthCert = new BirthCertificate($pid);

$birthCertInfo = $obj_birthCert->getBirthCertRecord($pid);
if ($birthCertInfo){
	extract($birthCertInfo);
	#$marriage_type = substr($parent_marriage_info, 0, 1); 
	#$parent_marriage_info_tmp = substr($parent_marriage_info, 4); 
	$attendant_type = substr(trim($birthCertInfo['attendant_type']),0,1);
	$attendant_type_others = substr(trim($birthCertInfo['attendant_type']),4);
}

	$border="1";
	$border2="0";
	$space=2;
#	$fontSizeLabel=9;
	$fontSizeInput=11;
	#$fontSizeInput=12;
	$fontSizeHeading=14;

	$pdf = new FPDF("P","mm","Legal");
	$pdf->AddPage("P");
	
	$pdf->SetDisplayMode(real,'default');
	
	$x = $pdf->GetX();
	
	#left margin 10mm
	#top margin 30mm
	
	$y=$x*3;
	
	$pdf->SetXY($x,$y);
	$pdf->SetFont("Arial","B",$fontSizeInput);
	
	$pdf->SetY(-0.5);	
	$z = $pdf->GetY();

	$pdf->Text($x+30, $y-10.5, "Davao del Sur");
	$pdf->Text($x+30, $y-5, "Davao City");
	$pdf->Text($x+105, $y-4, $registry_nr);
	
//1. NAME
	$pdf->SetXY($x+10, $y+3);	
	$pdf->MultiCell(50, 4,strtoupper($name_first), '', 'C','0');
	
	$pdf->SetXY($x+60, $y+3);	
	$pdf->MultiCell(40, 4,strtoupper($name_middle), '', 'C','0');
	
	$pdf->SetXY($x+100, $y+3);	
	$pdf->MultiCell(40, 4,strtoupper($name_last), '', 'C','0');
	
//2. SEX
	if ($sex=='m')
		$pdf->Text($x+13, $y+17, "x");
	if ($sex=='f')
		$pdf->Text($x+35, $y+17, "x");

//3. DATE OF BIRTH
	$arrayMonth = array ("","January","February","March","April","May","June","July","August","September","October","November","December");
	$birthMonthName = $arrayMonth[$birthMonth];
	$pdf->Text($x+108, $y+18.5, $birthDay."   ".$birthMonthName."   ".$birthYear);

//4. PLACE OF BIRTH
	$pdf->SetFont("Arial","B",$fontSizeInput+2);
	$pdf->Text($x+30, $y+34, "DAVAO MEDICAL CENTER, DAVAO CITY");

	$pdf->SetFont("Arial","B",$fontSizeInput);
//5a TYPE OF BIRTH
	if ($birth_type=="1")
		$pdf->Text($x+9, $y+44, "x");
	if ($birth_type=="2")
		$pdf->Text($x+39, $y+44, "x");
	if (($birth_type!="")&&($birth_type!="1")&&($birth_type!="2"))
		$pdf->Text($x+20, $y+48, "x");


	if (intval($birth_rank) == 1)
		$pdf->Text($x+75, $y+44, "x");
	if (intval($birth_rank) == 2)
		$pdf->Text($x+107, $y+44, "x");
	if (intval($birth_rank) > 2){
		$pdf->Text($x+89, $y+48, "x");
		
		$rank = $obj_birthCert->convertWord($birth_rank);
		$pdf->Text($x+123, $y+47, $rank);
	}

//5c.BIRTH ORDER
	$pdf->Text($x+20, $y+61, $birth_order);
	
//5d. WEIGHT AT BIRTH	
	$pdf->Text($x+108, $y+61, number_format($birth_weight));
	
//6. MAIDEN NAME

	$pdf->SetXY($x+10, $y+69);	
	$pdf->MultiCell(50, 4,strtoupper($m_name_first), '', 'C','0');
	
	$pdf->SetXY($x+60, $y+69);	
	$pdf->MultiCell(40, 4,strtoupper($m_name_middle), '', 'C','0');
	
	$pdf->SetXY($x+100, $y+69);	
	$pdf->MultiCell(40, 4,strtoupper($m_name_last), '', 'C','0');
	
// 7.CITIZENSHIP
	$pdf->Text($x+37, $y+86, $m_citizenship);

//8. RELIGION
	$pdf->Text($x+110, $y+86, $m_religion_name);

//9a
	$pdf->Text($x+25, $y+98.5, $m_total_alive);
	$pdf->Text($x+80, $y+98.5, $m_still_living);
	$pdf->Text($x+135, $y+98.5, $m_now_dead);

//10. OCCUPATION (MOTHER)
	$pdf->Text($x+35, $y+112, $m_occupation_name);
	
//11. AGE AT THE TIME OF THIS BIRTH (MOTHER)
	$pdf->Text($x+128, $y+112, $m_age);
	
//12. RESIDENCE
	$m_address = $m_residence_basic;

	if (!empty($m_address) && !empty($m_residence_mun))
		$m_address = $m_address.", ".$m_residence_mun;
	else
		$m_address = $m_address." ".$m_residence_mun;

	if (!empty($m_address) && !empty($m_residence_prov))
		$m_address = $m_address.", ".$m_residence_prov;
	else
		$m_address = $m_address." ".$m_residence_prov;
		
	#$pdf->Text($x+20, $y+123, $m_address);
	$pdf->SetXY($x+10, $y+121);	
	$pdf->MultiCell(140, 4,$m_address, '', 'L','0');

//13. FATHER'S NAME
	if ((($f_name_first=="N/A") || ($f_name_first=="n/a"))&&(($f_name_middle=="N/A") || ($f_name_middle=="n/a"))&&(($f_name_last=="N/A") || ($f_name_last=="n/a"))){
		$pdf->SetXY($x+70, $y+130);	
		$pdf->MultiCell(10, 4,"n/a", '', 'C','0');
	}else{
		$pdf->SetXY($x+10, $y+130);	
		$pdf->MultiCell(50, 4,strtoupper($f_name_first), '', 'C','0');
	
		$pdf->SetXY($x+60, $y+130);	
		$pdf->MultiCell(40, 4, strtoupper($f_name_middle), '', 'C','0');
	
		$pdf->SetXY($x+100, $y+130);	
		$pdf->MultiCell(40, 4,strtoupper($f_name_last), '', 'C','0');
	}	
	
//14.CITIZENSHIP (FATHER)
	if (($f_citizenship=="n/a")||($f_citizenship=="N/A"))
		$f_citizenship = "";
		
	$pdf->Text($x+37, $y+144, $f_citizenship);

//15. RELIGION
	if ($f_religion_name=="Not Applicable")
		$f_religion_name = "";
	$pdf->Text($x+110, $y+144, $f_religion_name);
	
//16. OCCUPATION (FATHER)
	if ($f_occupation_name=="Not Applicable")
		$f_occupation_name = "";
	$pdf->Text($x+35, $y+157, $f_occupation_name);
	
//17. AGE AT THE TIME OF THIS BIRTH (FATHER)
	if ($f_age==0)
		$f_age = "";
	$pdf->Text($x+128, $y+155.5, $f_age);
	
//18. DATE AND PLACE OF MARRIAGE OF PARENTS
	
	#if ($marriage_type=='1'){
	if ($is_married=='1'){
		$parent_marriage_info_tmp = date("F d, Y",strtotime($parent_marriage_date))." at ".$parent_marriage_place;
		$pdf->Text($x+25, $y+175, $parent_marriage_info_tmp);
	}else
		$pdf->Text($x+65, $y+175, "n/a");

//19a. ATTENDANT

	if ($attendant_type=='1')
		$pdf->Text($x+9, $y+185, "x");
	if ($attendant_type=='2')
		$pdf->Text($x+9, $y+188, "x");
	if ($attendant_type=='3')
		$pdf->Text($x+69, $y+185, "x");
	if ($attendant_type=='4')	
		$pdf->Text($x+69, $y+188, "x");
	if ($attendant_type=='5'){
		$pdf->Text($x+123, $y+185, "x");
		$pdf->Text($x+105, $y+189, $attendant_type_others);
	}

//19b. CERTIFICATION OF BIRTH
	if (($birth_time !='00:00:00') && ($birth_time!=""))
		$birth_time = convert24HourTo12HourLocal($birth_time);
	else
		$birth_time = '';
	if (($attendant_date_sign!='0000-00-00') && ($attendant_date_sign!="")){
#		$attendant_date_sign = @formatDate2Local($attendant_date_sign,$date_format);
		$tempYear = intval(substr($attendant_date_sign, 0, 4)); 
		$tempMonth = intval(substr($attendant_date_sign, 5, 7)); 
		$tempDay = intval(substr($attendant_date_sign, 8, 10)); 
		$attendant_date_sign =$tempDay." ".$arrayMonth[$tempMonth]." ".$tempYear;
	}else{
		$attendant_date_sign = '';
	}

		$pdf->Text($x+112, $y+201, $birth_time);
		$pdf->Text($x+20, $y+216, strtoupper($attendant_name));
		$pdf->Text($x+20, $y+221, $attendant_title);
		
		$pdf->SetXY($x+91, $y+209);	
		$pdf->MultiCell(60, 4,strtoupper($attendant_address), '', 'L','0');
		
		$pdf->Text($x+92, $y+221, $attendant_date_sign);

//20. INFORMANT
	if (($informant_date_sign!='0000-00-00') && ($informant_date_sign!="")){
#		$informant_date_sign = @formatDate2Local($informant_date_sign,$date_format);
		$tempYear = intval(substr($informant_date_sign, 0, 4)); 
		$tempMonth = intval(substr($informant_date_sign, 5, 7)); 
		$tempDay = intval(substr($informant_date_sign, 8, 10)); 
		$informant_date_sign =$tempDay." ".$arrayMonth[$tempMonth]." ".$tempYear;
	}else{
		$informant_date_sign = '';
	}

		$pdf->Text($x+20, $y+240, strtoupper($informant_name));
		$pdf->Text($x+33, $y+246, $informant_relation);
		$pdf->SetXY($x+91, $y+233);	
		$pdf->MultiCell(60, 4,strtoupper($informant_address), '', 'L','0');
		$pdf->Text($x+92, $y+246, $informant_date_sign);

//21. PREPARED BY
	if (($encoder_date_sign!='0000-00-00') && ($encoder_date_sign!="")){
#		$encoder_date_sign = @formatDate2Local($encoder_date_sign,$date_format);
		$tempYear = intval(substr($encoder_date_sign, 0, 4)); 
		$tempMonth = intval(substr($encoder_date_sign, 5, 7)); 
		$tempDay = intval(substr($encoder_date_sign, 8, 10)); 
		$encoder_date_sign =$tempDay." ".$arrayMonth[$tempMonth]." ".$tempYear;
	}else{
		$encoder_date_sign = '';
	}
		$pdf->Text($x+21, $y+270, strtoupper($encoder_name));
		$pdf->Text($x+21, $y+274, $encoder_title);
		$pdf->Text($x+21, $y+279, $encoder_date_sign);
#echo "late = ".$is_late_reg;	
	#if ($marriage_type=='1')
	if ($is_married=='1')
		$pdf->Output();
		
#	AFFIDAVIT OF ACKNOWLEDGMENT/ADMISSION OF PATERNITY
	$pdf->AddPage("P");
	$x = $pdf->GetX();
	$y = $pdf->GetY();
	#$pdf->Line($x, $y, $x*20, $y);
#echo "x, y = ".$x." , ".$y;
		#$pdf->Text(35, 16, strtoupper("$f_name_first $f_name_middle $f_name_last"));
		if (($f_name_first=="n/a")&&($f_name_first=="n/a")&&($f_name_first=="n/a"))
			$father = "no father"; 
		else
		 	$father = "$f_name_first $f_name_middle $f_name_last";		
		$pdf->Text($x+25, $y+2, strtoupper($father));

	if (($f_com_tax_date!='0000-00-00') && ($f_com_tax_date!="")){
		$tempYear = intval(substr($f_com_tax_date, 0, 4)); 
		$tempMonth = intval(substr($f_com_tax_date, 5, 7)); 
		$tempDay = intval(substr($f_com_tax_date, 8, 10)); 
		$f_com_tax_date =$tempDay." ".$arrayMonth[$tempMonth]." ".$tempYear;
	}else{
		$f_com_tax_date = '';
	}

	if (($m_com_tax_date!='0000-00-00') && ($m_com_tax_date!="")){
		$tempYear = intval(substr($m_com_tax_date, 0, 4)); 
		$tempMonth = intval(substr($m_com_tax_date, 5, 7)); 
		$tempDay = intval(substr($m_com_tax_date, 8, 10)); 
		$m_com_tax_date =$tempDay." ".$arrayMonth[$tempMonth]." ".$tempYear;
	}else{
		$m_com_tax_date = '';
	}
		/*
		$pdf->Text(44, 49, $f_com_tax_nr);
		$pdf->Text(180, 49, $m_com_tax_nr);
		$pdf->Text(30, 54, $f_com_tax_date);
		$pdf->Text(165, 54, $m_com_tax_date);
		$pdf->Text(30, 60, $f_com_tax_place);
		$pdf->Text(165, 60, $m_com_tax_place);
		*/
		$pdf->Text($x+36, $y+35, $f_com_tax_nr);
		$pdf->Text($x+170, $y+35, $m_com_tax_nr);
		$pdf->Text($x+22, $y+40, $f_com_tax_date);
		$pdf->Text($x+155, $y+40, $m_com_tax_date);
		$pdf->Text($x+22, $y+46, $f_com_tax_place);
		$pdf->Text($x+155, $y+46, $m_com_tax_place);

	if ($officer_date_sign!=""){
		$officerYear = intval(substr($officer_date_sign, 0, 4)); 
		$officerMonth = intval(substr($officer_date_sign, 5, 7)); 
		$officerDay = intval(substr($officer_date_sign, 8, 10));
		$officerMonthName = $arrayMonth[$officerMonth];
	}
		/*	
		$pdf->Text(110, 72, $officerDay);
		$pdf->Text(138, 72, $officerMonthName);
		$pdf->Text(195, 72, $officerYear);
		$pdf->Text(25, 77, $officer_place_sign);
		*/
		if ($officerDay==0)
			$officerDay = "";
		
		if ($officerYear==0)	
			$officerYear = "";
			
		$pdf->Text($x+100, $y+58, $officerDay);
		$pdf->Text($x+133, $y+58, $officerMonthName);
		$pdf->Text($x+187, $y+58, $officerYear);
		$pdf->Text($x+15, $y+62.5, $officer_place_sign);
		/*
		$address1 = "";
		$address2 = "";
		$index = strlen($officer_address);
		if (strlen($officer_address)>45){
			$temp = substr($officer_address,0,45);
			$index = strrpos($temp," ");
		}
		$address1 = trim(substr($officer_address,0,$index));
		$address2 = trim(substr($officer_address,$index));
		*/
		/*
		$pdf->Text(150, 90, $officer_title);
		$pdf->Text(130, 98, $address1);
		$pdf->Text(130, 102, $address2);
		$pdf->Text(22, 103, strtoupper($officer_name));
		*/
		$pdf->Text($x+140, $y+76, $officer_title);
		#$pdf->Text($x+120, $y, $address1);
		#$pdf->Text($x+, $y, $address2);
		$pdf->SetXY($x+130, $y+85);	
		$pdf->MultiCell(80, 4,$officer_address, '', 'L','0');
		
		$pdf->Text($x+12, $y+88, strtoupper($officer_name));
		#$pdf->SetXY($x+12, $y+89);	
		#$pdf->MultiCell(80, 4,strtoupper($officer_name), '1', 'L','0');
/*		
if ($is_late_reg=='1')
		$pdf->Output();	
		
#AFFIDAVIT OF LATE/DELAYED REGISTRATION
	$pdf->AddPage("P");
	$x = $pdf->GetX();
	$y = $pdf->GetY();
	$pdf->Line($x, $y, $x*20, $y);				
*/
	$pdf->Output();	
?>