<?php
error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);  //set the error level reporting
require_once('./roots.php'); //traverse the root directory
//include_once($root_path.'/classes/fpdf/fpdf.php');
require($root_path.'/modules/repgen/repgen.inc.php');  

require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/class_blood_bank.php');

class RepGen_Promissory extends Repgen {
  var $printable_width = 190.5;
  var $half_width = 95.25;
  function RepGen_Promissory() {
    
    $this->RepGen('Promissory Note', 'P', 'Letter');
    //$this->SetMargins(120.7, 120.7, 120.7);
    $this->DEFAULT_LEFTMARGIN = 12.7;
    $this->DEFAULT_RIGHTMARGIN = 12.7;
    $this->DEFAULT_TOPMARGIN = 12.7;
    $this->ColumnWidth = array($this->printable_width/6,$this->printable_width/6,$this->printable_width/6,$this->printable_width/6,$this->printable_width/6,$this->printable_width/6);
    $this->RowHeight = 5.5;
    $this->Alignment = array('L','L','L','C','L','L');
    $this->PageOrientation = "P";
    $this->NoWrap = false;
  }
  function Header() {
   global $root_path;
   /** Print Header **/
   $hospital = new Hospital_Admin();
   $hospital_info = $hospital->getAllHospitalInfo();
   $hospital_info_array = array($hospital_info['hosp_country'], strtoupper($hospital_info['hosp_agency']), 
                                strtoupper($hospital_info['hosp_name']), $hospital_info['hosp_addr1']);
   $this->SetFont("Times", "B", "10");
   $this->MultiCell(0, 5, implode("\n", $hospital_info_array), 0, 'C'); 
   $this->Image($root_path.'gui/img/logos/dmc_logo.jpg',40,5,30,30);
   $this->Line(0, $this->GetY()+2.54, 215.8, $this->GetY()+2.54);
   $this->SetY($this->GetY()+10.08); 
   /** End: Print Header **/

   /** For title **/
   $this->SetFont('Arial', 'BU', 16);
   $this->Cell(0, 6, 'PROMISSORY NOTE', 0, 1, 'C');
   $this->Ln(8);
   /** End for title **/

  /** Get the details from the database **/
  if (isset($_GET['refno'])) {
    $refno = $_GET['refno'];
    $blood_bank = new SegBloodBank();
    if($data = $blood_bank->get_promissory_note($refno)) {
      $borrowers_name =  $data['borrowers_name'];
      $details = $blood_bank->get_blood_bank_details_by_refno($refno);  
    }  
  }
  /** End : Get the details from the database **/

  /** Body of letter **/
  $this->SetX(25.4);
  $this->SetFont('Arial', '', 12);
  $this->Write(5, 'I, ');
  $this->SetFont('Arial', 'BU', 12);
  $this->Write(5, $borrowers_name);
  $this->SetFont('Arial', '', 12); 
  $this->Write(5, ', hereby promise to replace the blood that I borrowed within 24 hours upon receiving the unit.');
  $this->Ln(8);
  $this->SetX(25.4);
  $this->Write(5, 'I am fully aware that failure to comply means non-issuance of clearance from the bank.');
  $this->Ln(12);
  /** End: Body of Letter **/

  /** Patient Details **/
  $this->Write(6, 'Patient Name');
  $this->SetX('50.9');
  $this->Write(6, ': ');
  $this->SetFont('Arial', 'BU', 12);
  $this->Write(6, $details['patient_name']);
  $this->SetFont('Arial', '', 12);
  $this->Ln();
  $this->Write(6, 'Ward');
  $this->SetX('50.9');
  $this->Write(6, ': ');
  $this->SetFont('Arial', 'BU', 12);
  $this->Write(6, $details['ward']);
  $this->SetFont('Arial', '', 12);
  $this->Ln();
  $this->Write(6, 'Blood Type');
  $this->SetX('50.9');
  $this->Write(6, ': ');
  $this->SetFont('Arial', 'BU', 12);
  $this->Write(6, $details['blood_type']);
  $this->Ln(10);
  /** End : Patient **/

  $row=6;
  $this->SetFont('Arial', 'B', 8);
  $this->Cell($this->ColumnWidth[0],$row,'Date',1,0,'C',1);
  $this->Cell($this->ColumnWidth[1],$row,'No. of Units',1,0,'C',1);
  $this->Cell($this->ColumnWidth[2],$row,'Serial No.',1,0,'C',1);
  $this->Cell($this->ColumnWidth[3],$row,'Date Replaced',1,0,'C',1);
  $this->Cell($this->ColumnWidth[4],$row,'No. of Units Replaced',1,0,'C',1);
  $this->Cell($this->ColumnWidth[5],$row,'Remarks',1,0,'C',1);
  $this->Ln();
}
  
  function FetchData() {
    if (isset($_GET['refno'])) {
      $refno = $_GET['refno'];
      $blood_bank = new SegBloodBank();
      $this->Data = $blood_bank->get_blood_bank_items($refno);  
    }
  }
  
  function Footer() {
    $this->Ln(15);
    $this->SetFont('Arial', '', 12);
    $this->Cell(100, 6, 'Signature of Borrower', 'T', 1, 'C', 1);
    $this->Ln(15);
    $this->Cell(100, 6, 'Blood Bank Personnel Copy', 'T', 0, 'C', 1);
  }
}




$x = new RepGen_Promissory();
$x->AliasNbPages();
$x->FetchData();

$x->Report();

?>