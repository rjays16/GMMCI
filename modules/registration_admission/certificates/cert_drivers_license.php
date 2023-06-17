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

include_once($root_path.'include/care_api_classes/class_personell.php');
$pers_obj=new Personell;

$pdf  = new FPDF("P","mm","Letter");
$pdf->AddPage("P");
$pdf->SetLeftMargin(7);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(150,4);   
$pdf->Cell(0,4, "OPD-MDC",0,1,'L');
$pdf->Ln(2);

$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(67,4);
$pdf->Cell(0,6,"MEDICAL CERTIFICATE",0,1,'L');

$pdf->SetFont('Arial', '', 9);
$pdf->Cell(120,4);
$pdf->Cell(15, 6, "DATE : ", 0,1,'C');

#$pdf->Cell(10,4);
$pdf->SetFont('Arial', '', 10);
$title = "Driver's Physical and Medical Examination (for New and Renewal of Driver's License Applications)";
$length = $pdf->GetStringWidth($title);
$pdf->Cell($length, 4, $title, 0,1,'L');
$pdf->ColumnWidth = array(40,30,30,42);
$total = $pdf->ColumnWidth[0] + $pdf->ColumnWidth[1] + $pdf->ColumnWidth[2] + $pdf->ColumnWidth[3];
$ans = "";

$pdf->SetFont('Arial', '', 9);
$pdf->Cell($pdf->ColumnWidth[0], 4, "Visual Acuity", "TLR", 0, 'C');
$pdf->Cell($pdf->ColumnWidth[1], 4, "Hearing", "TLR", 0, 'C');
$pdf->Cell($pdf->ColumnWidth[2], 4, "General", "TLR",0,'C');
$pdf->Cell($pdf->ColumnWidth[3], 4, "General Health", "TLR",0,'C');
$pdf->Cell(0,4,"COMMENTS:",0,1,'L');

$pdf->Cell($pdf->ColumnWidth[0]/2, 4, "Right", "TLR", 0,'C');
$pdf->Cell($pdf->ColumnWidth[0]/2, 4, "Left", "TLR", 0, 'C');
$pdf->Cell($pdf->ColumnWidth[1]/2, 4, "Right", "TLR",0,'C');
$pdf->Cell($pdf->ColumnWidth[1]/2, 4, "Left", "TLR", 0, 'C');
$pdf->Cell($pdf->ColumnWidth[2], 4, "Physique", "LR", 0, 'C');
$pdf->Cell($pdf->ColumnWidth[3]/2, 4, "BP", "TLR", 0, 'C');
$pdf->Cell($pdf->ColumnWidth[3]/2, 4, "Contagious", "TLR", 0, 'C');
$pdf->Cell(30, 4, "FIT TO DRIVE", 0,0,'L');
$pdf->Cell(3, 3, $ans, 0, 1);

$pdf->Cell($pdf->ColumnWidth[0]/2, 4, "Eye", "LRB", 0, 'C');
$pdf->Cell($pdf->ColumnWidth[0]/2, 4, "Eye", "LRB", 0, 'C');
$pdf->Cell($pdf->ColumnWidth[1]/2, 4, "Ear", "LRB", 0, 'C');
$pdf->Cell($pdf->ColumnWidth[1]/2, 4, "Ear", "LRB", 0, 'C');
$pdf->Cell($pdf->ColumnWidth[2], 4, "", "LRB", 0, 'C');
$pdf->Cell($pdf->ColumnWidth[3]/2, 4, "", "LRB", 0, 'C');
$pdf->Cell($pdf->ColumnWidth[3]/2, 4, "Diseases", "LRB", 0, 'C');
$pdf->Cell(30, 4, "UNFIT TO DRIVE", 0,0, 'L');
$pdf->Cell(3, 3, $ans, 0, 1);

$pdf->Cell($total, 4, "",0,0);
$pdf->Cell(30, 4, "W/O Condition", 0, 1, 'L'); 
$pdf->Cell($total, 4, "", 0, 0);
$pdf->Cell(30, 4, "W/ Condition", 0, 1, 'L');
$pdf->Cell();

$pdf->Output(); 
?>
