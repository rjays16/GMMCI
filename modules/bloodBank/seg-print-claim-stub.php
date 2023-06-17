<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'classes/fpdf/fpdf.php');
require($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');

/*
CREATED BY NICK 11/23/2013
*/
class Blood_Claim_Stub extends FPDF{

  #SETTINGS
  var $hosp_name = '';  
  var $fontFamily = 'Arial';
  var $fontSizeSmall = 8;
  var $fontSizeMedium = 10;
  var $PageSize = array(215.9,76.01);
  var $PageTitle = 'BLOOD BANK CLAIM STUB';

  function Blood_Claim_Stub(){
    $this->FPDF('P','mm',$this->PageSize);
    $this->SetTitle($this->PageTitle, true);
  }

  function Header(){
    global $db;
    $objInfo = new Hospital_Admin();
    if ($row = $objInfo->getAllHospitalInfo()){
      $hosp_name = $row['hosp_name'];
    }
    $this->SetFont($this->fontFamily,'B', $this->fontSizeMedium);
    $this->Cell(0,4,$hosp_name, 0, 1,'C');
    $this->SetFont($this->fontFamily,'', $this->fontSizeSmall);
    $this->Cell(0,4,'Department of Pathology and Clinical Laboratories', 0, 1,'C');
    $this->SetFont($this->fontFamily,'',$this->fontSizeSmall);
    $this->Cell(0,4,'Blood Transfusion Service', 0, 1,'C');
    $this->SetFont($this->fontFamily,'B', $this->fontSizeSmall);
    $this->Cell(0,4,'CLAIM STUB', 0, 1, 'C');
    $this->Ln(5);
    $this->SetFont($this->fontFamily,'', $this->fontSizeMedium);
  }

  function Patient_Details(){

    //get values
    #DEFAULTS
    $refno='';
    $cmCheck = '';
    $coombsCheck = '';
    $compCheck = '';
    $duCheck = 'images/uncheck2.png';
    $others = '';
    $cmVal = 0;
    $coombsVal = 0;
    $compVal = 0;
    $duVal = 0;
    #REFRENCE NUMBER
    if(isset($_GET['refno']))
        $refno = $_GET['refno'];
    #CROSSMATSHING CHECK
    if(isset($_GET['cmCheck'])){
        if($_GET['cmCheck'] == 'true')
            $cmCheck = 'images/check2.png';
        else
            $cmCheck = 'images/uncheck2.png';
    }
    #COOMBS TEST CHECK
    if(isset($_GET['coombsCheck'])){
        if($_GET['coombsCheck'] == 'true')
            $coombsCheck = 'images/check2.png';
        else
            $coombsCheck = 'images/uncheck2.png';
    }
    #COMPONENT CHECK
    if(isset($_GET['compCheck'])){
        if($_GET['compCheck'] == 'true')
            $compCheck = 'images/check2.png';
        else
            $compCheck = 'images/uncheck2.png';
    }
    #DU VARIANT CHECK
    if(isset($_GET['duCheck'])){
        if($_GET['duCheck'] == 'true')
            $duCheck = 'images/check2.png';
        else
            $duCheck = 'images/uncheck2.png';
    }
    #CROSSMATCHING VALUE
    if(isset($_GET['cmVal']) || $_GET['cmVal']!=''){
        $cmVal = $_GET['cmVal'];
    }
    #COOMBS TEST VALUE
    if(isset($_GET['coombsVal']) || $_GET['coombsVal']!=''){
        $coombsVal = $_GET['coombsVal'];
    }
    #COMPONENTS VALUE
    if(isset($_GET['compVal']) || $_GET['compVal']!=''){
        $compVal = $_GET['compVal'];
    }
    #DU VARIANT VALUE
    if(isset($_GET['duVal']) || $_GET['duVal']!=''){
        $duVal = $_GET['duVal'];
    }
    #DU VARIANT VALUE
    if(isset($_GET['others']) || $_GET['others']!=''){
        $others = $_GET['others'];
    }

    $srvObj=new SegLab();
    $row_px = $srvObj->getClaimStubInfo($refno);
    //end get values

    //SHOW VALUES
    $this->SetFont($this->fontFamily,'',$this->fontSizeSmall);
    $this->Cell(75, 5, 'Patient: '.$row_px['patient_name'],'TLR');
    $this->SetTextColor(0,0,255);
    $this->SetFont($this->fontFamily,'U', $this->fontSizeSmall);
    $this->Cell(80, 5, 'Results to be claimed','TR');
    $this->SetTextColor(0,0,0);
    $this->SetFont($this->fontFamily,'', $this->fontSizeSmall);
    $this->Cell(60, 5, 'Date/Time Received:');   
    $this->Ln();

    $this->Cell(75, 5, 'Hospital Case no:'.$row_px['refno'],'LR');    
    $this->Cell(40, 5, '');    
    $this->Cell(40, 5, 'RETYPING','R');
    $this->Cell(60, 5, '     '.$row_px['received_dt']);
    $this->Ln();

    $this->Cell(75, 5, 'Ward:'.$row_px['ward'],'BLR');    
    $this->Cell(40, 5, 'Crossmatching  '.$this->Image($cmCheck, $this->GetX()+22.5, $this->GetY()+0.5, 3).'       x  '.$cmVal);    
    $this->Cell(40, 5, 'Coombs Test  '.$this->Image($coombsCheck, $this->GetX()+19, $this->GetY()+0.5, 3).'     x '.$coombsVal,'R');
    $this->Cell(60, 5, 'Date/Time Due:');
    $this->Ln();

    $this->Cell(75, 5, '');
    $this->Cell(40, 5, '(PC,FFP,CRYO)  '.$this->Image($compCheck, $this->GetX()+22.5, $this->GetY()+0.5, 3).'     x '.$compVal,'L');    
    $this->Cell(40, 5, 'Du Variant  '.$this->Image($duCheck, $this->GetX()+16, $this->GetY()+0.5, 3).'     x '.$duVal,'R');
    //$this->Cell(40, 5, 'Du Variant [ '.$duCheck.' ] x '.$duVal,'R');
    $this->Cell(60, 5, '     '.$row_px['due_dt']);
    $this->Ln();

    $this->Cell(75, 5, '');
    $this->Cell(15, 5, 'OTHERS:','BL');
    $this->SetFont($this->fontFamily,'U','8');
    $this->Cell(65, 5, $others,'BR');
    $this->Cell(60, 5, '');
  }

}

//DISPLAY
$objPdf = new Blood_Claim_Stub();
$objPdf->AliasNbPages();
$objPdf->AddPage();
$objPdf->Patient_Details();
$objPdf->Output();

?>