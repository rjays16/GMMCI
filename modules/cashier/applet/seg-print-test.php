//dapat call javascript function for DOM ng objects to be printed
//i forgot!
<!--
<script text="text/javascript" language="javascript">
function get_ObjectstoPrint(passed_Data)
{
	this.passed_Data = new Object(passed_Data);
}
</script>
->

<?php
	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');

	/*include this pla
	require_once('json/json.php');
	require_once('seg-cashier-printOR.php');*/

	include_once($root_path."/classes/fpdf/pdf.class.php");
	include_once($root_path."include/care_api_classes/class_cashier.php");
	
	/*include path of PrintOR class if ito gamitin*/
	
	$cClass = new SegCashier();
	
	/*added variable*/
	$print_or_class = new PrintOR();
	/*added variable*/
	
	$ORNo = $_REQUEST['nr'];
	$Mode = $_REQUEST['mode'];
	if (!$Mode) $Mode = 'R';
	$info = $cClass->GetPayInfo( $ORNo );
	
	if ($Mode == 'R') {
		$rsDetails = $cClass->GetPayDetails( $ORNo );
	}
	
	global $db;
	
	$pdf = new PDF("P",'mm','Letter');
	$pdf->AliasNbPages();   #--added
	$pdf->AddPage("P");
		
	$font="Arial";
	$borderYes="1";
	$borderNo="0";
	$newLineYes="1";
	$newLineNo="0";
	$fillYes="1";
	$fillNo="0";
	$space=2;
	
	$pdf->Image($root_path.'gui/img/logos/dmc_logo.jpg',74,6,15);

  $pdf->Ln(2);		
	$pdf->SetFont($font,"B","10");
	$pdf->Cell(17,4);
  $pdf->Cell(0,4,'Republic of the Philippines',$borderNo,$newLineYes,'C');

	$pdf->Cell(17,4);
	$pdf->Cell(0,4,'Gonzales Maranan Medical Center Incorporated',$borderNo,$newLineYes,'C');

	$pdf->SetFont($font,"B","8");
	$pdf->Cell(17,4);
  $pdf->Cell(0,4,'Quezon Ave., Digos City, Davao del Sur',$borderNo,$newLineYes,'C');
  $pdf->Ln(8);

	if ($info['type_main']) {
		$pdf->SetFont($font,"B","11");
		$pdf->Cell(0,5,$info['type_main'],$borderNo,$newLineYes,'C');	
		if ($info['type_sub']) {
			$pdf->SetFont($font,"B","10");
			$pdf->Cell(0,5,"(".$info['type_sub'].")",$borderNo,$newLineYes,'C');
		}
	}
	else {
		$pdf->SetFont($font,"B","11");
		$pdf->Cell(0,5,"PAYMENT DETAILS",$borderNo,$newLineYes,'C');	
	}

	$pdf->Ln(6);	
	
	$maxW = 196;
	$pdf->SetFont($font,"B","10");
	$pdf->Cell($maxW*0.15,4,'OR Number',$borderNo,$newLineNo,'L');
	$pdf->SetFont($font,"","10");
	$pdf->Cell($maxW*0.35,4,$info['or_no'],$borderNo,$newLineNo,'L');	
	$pdf->SetFont($font,"B","10");
	$pdf->Cell($maxW*0.25,4,'Transaction Date',$borderNo,$newLineNo,'R');
	$pdf->SetFont($font,"","10");
	$pdf->Cell($maxW*0.25,4,date("F j,Y h:ia",strtotime($info['or_date'])),$borderNo,$newLineYes,'R');
	
	$pdf->SetFont($font,"B","10");
	$pdf->Cell($maxW*0.15,4,'PID',$borderNo,$newLineNo,'L');
	$pdf->SetFont($font,"","10");
	$pdf->Cell($maxW*0.35,4,$info['pid'],$borderNo,$newLineNo,'L');	
	$pdf->SetFont($font,"B","10");
	$pdf->Cell($maxW*0.25,4,'Case No.',$borderNo,$newLineNo,'R');
	$pdf->SetFont($font,"","10");
	$pdf->Cell($maxW*0.25,4,$info['encounter_nr'],$borderNo,$newLineYes,'R');
	
	$pdf->SetFont($font,"B","10");
	$pdf->Cell($maxW*0.15,4,'Name',$borderNo,$newLineNo,'L');
	$pdf->SetFont($font,"","10");
	$pdf->Cell(0,4,$info['or_name'],$borderNo,$newLineYes,'L');
	
	$pdf->SetFont($font,"B","10");
	$pdf->Cell($maxW*0.15,4,'Address',$borderNo,$newLineNo,'L');
	$pdf->SetFont($font,"","10");
	$pdf->Cell(0,4,$info['or_address'],$borderNo,$newLineYes,'L');
	
	$pdf->Ln(3);
	$pdf->SetFont($font,"B","10");
	$pdf->SetFillColor(220);
	$pdf->Cell(0,1,'',$borderYes,$newLineYes,'L',$fillYes);
	$pdf->Cell($maxW*0.15,5,'Code',$borderYes,$newLineNo,'C',$fillYes);
	$pdf->Cell($maxW*0.50,5,'Particulars',$borderYes,$newLineNo,'C',$fillYes);
	$pdf->Cell($maxW*0.15,5,'Qty',$borderYes,$newLineNo,'C',$fillYes);
	$pdf->Cell($maxW*0.20,5,'Amount Due',$borderYes,$newLineYes,'C',$fillYes);
	$pdf->SetFillColor(220);
	
	//added codes - e2 dpat gamitin
	$print_or_class->patient_or_no = $info['or_no'];
	$print_or_class->patient_or_date = $info['or_date'];
	$print_or_class->patient_or_name = $info['or_name'];
	//added codes
	
	$pdf->SetFont('Courier',"","9");
	
	$total = 0;
	
	//added variable count - counter
	$count = 0;
	
	if ($Mode == 'R') {
		while ($row = $rsDetails->FetchRow()) {
			if ($row['qty'] == 0.0) $row['qty'] = '-';
			$pdf->Cell($maxW*0.15,5,$row['service_code'],$borderYes,$newLineNo,'L',$fillNo);
			$pdf->Cell($maxW*0.50,5,$row['service'],$borderYes,$newLineNo,'L',$fillNo);
			$pdf->Cell($maxW*0.15,5,(int)$row['qty'],$borderYes,$newLineNo,'C',$fillNo);
			$pdf->Cell($maxW*0.20,5,number_format($row['amount_due'],2),$borderYes,$newLineYes,'R',$fillNo);
			
			/*added codes*/
			$print_or_class->serv_code['count'] = $row['service_code'];
			$print_or_class->servc['count'] = $row['service'];
			$print_or_class->amt['count'] = $row['amount_due'];
			
			
			$total += $row['amount_due'];
			
			$count ++;	/*added codes*/
		}
		
		/*added codes*/
		$print_or_class->total_amount = $total;
		$json = new Services_JSON();
		$json_array[] = $print_or;
		$json_array_to_pass = $json->encode($json_array);
		echo "<script text='text/javascript' language='javascript'>";
		echo "get_ObjectsToPrint('$json_array_to_pass')";
		echo "</script>";

		
		$pdf->SetFont($font,"B","10");
		$pdf->Cell($maxW*0.80,6,"Total",$borderYes,$newLineNo,'R',$fillYes);
		$pdf->SetFont('Courier',"","9");
		$pdf->Cell($maxW*0.20,6,number_format($total,2),$borderYes,$newLineYes,'R',$fillNo);
	}

	if ($Mode == 'D') {
		$pdf->Cell($maxW*0.15,5,'',$borderYes,$newLineNo,'L',$fillNo);
		$pdf->Cell($maxW*0.50,5,'Deposit',$borderYes,$newLineNo,'L',$fillNo);
		$pdf->Cell($maxW*0.15,5,'-',$borderYes,$newLineNo,'C',$fillNo);
		$pdf->Cell($maxW*0.20,5,number_format($info['amount_due'],2),$borderYes,$newLineYes,'R',$fillNo);
		
		$pdf->SetFont($font,"B","10");
		$pdf->Cell($maxW*0.80,6,"Total",$borderYes,$newLineNo,'R',$fillYes);
		$pdf->SetFont('Courier',"","9");
		$pdf->Cell($maxW*0.20,6,number_format($info['amount_due'],2),$borderYes,$newLineYes,'R',$fillNo);
	}
	
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
	$pdf->Output();	
?>