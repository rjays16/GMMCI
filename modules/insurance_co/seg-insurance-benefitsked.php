<?php
							
	include("roots.php");
	require('./roots.php');
	
	include_once($root_path."/classes/fpdf/fpdf.php");
	require_once($root_path.'include/inc_environment_global.php');
	include_once($root_path.'include/inc_date_format_functions.php');
	
	/* Load the insurance object */
	require_once($root_path.'include/care_api_classes/class_insurance.php');
	$ins_obj=new Insurance;

	$pdf = new FPDF();		
	$pdf->AliasNbPages();   #--added
	$pdf->AddPage("P");
	
	$borderYes="1";
	$borderNo="0";
	$newLineYes="1";
	$newLineNo="0";
	$space=2;
	
	$pdf->Image($root_path.'gui/img/logos/dmc_logo.jpg',20,10,20,20);
	
	$pdf->SetFont("Times","B","10");
   $pdf->Cell(0,4,'Gonzales Maranan Medical Center Incorporated',$borderNo,$newLineYes,'C');
	$pdf->SetFont("Times","B","10");
   $pdf->Cell(0,4,'OUTPATIENT and PREVENTIVE CARE CENTER',$borderNo,$newLineYes,'C');
	$pdf->Ln(2);
	$pdf->SetFont("Times","B","8");
   $pdf->Cell(0,4,'Quezon Ave., Digos City, Davao del Sur',$borderNo,$newLineYes,'C');
   $pdf->Ln($space*2);
	
	$hcare_id = $_GET['hcare_id'];
	
	if ($_GET['effective_date']){
		$effective_date = date("Y-m-d",strtotime($_GET['effective_date']));
		#$date = date("F d, Y", strtotime($effective_date));
	}else{
		$effective_date = "0000-00-00";
		#$date =
	}
	$insurance = $ins_obj->getInsuranceInfo($hcare_id);
	
	$pdf->Cell(0,4,strtoupper($insurance['name']),$borderNo,$newLineYes,'C');
   $pdf->Ln($space*2);
	$pdf->Cell(0,4,'BENEFIT SCHEDULE',$borderNo,$newLineYes,'C');
	
	$pdf->Ln($space*2);
	$pdf->Cell(0,4,'EFFECTIVITY DATE (Y-M-D) : '.$effective_date,$borderNo,$newLineYes,'C');
	
   $pdf->Ln($space*2);
	$pdf->SetFont("Arial","B","9");
	$pdf->Cell(40,4,'BENEFIT ITEM','LTR',$newLineno,'L');
	$pdf->Cell(150,4,'CASE TYPES','TBR',$newLineno,'C');
	$pdf->Ln($space*2);
	$pdf->Cell(40,4,'','LBR',$newLineNo,'L');
	$pdf->Cell(37,4,'A','TBR',$newLineNo,'C');
	$pdf->Cell(37,4,'B','TBR',$newLineNo,'C');
	$pdf->Cell(37,4,'C','TBR',$newLineNo,'C');
	$pdf->Cell(39,4,'D','TBR',$newLineNo,'C');
	$pdf->Ln($space*2);
	$pdf->Cell(40,4,'Room & Board','LTBR',$newLineNo,'L');
	
	if ($room1 = $ins_obj->getBenefitShedule($hcare_id, 1, 1, $effective_date)) {
	#echo "inse = ".$ins_obj->sql;	
	    $rsroom1 = $room1->FetchRow();
    }
	$pdf->Cell(37,4,number_format($rsroom1['rateperday'],2).' /day','TBR',$newLineNo,'C');    
	
	if ($room2 = $ins_obj->getBenefitShedule($hcare_id, 1, 2, $effective_date)) {
	    $rsroom2 = $room2->FetchRow();
    }
	$pdf->Cell(37,4,number_format($rsroom2['rateperday'],2).' /day','TBR',$newLineNo,'C');    

	if ($room3 = $ins_obj->getBenefitShedule($hcare_id, 1, 3, $effective_date)) {
	    $rsroom3 = $room3->FetchRow();
    }
	$pdf->Cell(37,4,number_format($rsroom3['rateperday'],2).' /day','TBR',$newLineNo,'C');    

	if ($room4 = $ins_obj->getBenefitShedule($hcare_id, 1, 4, $effective_date)) {
	    $rsroom4 = $room4->FetchRow();
    }
	$pdf->Cell(39,4,number_format($rsroom4['rateperday'],2).' /day','TBR',$newLineNo,'C');
	
	$pdf->Ln($space*2);
	
	$pdf->Cell(40,4,'Drugs & Medicine','LTBR',$newLineNo,'L');
	
	if ($med1 = $ins_obj->getBenefitShedule($hcare_id, 2, 1, $effective_date)) {
	    #echo  $ins_obj->sql;
	    $rsmed1 = $med1->FetchRow();
    }
	$pdf->Cell(37,4,number_format($rsmed1['amountlimit'],2),'TBR',$newLineNo,'C');    
	
	if ($med2 = $ins_obj->getBenefitShedule($hcare_id, 2, 2, $effective_date)) {
	    $rsmed2 = $med2->FetchRow();
    }
	$pdf->Cell(37,4,number_format($rsmed2['amountlimit'],2),'TBR',$newLineNo,'C');    
	    
	if ($med3 = $ins_obj->getBenefitShedule($hcare_id, 2, 3, $effective_date)) {
	    $rsmed3 = $med3->FetchRow();	    
    }
    $pdf->Cell(37,4,number_format($rsmed3['amountlimit'],2),'TBR',$newLineNo,'C');
	
	if ($med4 = $ins_obj->getBenefitShedule($hcare_id, 2, 4, $effective_date)) {
	    $rsmed4 = $med4->FetchRow();
    }
	$pdf->Cell(39,4,number_format($rsmed4['amountlimit'],2),'TBR',$newLineNo,'C');
	
	$pdf->Ln($space*2);
	$pdf->Cell(40,4,'Xray, Lab & Others','LTBR',$newLineNo,'L');
	
	if ($serv1 = $ins_obj->getBenefitShedule($hcare_id, 3, 1, $effective_date)) {
	    $rsserv1 = $serv1->FetchRow();
    }
	$pdf->Cell(37,4,number_format($rsserv1['amountlimit'],2),'TBR',$newLineNo,'C');
	
	if ($serv2 = $ins_obj->getBenefitShedule($hcare_id, 3, 2, $effective_date)) {
	    $rsserv2 = $serv2->FetchRow();
    }
	$pdf->Cell(37,4,number_format($rsserv2['amountlimit'],2),'TBR',$newLineNo,'C');
	
	if ($serv3 = $ins_obj->getBenefitShedule($hcare_id, 3, 3, $effective_date)) {
	    $rsserv3 = $serv3->FetchRow();
    }
	$pdf->Cell(37,4,number_format($rsserv3['amountlimit'],2),'TBR',$newLineNo,'C');
	
	if ($serv4 = $ins_obj->getBenefitShedule($hcare_id, 3, 4, $effective_date)) {
	    $rsserv4 = $serv4->FetchRow();
    }
	$pdf->Cell(39,4,number_format($rsserv4['amountlimit'],2),'TBR',$newLineNo,'C');    
	
	$pdf->Ln($space*2);
	
	$pdf->Cell(40,4,'Operating Room','LTR',$newLineNo,'L');
	
	if ($OR1 = $ins_obj->getBenefitShedule($hcare_id, 5, 1, $effective_date)) {
        $rsOR1 = $OR1->FetchRow();
    }
    $pdf->Cell(37,4,'RVU 30 & below = ','TR',$newLineNo,'C');
	
	if ($OR2 = $ins_obj->getBenefitShedule($hcare_id, 5, 2, $effective_date)) {
	    $rsOR2 = $OR2->FetchRow();
    }
	$pdf->Cell(37,4,'RVU 81 to 200 = ','TR',$newLineNo,'C');
	
	if ($OR3 = $ins_obj->getBenefitShedule($hcare_id, 5, 3, $effective_date)) {
	    $rsOR3 = $OR3->FetchRow();
    }
	$pdf->Cell(37,4,'RVU 201 to 500 = ','TR',$newLineNo,'C');
	
	if ($OR4 = $ins_obj->getBenefitShedule($hcare_id, 5, 4, $effective_date)) {
	    $rsOR4 = $OR4->FetchRow();
    }
	$pdf->Cell(39,4,'RVU 501 & above = ','TR',$newLineNo,'C');
	
	$pdf->Ln($space*2);
	$pdf->Cell(40,4,'','LBR',$newLineNo,'L');
	$pdf->Cell(37,4,number_format($rsOR1['amountlimit'],2),'BR',$newLineNo,'C');
	$pdf->Cell(37,4,number_format($rsOR2['amountlimit'],2),'BR',$newLineNo,'C');
	$pdf->Cell(37,4,number_format($rsOR3['amountlimit'],2),'BR',$newLineNo,'C');
	$pdf->Cell(39,4,number_format($rsOR4['amountlimit'],2),'BR',$newLineNo,'C');
	/*
	$pdf->Ln($space*2);
	$pdf->Cell(40,4,'','LBR',$newLineNo,'L');
	$pdf->Cell(37,4,'Max. of '.number_format($rsOR1['amountlimit'],2),'BR',$newLineNo,'C');
	$pdf->Cell(37,4,'Max. of '.number_format($rsOR2['amountlimit'],2),'BR',$newLineNo,'C');
	$pdf->Cell(37,4,'Max. of '.number_format($rsOR3['amountlimit'],2),'BR',$newLineNo,'C');
	$pdf->Cell(39,4,'Max. of '.number_format($rsOR4['amountlimit'],2),'BR',$newLineNo,'C');
	*/
	$pdf->Ln($space*2);
	
	$pdf->Cell(190,4,'','LTR',$newLineNo,'L');
	$pdf->Ln($space*2);
	$pdf->Cell(190,4,'Professional Fees:','LBR',$newLineNo,'L');
	$pdf->Ln($space*2);
	
	$pdf->Cell(40,4,'General Practitioner','LTR',$newLineNo,'L');
	
	if ($gen1 = $ins_obj->getBenefitShedule($hcare_id, 4, 1, $effective_date)) {
        $rsgen1 = $gen1->FetchRow();
    }
    $pdf->Cell(37,4,number_format($rsgen1['rateperday'],2).' /day','TR',$newLineNo,'C');
	
	if ($gen2 = $ins_obj->getBenefitShedule($hcare_id, 4, 2, $effective_date)) {
	    $rsgen2 = $gen2->FetchRow();
    }
	$pdf->Cell(37,4,number_format($rsgen2['rateperday'],2).' /day','TR',$newLineNo,'C');
	
	if ($gen3 = $ins_obj->getBenefitShedule($hcare_id, 4, 3, $effective_date)) {
        $rsgen3 = $gen3->FetchRow();
    }
    $pdf->Cell(37,4,number_format($rsgen3['rateperday'],2).' /day','TR',$newLineNo,'C');
	
	if ($gen4 = $ins_obj->getBenefitShedule($hcare_id, 4, 4, $effective_date)) {
	    $rsgen4 = $gen4->FetchRow();
    }
	$pdf->Cell(39,4,number_format($rsgen4['rateperday'],2).' /day','TR',$newLineNo,'C');
	
	$pdf->Ln($space*2);
	$pdf->Cell(40,4,'','LBR',$newLineNo,'L');
	$pdf->Cell(37,4,'Max. of '.number_format($rsgen1['amountlimit'],2),'BR',$newLineNo,'C');
	$pdf->Cell(37,4,'Max. of '.number_format($rsgen2['amountlimit'],2),'BR',$newLineNo,'C');
	$pdf->Cell(37,4,'Max. of '.number_format($rsgen3['amountlimit'],2),'BR',$newLineNo,'C');
	$pdf->Cell(39,4,'Max. of '.number_format($rsgen4['amountlimit'],2),'BR',$newLineNo,'C');
	
	$pdf->Ln($space*2);
	$pdf->Cell(40,4,'Specialist','LTR',$newLineNo,'L');
	
	if ($spe1 = $ins_obj->getBenefitShedule($hcare_id, 9, 1, $effective_date)) {
	    $rsspe1 = $spe1->FetchRow();
    }
	$pdf->Cell(37,4,number_format($rsspe1['rateperday'],2).' /day','TR',$newLineNo,'C');
	
	if ($spe2 = $ins_obj->getBenefitShedule($hcare_id, 9, 2, $effective_date)) {
	    $rsspe2 = $spe2->FetchRow();
    }
	$pdf->Cell(37,4,number_format($rsspe2['rateperday'],2).' /day','TR',$newLineNo,'C');
	
	if ($spe3 = $ins_obj->getBenefitShedule($hcare_id, 9, 3, $effective_date)) {
	    $rsspe3 = $spe3->FetchRow();
    }
	$pdf->Cell(37,4,number_format($rsspe3['rateperday'],2).' /day','TR',$newLineNo,'C');
	
	if ($spe4 = $ins_obj->getBenefitShedule($hcare_id, 9, 4, $effective_date)) {
	    $rsspe4 = $spe4->FetchRow();
    }
	$pdf->Cell(39,4,number_format($rsspe4['rateperday'],2).' /day','TR',$newLineNo,'C');
	
	$pdf->Ln($space*2);
	$pdf->Cell(40,4,'','LBR',$newLineNo,'L');
	$pdf->Cell(37,4,'Max. of '.number_format($rsspe1['amountlimit'],2),'BR',$newLineNo,'C');
	$pdf->Cell(37,4,'Max. of '.number_format($rsspe2['amountlimit'],2),'BR',$newLineNo,'C');
	$pdf->Cell(37,4,'Max. of '.number_format($rsspe3['amountlimit'],2),'BR',$newLineNo,'C');
	$pdf->Cell(39,4,'Max. of '.number_format($rsspe4['amountlimit'],2),'BR',$newLineNo,'C');
	
	$pdf->Ln($space*2);
	$pdf->Cell(40,4,'Surgeon','LTR',$newLineNo,'L');
	
	if ($sur1 = $ins_obj->getBenefitShedule($hcare_id, 10, 1, $effective_date)) {
        $rssur1 = $sur1->FetchRow();
    }
    $pdf->Cell(37,4,number_format($rssur1['rateperRVU'],2).' /RVU','TR',$newLineNo,'C');
	
	if ($sur2 = $ins_obj->getBenefitShedule($hcare_id, 10, 2, $effective_date)) {
	    $rssur2 = $sur2->FetchRow();
    }
	$pdf->Cell(37,4,number_format($rssur1['rateperRVU'],2).' /RVU','TR',$newLineNo,'C');
	
	if ($sur3 = $ins_obj->getBenefitShedule($hcare_id, 10, 3, $effective_date)) {
	    $rssur3 = $sur3->FetchRow();
    }
	$pdf->Cell(37,4,number_format($rssur1['rateperRVU'],2).' /RVU','TR',$newLineNo,'C');    
	
	if ($sur4 = $ins_obj->getBenefitShedule($hcare_id, 10, 4, $effective_date)) {
	    $rssur4 = $sur4->FetchRow();
    }
	$pdf->Cell(39,4,number_format($rssur1['rateperRVU'],2).' /RVU','TR',$newLineNo,'C');
	
	$pdf->Ln($space*2);
	$pdf->Cell(40,4,'','LBR',$newLineNo,'L');
	$pdf->Cell(37,4,'Max. of '.number_format($rssur1['amountlimit'],2),'BR',$newLineNo,'C');
	$pdf->Cell(37,4,'Max. of '.number_format($rssur2['amountlimit'],2),'BR',$newLineNo,'C');
	$pdf->Cell(37,4,'Max. of '.number_format($rssur3['amountlimit'],2),'BR',$newLineNo,'C');
	$pdf->Cell(39,4,'Max. of '.number_format($rssur4['amountlimit'],2),'BR',$newLineNo,'C');
	
	$pdf->Ln($space*2);
	$pdf->Cell(40,4,'Anesthesiologist','LTR',$newLineNo,'L');
	
	if ($anes1 = $ins_obj->getBenefitShedule($hcare_id, 11, 1, $effective_date)) {
	    $rsanes1 = $anes1->FetchRow();
    }
	$pdf->Cell(37,4,number_format($rsanes1['rateperday'],0, '.', '').'% of Surgeon\'s Fee','TR',$newLineNo,'C');
	
	if ($anes2 = $ins_obj->getBenefitShedule($hcare_id, 11, 2, $effective_date)) {
	    $rsanes2 = $anes2->FetchRow();
    }
	$pdf->Cell(37,4,number_format($rsanes2['rateperday'],0, '.', '').'% of Surgeon\'s Fee','TR',$newLineNo,'C');
	
	if ($anes3 = $ins_obj->getBenefitShedule($hcare_id, 11, 3, $effective_date)) {
	    $rsanes3 = $anes3->FetchRow();
    }
	$pdf->Cell(37,4,number_format($rsanes3['rateperday'],0, '.', '').'% of Surgeon\'s Fee','TR',$newLineNo,'C');
	
	if ($anes4 = $ins_obj->getBenefitShedule($hcare_id, 11, 4, $effective_date)) {
	    $rsanes4 = $anes4->FetchRow();
    }
	$pdf->Cell(39,4,number_format($rsanes4['rateperday'],0, '.', '').'% of Surgeon\'s Fee','TR',$newLineNo,'C');
	
	$pdf->Ln($space*2);
	$pdf->Cell(40,4,'','LBR',$newLineNo,'L');
	$pdf->Cell(37,4,'Max. of '.number_format($rsanes1['amountlimit'],2),'BR',$newLineNo,'C');
	$pdf->Cell(37,4,'Max. of '.number_format($rsanes2['amountlimit'],2),'BR',$newLineNo,'C');
	$pdf->Cell(37,4,'Max. of '.number_format($rsanes3['amountlimit'],2),'BR',$newLineNo,'C');
	$pdf->Cell(39,4,'Max. of '.number_format($rsanes4['amountlimit'],2),'BR',$newLineNo,'C');
	#$pdf->Ln($space*3);
	#$pdf->Cell(60,4,'*','LTBR',$borderNo,'L');
	$pdf->Ln(4);
	
	$pdf->Output();	
?>