<?php

include("roots.php");
include_once($root_path."/classes/fpdf/fpdf.php");
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');

require_once($root_path.'/include/care_api_classes/class_drg.php');
$objDRG= new DRG;

include_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter;

include_once($root_path.'include/care_api_classes/class_cert_med.php');

include_once($root_path.'include/care_api_classes/class_ward.php');
$ward_obj=new Ward;

include_once($root_path.'include/care_api_classes/class_personell.php');
$pers_obj=new Personell;

require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
$objInfo = new Hospital_Admin();

include_once($root_path.'include/care_api_classes/class_cert_birth.php');
$obj_birthCert = new BirthCertificate($pid);


//$_GET['encounter_nr'] = 2007500006;
/*
if($_GET['encounter_nr']){
	if(!($encInfo = $enc_obj->getEncounterInfo($_GET['encounter_nr']))){
		echo '<em class="warn"> sorry byt the page cannot be displayed!</em>';
		exit();
	}
	extract($encInfo);
}else{
	echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid Case Number!</em>';
	exit();
}
*/
#echo $enc_obj->sql;
//set border
$border="1";
$border2="0";
$spacing =2;
// font setup
$fontSizeLabel = 8;
$fontSizeInput = 11;
$fontSizeText = 12;
$fontSizeHeader = 14;
//fontstyle setup
$fontStyle = "Arial";
$fontStyle2 = "Times";

$birthCertInfo = $obj_birthCert->getBirthCertRecord($pid);
$count = $obj_birthCert->count;
#echo "ss = ".$count;
#echo $obj_birthCert->sql;
if($birthCertInfo){
	extract($birthCertInfo);
}

//instantiate fpdf class
$pdf  = new FPDF("P","mm","Letter");
$pdf->AddPage("P");
$pdf->SetLeftMargin(26);
$pdf->SetRightMargin(25.5);

$pdf->SetFont($fontStyle, "", $fonSizeInput);
#$pdf->SetXY(20,$y-10);
$pdf->Cell(0,4,'DMC FORM No. 58-A', $border2,1,'L');


$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetXY($x,$y+10);

/*
//Header - Republic of the Philippines / Department of Health
$pdf->SetFont($fontStyle, "", $fonSizeInput);
$pdf->Cell(0,4,'Republic of the Philippines', $border2,1,'C');
$pdf->Ln(1);
$pdf->Cell(0,4,'DEPARTMENT OF HEALTH', $border2,1,'C');

//Hospital name- Davao Medical Center
$pdf->Ln(2);
$pdf->setFont($fontStyle,"B", $fontSizeHeader-2);
$pdf->Cell(0,4,'DAVAO MEDICAL CENTER',$border2, 1, 'C');

//Hospital Address
$pdf->Ln(2);
$pdf->setFont($fontStyle,"", $fontSizeInput);
$pdf->Cell(0,4,'Bajada, Davao City',$border2, 1, 'C');
*/
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

		$pdf->SetFont("Arial","","11");
		$pdf->Cell(0,4,$row['hosp_country'],$border2,1,'C');
		#$pdf->SetFont("Arial","","11");
		$pdf->Cell(0,4,$row['hosp_agency'],$border2,1,'C');

		$pdf->Ln(1);
		$pdf->SetFont("Arial","B","12");
		$pdf->Cell(0,4,$row['hosp_name'],$border2,1,'C');

		$pdf->SetFont("Arial","","11");
		$pdf->Cell(0,4,$row['hosp_addr1'],$border2,1,'C');

$pdf->Ln(8);
$pdf->setFont($fontStyle,"", $fontSizeInput);
$pdf->Cell(0,4,'Date : '.date("F d, Y"),$border2, 1, 'R');

$pdf->Ln(6);
$pdf->setFont($fontStyle,"B", $fontSizeHeader);

$pdf->Cell(10,4,'',$border2, 0, '');
$pdf->MultiCell(160, 6,'RELEASE OF RESPONSIBILITY FOR ERRONEOUS ENTRY ON BIRTH CERTIFICATE', '0', 'C','0');

$pdf->Image($root_path.'gui/img/logos/dmc_logo.jpg',30,15,25,30);

$pdf->Ln(6);
$pdf->Cell(0,4, '', "T","0",'L');

#Name
$pdf->Ln(3);
$pdf->SetFont($fontStyle,"", $fontSizeText);
$pdf->Cell(30,3, 'Name :', $border2,0,'L');

#$name_patient = stripslashes(strtoupper($name_first)).' '.stripslashes(strtoupper($name_middle)).' '.stripslashes(strtoupper($name_last));
if ($count){
	$pdf->SetFont($fontStyle,"B", $fontSizeText);
	$name_patient = stripslashes(strtoupper($name_last)).', '.stripslashes(strtoupper($name_first)).' '.stripslashes(strtoupper($name_middle));
}else{
	$pdf->SetFont($fontStyle,"I", $fontSizeText);
	$name_patient = "NO BIRTH CERTIFICATE RECORD YET";
}
$pdf->Cell(0,3, $name_patient, $border2,0,'L');

#Age
$pdf->Ln(5);
$pdf->SetFont($fontStyle,"", $fontSizeText);
$pdf->Cell(30,3, 'Age :', $border2,0,'L');
$pdf->SetFont($fontStyle,"B", $fontSizeText);

if (stristr($age,'years')){
				$age = substr($age,0,-5);
				if ($age>1)
						$labelyear = "years";
				else
						$labelyear = "year";

				$age = floor($age)." ".$labelyear;
		}elseif (stristr($age,'year')){
				$age = substr($age,0,-4);
				if ($age>1)
						$labelyear = "years";
				else
						$labelyear = "year";

				$age = floor($age)." ".$labelyear;

		}elseif (stristr($age,'months')){
				$age = substr($age,0,-6);
				if ($age>1)
						$labelmonth = "months";
				else
						$labelmonth = "month";

				$age = floor($age)." ".$labelmonth;

		}elseif (stristr($age,'month')){
				$age = substr($age,0,-5);

				if ($age>1)
						$labelmonth = "months";
				else
						$labelmonth = "month";

				$age = floor($age)." ".$labelmonth;

		}elseif (stristr($age,'days')){
				$age = substr($age,0,-4);

				if ($age>30){
						$age = $age/30;
						if ($age>1)
								$label = "months";
						else
								$label = "month";

				}else{
						if ($age>1)
								$label = "days";
						else
								$label = "day";
				}

				$age = floor($age).' '.$label;

		}elseif (stristr($age,'day')){
				$age = substr($age,0,-3);

				if ($age>1)
						$labelday = "days";
				else
						$labelday = "day";

				$age = floor($age)." ".$labelday;
		}else{
				if ($age){
						if ($age>1)
								$labelyear = "years";
						else
								$labelyear = "year";

						$age = $age." ".$labelyear;
				}else
						$age = "0 day";
		}

$pdf->Cell(50,3, $age.' old', $border2,0,'L');

#Sex
$pdf->SetFont($fontStyle,"", $fontSizeText);
$pdf->Cell(35,3, 'Sex :', $border2,0,'L');
$pdf->SetFont($fontStyle,"B", $fontSizeText);
if ($sex=='f')
	$gender = "FEMALE";
elseif ($sex='M')
	$gender = "MALE";

$pdf->Cell(50,3, stripslashes(strtoupper($gender)), $border2,0,'L');

#Civil Status
$pdf->Ln(5);
$pdf->SetFont($fontStyle,"", $fontSizeText);
$pdf->Cell(30,3, 'Civil Status :', $border2,0,'L');
$pdf->SetFont($fontStyle,"B", $fontSizeText);
$pdf->Cell(50,3, ucwords(strtolower($civil_status)), $border2,0,'L');

$pdf->SetFont($fontStyle,"", $fontSizeText);
$pdf->Cell(35,3, 'Registration No. :', $border2,0,'L');
$pdf->SetFont($fontStyle,"B", $fontSizeText);
$pdf->Cell(50,3, $pid, $border2,0,'L');

$namepatient = stripslashes(strtoupper($name_first)).' '.stripslashes(strtoupper($name_middle)).' '.stripslashes(strtoupper($name_last));

$text = " 					I HEREBY CERTIFY THAT THE BIRTH CERTIFICATE $namepatient ".
			"HAS BEEN PRESENTED TO ME FOR FINAL REVIEW AND FOR CORRECTION OF ANY ".
			" ERRONEOUS ENTRY, TOPOGRAPHICAL OR OTHERWISE.";

$text3 = " 					I THEREFORE ASSUME FULL RESPONSIBILITY FOR ANY CORRECTION WHICH ".
					" HAS TO BE MADE AFTER THE BIRTH CERTIFICATE HAS BEEN REGISTERED. I HEREBY ".
			" RELEASE THE ".strtoupper($row['hosp_name'])." - ".strtoupper($row['mun_name'])." FROM ALL LIABILITY OF ".
			" WHATEVER NATURE WHICH MAY ARISE FROM THE REGISTRATION OF THE ".
			" CERTIFICATE WITH AN ERRONEOUS ENTRY";

$pdf->SetFont($fontStyle,"", $fontSizeText);
$pdf->Ln(15);
$pdf->MultiCell(0,6, $text, "",1,'J');
#$pdf->SetFont($fontStyle,"B", $fontSizeText);
#$pdf->MultiCell(0,6, $namepatient, "",0,'L');
#$pdf->Cell(50,3, $namepatient, $border2,0,'L');
$pdf->SetFont($fontStyle,"", $fontSizeText);
#$x = $pdf->GetX();
#$pdf->SetX($x);
#$pdf->MultiCell(0,6, $text2, "",1,'J');
$pdf->Ln(3);
$pdf->MultiCell(0,6, $text3, "",1,'J');

$pdf->Ln(35);
$pdf->SetFont($fontStyle,"", $fontSizeText);
$pdf->Cell(54,7, mb_strtoupper($encoder_name), "",0,'C');
$pdf->Cell(50,7, '', "",0,'L');
$pdf->Cell(55,7, mb_strtoupper($informant_name), "",0,'C');
$pdf->Ln(5);
$pdf->SetFont($fontStyle,"", $fontSizeText);
$pdf->Cell(55,7, 'SIGNATURE OF WITNESS', "T",0,'C');
$pdf->Cell(50,7, '', "",0,'L');
$pdf->Cell(55,7, 'INFORMANT\'S SIGNATURE', "T",0,'C');

$pdf->Ln(30);
$pdf->SetFont($fontStyle,"", $fontSizeText);
$pdf->Cell(55,7, date("m/d/Y")."    ".date("h:i A"), "",0,'C');
$pdf->Cell(50,7, '', "",0,'L');
$pdf->Cell(55,7, mb_strtoupper($informant_relation), "",0,'C');
$pdf->Ln(5);
$pdf->Cell(55,7, 'DATE AND TIME', "T",0,'C');
$pdf->Cell(50,7, '', "",0,'L');
$pdf->Cell(55,7, 'RELATION TO PATIENT', "T",0,'C');

$pdf->Output();

?>