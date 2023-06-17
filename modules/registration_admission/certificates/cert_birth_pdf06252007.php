<?php
include("roots.php");
include_once($root_path."/classes/fpdf/fpdf.php");
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');
include_once($root_path.'include/care_api_classes/class_person.php');
$person_obj=new Person;

if (isset($_GET['id']) && $_GET['id']){
	$pid = $_GET['id'];
}

if ($pid){
	if (!($basicInfo=$person_obj->BasicDataArray($pid))){
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
	$fontSizeLabel=9;
	$fontSizeInput=14;
	$fontSizeHeading=15;

	//Cell(float w [, float h [, string txt [, mixed border [, int ln [, string align [, int fill [, mixed link]]]]]]])
	$pdf = new FPDF();
	$pdf->AddPage("P");
	
	$pdf->SetLeftMargin(0);
#	$pdf->SetTopMargin(100);

	$pdf->Ln(14);
	$pdf->SetFont("Arial","",$fontSizeLabel-2);
    $pdf->Cell(158,4,'',"",'L');	
	
	$pdf->SetFont("Arial","B",$fontSizeLabel-2);
	$pdf->Cell(40,34,'',"",'L');	
	
//	$pdf->Ln(0);
/* $pdf->SetFont("Arial","",$fontSizeLabel-4);
    $pdf->Cell(140,2,'(To be accomplished in quadruplicate).',"L",'R');	
*/
	
	
	$pdf->SetFont("Arial","B",$fontSizeLabel);
	
	$pdf->Text(35, 62, "Davao del Sur");
	$pdf->Text(35, 67.5, "Davao City");
	$pdf->Text(115, 68, "$registry_nr");
	
//1. NAME
	$pdf->Text(33, 79, "$name_first");
	$pdf->Text(73, 79, "$name_middle");
	$pdf->Text(118, 79, "$name_last");
	
//2. SEX
	if ($sex=='m')
		$pdf->Text(15, 89, "x");
	if ($sex=='f')
		$pdf->Text(35, 89, "x");
	
//3. DATE OF BIRTH
	if ($birthDay<10)
		$birthDay = "0".$birthDay;
	if ($birthMonth<10)
		$birthMonth = "0".$birthMonth;

	$pdf->Text(110, 90, "$birthDay");
	$pdf->Text(125, 90, "$birthMonth");
	$pdf->Text(140, 90, "$birthYear");
	
//4. PLACE OF BIRTH
	$pdf->Text(35, 105, "Gonzales Maranan Medical Center Incorporated");
	$pdf->Text(90, 105, "DIGOS City");
	$pdf->Text(122, 105, "Davao del Sur");

//5a TYPE OF BIRTH
/*
	$pdf->Text(10, 118, "single");
	$pdf->Text(30, 118, "twin");
	$pdf->Text(20, 121.5, "triplet");
*/
	if ($birth_type=="1")
		$pdf->Text(8, 118, "x");
	if ($birth_type=="2")
		$pdf->Text(35, 118, "x");
	if (($birth_type!="")&&($birth_type!="1")&&($birth_type!="2"))
		$pdf->Text(20, 122, "x");
	
//5b IF MULTIPLE BIRTH, CHILD WAS
/*
	$pdf->Text(75, 118, "1st");
	$pdf->Text(103, 118, "2nd");
	$pdf->Text(85, 121.5, "Others");
	$pdf->Text(124, 121.5, "Specify");
*/
	if (intval($birth_rank) == 1)
		$pdf->Text(75, 118, "x");
	if (intval($birth_rank) == 2)
		$pdf->Text(105, 118, "x");
	if (intval($birth_rank) > 2){
		$pdf->Text(87, 121.5, "x");
		$pdf->Text(122, 121.5, "$birth_rank");
	}

//5c.BIRTH ORDER
    $pdf->Text(15, 134, "$birth_order");

//5d. WEIGHT AT BIRTH	
#	$pdf->Text(96, 134, "$birth_weight");
	$pdf->Text(100, 134, "$birth_weight");
	
//6. MAIDEN NAME
/*
	$pdf->Text(32, 146, "MAIDEN_Fname");
	$pdf->Text(73, 146, "MAIDEN middle");
	$pdf->Text(117, 146, "MAIDEN last");
*/
	$pdf->Text(25, 146, "$m_name_first");
	$pdf->Text(68, 146, "$m_name_middle");
	$pdf->Text(110, 146, "$m_name_last");
	
// 7.CITIZENSHIP
	$pdf->Text(35, 157, "$m_citizenship_name");

//8. RELIGION
	$pdf->Text(115, 157, "$m_religion_name");
	
//9a
	$pdf->Text(25, 172, "$m_total_alive");
	$pdf->Text(83, 172, "$m_still_living");
	$pdf->Text(138, 172, "$m_now_dead");

//10. OCCUPATION (MOTHER)
	$pdf->Text(45, 183, "$m_occupation_name");
	
//11. AGE AT THE TIME OF THIS BIRTH (MOTHER)
	$pdf->Text(127, 184.5, "$m_age");
	
//12. RESIDENCE
	$pdf->Text(30, 196, "Baranggay");
	$pdf->Text(85, 196, "City/Municipality");
	$pdf->Text(120, 196, "Province");

//13. FATHER'S NAME
/*
	$pdf->Text(35, 208.5, "FATHER_Fname");
	$pdf->Text(75, 208.5, "FATHER_middle");
	$pdf->Text(116, 208.5, "FATHER_last");
*/
	$pdf->Text(25, 208.5, "$f_name_first");
	$pdf->Text(68, 208.5, "$f_name_middle");
	$pdf->Text(110, 208.5, "$f_name_last");
	
//14.CITIZENSHIP (FATHER)
	$pdf->Text(35, 218, "$f_citizenship_name");

//15. RELIGION
	$pdf->Text(115, 218, "$f_religion_name");
	
//16. OCCUPATION (FATHER)
	$pdf->Text(45, 228, "$f_occupation_name");
	
//11. AGE AT THE TIME OF THIS BIRTH (FATHER)
	$pdf->Text(128, 230, "$f_age");
	

	
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
	$pdf->Output();	
?>