<?php
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
#	echo "basicInfo : <br> \n"; print_r($basicInfo); echo "<br>\n";
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
	$marriage_type = substr($parent_marriage_info, 0, 1); 
	$parent_marriage_info_tmp = substr($parent_marriage_info, 4); 
#	echo "marriage_type  = '".$marriage_type ."' <br> \n";
#	echo "parent_marriage_info_tmp = '".$parent_marriage_info_tmp."' <br> \n";
	$attendant_type = substr(trim($birthCertInfo['attendant_type']),0,1);
	$attendant_type_others = substr(trim($birthCertInfo['attendant_type']),4);
#	$attendant_type = substr(trim($attendant_type),0,1);
#	$attendant_type_others = substr(trim($attendant_type),4);
#	echo "attendant_type = '".$attendant_type."' <br> \n";
#	echo "attendant_type_others = '".$attendant_type_others."' <br> \n";
#	echo "birthCertInfo : <br>"; print_r($birthCertInfo); echo "<br> \n";
}

	$border="1";
	$border2="0";
	$space=2;
#	$fontSizeLabel=9;
	$fontSizeInput=11;
	$fontSizeHeading=14;

	//Cell(float w [, float h [, string txt [, mixed border [, int ln [, string align [, int fill [, mixed link]]]]]]])
#	$pdf = new FPDF();
/*
	$pdf = new FPDF("P","mm","Legal");
	$pdf = new FPDF("P","mm","Letter");
	$pdf = new FPDF("P","mm","A3");
	$pdf = new FPDF("P","mm","A4");
	$pdf = new FPDF("P","mm","A5");
*/
	$pdf = new FPDF("P","mm","Legal");
	$pdf->AddPage("P");
	
#	$pdf->SetLeftMargin(0);
	$pdf->SetTopMargin(30);
	
	$pdf->SetFont("Arial","B",$fontSizeInput);
/*
	$pdf->Text(30, 29, "Davao del Sur");
	$pdf->Text(38, 34.5, "Davao City");
	$pdf->Text(120, 35, $registry_nr);
*/
	$pdf->Text(30, 25, "Davao del Sur");
	$pdf->Text(38, 30.5, "Davao City");
	$pdf->Text(120, 31, $registry_nr);

//1. NAME
/*
	$pdf->Text(40, 45, strtoupper($name_first));
	$pdf->Text(80, 45, strtoupper($name_middle));
	$pdf->Text(120, 45, strtoupper($name_last));
*/
	$pdf->Text(30, 42, strtoupper($name_first));
	$pdf->Text(80, 42, strtoupper($name_middle));
	$pdf->Text(120, 42, strtoupper($name_last));

//2. SEX
/*
#	if ($sex=='m')
		$pdf->Text(25, 55, "x");
#	if ($sex=='f')
		$pdf->Text(48, 55, "x");
*/
#	if ($sex=='m')
		$pdf->Text(22, 52, "x");
#	if ($sex=='f')
		$pdf->Text(45, 52, "x");

//3. DATE OF BIRTH
	$arrayMonth = array ("","January","February","March","April","May","June","July","August","September","October","November","December");
	$birthMonthName = $arrayMonth[$birthMonth];
#	$pdf->Text(120, 57, $birthDay."  ".$birthMonthName."  ".$birthYear);
	$pdf->Text(118, 53, $birthDay."  ".$birthMonthName."  ".$birthYear);

//4. PLACE OF BIRTH
	$pdf->SetFont("Arial","B",$fontSizeInput+2);
	$pdf->Text(40, 70, "Gonzales Maranan Medical Center Incorporated, DAVAO CITY");

	$pdf->SetFont("Arial","B",$fontSizeInput);
//5a TYPE OF BIRTH
/*
	$pdf->Text(10, 118, "single");
	$pdf->Text(30, 118, "twin");
	$pdf->Text(20, 121.5, "triplet");
*/
/*
#	if ($birth_type=="1")
		$pdf->Text(20, 82, "x");
#	if ($birth_type=="2")
		$pdf->Text(50, 82, "x");
#	if (($birth_type!="")&&($birth_type!="1")&&($birth_type!="2"))
		$pdf->Text(32, 86, "x");
*/
#	if ($birth_type=="1")
		$pdf->Text(18, 79, "x");
#	if ($birth_type=="2")
		$pdf->Text(48, 79, "x");
#	if (($birth_type!="")&&($birth_type!="1")&&($birth_type!="2"))
		$pdf->Text(30, 83, "x");


//5b IF MULTIPLE BIRTH, CHILD WAS
/*
	$pdf->Text(75, 118, "1st");
	$pdf->Text(103, 118, "2nd");
	$pdf->Text(85, 121.5, "Others");
	$pdf->Text(124, 121.5, "Specify");
*/
/*
#	if (intval($birth_rank) == 1)
		$pdf->Text(90, 82, "x");
#	if (intval($birth_rank) == 2)
		$pdf->Text(122, 82, "x");
#	if (intval($birth_rank) > 2){
		$pdf->Text(102, 86, "x");
		$pdf->Text(135, 86, $birth_rank);
#	}
*/
#	if (intval($birth_rank) == 1)
		$pdf->Text(85, 79, "x");
#	if (intval($birth_rank) == 2)
		$pdf->Text(117, 79, "x");
#	if (intval($birth_rank) > 2){
		$pdf->Text(98, 83, "x");
		$pdf->Text(132, 83, $birth_rank);
#	}

//5c.BIRTH ORDER
#	$pdf->Text(35, 98, $birth_order);
	$pdf->Text(35, 96, $birth_order);
	
//5d. WEIGHT AT BIRTH	
#	$pdf->Text(108, 98, $birth_weight);
	$pdf->Text(108, 96, $birth_weight);
	
//6. MAIDEN NAME
/*
	$pdf->Text(32, 146, "MAIDEN_Fname");
	$pdf->Text(73, 146, "MAIDEN middle");
	$pdf->Text(117, 146, "MAIDEN last");
*/
/*
	$pdf->Text(35, 138, "$m_name_first");
	$pdf->Text(75, 138, "$m_name_middle");
	$pdf->Text(115, 138, "$m_name_last");
*/
	$pdf->Text(35, 110, strtoupper($m_name_first));
	$pdf->Text(75, 110, strtoupper($m_name_middle));
	$pdf->Text(115, 110, strtoupper($m_name_last));

	
// 7.CITIZENSHIP
#	$pdf->Text(35, 122, $m_citizenship_name);
#	$pdf->Text(35, 121, $m_citizenship_name);
	$pdf->Text(35, 121, $m_citizenship);

//8. RELIGION
#	$pdf->Text(100, 122, $m_religion_name);
	$pdf->Text(100, 121, $m_religion_name);

	
//9a
	$pdf->Text(32, 134, $m_total_alive);
	$pdf->Text(88, 134, $m_still_living);
	$pdf->Text(140, 134, $m_now_dead);

//10. OCCUPATION (MOTHER)
	$pdf->Text(35, 147, $m_occupation_name);
	
//11. AGE AT THE TIME OF THIS BIRTH (MOTHER)
	$pdf->Text(135, 146, $m_age);
	
//12. RESIDENCE
/*
	$pdf->Text(30, 185, "$m_residence_basic");
	$pdf->Text(85, 185, "$m_residence_mun");
	$pdf->Text(120, 185, "$m_residence_prov");
*/
#	$pdf->Text(35, 158, $m_residence_basic.", ".$m_residence_mun.", ".$m_residence_prov);

	$m_address = $m_residence_basic;
	if (!empty($m_address) && !empty($m_residence_mun))
		$m_address = $m_address.", ".$m_residence_mun;
	else
		$m_address = $m_address." ".$m_residence_mun;

	if (!empty($m_address) && !empty($m_residence_prov))
		$m_address = $m_address.", ".$m_residence_prov;
	else
		$m_address = $m_address." ".$m_residence_prov;
		
	$pdf->Text(35, 158, $m_address);

//13. FATHER'S NAME
/*
	$pdf->Text(35, 208.5, "FATHER_Fname");
	$pdf->Text(75, 208.5, "FATHER_middle");
	$pdf->Text(116, 208.5, "FATHER_last");
*/
/*
	$pdf->Text(35, 168, $f_name_first);
	$pdf->Text(75, 168, $f_name_middle);
	$pdf->Text(115, 168, $f_name_last);
*/
	$pdf->Text(35, 170, strtoupper($f_name_first));
	$pdf->Text(75, 170, strtoupper($f_name_middle));
	$pdf->Text(115, 170, strtoupper($f_name_last));
	
//14.CITIZENSHIP (FATHER)
#	$pdf->Text(35, 178, $f_citizenship_name);
#	$pdf->Text(35, 179, $f_citizenship_name);
	$pdf->Text(35, 179, $f_citizenship);

//15. RELIGION
#	$pdf->Text(110, 178, $f_religion_name);
	$pdf->Text(110, 179, $f_religion_name);
	
//16. OCCUPATION (FATHER)
#	$pdf->Text(35, 190, $f_occupation_name);
	$pdf->Text(35, 191, $f_occupation_name);
	
//17. AGE AT THE TIME OF THIS BIRTH (FATHER)
#	$pdf->Text(132, 190, $f_age);
	$pdf->Text(132, 190, $f_age);
	
//18. DATE AND PLACE OF MARRIAGE OF PARENTS
	if ($marriage_type=='1')
		$pdf->Text(25, 209, $parent_marriage_info_tmp);
	else
		$pdf->Text(35, 209, "N/A");

//19a. ATTENDANT
/*
#	if (($attendant_type=='1')
		$pdf->Text(18, 218, "x");
#	if (($attendant_type=='2')
		$pdf->Text(78, 218, "x");
#	if (($attendant_type=='3')
		$pdf->Text(132, 218, "x");
#	if (($attendant_type=='4')	
		$pdf->Text(18, 222, "x");
#	if (($attendant_type=='5'){
		$pdf->Text(78, 222, "x");
		$pdf->Text(115, 222, $attendant_type_others);
#	}
*/

#	if (($attendant_type=='1')
		$pdf->Text(18, 219, "x");
#	if (($attendant_type=='2')
		$pdf->Text(78, 219, "x");
#	if (($attendant_type=='3')
		$pdf->Text(132, 219, "x");
#	if (($attendant_type=='4')	
		$pdf->Text(18, 223, "x");
#	if (($attendant_type=='5'){
		$pdf->Text(78, 223, "x");
		$pdf->Text(115, 223, $attendant_type_others);
#	}

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

	$address1 = "";
	$address2 = "";
	$index = strlen($attendant_address);
	if (strlen($attendant_address)>35){
		$temp = substr($attendant_address,0,35);
		$index = strrpos($temp," ");
	}
	$address1 = trim(substr($attendant_address,0,$index));
	$address2 = trim(substr($attendant_address,$index));
/*
		$pdf->Text(115, 236, $birth_time);
		$pdf->Text(27, 251, $attendant_name);
		$pdf->Text(32, 256, $attendant_title);
		$pdf->Text(102, 247, $address1);
		$pdf->Text(88, 251, $address2);
		$pdf->Text(98, 256, $attendant_date_sign);
*/
		$pdf->Text(120, 235, $birth_time);
		$pdf->Text(29, 251, strtoupper($attendant_name));
		$pdf->Text(32, 256, $attendant_title);
		$pdf->Text(101, 247, $address1);
		$pdf->Text(88, 251, $address2);
		$pdf->Text(100, 256, $attendant_date_sign);

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

	$address1 = "";
	$address2 = "";
	$index = strlen($informant_address);
	if (strlen($informant_address)>35){
		$temp = substr($informant_address,0,35);
		$index = strrpos($temp," ");
	}
	$address1 = trim(substr($informant_address,0,$index));
	$address2 = trim(substr($informant_address,$index));

/*
		$pdf->Text(28, 273, $informant_name);
		$pdf->Text(38, 278, $informant_relation);
		$pdf->Text(102, 269, $address1);
		$pdf->Text(88, 273, $address2);
		$pdf->Text(98, 278, $informant_date_sign);
*/
		$pdf->Text(30, 275, strtoupper($informant_name));
		$pdf->Text(40, 280, $informant_relation);
		$pdf->Text(101, 271, $address1);
		$pdf->Text(88, 275, $address2);
		$pdf->Text(100, 281, $informant_date_sign);

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
/*
		$pdf->Text(28, 299, $encoder_name);
		$pdf->Text(32, 303, $encoder_title);
		$pdf->Text(20, 307, $encoder_date_sign);
*/
		$pdf->Text(30, 304, strtoupper($encoder_name));
		$pdf->Text(34, 309, $encoder_title);
		$pdf->Text(20, 314, $encoder_date_sign);

	if ($marriage_type=='1')
		$pdf->Output();

#	AFFIDAVIT OF ACKNOWLEDGMENT/ADMISSION OF PATERNITY
	$pdf->AddPage("P");

#		$pdf->Text(35, 5, "$f_name_first $f_name_middle $f_name_last");
#		$pdf->Text(135, 5, "$m_name_first $m_name_middle $m_name_last");
		$pdf->Text(35, 16, strtoupper("$f_name_first $f_name_middle $f_name_last"));
#		$pdf->Text(135, 16, strtoupper("$m_name_first $m_name_middle $m_name_last"));

	if (($f_com_tax_date!='0000-00-00') && ($f_com_tax_date!="")){
#		$f_com_tax_date = @formatDate2Local($f_com_tax_date,$date_format);
		$tempYear = intval(substr($f_com_tax_date, 0, 4)); 
		$tempMonth = intval(substr($f_com_tax_date, 5, 7)); 
		$tempDay = intval(substr($f_com_tax_date, 8, 10)); 
		$f_com_tax_date =$tempDay." ".$arrayMonth[$tempMonth]." ".$tempYear;
	}else{
		$f_com_tax_date = '';
	}

	if (($m_com_tax_date!='0000-00-00') && ($m_com_tax_date!="")){
#		$m_com_tax_date = @formatDate2Local($m_com_tax_date,$date_format);
		$tempYear = intval(substr($m_com_tax_date, 0, 4)); 
		$tempMonth = intval(substr($m_com_tax_date, 5, 7)); 
		$tempDay = intval(substr($m_com_tax_date, 8, 10)); 
		$m_com_tax_date =$tempDay." ".$arrayMonth[$tempMonth]." ".$tempYear;
	}else{
		$m_com_tax_date = '';
	}
/*
		$pdf->Text(44, 30, $f_com_tax_nr);
		$pdf->Text(185, 30, $m_com_tax_nr);
		$pdf->Text(30, 35, $f_com_tax_date);
		$pdf->Text(165, 35, $m_com_tax_date);
		$pdf->Text(30, 40, $f_com_tax_place);
		$pdf->Text(165, 40, $m_com_tax_place);
*/
		$pdf->Text(44, 49, $f_com_tax_nr);
		$pdf->Text(180, 49, $m_com_tax_nr);
		$pdf->Text(30, 54, $f_com_tax_date);
		$pdf->Text(165, 54, $m_com_tax_date);
		$pdf->Text(30, 60, $f_com_tax_place);
		$pdf->Text(165, 60, $m_com_tax_place);

	if ($officer_date_sign!=""){
		$officerYear = intval(substr($officer_date_sign, 0, 4)); 
		$officerMonth = intval(substr($officer_date_sign, 5, 7)); 
		$officerDay = intval(substr($officer_date_sign, 8, 10));
		$officerMonthName = $arrayMonth[$officerMonth];
	}
/*
		$pdf->Text(110, 52, $officerDay);
		$pdf->Text(138, 52, $officerMonthName);
		$pdf->Text(192, 52, $officerYear);
		$pdf->Text(25, 56, $officer_place_sign);
*/
		$pdf->Text(110, 72, $officerDay);
		$pdf->Text(138, 72, $officerMonthName);
		$pdf->Text(195, 72, $officerYear);
		$pdf->Text(25, 77, $officer_place_sign);

		$address1 = "";
		$address2 = "";
		$index = strlen($officer_address);
		if (strlen($officer_address)>45){
			$temp = substr($officer_address,0,45);
			$index = strrpos($temp," ");
		}
		$address1 = trim(substr($officer_address,0,$index));
		$address2 = trim(substr($officer_address,$index));
/*
		$pdf->Text(150, 70, $officer_title);
		$pdf->Text(132, 78, $address1);
		$pdf->Text(132, 82, $address2);
		$pdf->Text(25, 82, $officer_name);
*/
		$pdf->Text(150, 90, $officer_title);
		$pdf->Text(130, 98, $address1);
		$pdf->Text(130, 102, $address2);
		$pdf->Text(22, 103, strtoupper($officer_name));

	$pdf->Output();	
/*	
	$pdf->Ln(1);
	$pdf->SetFont("Arial","","12");
	$pdf->Cell(158,8,'',"",1,'C');
	
		//$pdf->Ln(3);
	$pdf->Cell(158,8,'',"",1,'C');


	//$pdf->Ln(1);
	$pdf->SetFont('Arial','B',14);
	$pdf->Cell(158,8,'',"",1,'C');
	
	//$pdf->Ln(5);
	$pdf->SetFont("Arial","","8");
	$pdf->Cell(158,3,'',"",1,'C');
	$pdf->Cell(158,6,'',"",1,'C');
	
	
	
	$pdf->SetFont("Arial","",$fontSizeLabel);
    $pdf->Cell(30,9,'',"",0,1,'L');	
	$pdf->Cell(78,10,'      Davao Del Sur',"",'C');
	$pdf->Cell(50,9,'',"",'L');

	
	
	
	$pdf->Ln(4);

$pdf->Cell(30,9,'',"");
$pdf->Cell(78,9,'      Davao City',"",'C');
$pdf->Cell(50,9,'            2000793480',"",'L');

$pdf->Ln(8);
$pdf->Cell(7,40,'',"");



$pdf->Cell(151,6,'    ',"");

$pdf->Ln(6);
$pdf->Cell(7);
$pdf->Cell(151,6,'                                 Carlo Domingo                                Dumaguin                               Erasquin',"");

$pdf->Ln(6);

$pdf->Cell(7);
$pdf->Cell(66,6,'',"");
$pdf->Cell(85,6,'',"");

$pdf->Ln(6);
$pdf->Cell(7);
$pdf->Cell(66,6,'    X       ',"");
$pdf->Cell(85,6,'                                     21           June         1977    ',"");

$pdf->Ln(6);

$pdf->Cell(7);
$pdf->Cell(151,10,'',"");

$pdf->Ln(6);

$pdf->Cell(7);
$pdf->Cell(151,10,'                                                           Davao City ,                     Davao Del Sur',"");



$pdf->Ln(9);
$pdf->Cell(7);
$pdf->Cell(66,9,'',"");
$pdf->Cell(85,9,'',"");

$pdf->Ln(6);
$pdf->Cell(7);
$pdf->Cell(66,9,'  X ',"");
$pdf->Cell(85,9,'N/A',"");


$pdf->Ln(7);
$pdf->Cell(7);
$pdf->Cell(90,9,'',"");
$pdf->Cell(61,9,'',"");

$pdf->Ln(7);
$pdf->Cell(7);
$pdf->Cell(90,9,'       Live Birth ',"");
$pdf->Cell(61,9,'  5lbs 8oz',"");



$pdf->Ln(8);
$pdf->Cell(7,44,'',"");
$pdf->Cell(151,9,'    ',"");

$pdf->Ln(5);

$pdf->Cell(7);
$pdf->Cell(151,9,'                               Olivia                         Gamon                      Dumaguin',"");


$pdf->Ln(8);
$pdf->Cell(7);
$pdf->Cell(90,8,'',"");
$pdf->Cell(61,8,'',"");

$pdf->Ln(3);
$pdf->Cell(7);
$pdf->Cell(90,8,'                              Filipino ',"");
$pdf->Cell(61,8,'                      Pentecostal',"");


$pdf->Ln(8);
$pdf->Cell(7);
$pdf->Cell(50,9,'',"");
$pdf->Cell(50,9,'',"");
$pdf->Cell(51,9,'',"");



$pdf->Ln(5);
$pdf->Cell(7);
$pdf->Cell(50,9,'                      1',"");
$pdf->Cell(50,9,'                      1',"");
$pdf->Cell(51,9,'                        0',"");

$pdf->Ln(9);
$pdf->Cell(7);
$pdf->Cell(100,8,' ',"");
$pdf->Cell(51,8,'',"");

$pdf->Ln(4);
$pdf->Cell(7);
$pdf->Cell(100,8,'                                                       Housewife ',"");
$pdf->Cell(51,8,'                                  24',"");

$pdf->Ln(7);
$pdf->Cell(7);
$pdf->Cell(151,6,'    ',"");

$pdf->Ln(6);
$pdf->Cell(7);
$pdf->Cell(151,6,'                                Dumoy                            Davao City                  Davao del Sur',"");

$pdf->Ln(6);
$pdf->Cell(7,24,'',"");
$pdf->Cell(151,7,'    ',"");

$pdf->Ln(5);
$pdf->Cell(7);
$pdf->Cell(151,7,'                                 Oscar                 de Asis               Erasquin',"");

$pdf->Ln(4);
$pdf->Cell(7);
$pdf->Cell(90,5,'',"");
$pdf->Cell(61,5,'',"");

$pdf->Ln(5);
$pdf->Cell(7);
$pdf->Cell(90,5,'                                                          Filipino',"");
$pdf->Cell(61,5,'               Pentecostal',"");

//Occupation & Age at the time of this birth
$pdf->Ln(5);
$pdf->Cell(7);
$pdf->Cell(90,8,'   ',"");
$pdf->Cell(61,8,'',"");

$pdf->Ln(5);
$pdf->Cell(7);
$pdf->Cell(100,8,'                                           Engineer ',"");
$pdf->Cell(51,8,'                          24',"");


*/
?>