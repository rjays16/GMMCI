<?php
  #created by Cherry 11-13-09
  #medical/dental examination certificate
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
#$driverCertInfo = $obj_medCert->getDriverCertRecord($encounter_nr);
$dentalCertInfo = $obj_medCert->getMedDentalCertRecord($encounter_nr);
#echo "sql =".$obj_medCert->sql;
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
$fontStyleUnderline = "U";
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
$pdf->Cell(0, $height3, "MEDICAL / DENTAL EXAMINATION CERTIFICATE", $border2, 1, 'C');
$pdf->Ln($height);

//Clinic No.
$pdf->SetFont($fontFamily, $fontStyleNormal, $fontSizeLabel);
$pdf->Cell(0, $height, "Clinic No. 1630606", $border2, 1, 'C');

//Date
if($dentalCertInfo["create_dt"]!=NULL){
    $date_created = date("F j, Y",strtotime($dentalCertInfo["create_dt"]));
}elseif ($dentalCertInfo["modify_dt"]!=NULL){
    $date_created = date("m-d-Y",strtotime($dentalCertInfo["modify_dt"]));
}else
    $date_created = @formatDate2Local(date('F j, Y'),$date_format);

$pdf->Cell(0, $height, "Date: ".$date_created, $border2, 1, 'C');
$pdf->Ln($height);

//this certifies.....
$pdf->SetFont($fontFamily2, $fontStyleNormal, $fontSizeText2);
$pdf->Cell(20, $height);
$sex = ($sex == "m")? "male":"female";  

if (trim($brgy_name)=='NOT PROVIDED')
    $brgy_name = "";
else    
    $brgy_name = trim($brgy_name).", ";
    
if (trim($mun_name)=='NOT PROVIDED')
    $mun_name = "";
    
$address = trim($street_name).", ".$brgy_name.trim($mun_name)." ".trim($prov_name);
$middle_initial = $name_middle[0];

$pdf->MultiCell(180, $height+2, "       This certifies that ".stripslashes(strtoupper($name_first)).' '.$middle_initial.'. '.stripslashes(strtoupper($name_last)).", "
                .$age." old (".$sex."/".$civil_status.") and a resident of ".$address.",  was examined in this clinic on "
                .$date_created." with the following findings:", 0, 'L');
$pdf->Ln($height);
                
if($dentalCertInfo['dentist_nr']){
    //findings (dentist)
    $pdf->SetFont($fontFamily2, $fontStyleBold, $fontSizeText2);
    $pdf->MultiCell(180, $height3, strtoupper($dentalCertInfo['dentist_diagnosis']), 0, 'C');
    $pdf->Ln($height*2);
    
    //dentist in-charge
    $pdf->Cell(120, $height);
    $pdf->Cell(10, $height, "(sgd)", 0,0, 'L');
    $pdf->SetFont($fontFamily2, $fontStyleBoldUnderline, $fontSizeText2);
    
    $docInfo = $pers_obj->getPersonellInfo($dentalCertInfo['dentist_nr']);
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
    $doctor = trim($docInfo['name_first'])." ".trim($docInfo['name_2'])." ".trim($dr_middleInitial)." ".trim($docInfo['name_last'].', DMD');
    
    $pdf->Cell(50, $height, $doctor, 0, 1, 'L');
    $pdf->Ln(2);
    $pdf->Cell(130, $height);
    $pdf->SetFont($fontFamily2, $fontStyleBold, $fontSizeLabel);
    $pdf->Cell(50, $height, "DENTIST IN-CHARGE", 0,1,'C');
    $pdf->Cell(140, $height);
    $pdf->Cell(18, $height, "License No: ", 0, 0, 'L');
    $pdf->SetFont($fontFamily2, $fontStyleUnderline, $fontSizeLabel);
    $pdf->Cell(32, $height, $docInfo['license_nr'], 0,1,'L');
    $pdf->Ln($height);
}

if($dentalCertInfo['ent_nr']){
    // findings (ent physician)
    $pdf->SetFont($fontFamily2, $fontStyleBold, $fontSizeText2);
    $pdf->MultiCell(180, $height3, strtoupper($dentalCertInfo['ent_diagnosis']), 0, 'C');
    $pdf->Ln($height*2);
    
    //ent physician
    $pdf->Cell(120, $height);
    $pdf->Cell(10, $height, "(sgd)", 0,0, 'L');
    $pdf->SetFont($fontFamily2, $fontStyleBoldUnderline, $fontSizeText2);
    
    $docInfo = $pers_obj->getPersonellInfo($dentalCertInfo['ent_nr']);
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
    $doctor = trim($docInfo['name_first'])." ".trim($docInfo['name_2'])." ".trim($dr_middleInitial)." ".trim($docInfo['name_last'].', DMD');
    
    $pdf->Cell(50, $height, $doctor, 0, 1, 'L');
    $pdf->Ln(2);
    $pdf->Cell(130, $height);
    $pdf->SetFont($fontFamily2, $fontStyleBold, $fontSizeLabel);
    $pdf->Cell(50, $height, "ENT PHYSICIAN", 0,1,'C');
    $pdf->Cell(140, $height);
    $pdf->Cell(18, $height, "License No: ", 0, 0, 'L');
    $pdf->SetFont($fontFamily2, $fontStyleUnderline, $fontSizeLabel);
    $pdf->Cell(32, $height, $docInfo['license_nr'], 0,1,'L');
    $pdf->Ln($height);
    
}
if($dentalCertInfo['oph_nr']){
    // findings (ophthalmology physician)
    $pdf->SetFont($fontFamily2, $fontStyleBold, $fontSizeText2);
    $pdf->MultiCell(180, $height3, strtoupper($dentalCertInfo['oph_diagnosis']), 0, 'C');
    $pdf->Ln($height*2);
    
    //ophthalmologist
    $pdf->Cell(120, $height);
    $pdf->Cell(10, $height, "(sgd)", 0,0, 'L');
    $pdf->SetFont($fontFamily2, $fontStyleBoldUnderline, $fontSizeText2);
    
    $docInfo = $pers_obj->getPersonellInfo($dentalCertInfo['oph_nr']);
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
    $doctor = trim($docInfo['name_first'])." ".trim($docInfo['name_2'])." ".trim($dr_middleInitial)." ".trim($docInfo['name_last'].', DMD');
    
    $pdf->Cell(50, $height, $doctor, 0, 1, 'L');
    $pdf->Ln(2);
    $pdf->Cell(130, $height);
    $pdf->SetFont($fontFamily2, $fontStyleBold, $fontSizeLabel);
    $pdf->Cell(50, $height, "OPHTHALMOLOGIST", 0,1,'C');
    $pdf->Cell(140, $height);
    $pdf->Cell(18, $height, "License No: ", 0, 0, 'L');
    $pdf->SetFont($fontFamily2, $fontStyleUnderline, $fontSizeLabel);
    $pdf->Cell(32, $height, $docInfo['license_nr'], 0,1,'L');
    $pdf->Ln($height);
    
}

if($dentalCertInfo['physician_nr']){
    // findings (physician in-charge)
    $pdf->SetFont($fontFamily2, $fontStyleBold, $fontSizeText2);
    $pdf->MultiCell(180, $height3, strtoupper($dentalCertInfo['physician_diagnosis']), 0, 'C');
    $pdf->Ln($height*2);
    
    //ent physician
    $pdf->Cell(120, $height);
    $pdf->Cell(10, $height, "(sgd)", 0,0, 'L');
    $pdf->SetFont($fontFamily2, $fontStyleBoldUnderline, $fontSizeText2);
    
    $docInfo = $pers_obj->getPersonellInfo($dentalCertInfo['physician_nr']);
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
    $doctor = trim($docInfo['name_first'])." ".trim($docInfo['name_2'])." ".trim($dr_middleInitial)." ".trim($docInfo['name_last'].', DMD');
    
    $pdf->Cell(50, $height, $doctor, 0, 1, 'L');
    $pdf->Ln(2);
    $pdf->Cell(130, $height);
    $pdf->SetFont($fontFamily2, $fontStyleBold, $fontSizeLabel);
    $pdf->Cell(50, $height, "PHYSICIAN IN-CHARGE", 0,1,'C');
    $pdf->Cell(140, $height);
    $pdf->Cell(18, $height, "License No: ", 0, 0, 'L');
    $pdf->SetFont($fontFamily2, $fontStyleUnderline, $fontSizeLabel);
    $pdf->Cell(32, $height, $docInfo['license_nr'], 0,1,'L');
    $pdf->Ln($height);
    
}
 
$pdf->Ln($height*4); 
$pdf->SetFont($fontFamily2, $fontStyleNormal, $fontSizeLabel);
$pdf->Cell(0, $height, "Not Valid", 0, 1, 'L');
$pdf->Cell(0, $height, "Without DMC Seal", 0, 1, 'L');

if ($dentalCertInfo["modify_id"])
    $encoder = $dentalCertInfo["modify_id"];
else
    $encoder = $dentalCertInfo["create_id"];        
    
$pdf->encoder = $encoder;       
$pdf->Output();
  
?>
