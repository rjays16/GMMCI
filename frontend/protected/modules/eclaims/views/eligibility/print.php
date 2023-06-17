<?php
/**
 * Renders:
 * 
 * @author ? <?>
 * @copyright Copyright &copy; 2013-2014. Segworks Technologies Corporation
 * @since 1.0
 * 
 * @package eclaims.views.eligibility.print
 * 
 * @var $this EligibilityController
 * @var $encounter EclaimsEncounter
 */
$pdf = Yii::createComponent('application.extensions.tcpdf.ETcPdf', 'P', 'mm', 'Letter', true, 'UTF-8');
/* @var $pdf TCPDF */

$o = array(
    'styles' => array(
        /**
         * array(family, style, size, fontFile, subset, output)
         */
        'title' => array('', 'B', 16),
        'subTitle' => array('', 'I', 11),
        'subTitle2' => array('', 'I', 10),
        'header' => array('', 'B', 11),
        'label' => array('', 'B', 10),
        'value' => array('', '', 10),
        'value2' => array('', '', 9),
        'notes' => array('', 'I', 8),
        'signature' => array('', 'B', 10),
        'signatureNotes' => array('', 'I', 9),
        'watermark' => array('', 'I', 7),
        'watermark2' => array('', 'I', 6),
    ),
    'columnWidths' => array(
        'details' => array($pdf->getPageWidth()*0.35, 0),
        'footer' => array($pdf->getPageWidth()*0.33, $pdf->getPageWidth()*0.33, 0)
    )
);

$setFont = function($params) use ($pdf) {
    return call_user_func_array(array($pdf, 'SetFont'), $params);
};

$iff = function($value, $default='') {
    return empty($value) ? $default : $value;
};

$pdf->setPrintHeader(false);
$pdf->AddPage('', '', true);
$pdf->SetAutoPageBreak(false);

$pdf->SetLineStyle(array(
    'width' => 0.05
));

// Added by jeff 03-22-18
global $root_path;
$pdf->Image($root_path.'images/phic_sm.png', $pdf->GetX() + 0,  $pdf->GetY()-1, 14, 12, 'PNG');
$pdf->Image($root_path.'images/phic_mem.jpg', $pdf->GetX() + 180,  $pdf->GetY()-1, 13, 15, 'JPEG');

$setFont($o['styles']['subTitle']);
$pdf->Cell(0, 0, 'Republic of the Philippines', 0, 1, 'C');

$setFont($o['styles']['title']);
$pdf->Cell(0, 0, 'PHILIPPINE HEALTH INSURANCE CORPORATION', 0, 1, 'C');

$setFont($o['styles']['subTitle2']);
$pdf->Cell(0, 0, 'Citystate Centre Building, 709 Shaw Boulevard, Pasig City Healthline 441-7444', 0, 1, 'C');

$pdf->Ln(5);

$setFont($o['styles']['title']);
$pdf->Cell(0, 0, 'PhilHealth Benefit Eligibility Form', 0, 1, 'C');

$setFont($o['styles']['subTitle']);
$pdf->Cell(0, 0, '"Bawat Pilipino Miyembro, Bawat Miyembro Protektado, Kalusuguan Natin Sigurado"', 0, 1, 'C');

$pdf->Ln(5);
$setFont($o['styles']['label']);
$pdf->Cell($o['columnWidths']['details'][0], 0, 'Date/Time of Generation:', 0, 0, 'L');
$setFont($o['styles']['value']);
//$pdf->Cell($o['columnWidths']['details'][1], 0, date("F m, Y H:i:sa", strtotime($encounter->eligibility->as_of)), 0, 1, 'L');

/* Temporary: For mam Billing's sake */
$request = Yii::app()->getRequest();
$printDate = $request->getQuery('_date');
if(empty($printDate)) {
    $pdf->Cell($o['columnWidths']['details'][1], 0, date("F d, Y h:i:sA"), 0, 1, 'L');
} else {
    $_tempdatetime = date("F d, Y", strtotime($printDate)) . ' ' . date('h:i:sA');
    $pdf->Cell($o['columnWidths']['details'][1], 0, $_tempdatetime, 0, 1, 'L');
}


$setFont($o['styles']['label']);
$pdf->Cell($o['columnWidths']['details'][0], 0, 'CEWS Tracking No.:', 0, 0, 'L');
$setFont($o['styles']['value']);
$pdf->Cell($o['columnWidths']['details'][1], 0, $encounter->eligibility->tracking_number, 0, 1, 'L');


$pdf->Ln(5);

$setFont($o['styles']['header']);
$pdf->Cell(0, 0, 'HEALTH CARE INSTITUTION (HCI) INFORMATION', 'T', 1, 'L');

$pdf->Ln(1);

$setFont($o['styles']['label']);
$pdf->Cell($o['columnWidths']['details'][0], 0, 'Name of Institution:', 0, 0, 'L');
$setFont($o['styles']['value']);
$pdf->Cell($o['columnWidths']['details'][1], 0, $hciName, 0, 1, 'L');
$setFont($o['styles']['label']);
$pdf->Cell($o['columnWidths']['details'][0], 0, 'Accreditation No.:', 0, 0, 'L');
$setFont($o['styles']['value']);
$pdf->Cell($o['columnWidths']['details'][1], 0, $hciCode, 0, 1, 'L');

$pdf->Ln(5);

$setFont($o['styles']['header']);
$pdf->Cell(0, 0, 'MEMBER INFORMATION', 'T', 1, 'L');

$pdf->Ln(1);

$setFont($o['styles']['label']);
$pdf->Cell($o['columnWidths']['details'][0], 0, 'PhilHealth Identification No.:', 0, 0, 'L');
$setFont($o['styles']['value']);

if(!empty($encounter->eligibility)) {
    $_pin = $encounter->eligibility->member_pin;
} else {
    $_pin = $encounter->person->phicMember->insurance_nr;
}

$pdf->Cell($o['columnWidths']['details'][1], 0, $iff($_pin, '-'), 0, 1, 'L');
$setFont($o['styles']['label']);
$pdf->Cell($o['columnWidths']['details'][0], 0, 'Name of Member:', 0, 0, 'L');
$setFont($o['styles']['value']); 
$pdf->Cell($o['columnWidths']['details'][1], 0, $iff(mb_convert_case($encounter->person->phicMember->FullName, MB_CASE_UPPER, "UTF-8"), '-'), 0, 1, 'L');
$setFont($o['styles']['label']);
$pdf->Cell($o['columnWidths']['details'][0], 0, 'Sex', 0, 0, 'L');
$setFont($o['styles']['value']);
$pdf->Cell($o['columnWidths']['details'][1], 0, $iff($encounter->person->phicMember->Sex), 0, 1, 'L');
$setFont($o['styles']['label']);
$pdf->Cell($o['columnWidths']['details'][0], 0, 'Date of Birth:', 0, 0, 'L');
$setFont($o['styles']['value']);
$pdf->Cell($o['columnWidths']['details'][1], 0, date("Y-m-d", strtotime($encounter->person->phicMember->birth_date)), 0, 1, 'L');
$setFont($o['styles']['label']);
$pdf->Cell($o['columnWidths']['details'][0], 0, 'Member Category:', 0, 0, 'L');
$setFont($o['styles']['value']);
$pdf->Cell($o['columnWidths']['details'][1], 0, $encounter->person->phicMember->MemberTypeDesc, 0, 1, 'L');

$pdf->Ln(5);

$setFont($o['styles']['header']);
$pdf->Cell(0, 0, 'PATIENT INFORMATION', 'T', 1, 'L');

$pdf->Ln(1);

$setFont($o['styles']['label']);
$pdf->Cell($o['columnWidths']['details'][0], 0, 'Name of Patient:', 0, 0, 'L');
$setFont($o['styles']['value']);
$pdf->Cell($o['columnWidths']['details'][1], 0, mb_convert_case($encounter->person->FullName, MB_CASE_UPPER, "UTF-8"), 0, 1, 'L');
$setFont($o['styles']['label']);
$pdf->Cell($o['columnWidths']['details'][0], 0, 'Date Admitted:', 0, 0, 'L');
$setFont($o['styles']['value']);
$pdf->Cell($o['columnWidths']['details'][1], 0, date("Y-m-d", strtotime($encounter->getAdmissionDt())), 0, 1, 'L');

// Comment out by jeff 03-22-18
// $setFont($o['styles']['label']);
// $pdf->Cell($o['columnWidths']['details'][0], 0, 'Date Discharged:', 0, 0, 'L');
// $setFont($o['styles']['value']);

// $dischargedDate = null;
// if ($encounter->person->isDead()) {
//     $dischargedDate = date("Y-m-d", strtotime($encounter->person->death_date));
// }
// elseif (!empty($encounter->finalBill->bill_dte)) {
//     $dischargedDate = date("Y-m-d", strtotime($encounter->finalBill->bill_dte));
// }

// $pdf->Cell($o['columnWidths']['details'][1], 0, $dischargedDate, 0, 1, 'L');
// END 03-22-18 ----

$setFont($o['styles']['label']);
$pdf->Cell($o['columnWidths']['details'][0], 0, 'Sex:', 0, 0, 'L');
$setFont($o['styles']['value']);
$pdf->Cell($o['columnWidths']['details'][1], 0, $encounter->person->Sex, 0, 1, 'L');
$setFont($o['styles']['label']);
$pdf->Cell($o['columnWidths']['details'][0], 0, 'Date of Birth:', 0, 0, 'L');
$setFont($o['styles']['value']);
$pdf->Cell($o['columnWidths']['details'][1], 0, date("Y-m-d", strtotime($encounter->person->date_birth)), 0, 1, 'L');


$pdf->Ln(5);

$setFont($o['styles']['header']);
$pdf->Cell(0, 0, 'PHIC BENEFIT ELIGIBILITY INFORMATION', 'T', 1, 'L');

$pdf->Ln(1);

$setFont($o['styles']['label']);
$pdf->Cell($o['columnWidths']['details'][0], 0, 'ELIGIBLE TO AVAIL PHIC BENEFITS?', 0, 0, 'L');
$setFont($o['styles']['value']);
$pdf->Cell($o['columnWidths']['details'][1], 0, $encounter->eligibility->Eligibility, 0, 1, 'L');

// Added by jeff 03-22-18
if (strtoupper($encounter->eligibility->Eligibility) == 'NO') {

    $setFont($o['styles']['watermark2']);
    $pdf->Cell($o['columnWidths']['footer'][0], 0, 'Document/s:', '', 0, 'L');
    $pdf->Cell($o['columnWidths']['footer'][0], 0, 'Reason/s:', '', 0, 'L');
    $pdf->Ln(3);

    foreach($encounter->eligibility->document as $i=>$why){
        $setFont($o['styles']['watermark2']);
        $pdf->Cell(5, 0, ($i+1), 0, 0, 'L');
        $pdf->Cell($o['columnWidths']['footer'][0], 0,'SUBMIT '. $why->name,'', 0, 'L');
        $pdf->Cell($o['columnWidths']['footer'][0], 0,$why->reason,'', 0, 'L');
        }
}

$pdf->Ln(10);
$setFont($o['styles']['header']);
$pdf->Cell(0, 0, 'ATTACHED DOCUMENTS', 'T', 1, 'L');

$setFont($o['styles']['watermark']);
$pdf->Cell($o['columnWidths']['footer'][0], 0,'N/A', '', 0, 'L');

// comment out by jeff 03-22-18
// foreach($encounter->eligibility->document as $i=>$doc){
//     // $pdf->Cell(0, 0, $docs->name, 0, 1, 'L');
//     $pdf->Cell(10, 0, ($i+1), 0, 0, 'L');
//     $pdf->MultiCell(0, 0, $doc->name . ' (<i>' . $doc->reason . '</i>)' , 0, 'L', false, 1, '', '', true, 0, true);
// }

$pdf->Ln(5);

$setFont($o['styles']['header']);
$pdf->Cell(0, 0, 'IMPORTANT REMINDERS', 'T', 1, 'L');

$pdf->Ln(1);


$setFont($o['styles']['notes']);
//$pdf->MultiCell($w, $h, $txt, $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
$pdf->MultiCell(0, 0, '1. Generation and printing of this form is FREE for all PhilHealthmembers and their dependents.', 0, 'J', false, 1, '', '', true, 0, true);
$pdf->MultiCell(0, 0, '2. This form shall be submitted along with the required PhilHealth claims forms and is valid only for the confinement/admission stated above.', 0, 'J', false, 1, '', '', true, 0, true);
$pdf->MultiCell(0, 0, '3. This does not include eligibility to the rule of <b>SINGLE PERIOD OF CONFINEMENT (SPC)</b>. It shall be established when the claim is processed by PhilHealth. Non-qualification to the rule on SPC shall result to denial of this claim.', 0, 'J', false, 1, '', '', true, 0, true);


$pdf->Ln(20);
$setFont($o['styles']['signature']);
$pdf->Cell($o['columnWidths']['footer'][0], 0, 'Member/Representative', 'T', 0, 'C');
$pdf->Cell(5);
$pdf->Cell($o['columnWidths']['footer'][0], 0, 'IHCP Portal User', 'T', 1, 'C');


$setFont($o['styles']['signatureNotes']);
$pdf->Cell($o['columnWidths']['footer'][0], 0, 'Signature Over Printed Name/Thumbmark', '', 0, 'C');
$pdf->Cell(5);
$pdf->Cell($o['columnWidths']['footer'][0], 0, 'Signature Over Printed Name', '', 0, 'C');


// $pdf->Image($file, $x, $y, $w, $h, $type, $link, $align, $resize, $dpi, $palign, $ismask, $imgmask, $border, $fitbox, $hidden, $fitonpage, $alt, $altimgs)
// $pdf->Image($this->createAbsoluteUrl('QRCode/phic'), $pdf->GetX() + 10,  $pdf->GetY()-20, 40, 40, 'PNG');

// Mod by JEFF for generation of QRCode error in .35 | 03-25-18
$pdf->Image($root_path.'images/phic_qrcode.png', $pdf->GetX() + 10,  $pdf->GetY()-20, 40, 40, 'PNG');

// Mod by JEFF for generation of QRCode error in .35 | 03-25-18
$pdf->Image($root_path.'images/phic_qrcode.png', $pdf->GetX() + 10,  $pdf->GetY()-20, 40, 40, 'PNG');

// Added by jeff 03-22-18
$pdf->Ln(40);
$setFont($o['styles']['watermark']);
$pdf->Cell(0,0,'Philippine Health Insurance Corporation','', 0, 'C');
$pdf->Ln(3);
$pdf->Cell(0,0,'Citystate Centre, 709 Shaw Boulevard, Pasig City. Healthline 441-7444','', 0, 'C');

$pdf->Output();
