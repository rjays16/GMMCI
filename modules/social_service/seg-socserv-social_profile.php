<?php
require('./roots.php');
include_once($root_path."/classes/fpdf/pdf.class.php");
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');

class SocialProfile extends FPDF{
  var $from;
  
  function SocialProfile(){
    $this->FPDF("P", 'mm', 'Letter'); 
    #if ($from) $this->from=date("Y-m-d",strtotime($from));  
    //185 total width
  }
  function Header(){
    $this->SetFont('Arial', '', 14);
    $this->Cell(0, 6, "Gonzales Maranan Medical Center Incorporated", 0, 1, 'C');
    $this->SetFont('Arial', '', 10);
    $this->Cell(0, 4, "Medical Social Service", 0, 1, 'C');
    $this->Ln(5);
    $this->SetFont('Arial', 'B', 12);
    $this->Cell(0, 4, "SOCIAL PROFILE", 0, 1, 'C');
    $this->Ln(5);
  }
  
  function Profile(){
    $dlength = 50;
    
      $this->SetFont('Arial', 'B', 10);
      $this->Cell(145, 4);
      $x = $this->GetX();
      $y = $this->GetY();   
      $this->Cell($dlength, 4, "June 13, 2009", 0, 1, 'C'); //Date
      $this->Line($x, $y+4, $x+$dlength, $y+4);
      $this->Cell(145, 4);
      $this->Cell($dlength, 4, "Date", 0, 1, 'C');
      $this->Ln(2);
      
      $this->SetLeftMargin(20);
      $this->Cell(35, 6, "I.      Patient's Name:", 0, 0, 'L');
      $x1 = $this->GetX();
      $y1 = $this->GetY();
      $this->Cell(150, 6, "", 0, 1, 'L');   //Patient's Name
      $this->Line($x1, $y1+5, $x1+150, $y1+5);
      $this->Ln();
      $this->Cell(35, 6, "II.     MSW Category:", 0, 0, 'L');
      $this->Cell(150, 6, "", 0, 1, 'L');  //MSW Category
      $this->Line($x1, $y1+17, $x1+150, $y1+17);
      $this->Ln();
      $this->Cell(68, 4, "III.    Psychosocial Assessment/Diagnosis:", 0, 1, 'L');
      $this->Line($x1+38, $y1+28, $x1+150, $y1+28);
      $x = $this->GetX();
      $this->Line($x+9, $y1+34, $x+185, $y1+34);
      $this->Line($x+9, $y1+40, $x+185, $y1+40);
      $this->Line($x+9, $y1+46, $x+185, $y1+46);
      $this->Ln(24);
      $this->Cell(35, 6, "IV.     Action Plans:", 0, 0, 'L');
      $this->Cell(150, 6, "", 0, 1, 'L'); //Action Plans
      $this->Line($x1, $y1+57, $x1+150, $y1+57);
      $this->Line($x+9, $y1+63, $x+185, $y1+63);
      $this->Ln(10);
      $this->Cell(42, 6, "V.      Recommendation:", 0,0,'L');
      $x2 = $this->GetX();
      $y2 = $this->GetY();
      $this->Cell(143, 6, "", 0, 0, 'L');
      $this->Line($x2, $y2+5, $x2+143, $y2+5);
      $this->Line($x+9, $y2+11, $x+185, $y2+11);
  }
  
  function Signatures(){
    $this->SetFont('Arial', 'B', 10);
    $this->Ln(20);
    $this->Cell(140, 4, "", 0);
    $this->Cell(45, 4, "Prepared by:", 0, 0, 'L');
    $this->Ln(15);
    $this->Cell(140, 4, "", 0);
    $x = $this->GetX();
    $y = $this->GetY();
    $this->Line($x, $y, $x+45, $y);
    $this->Cell(45, 4, "Medical Social Worker", 0, 1, 'C');
    $this->Ln(10);
    $this->Cell(0, 4, "Noted by:",0,0, 'L');
    $this->Ln(15);
    $x1 = $this->GetX();
    $y1 = $this->GetY();
    $this->Line($x1, $y1, $x1+40, $y1);
    $this->Cell(40, 4, "Chief, MSW", 0,0,'C');
    
    
  }
}
$pdf = new SocialProfile();
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->Profile();
$pdf->Signatures();
$pdf->Output(); 
?>
