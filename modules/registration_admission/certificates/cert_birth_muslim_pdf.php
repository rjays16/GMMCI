<?php
#edited by VAN 05-20-08
include("roots.php");
include_once($root_path."/classes/fpdf/fpdf.php");
require_once($root_path.'include/inc_environment_global.php');
#include_once($root_path.'include/inc_date_format_functions.php');

if (isset($_GET['id']) && $_GET['id']){
	$pid = $_GET['id'];
}

include_once($root_path.'include/care_api_classes/class_person.php');
$person_obj=new Person($pid);

require_once($root_path.'include/care_api_classes/class_personell.php');
$pers_obj=new Personell;

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
    }

include_once($root_path.'include/care_api_classes/class_address.php');
$address_country = new Address('country');

if ($pid){

	if (!($basicInfo=$person_obj->getAllInfoArray($pid))){
		#echo $person_obj->sql;
		echo '<em class="warn"> sorry byt the page cannot be displayed!</em>';
		exit();
	}
	
	extract($basicInfo);
}else{
	echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid HRN!</em>';
	exit();
}

$birthYear = intval(substr($date_birth, 0, 4)); 
$birthMonth = intval(substr($date_birth, 5, 7)); 
$birthDay = intval(substr($date_birth, 8, 10)); 

include_once($root_path.'include/care_api_classes/class_cert_birth.php');
$obj_birthCert = new BirthCertificate($pid);


$wsign = $_GET['wsign'];
#echo 's = '.$wsign;

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

	$pdf = new FPDF("P","mm","Letter");
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
	#$pdf->Line($x, $y, $x*20,$y);
	$pdf->Text($x+35, $y-15, $row['prov_name']);
	$pdf->Text($x+35, $y-10, $row['mun_name']);
	$pdf->Text($x+155, $y-15, $registry_nr);
	
		
//1. NAME
	$pdf->SetXY($x+10, $y+3);	
	$pdf->MultiCell(40, 4,strtoupper($name_first), '', 'C','0');
	
	$pdf->SetXY($x+50, $y+3);	
	$pdf->MultiCell(35, 4,strtoupper($name_middle), '', 'C','0');
	
	$pdf->SetXY($x+85, $y+3);	
	$pdf->MultiCell(35, 4,strtoupper($name_last), '', 'C','0');
	
//DATE OF BIRTH
	$arrayMonth = array ("","January","February","March","April","May","June","July","August","September","October","November","December");
	$birthMonthName = $arrayMonth[$birthMonth];
	$pdf->Text($x+155, $y+5, $birthDay."   ".$birthMonthName."   ".$birthYear);

//ETHNIC ORIGIN
//FATHER
	$f_rs_ethnic = $person_obj->getEthnic_orig("nr = '$f_ethnic'");
	$father_ethnic = $f_rs_ethnic->FetchRow();
	#$pdf->Text($x+115, $y+21, strtoupper($father_ethnic['name']));	
	if ($f_ethnic!=1)
		$f_ethnic = strtoupper($father_ethnic['name']);
	else
		$f_ethnic = "";	
	$pdf->Text($x+20, $y+22, $f_ethnic);	

//MOTHER
	$m_rs_ethnic = $person_obj->getEthnic_orig("nr = '$m_ethnic'");
	$mother_ethnic = $m_rs_ethnic->FetchRow();
	#$pdf->Text($x+20, $y+22, strtoupper($mother_ethnic['name']));	
	
	if ($m_ethnic!=1)
		$m_ethnic = strtoupper($mother_ethnic['name']);
	else
		$m_ethnic = "";	
		
	$pdf->Text($x+115, $y+21, $m_ethnic);	

  
	
//INFORMANT
	if (($informant_date_sign!='0000-00-00') && ($informant_date_sign!="")){
		$tempYear = intval(substr($informant_date_sign, 0, 4)); 
		$tempMonth = intval(substr($informant_date_sign, 5, 7)); 
		$tempDay = intval(substr($informant_date_sign, 8, 10)); 
		$informant_date_sign =$tempDay." ".$arrayMonth[$tempMonth]." ".$tempYear;
	}else{
		$informant_date_sign = '';
	}
	
	if ($wsign){
		$perrec = '';
	}else{
		$informant_date_sign = '';
		$perrec = 'as per record';
	}
	
	
	$pdf->Text($x+40, $y+32, $perrec);
	$pdf->Text($x+35, $y+35.5, strtoupper($informant_name));
	$pdf->Text($x+50, $y+40, $informant_relation);
	$pdf->SetXY($x+127, $y+27);	
	$pdf->MultiCell(75, 4,strtoupper($informant_address), '0', 'L','0');
	$pdf->Text($x+127, $y+39, $informant_date_sign);

	$pdf->Output();	
?>