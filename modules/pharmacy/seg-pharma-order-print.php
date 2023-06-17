<?php
	include_once($root_path."/classes/fpdf/pdf.class.php");
	include_once($root_path."include/care_api_classes/class_order.php");
	$order_obj = new SegOrder("pharma");
	$infoResult = $order_obj->getOrderInfo($_REQUEST['ref']);
	if ($infoResult)	$info = $infoResult->FetchRow();
	
	include_once($root_path."include/care_api_classes/class_product.php");
	$prod_obj = new Product();
	
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
  $pdf->Cell(0,4,'Quezon Ave., Digos City',$borderNo,$newLineYes,'C');
  $pdf->Ln(8);


	$areaname = $db->GetOne("SELECT area_name FROM seg_pharma_areas WHERE area_code='".$info['pharma_area']."'");
	$pdf->SetFont($font,"B","11");
	$pdf->Cell(0,5,$areaname,$borderNo,$newLineYes,'C');

	$type = ($info["is_cash"] == "1") ? "CASH" : "CHARGE";
	$pdf->SetFont($font,"B","11");
	$pdf->Cell(0,5,"PHARMACY ORDER REQUEST ($type)",$borderNo,$newLineYes,'C');
	
	if ($info["is_urgent"] == "1") {
		$pdf->SetFont($font,"B","15");
		$pdf->SetTextColor(220,0,0);
		$pdf->Cell(0,6,"URGENT",$borderNo,$newLineYes,'C');
		$pdf->SetTextColor(0,0,0);
	}
	
	$pdf->Ln(6);
	
	
	$maxW = 196;
	$pdf->SetFont($font,"B","10");
	$pdf->Cell($maxW*0.15,4,'Reference No.',$borderNo,$newLineNo,'L');
	$pdf->SetFont($font,"","10");
	$pdf->Cell($maxW*0.35,4,$info['refno'],$borderNo,$newLineNo,'L');	
	$pdf->SetFont($font,"B","10");
	$pdf->Cell($maxW*0.25,4,'Order Date',$borderNo,$newLineNo,'R');
	$pdf->SetFont($font,"","10");
	$pdf->Cell($maxW*0.25,4,date("F j,Y h:ia",strtotime($info['orderdate'])),$borderNo,$newLineYes,'R');
	
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
	$pdf->Cell(0,4,$info['ordername'],$borderNo,$newLineYes,'L');
	
	$pdf->SetFont($font,"B","10");
	$pdf->Cell($maxW*0.15,4,'Address',$borderNo,$newLineNo,'L');
	$pdf->SetFont($font,"","10");
	$pdf->Cell(0,4,$info['orderaddress'],$borderNo,$newLineYes,'L');
	
	$pdf->Ln(3);
	$pdf->SetFont($font,"B","12");
	$pdf->Cell(0,6,'Item List',$borderNo,$newLineYes,'L');
	$pdf->SetFont($font,"B","10");
	$pdf->SetFillColor(220);
	$pdf->Cell(0,1,'',$borderYes,$newLineYes,'L',$fillYes);
	$pdf->Cell($maxW*0.15,5,'Item No.',$borderYes,$newLineNo,'C',$fillYes);
	$pdf->Cell($maxW*0.45,5,'Description',$borderYes,$newLineNo,'C',$fillYes);
	$pdf->Cell($maxW*0.15,5,'Price',$borderYes,$newLineNo,'C',$fillYes);
	$pdf->Cell($maxW*0.10,5,'Qty',$borderYes,$newLineNo,'C',$fillYes);
	$pdf->Cell($maxW*0.15,5,'Amount',$borderYes,$newLineYes,'C',$fillYes);
	$pdf->SetFillColor(220);
	
	$pdf->SetFont('Courier',"","9");
	$result = $order_obj->getOrderItemsFullInfo($_REQUEST['ref']);
	$total = 0;
	while ($row = $result->FetchRow()) {
		$pdf->Cell($maxW*0.15,5,$row['bestellnum'],$borderYes,$newLineNo,'L',$fillNo);
		$pdf->Cell($maxW*0.45,5,$row['artikelname'],$borderYes,$newLineNo,'L',$fillNo);
		$pdf->Cell($maxW*0.15,5,number_format($row['force_price'],2),$borderYes,$newLineNo,'R',$fillNo);
		$pdf->Cell($maxW*0.10,5,$row['quantity'],$borderYes,$newLineNo,'C',$fillNo);
		$amount = $row['force_price'] * $row['quantity'];
		$total += $amount;
		$pdf->Cell($maxW*0.15,5,number_format($amount,2),$borderYes,$newLineYes,'R',$fillNo);
	}
	$pdf->SetFont($font,"B","10");
	$pdf->Cell($maxW*0.85,6,"Total",$borderYes,$newLineNo,'R',$fillYes);
	$pdf->SetFont('Courier',"","9");
	$pdf->Cell($maxW*0.15,6,number_format($total,2),$borderYes,$newLineYes,'R',$fillNo);
	
	$pdf->Output();	
?>