<?php

require('./roots.php');
require_once $root_path.'include/inc_environment_global.php';

require_once $root_path."/classes/fpdf/pdf.class.php";
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
require_once $root_path."include/care_api_classes/class_credit_memo.php";
$cm = new SegCreditMemo();
$Nr = $_REQUEST['nr'];
$info = $cm->getMemoInfo( $Nr );
$rsDetails = $cm->getMemoDetails( $Nr );
$details = array();
while ($row=$rsDetails->FetchRow()) {
	$details[] = $row;
}

$pdf = new PDF("P",'mm','Letter');
$pdf->AliasNbPages();   #--added
$pdf->AddPage();

function PrintOnce() {
	global $pdf;
	global $db;
	global $cm;
	global $info;
	global $details;
		
	$font="Arial";
	$borderYes="1";
	$borderNo="0";
	$newLineYes="1";
	$newLineNo="0";
	$fillYes="1";
	$fillNo="0";
	$space=2;
	
	#$pdf->Image($root_path.'gui/img/logos/dmc_logo.jpg',74,6,15);
	
	$objInfo = new Hospital_Admin();

	if ($row = $objInfo->getAllHospitalInfo()) {
		$row['hosp_agency'] = strtoupper($row['hosp_agency']);
		$row['hosp_name']   = strtoupper($row['hosp_name']);
	}
	else {
		$row['hosp_country'] = "Republic of the Philippines";
		$row['hosp_agency']  = "DEPARTMENT OF HEALTH";
		$row['hosp_name']    = "Gonzales Maranan Medical Center Incorporated";
		$row['hosp_addr1']   = "Quezon Ave., Digos City, Davao del Sur";
	}

	$pdf->SetAutoPageBreak(FALSE);
	$pdf->Ln(2);		
	$pdf->SetFont($font,"B","10");
	#$pdf->Cell(17,4);
	$pdf->Cell(0,4,$row['hosp_country'],$borderNo,$newLineYes,'C');

	#$pdf->Cell(17,4);
	$pdf->Cell(0,4,$row['hosp_name'],$borderNo,$newLineYes,'C');

	$pdf->SetFont($font,"B","8");
	#$pdf->Cell(17,4);
	$pdf->Cell(0,4,"CASHIER'S OFFICE",$borderNo,$newLineYes,'C');
	$pdf->Ln(8);

	$pdf->SetFont($font,"B","12");
	$pdf->Cell(0,5,"CREDIT MEMO FOR REFUND OFFICIAL RECEIPT",$borderNo,$newLineYes,'C');	

	$pdf->Ln(6);	
	
	$maxW = 196;
	$padding = 7;
	$rowH = 4;
	
	$pdf->SetFont($font,"B","10");
	$pdf->Cell($padding,$rowH);
	$pdf->Cell($maxW*0.12,$rowH,'Memo No.',$borderNo,$newLineNo,'L');
	$pdf->SetFont($font,"","10");
	$pdf->Cell($maxW*0.42,$rowH,$info['memo_nr'],$borderNo,$newLineNo,'L');	
	$pdf->SetFont($font,"B","10");
	$pdf->Cell($maxW*0.17,$rowH,'Issue Date',$borderNo,$newLineNo,'R');
	$pdf->SetFont($font,"","10");
	if ($info['issue_date'])
		$pdf->Cell($maxW*0.28-$padding*2,$rowH,date("M j,Y h:ia",strtotime($info['issue_date'])),$borderNo,$newLineYes,'R');
	else
		$pdf->Cell($maxW*0.28-$padding*2,$rowH,'No date indicated',$borderNo,$newLineYes,'R');
	
	$pdf->SetFont($font,"B","10");
	$pdf->Cell($padding,$rowH);
	$pdf->Cell($maxW*0.12,$rowH,'HRN',$borderNo,$newLineNo,'L');
	$pdf->SetFont($font,"","10");
	$pdf->Cell($maxW*0.42,$rowH,$info['pid'],$borderNo,$newLineNo,'L');	
	$pdf->SetFont($font,"B","10");
	$pdf->Cell($maxW*0.17,$rowH,'Case No.',$borderNo,$newLineNo,'R');
	$pdf->SetFont($font,"","10");
	$pdf->Cell($maxW*0.28-$padding*2,$rowH,$info['encounter_nr'],$borderNo,$newLineYes,'C');
	
	$pdf->SetFont($font,"B","10");
	$pdf->Cell($padding,$rowH);
	$pdf->Cell($maxW*0.12,$rowH,'Name',$borderNo,$newLineNo,'L');
	$pdf->SetFont($font,"","10");
	$pdf->Cell(0,$rowH,$info['memo_name'],$borderNo,$newLineYes,'L');
	
#	$pdf->SetFont($font,"B","10");
#	$pdf->Cell($padding,$rowH);
#	$pdf->Cell($maxW*0.12,$rowH,'Address',$borderNo,$newLineNo,'L');
#	$pdf->SetFont($font,"","10");
#	$pdf->Cell(0,$rowH,$info['memo_address'],$borderNo,$newLineYes,'L');
	
	$pdf->Ln(3);
	$pdf->SetFont($font,"B","9");
	$pdf->SetFillColor(0xFF);
	
	$rowH = 5;
	$colW = array(
		$maxW*0.15,
		$maxW*0.08,
		$maxW*0.14,
		$maxW*0.52,//$maxW*0.33,
		$maxW*0.14,
		$maxW*0.05,
		$maxW*0.14);
	
	$pdf->Cell($colW[0],$rowH,'OR #',$borderYes,$newLineNo,'C',$fillYes);
	#$pdf->Cell($colW[1],$rowH,'Source',$borderYes,$newLineNo,'C',$fillYes);
	#$pdf->Cell($colW[2],$rowH,'Code',$borderYes,$newLineNo,'C',$fillYes);
	$pdf->Cell($colW[3],$rowH,'Particulars',$borderYes,$newLineNo,'C',$fillYes);
	$pdf->Cell($colW[4],$rowH,'Price',$borderYes,$newLineNo,'C',$fillYes);
	$pdf->Cell($colW[5],$rowH,'Qty',$borderYes,$newLineNo,'C',$fillYes);
	$pdf->Cell($colW[6],$rowH,'Total',$borderYes,$newLineYes,'C',$fillYes);
	
	$pdf->SetFont('Arial',"","9");
	
	$total_refund = 0;
	
	foreach ($details as $i=>$row) {
		if ($row['refund'] == 0.0) $row['refund'] = '-';
		$total = (float) $row['refund'] * (float) $row['price'];

		$pdf->SetFont('Arial',"B","11");
		$pdf->Cell($colW[0],$rowH,$row['or_no'],$borderYes,$newLineNo,'C',$fillNo);
		$pdf->SetFont('Arial',"","9");
		#$pdf->Cell($colW[1],$rowH,$row['ref_source'],$borderYes,$newLineNo,'L',$fillNo);
		#$pdf->Cell($colW[2],$rowH,$row['service_code'],$borderYes,$newLineNo,'L',$fillNo);
		$pdf->Cell($colW[3],$rowH,$row['service_name'],$borderYes,$newLineNo,'L',$fillNo);
		$pdf->Cell($colW[4],$rowH,number_format($row['price'],2),$borderYes,$newLineNo,'R',$fillNo);
		$pdf->Cell($colW[5],$rowH,(int)$row['refund'],$borderYes,$newLineNo,'C',$fillNo);
		$pdf->Cell($colW[6],$rowH,number_format($total,2),$borderYes,$newLineYes,'R',$fillNo);
		$total_refund += $total;
	}


	$pdf->SetFont($font,"B","9");
	$pdf->Cell($maxW-$colW[4]-$colW[5]-$colW[6],$rowH,'','TLB',$newLineNo,'R',$fillYes);
	$pdf->Cell($colW[4]+$colW[5],$rowH,"Total refund",'TRB',$newLineNo,'C',$fillYes);
	$pdf->SetFont('Arial',"","9");
	$pdf->Cell($colW[6],$rowH,number_format($total_refund,2),$borderYes,$newLineYes,'R',$fillNo);


	$pdf->Ln(4);
	
	$rowH = 5;
	$padding = 8;
	#$pdf->SetY(-44);
	$pdf->SetFont($font,"B",9);
	$colW = array(
		$maxW*0.25,
		$maxW*0.4,
		$maxW*0.1,
		$maxW*0.25-$padding*2
	);

	$pdf->Cell($padding,$rowH);
	$pdf->Cell($colW[0],$rowH,"Collection officer:",$borderNo,$newLineNo,'L',$fillNo);
	$pdf->Cell($colW[1],$rowH,'',$borderNo,$newLineNo);
	$pdf->Cell($colW[2],$rowH,"Received refund: Php",$borderNo,$newLineNo,'R',$fillNo);
	$pdf->SetFont('Arial','B',12);
	$pdf->Cell($colW[3],$rowH,number_format($total_refund,2),'B',$newLineYes,'L',$fillNo);
	$pdf->SetFont($font,"B","9");

	$pdf->Cell(0,$rowH*1.5,'',$borderNo,$newLineYes);

	$pdf->Cell($padding,$rowH);
	
	$pdf->Cell($colW[0],$rowH,strtoupper($info["personnel_name"]),'T',$newLineNo,'C',$fillNo);
	$pdf->Cell($colW[1]+$colW[2]-25,$rowH);
	$pdf->Cell($colW[3]+20,$rowH,'Signature Over Printed Name','T',$newLineYes,'C');
	
	$pdf->Cell($padding,$rowH);
	$pdf->SetFont($font,"","9");
	$pdf->Cell($colW[0],$rowH,strtoupper($info["job_position"]),'',$newLineNo,'C',$fillNo);
	$pdf->Cell(0,$rowH*1.5,'',$borderNo,$newLineYes);
	
	$pdf->SetFont($font,"B","9");
	$w = $colW[0] * 1.5;
	$width2 = ($maxW-$w)/2;
	$pdf->Cell($width2,$rowH);
	$pdf->Cell($w,$rowH,strtoupper($_SESSION['sess_temp_fullname']),'T',$newLineYes,'C');
	
	#$pdf->Cell($padding,$rowH-2);
	$pdf->SetFont($font,"","9");
	$pdf->Cell($width2,$rowH);
	#$pdf->Cell($colW[0],$rowH-2,"Administrative Officer III",$borderNo,$newLineYes,'C',$fillNo);
	
	$query = "SELECT job_position\n".
		"FROM care_users `u`\n".
		"LEFT JOIN care_personell `p` ON `u`.personell_nr=`p`.nr\n".
		"WHERE `u`.login_id=".$db->qstr($_SESSION['sess_temp_userid']);
	$position=$db->GetOne($query);
	$pdf->Cell($w,$rowH-2,$position,$borderNo,$newLineYes,'C',$fillNo);
	/*
	$result = $order_obj->getOrderItemsFullInfo($_REQUEST['ref']);
	$total = 0;
	while ($row = $result->FetchRow()) {
		$pdf->Cell($maxW*0.15,5,$row['bestellnum'],$borderYes,$newLineNo,'L',$fillNo);
		$pdf->Cell($maxW*0.45,5,$row['artikelname'],$borderYes,$newLineNo,'L',$fillNo);
		$pdf->Cell($maxW*0.15,5,money_format("%!0.2i",$row['force_price']),$borderYes,$newLineNo,'R',$fillNo);
		$pdf->Cell($maxW*0.10,5,$row['quantity'],$borderYes,$newLineNo,'C',$fillNo);
		$amount = $row['force_price'] * $row['quantity'];
		$total += $amount;
		$pdf->Cell($maxW*0.15,5,money_format("%!0.2i",$amount),$borderYes,$newLineYes,'R',$fillNo);
	}
	
	*/
}


PrintOnce();
$pdf->SetX(0);
$pdf->SetY(139.7);
$pdf->Cell(0,3.12,'','T',$newLineYes);
$pdf->Ln();

PrintOnce();






$pdf->Output();	
