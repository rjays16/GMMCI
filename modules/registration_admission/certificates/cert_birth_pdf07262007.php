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
#	$pdf->SetTopMargin(100);
/*
	$pdf->Ln(14);
	$pdf->SetFont("Arial","",$fontSizeLabel-2);
    $pdf->Cell(158,4,'',"",'L');	
	
	$pdf->SetFont("Arial","B",$fontSizeLabel-2);
	$pdf->Cell(40,34,'',"",'L');	
*/	
//	$pdf->Ln(0);
/* $pdf->SetFont("Arial","",$fontSizeLabel-4);
    $pdf->Cell(140,2,'(To be accomplished in quadruplicate).',"L",'R');	
*/
	
	
	$pdf->SetFont("Arial","B",$fontSizeInput);
/*	
	$pdf->Text(35, 59, "Davao del Sur");
	$pdf->Text(35, 64.5, "Davao City");
	$pdf->Text(115, 65, "$registry_nr");
*/
	$pdf->Text(35, 56, "Davao del Sur");
	$pdf->Text(35, 61.5, "Davao City");
	$pdf->Text(115, 62, "$registry_nr");
	
//1. NAME
	$pdf->Text(36, 73, strtoupper($name_first));
	$pdf->Text(73, 73, strtoupper($name_middle));
	$pdf->Text(118, 73, strtoupper($name_last));
	
//2. SEX
	if ($sex=='m')
		$pdf->Text(21, 83, "x");
#	if ($sex=='f')
		$pdf->Text(44, 83, "x");
	
//3. DATE OF BIRTH
	if ($birthDay<10)
		$birthDay = "0".$birthDay;
	if ($birthMonth<10)
		$birthMonth = "0".$birthMonth;

	$pdf->Text(113, 83.5, "$birthDay");
	$pdf->Text(128, 83.5, "$birthMonth");
	$pdf->Text(136, 83.5, "$birthYear");
	
//4. PLACE OF BIRTH
	$pdf->SetFont("Arial","B",$fontSizeInput+2);
	$pdf->Text(35, 98, "Gonzales Maranan Medical Center Incorporated, DAVAO CITY");
#	$pdf->Text(110, 98, "Davao City");
#	$pdf->Text(122, 98, "Davao del Sur");

	$pdf->SetFont("Arial","B",$fontSizeInput);
//5a TYPE OF BIRTH
/*
	$pdf->Text(10, 118, "single");
	$pdf->Text(30, 118, "twin");
	$pdf->Text(20, 121.5, "triplet");
*/
#	if ($birth_type=="1")
		$pdf->Text(18, 109, "x");
#	if ($birth_type=="2")
		$pdf->Text(45, 109, "x");
#	if (($birth_type!="")&&($birth_type!="1")&&($birth_type!="2"))
		$pdf->Text(30, 113, "x");
	
//5b IF MULTIPLE BIRTH, CHILD WAS
/*
	$pdf->Text(75, 118, "1st");
	$pdf->Text(103, 118, "2nd");
	$pdf->Text(85, 121.5, "Others");
	$pdf->Text(124, 121.5, "Specify");
*/
#	if (intval($birth_rank) == 1)
		$pdf->Text(82, 109, "x");
#	if (intval($birth_rank) == 2)
		$pdf->Text(110, 109, "x");
#	if (intval($birth_rank) > 2){
		$pdf->Text(94, 113, "x");
		$pdf->Text(127, 113, "$birth_rank");
#	}

//5c.BIRTH ORDER
    $pdf->Text(35, 125, "$birth_order");

//5d. WEIGHT AT BIRTH	
#	$pdf->Text(96, 134, "$birth_weight");
	$pdf->Text(105, 125, "$birth_weight");
	
//6. MAIDEN NAME
/*
	$pdf->Text(32, 146, "MAIDEN_Fname");
	$pdf->Text(73, 146, "MAIDEN middle");
	$pdf->Text(117, 146, "MAIDEN last");
*/
	$pdf->Text(35, 138, "$m_name_first");
	$pdf->Text(75, 138, "$m_name_middle");
	$pdf->Text(115, 138, "$m_name_last");
	
// 7.CITIZENSHIP
	$pdf->Text(35, 149, "$m_citizenship_name");

//8. RELIGION
	$pdf->Text(110, 149, "$m_religion_name");
	
//9a
	$pdf->Text(32, 161, "$m_total_alive");
	$pdf->Text(86, 161, "$m_still_living");
	$pdf->Text(135, 161, "$m_now_dead");

//10. OCCUPATION (MOTHER)
	$pdf->Text(35, 174, "$m_occupation_name");
	
//11. AGE AT THE TIME OF THIS BIRTH (MOTHER)
	$pdf->Text(132, 174, "$m_age");
	
//12. RESIDENCE
	$pdf->Text(30, 185, "$m_residence_basic");
	$pdf->Text(85, 185, "$m_residence_mun");
	$pdf->Text(120, 185, "$m_residence_prov");

//13. FATHER'S NAME
/*
	$pdf->Text(35, 208.5, "FATHER_Fname");
	$pdf->Text(75, 208.5, "FATHER_middle");
	$pdf->Text(116, 208.5, "FATHER_last");
*/
	$pdf->Text(25, 195, "$f_name_first");
	$pdf->Text(68, 195, "$f_name_middle");
	$pdf->Text(110, 195, "$f_name_last");
	
//14.CITIZENSHIP (FATHER)
	$pdf->Text(35, 205, "$f_citizenship_name");

//15. RELIGION
	$pdf->Text(110, 205, "$f_religion_name");
	
//16. OCCUPATION (FATHER)
	$pdf->Text(35, 216, "$f_occupation_name");
	
//17. AGE AT THE TIME OF THIS BIRTH (FATHER)
	$pdf->Text(132, 216, "$f_age");
	
//18. DATE AND PLACE OF MARRIAGE OF PARENTS
	if ($marriage_type=='1')
		$pdf->Text(25, 234, "$parent_marriage_info_tmp");

//19a. ATTENDANT
#	if (($attendant_type=='1')
		$pdf->Text(17, 244, "x");
#	if (($attendant_type=='2')
		$pdf->Text(75, 244, "x");
#	if (($attendant_type=='3')
		$pdf->Text(128, 244, "x");
#	if (($attendant_type=='4')	
		$pdf->Text(17, 249, "x");
#	if (($attendant_type=='5'){
		$pdf->Text(75, 249, "x");
		$pdf->Text(110, 249, "$attendant_type_others");
#	}

//19b. CERTIFICATION OF BIRTH
	if (($birth_time !='00:00:00') && ($birth_time!=""))
		$birth_time = convert24HourTo12HourLocal($birth_time);
	else
		$birth_time = '';
	if (($attendant_date_sign!='0000-00-00') && ($attendant_date_sign!=""))
		$attendant_date_sign = @formatDate2Local($attendant_date_sign,$date_format);
	else
		$attendant_date_sign = '';

	$address1 = "";
	$address2 = "";
	$index = strlen($attendant_address);
	if (strlen($attendant_address)>35){
		$temp = substr($attendant_address,0,35);
		$index = strrpos($temp," ");
	}
	$address1 = trim(substr($attendant_address,0,$index));
	$address2 = trim(substr($attendant_address,$index));

		$pdf->Text(115, 259, "$birth_time");
		$pdf->Text(25, 274, "$attendant_name");
		$pdf->Text(32, 279, "$attendant_title");
#		$pdf->Text(97, 270, "$attendant_address");
		$pdf->Text(97, 270, "$address1");
		$pdf->Text(85, 274, "$address2");
		$pdf->Text(95, 279, "$attendant_date_sign");

//20. INFORMANT
	if (($informant_date_sign!='0000-00-00') && ($informant_date_sign!=""))
		$informant_date_sign = @formatDate2Local($informant_date_sign,$date_format);
	else
		$informant_date_sign = '';

	$address1 = "";
	$address2 = "";
	$index = strlen($informant_address);
	if (strlen($informant_address)>35){
		$temp = substr($informant_address,0,35);
		$index = strrpos($temp," ");
	}
	$address1 = trim(substr($informant_address,0,$index));
	$address2 = trim(substr($informant_address,$index));

		$pdf->Text(25, 297, "$informant_name");
		$pdf->Text(35, 302, "$informant_relation");
#		$pdf->Text(97, 293, "$informant_address");
		$pdf->Text(97, 293, "$address1");
		$pdf->Text(85, 297, "$address2");
		$pdf->Text(95, 302, "$informant_date_sign");

//21. PREPARED BY
	if (($encoder_date_sign!='0000-00-00') && ($encoder_date_sign!=""))
		$encoder_date_sign = @formatDate2Local($encoder_date_sign,$date_format);
	else
		$encoder_date_sign = '';

		$pdf->Text(25, 326, "$encoder_name");
		$pdf->Text(32, 330, "$encoder_title");
		$pdf->Text(20, 335, "$encoder_date_sign");

	if ($marriage_type=='1')
		$pdf->Output();

#	AFFIDAVIT OF ACKNOWLEDGMENT/ADMISSION OF PATERNITY
	$pdf->AddPage("P");

		$pdf->Text(32, 33, "$f_name_first $f_name_middle $f_name_last");
		$pdf->Text(130, 33, "$m_name_first $m_name_middle $m_name_last");

	if (($f_com_tax_date!='0000-00-00') && ($f_com_tax_date!=""))
		$f_com_tax_date = @formatDate2Local($f_com_tax_date,$date_format);
	else
		$f_com_tax_date = '';
	if (($m_com_tax_date!='0000-00-00') && ($m_com_tax_date!=""))
		$m_com_tax_date = @formatDate2Local($m_com_tax_date,$date_format);
	else
		$m_com_tax_date = '';

		$pdf->Text(44, 65, "$f_com_tax_nr");
		$pdf->Text(170, 65, "$m_com_tax_nr");
		$pdf->Text(30, 70, "$f_com_tax_date");
		$pdf->Text(156, 70, "$m_com_tax_date");
		$pdf->Text(30, 75, "$f_com_tax_place");
		$pdf->Text(156, 75, "$m_com_tax_place");

	if ($officer_date_sign!=""){
		$officerYear = intval(substr($officer_date_sign, 0, 4)); 
		$officerMonth = intval(substr($officer_date_sign, 5, 7)); 
		$officerDay = intval(substr($officer_date_sign, 8, 10));
		switch($officerMonth){
			case 1: $officerMonthName = 'January';	break;
			case 2: $officerMonthName = 'February';	break;
			case 3: $officerMonthName = 'March';	break;
			case 4: $officerMonthName = 'April';	break;
			case 5: $officerMonthName = 'May';	break;
			case 6: $officerMonthName = 'June';	break;
			case 7: $officerMonthName = 'July';	break;
			case 8: $officerMonthName = 'August';	break;
			case 9: $officerMonthName = 'September';	break;
			case 10: $officerMonthName = 'October';	break;
			case 11: $officerMonthName = 'November';	break;
			case 12: $officerMonthName = 'December';	break;
		}
	}

		$pdf->Text(107, 88, "$officerDay");
		$pdf->Text(135, 88, "$officerMonthName");
		$pdf->Text(188, 88, "$officerYear");
		$pdf->Text(25, 92, "$officer_place_sign");

		$address1 = "";
		$address2 = "";
		$index = strlen($officer_address);
		if (strlen($officer_address)>45){
			$temp = substr($officer_address,0,45);
			$index = strrpos($temp," ");
		}
		$address1 = trim(substr($officer_address,0,$index));
		$address2 = trim(substr($officer_address,$index));

		$pdf->Text(150, 105, "$officer_title");
#		$pdf->Text(145, 110, "$officer_address");
		$pdf->Text(130, 113, "$address1");
		$pdf->Text(130, 117, "$address2");
		$pdf->Text(25, 117, "$officer_name");

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