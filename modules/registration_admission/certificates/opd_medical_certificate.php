<?php

include("roots.php");
include_once($root_path."/classes/fpdf/fpdf.php");
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');

require_once($root_path.'/include/care_api_classes/class_drg.php');
$objDRG= new DRG;

include_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter;

//$_GET['encounter_nr'] = 2007500006;

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
 

//instantiate fpdf class
$pdf  = new FPDF();
$pdf->AddPage("P");

//Header - Republic of the Philippines / Department of Health
$pdf->SetFont($fontStyle, "I", $fonSizeInput);
$pdf->Cell(0,4,'Republic of the Philippines', $border2,1,'C');
$pdf->Cell(0,4,'DEPARTMENT OF HEALTH', $border2,1,'C');

//Hospital name- Gonzales Maranan Medical Center Incorporated
$pdf->Ln(2);
$pdf->setFont($fontStyle,"B", $fontSizeHeader-2);
$pdf->Cell(0,4,'Gonzales Maranan Medical Center Incorporated',$border2, 1, 'C');

//Hospital Address
$pdf->Ln(2);
$pdf->setFont($fontStyle,"", $fontSizeInput);
$pdf->Cell(0,4,'Digos City',$border2, 1, 'C');

//File No.. Line -2 
$pdf->Ln(2);
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(170, 3 , 'CASE NO.:'.$encounter_nr.'', "",0,'R');

//Date .. Line - 3
$pdf->Ln(3);
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(150, 3 , 'DATE: '.$currentdate.'', "",0,'R');

//Document Title - Medical Certificate  Line 4
$pdf->Ln(4);
$pdf->SetFont($fontStyle,"B", $fontSizeHeader);
$pdf->Cell(0,16 , 'M E D I C A L  C E R T I F I C A T E', $border2,1,'C');

//Salutation
$pdf->Ln(6);
$pdf->SetFont($fontStyle,"", $fontSizeText);
$pdf->Cell(0,3, 'TO WHOM IT MAY CONCERN:', $border2,1,'L');

//Content text
$sex = ($sex == "m")? "MALE":"FEMALE";
$address = "$street_name, $brgy_name, $mun_name $zipcode $prov_name";
$pdf->Ln(7);
$pdf->SetFont($fontStyle,"",$fontSizeText);
$pdf->MultiCell(180, 6,'            This is to certify that '.$name_first.' '.$name_middle.' '.$name_last.' , '.$age.' y.o '.$sex.' '.  	//patient name
					', '.mb_strtoupper($civil_status).'  and a resident of '.$address.' '.                       	//address
					'was examined, treated, confined in this hospital on / from '.  
					'date  '.                                                       //date
					'with the following findings/diagnosis.',0,'L',0);																

$pdf->Ln(8);
$pdf->Cell(0,3,'(chart/phic)',"",1,'L');

//Diagnosis
$pdf->Ln(25);
$pdf->MultiCell(175,6,'PREGNANCY UTERINE TERM, CEEPHALIX, DELIVERED LIVE BIRTH BABY BOY, '.
				'OLIGOHYDRAMNIOS SEVERS G1P1 (1-0-0-1).',0,'L',0);

//Operation
$pdf->Ln(5);
$pdf->MultiCell(180,6,'OPERATION: '.
					'LOW SEGMENT TRANSVERSE CAESAREAN SECTION I. ',0,'L',0);
$pdf->Cell(20, 6,"(01-09-2007)","",0,"L");
					
//Advised		
$pdf->Ln(60);
$pdf->cell(26, 6,"ADVISED TO REST FOR  _________________  DAYS","",0,"L");


$pdf->Ln(24);
$pdf->Cell(15, 6, "[ NML ] ID No. 34591","",0,"L");

//Doctor Name
$pdf->setFont($fontStyle,"B",$fontSizeText);
$pdf->Cell(160,6,"Josie Ofelia TUBURAN, M.D.","",0,"R");

$pdf->Ln(5);
$pdf->setFont($fontStyle,"",$fontSizeText);
$pdf->Cell(155,6,"Attending Physician","",0,"R");
$pdf->Ln(6);
$pdf->Cell(168,6,"Lic No. _______________","",0,"R");


$pdf->Ln(8);
$pdf->Cell(30,6,"NOT VALID","",0,"R");
$pdf->Ln();
$pdf->Cell(50,6,"WITHOUT DMC SEAL","",0,"R");


//print pdf
$pdf->Output();

?>