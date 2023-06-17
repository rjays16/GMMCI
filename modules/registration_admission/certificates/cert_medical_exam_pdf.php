<?php
#created by Cherry 11-12-09
#medical certificate for driver's physical and medical examination (for new and renewal of Driver's License Applications)
include("roots.php");
#include_once($root_path."/classes/fpdf/fpdf.php");
include_once($root_path."/classes/fpdf/cert-pdf.class.php");
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

if ($row = $objInfo->getAllHospitalInfo()) {      
    $row['hosp_agency'] = strtoupper($row['hosp_agency']);
    $row['hosp_name']   = strtoupper($row['hosp_name']);
    $row['hosp_country'] = strtoupper($row['hosp_country']);
  }
  else {
     $row['hosp_country'] = "Republic of the Philippines";
     $row['hosp_agency']  = "DEPARTMENT OF HEALTH";
     $row['hosp_name']    = "Gonzales Maranan Medical Center Incorporated - Industrial Clinic";
     $row['hosp_region']  = "Center for Health Development - Davao Region";
  }
global $db;

if($_GET['id']){
    if(!($encInfo = $enc_obj->getEncounterInfo($_GET['id']))){
        echo '<em class="warn"> sorry byt the page cannot be displayed!</em>';
        exit();
    }
    extract($encInfo);
}else{
    echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid Case Number!</em>';
    exit();
} 

$obj_medCert = new MedCertificate($encounter_nr);
$driverCertInfo = $obj_medCert->getDriverCertRecord($encounter_nr);
#echo "sql = ".$enc_obj->sql;

$wardName = $ward_obj->WardName($encInfo['current_ward_nr']); 

//set border 
$border="1";
$border2="0";
$spacing =2;
// font setup
$fontSizeLabel = 9;
$fontSizeLabel2 = 7;
$fontSizeText = 11;  
$fontSizeText2 = 12;  
$fontSizeHeader = 10;
$fontSizeHeader2 = 14;
//font family setup
$fontFamily = "Arial";
$fontFamily2 = "Times";
//font style setup
$fontStyleNormal = "";
$fontStyleBold = "B";
$fontStyleBoldItalic = "BI";
$fontStyleItalic = "I";
$fontStyleBoldUnderline = "BU";
//height
$height = 4;
$height2 = 3;
$height3 = 5;

//table width
$tablewidth = 140;

//instantiate fpdf class
#$pdf  = new FPDF("P","mm","Letter");
$pdf  = new PDF("P","mm","Letter");
$pdf->AddPage("P");

$is_with_header = $_GET['show_header'];

if($is_with_header==1){

    //Header - Republic of the Philippines / Department of Health
    $pdf->SetFont($fontFamily2, $fontStyleNormal, $fonSizeText);
    $pdf->Cell(0, $height,$row['hosp_country'], $border2,1,'C');
    $pdf->SetFont($fontFamily2, $fontStyleNormal, $fontSizeText2);
    $pdf->Cell(0, $height, $row['hosp_agency'],$border2,1,'C');
    $pdf->SetFont($fontFamily2, $fontStyleNormal, $fontSizeText);
    $pdf->Cell(0, $height, "Center for Health Development - Davao Region", $border2,1,'C');
    

    //Hospital name
    $pdf->SetFont($fontFamily2, $fontStyleNormal, $fontSizeText2);
    $pdf->Cell(0,$height, ucwords(strtolower($row['hosp_name']))." - Industrial Clinic",$border2, 1, 'C');
    
    //Hospital info
    $pdf->Cell(0, $height, "Davao City   Tel. No. 225-4590",$border2, 1, 'C');
    $pdf->Ln(10);
    
    //Hospital Logo
    //$pdf->Image($root_path.'gui/img/logos/dmc_logo.jpg',20,5,25,30);
    $pdf->Image($root_path.'gui/img/logos/dmc_logo.jpg',40,10,20,20);
    $pdf->Image($root_path.'gui/img/logos/dmc_logo.jpg',150,10,20,20); 
}


/*//Line
$pdf->Line(10, 34, 206, 34);
$pdf->Rect(10, 34.5, 196,0.5,'F');
*/

//Document Title
$pdf->SetFont($fontFamily, $fontStyleBold, $fontSizeHeader2);
$pdf->Cell(0, $height3, "MEDICAL CERTIFICATE", $border2, 1, 'C');


//Date
$pdf->Ln(10);
$pdf->SetFont($fontFamily, $fontStyleBold, $fontSizeText);

if($driverCertInfo["create_dt"]!=NULL){
    $date_created = date("m-d-Y",strtotime($driverCertInfo["create_dt"]));
}elseif ($driverCertInfo["modify_dt"]!=NULL){
    $date_created = date("m-d-Y",strtotime($driverCertInfo["modify_dt"]));
}else
    $date_created = @formatDate2Local(date('Y-m-d'),$date_format);
$pdf->Cell(145,3);
$pdf->Cell(10, $height2, "Date:", $border2, 0, 'R');
$pdf->Cell(45, $height2 , ''.date("d-M-y", strtotime($driverCertInfo['create_dt'])), "",1,'');
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetFont($fontFamily, $fontStyleNormal, $fontSizeText);
$pdf->Line($x+155, $y, $x+180, $y);
//$pdf->Cell(130,4);
//$pdf->Cell(70,$height,"(Date)",0,1,'C');

//Driver's Physical and Medical Examination
$pdf->SetFont($fontFamily, $fontStyleNormal, $fontSizeLabel);
$pdf->Cell(0, $height, "Driver's Physical and Medical Examination (for New and Renewal of Driver's License Applications)", $border2, 1, 'L');

//Comments
if($driverCertInfo['comment_nr']==0)
    $fit = "X";
if($driverCertInfo['comment_nr']==1)
    $unfit = "X";
if($driverCertInfo['comment_nr']==2)
    $without_cond = "X";
if($driverCertInfo['comment_nr'] == 3){
     switch($driverCertInfo['with_cond_type']){
         case '3.a': 
                    $with_cond_A = "X";
                    break;
         case '3.b':
                    $with_cond_B = "X";
                    break;
         case '3.c':
                    $with_cond_C = "X";
                    break;
         case '3.d':
                    $with_cond_D = "X";
                    break;
         case '3.e':
                    $with_cond_E = "X";
                    break;
     }
}

//Table
$pdf->SetFont($fontFamily, $fontStyleNormal, $fontSizeLabel);
$pdf->Cell(30, $height, "", "TLR", 0);
$pdf->Cell(30, $height, "", "TLR", 0);
$pdf->Cell(40, $height, "General", "TLR", 0, 'C');
$pdf->Cell(40, $height, "", "TLR", 0);
$pdf->SetFont($fontFamily, $fontStyleBold, $fontSizeLabel);
$pdf->Cell(60, $height, "COMMENTS:", $border2, 1, 'L');

$pdf->SetFont($fontFamily, $fontStyleNormal, $fontSizeLabel);
$pdf->Cell(30, $height, "Visual Acuity", "LRB", 0, 'C');
$pdf->Cell(30, $height, "Hearing", "LRB", 0, 'C');
$pdf->Cell(40, $height, "Physique", "LRB", 0, 'C');
$pdf->Cell(40, $height, "General Health", "LRB", 0, 'C');
$pdf->Cell(30, $height, "FIT TO DRIVE", 0, 0, 'L');
$pdf->Cell(10, 4);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->Rect($x, $y, 4, 3.5);
$pdf->Cell(5,$height, $fit, $border2,1,'L');   //fit to drive

$pdf->Cell(15, $height3, "Right", "TLR", 0, 'C');
$pdf->Cell(15, $height3, "Left", "TLR", 0, 'C');
$pdf->Cell(15, $height3, "Right", "TLR", 0, 'C');
$pdf->Cell(15, $height3, "Left", "TLR", 0, 'C');
$pdf->Cell(40, $height3, "", "TLR", 0);
$pdf->Cell(12, $height3, "BP", "TLR", 0, 'C');
$pdf->Cell(28, $height3, "Contagious", "TLR", 0, 'C');
$pdf->Cell(30, $height3, "UNFIT TO DRIVE", 0, 0, 'L');
$pdf->Cell(10, 4);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->Rect($x, $y, 4,3.5);
$pdf->Cell(5,$height, $unfit, $border2,1);  //unfit to drive
//$pdf->Cell(60,1,"",0,1);
//$pdf->Cell(30, 4, "UNFIT TO DRIVE", 0, 0, 'L');
//$pdf->Cell(10, 4);
//$pdf->Cell(5, 4, "", $border,1);
//$pdf->Cell(15, 4, "");

$pdf->Cell(15, $height, "Eye", "LRB", 0, 'C');
$pdf->Cell(15, $height, "Eye", "LRB", 0, 'C');
$pdf->Cell(15, $height, "Ear", "LRB", 0, 'C');
$pdf->Cell(15, $height, "Ear", "LRB", 0, 'C');
$pdf->Cell(40, $height, "", "LRB", 0,'C');
$pdf->Cell(12, $height, "", "LRB", 0);
$pdf->Cell(28, $height, "Diseases", "LRB", 0, 'C');
$pdf->Cell(30, $height, "w/o Condition", 0, 0, 'L');
$pdf->Cell(10, 4);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->Rect($x,$y,4,3.5);
$pdf->Cell(5,$height, $without_cond, $border2,1);   // w/o condition
$xtable = $pdf->GetX();
$ytable = $pdf->GetY();

//Table boarders
//$pdf->Rect($xtable, $ytable, 15, 114);    // border for visual acuity (right eye)
//$pdf->Rect($xtable+15, $ytable, 15, 114); // border for visual acuity (left eye)
//$pdf->Rect($xtable+30, $ytable, 15, 114); // border for hearing (right ear)
//$pdf->Rect($xtable+45, $ytable, 15, 114); // border for hearing (left ear)
$pdf->Rect($xtable, $ytable, 15, 40);    // border for visual acuity (right eye)  
$pdf->Rect($xtable+15, $ytable, 15, 40); // border for visual acuity (left eye)
$pdf->Rect($xtable+30, $ytable, 15, 40); // border for hearing (right ear)
$pdf->Rect($xtable+45, $ytable, 15, 40); // border for hearing (left ear)                                                                          
$pdf->Rect($xtable+60, $ytable, 40, 114); // border for general physique
$pdf->Rect($xtable+100, $ytable, 12, 114); // border for BP 
$pdf->Rect($xtable+112, $ytable, 28, 114); // border for contagious diseases


$pdf->Cell($tablewidth, $height, "", 0,0);
$pdf->Cell(30, $height, "w/ Condition", 0, 1, 'L');

//$pdf->Cell($tablewidth, $height, "", 0,0);
$pdf->Cell(15, 4, $driverCertInfo['right_eye'], 0, 0, 'C'); //right eye
$pdf->Cell(15, 4, $driverCertInfo['left_eye'], 0, 0, 'C');  //left eye
$pdf->Cell(15, 4, $driverCertInfo['right_ear'], 0, 0, 'C'); //right ear
$pdf->Cell(15, 4, $driverCertInfo['left_ear'], 0, 0, 'C');  //left ear
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(39, 4, $driverCertInfo['gen_physique'], 0, 'L');  //general physique
$pdf->SetXY($x+40, $y);
$pdf->Cell(12, 4, $driverCertInfo['systole']."/".$driverCertInfo['diastole'], 0,0, 'C'); //BP
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(28, 4, $driverCertInfo['diseases'], 0, 'J');  //contagious disease
$pdf->SetXY($x+30, $y);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->Rect($x,$y,5,3);
$pdf->Cell(5, $height, $with_cond_A, $border2, 0, 'C');  // w/ condition option A
$pdf->Cell(1,4);
$pdf->Cell(52, $height, "A. WEAR CORRECTIVE LENSES", 0, 1,'L');

$pdf->Cell($tablewidth, $height, "", 0,0);
$pdf->Cell(2,4);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->Rect($x, $y, 5,3);
$pdf->Cell(5, $height, $with_cond_B, $border2, 0, 'C');  // w/ condition option B
$pdf->Cell(1,4);
$pdf->Cell(52, $height, "B. DRIVE ONLY W/ SPECIAL", 0,1,'L');
$pdf->Cell(152, 4);
$pdf->Cell(40, $height, "EQPT. FOR UPPER LIMBS", 0,1, 'L');

$pdf->Cell($tablewidth, $height, "", 0,0);
$pdf->Cell(2,4);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->Rect($x, $y, 5, 3);
$pdf->Cell(5, $height, $with_cond_C, $border2, 0, 'C');  // w/ condition option C
$pdf->Cell(1,4);
$pdf->Cell(52, $height, "C. DRIVE ONLY W/ SPECIAL", 0,1,'L');
$pdf->Cell(152,4);
$pdf->Cell(40,$height,"EQPT. FOR LOWER LIMBS", 0, 1, 'L');

$pdf->Cell($tablewidth, $height, "", 0,0);
$pdf->Cell(2,4);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->Rect($x, $y, 5, 3);
$pdf->Cell(5, $height, $with_cond_D, $border2, 0, 'C');  // w/ condition option D
$pdf->Cell(1,4);
$pdf->Cell(52, $height, "D. DAYLIGHT DRIVING ONLY", 0,1,'L');

$pdf->Cell($tablewidth, $height, "", 0,0);
$pdf->Cell(2,4);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->Rect($x, $y, 5, 3);
$pdf->Cell(5, $height, $with_cond_E, $border2, 0, 'C');  // w/ condition option E
$pdf->Cell(1,4);
$pdf->Cell(52, $height, "E. MUST BE ACCOMPANIED BY", 0,1,'L');
$pdf->Cell(152,4);
$pdf->Cell(40, $height,"A PERSON WITH NORMAL", 0, 1, 'L');
$pdf->Cell(152,4);
$pdf->Cell(40, $height,"HEARING", 0, 1, 'L');

$x = $pdf->GetX();
$y = $pdf->GetY();
//$pdf->Line($x, $y, $x+60, $y);
$pdf->Rect($x, $y, 60, $height*2);
$pdf->Cell(30, $height, "With Corrective", 0,0,'C');
$pdf->Cell(30, $height, "With Hearing", "L",0, 'C');
$pdf->Cell(80, 4);
$pdf->Cell(20, $height, "REMARKS: ", 0, 1, 'L');
$xrem = $pdf->GetX();
$yrem = $pdf->GetY();
//$pdf->Line($x, $y+($height-0.5), $x+60, $y+($height-0.5));

if($driverCertInfo['is_with_corrective_lenses']==0)
    $ans1 = "No";
else
    $ans1 = "Yes";
    
if($driverCertInfo['is_with_hearing_aid']==0)
    $ans2 = "No";
else
    $ans2 = "Yes";

$pdf->Cell(30, $height, "Lenses: ".$ans1, 0, 0, 'C');
$pdf->Cell(30, $height, " Aid: ".$ans2, "L", 1, 'C');
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->Rect($x, $y, 15, $height*4);
$pdf->Rect($x+15, $y, 15, $height*4);
$pdf->Rect($x+30, $y, 15, 66);
$pdf->Rect($x+45, $y, 15, 66);
$pdf->Cell($tablewidth, 4);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetXY($x, $yrem+4);
$pdf->MultiCell(60, 4, $driverCertInfo['remarks'], 0, 'L');
//if($driverCertInfo['remarks']){
//    $xrem = $pdf->GetX();
//    $yrem = $pdf->GetY();
//}

$pdf->Ln($height*2);
$pdf->Cell($tablewidth, 4);
$pdf->Cell(60 , $height, "I hereby certify to the medical examination",0,1,'L');
$pdf->Cell($tablewidth, 4);
$pdf->Cell(60, $height, "performed",0,1,'L');

//$x = $pdf->GetX();
//$y = $pdf->GetY();
//$pdf->Line($x, $y, $x+30, $y);
//if($driverCertInfo['remarks'])
//    $pdf->SetXY($xrem, $yrem+4);
$pdf->SetXY($xrem, $y+16);
$pdf->Cell(30, $height, "ISHIHARA PLATE", 1, 1, 'L');
$pdf->Cell(30, $height, strtoupper($driverCertInfo['i_plate']), 1, 1, 'C');
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->Rect($x, $y, 15, 42);
$pdf->Rect($x+15, $y, 15, 42);
$pdf->Ln($height*2);
$pdf->Cell($tablewidth, 4);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->Line($x+1, $y, $x+60, $y);
$pdf->SetFont($fontFamily, $fontStyleNormal, $fontSizeLabel2);
$pdf->Cell(60, $height, "Signature of the Examining Physician",0,1,'L');
$pdf->Ln(2);

$docInfo = $pers_obj->getPersonellInfo($driverCertInfo['dr_nr']);
    
    $dr_middleInitial = "";
    if (trim($docInfo['name_middle'])!=""){
        $thisMI=split(" ",$docInfo['name_middle']);    
        foreach($thisMI as $value){
            if (!trim($value)=="")
                $dr_middleInitial .= $value[0];
        }        
            if (trim($dr_middleInitial)!="")
            $dr_middleInitial = " ".$dr_middleInitial.".";
    }
    #$name_doctor = "Dr. ".trim($docInfo['name_first'])." ".trim($docInfo['name_2'])." ".$dr_middleInitial." ".trim($docInfo['name_last']);
    $doctor = trim($docInfo['name_first'])." ".trim($docInfo['name_2'])." ".trim($dr_middleInitial)." ".trim($docInfo['name_last']);

//echo "doctor= ".$doctor;    

    
$pdf->Cell($tablewidth, 4);
$pdf->SetFont($fontFamily, $fontStyleBold, $fontSizeLabel2);
$pdf->Cell(60, $height, strtoupper($doctor),0,1,'L');  //name
//$pdf->Cell(38, $height, strtoupper($doctor), 0,1,'L'); //name

//$pdf->Cell($tablewidth, 4);
$pdf->Ln(2);
$pdf->SetFont($fontFamily, $fontStyleNormal, $fontSizeLabel2);

$pdf->Cell($tablewidth, 4);
$pdf->Cell(20, $height, "LICENSE NO. ", 0, 0, 'L');
$pdf->Cell(40, $height, $docInfo['license_nr'], 0,1, 'L'); //licensed no.
$pdf->Ln(5);

$pdf->Cell($tablewidth, 4);
$pdf->Cell(60, $height, "LICENSE NO. ", 0,1, 'L');

$pdf->Cell($tablewidth, 4);
$pdf->Cell(60, $height, "VALID AT: DAVAO CITY ", 0, 1, 'L');
/*$pdf->Cell($tablewidth, 4);
$pdf->Cell(36, $height, "ACCREDITATION CERT. NO.: ", 0, 0, 'L');
$pdf->Cell(24, $height, "", 0,1,'L'); //accreditation cert no.

$pdf->Cell($tablewidth, 4);
$pdf->Cell(18, $height, "VALID UNTIL: ", 0,0,'L');
$pdf->Cell(42, $height, "", 0,1,'L'); //Valid Until

$pdf->Cell($tablewidth, 4);
$pdf->Cell(20, $height, "CONTROL NO.: ", 0, 0, 'L');
$pdf->Cell(40, $height, "", 0, 1, 'L'); //Control No.
$pdf->Ln(4);
$xtable2 = $pdf->GetX();
$ytable2 = $pdf->GetY();
*/

$pdf->Cell($tablewidth, 4);

$pdf->SetFont($fontFamily, $fontStyleBold, $fontSizeLabel2);
//$pdf->Cell(40, $height, "L.T.O. - ".$place, 0,1,'L');
$pdf->Ln($height*3);

$pdf->Cell(45, 4);
$pdf->SetFont($fontFamily, $fontStyleBold, $fontSizeLabel);
$pdf->Cell(15, $height, "HEIGHT: ", 0, 0, 'L');
$xline = $pdf->GetX();
$yline = $pdf->GetY();
$pdf->Line($xline, $yline+3.5, $xline+20, $yline+3.5);
$pdf->Cell(20, $height, $driverCertInfo['height']." ".$driverCertInfo['height_unit'], 0, 1, 'C'); //Height
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->Line($x, $y+3.5, $x+42, $y+3.5);
$pdf->Cell(45,4);
$pdf->Cell(15, $height, "WEIGHT: ", 0,0, 'L');
$xline = $pdf->GetX();
$yline = $pdf->GetY();
$pdf->Line($xline, $yline+3.5, $xline+20, $yline+3.5);
$pdf->Cell(20, $height, $driverCertInfo['weight']." ".$driverCertInfo['weight_unit'], 0, 1, 'C'); //Weight

$pdf->Cell(42, $height, "SIGNATURE", 0,1,'C');
$pdf->Ln(10);


$pdf->Cell(22,4);
$pdf->Cell(40, $height, stripslashes(strtoupper($name_last)), 0,0,'C');   //surname
$pdf->Cell(5,4);
$pdf->Cell(50, $height, stripslashes(strtoupper($name_first)), 0,0,'C');   //first name
$pdf->Cell(5,4);
$pdf->Cell(40, $height, stripslashes(strtoupper($name_middle)), 0,1,'C');   //maternal name

$pdf->Cell(22, 4);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->Line($x, $y, $x+40, $y);
$pdf->Cell(40, $height, "SURNAME", 0, 0, 'C');
$pdf->Cell(5,4);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->Line($x, $y, $x+50, $y);
$pdf->Cell(50,$height, "FIRST NAME", 0,0,'C');
$pdf->Cell(5,4);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->Line($x, $y, $x+40, $y);
$pdf->Cell(40, $height, "MATERNAL NAME", 0, 1, 'C');
$pdf->Ln(5);

$pdf->Cell($tablewidth, $height2, "VALID ONLY FOR 3 MONTHS", 0,1,'L');
$pdf->SetFont($fontFamily, $fontStyleBoldItalic, $fontSizeLabel2);
//$pdf->Cell(60, $height2, "Not Valid Without", 0, 1, 'L');

/*$pdf->SetFont($fontFamily, $fontStyleNormal, $fontSizeLabel2);
$pdf->Cell($tablewidth, $height2, "QR-OPD-MDC R-O 02/02/09", 0,0,'L');
$pdf->SetFont($fontFamily, $fontStyleBoldItalic, $fontSizeLabel2);
$pdf->Cell(60, $height2, "   Hospital Seal", 0,1, 'L');
*/
if ($driverCertInfo["modify_id"])
    $encoder = $driverCertInfo["modify_id"];
else
    $encoder = $driverCertInfo["create_id"];        
#$pdf->Cell(0, 3 , 'Prepared by : '.strtoupper($HTTP_SESSION_VARS["sess_user_name"]), "", 0,'');
#$pdf->Cell(0, 3 , 'Encoded by : '.$encoder, "", 0,'');
$pdf->encoder = $encoder;       
//print pdf
$pdf->Output();

?>