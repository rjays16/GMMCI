<?php
	#created by VAN 06-17-08						
	include("roots.php");
	require('./roots.php');
	
	#include_once($root_path."/classes/fpdf/fpdf.php");
	include_once($root_path."/classes/fpdf/pdf.class.php");
	require_once($root_path.'include/inc_environment_global.php');
	include_once($root_path.'include/inc_date_format_functions.php');
	
	require_once($root_path.'include/care_api_classes/class_radiology.php');
	$obj_radio = new SegRadio;

	require_once($root_path.'include/care_api_classes/class_department.php');
	$dept_obj=new Department;
	$dept_obj->preloadDept($dept_nr);

	require_once($root_path.'include/care_api_classes/class_personell.php');
	$pers_obj=new Personell;
	
	require_once($root_path.'include/care_api_classes/class_person.php');
	$person_obj=new Person;
	require_once($root_path.'include/care_api_classes/class_encounter.php');
	$enc_obj=new Encounter;
	
	require_once($root_path.'include/care_api_classes/class_ward.php');
	$ward_obj=new Ward;
	#array(width, length);
	$pdf = new PDF("P",'mm',array(215.9,130));	
	$pdf->AliasNbPages();   #--added
	$pdf->AddPage("P");
	
	$borderYes="1";
	$borderNo="0";
	$newLineYes="1";
	$newLineNo="0";
	$space=2;
	
   $pdf->Image($root_path.'gui/img/logos/dmc_logo.jpg',20,10,20,20);
		
   $pdf->SetFont("Times","B","10");
   $pdf->Cell(0,4,'Republic of the Philippines',$borderNo,$newLineYes,'C');
   $pdf->Cell(0,4,'Department of Health',$borderNo,$newLineYes,'C');
   $pdf->Cell(0,4,'Gonzales Maranan Medical Center Incorporated',$borderNo,$newLineYes,'C');
   $pdf->Cell(0,4,'OUTPATIENT and PREVENTIVE CARE CENTER',$borderNo,$newLineYes,'C');
   #$pdf->Ln(1);
   $pdf->SetFont("Times","B","8");
   $pdf->Cell(0,4,'Quezon Ave., Digos City',$borderNo,$newLineYes,'C');
   #$pdf->Ln(1);
   $pdf->SetFont("Times","B","10");
   $pdf->Cell(0,4,'DEPARTMENT OF RADIOLOGICAL & IMAGING SCIENCES',$borderNo,$newLineYes,'C');
   #$pdf->Ln(1);
   $pdf->Cell(0,4,'PATIENT\'S SCHEDULED REQUEST',$borderNo,$newLineYes,'C');
   $pdf->Ln($space*1);
	
    #added by daryl
   #if batch_nr is walk-in
   $batch_walk_ = $obj_radio->selectwalkin_batch_nr($batch_nr);
   $batch_walk = $batch_walk_->Fetchrow();
   if (($batch_walk['ifwalk']) == 1){
        $walk = 1;
   }

   $scheduleInfo = $obj_radio->getScheduledRadioRequestInfo($batch_nr,TRUE,'','',$walk);
     // echo $obj_radio->sql;
   if ((!$scheduleInfo) || empty($scheduleInfo)){
		$pdf->Cell(190,4,'Sorry but the page cannot be displayed! Please try again!',"",1,'R');
  }else{
  	extract($scheduleInfo);
    if ($walk == 1) {
    $pat_name = mb_strtoupper($name_first)." ".mb_strtoupper($name_middle)." ".mb_strtoupper($name_last);
    }else{
    $pat_name = mb_strtoupper($patient_name);
    }

	
	$pdf->SetFont("Arial","","8");
   	$pdf->Cell(47,4,'REFERENCE NUMBER',$borderNo,$newLineNo,'L');
	$pdf->Cell(5,4,' : ',$borderNo,$newLineNo,'L');
	$pdf->SetFont("Arial","B","9");
	$pdf->Cell(60,4,$batch_nr,$borderNo,$newLineYes,'L');
	
	$pdf->SetFont("Arial","","8");
   	$pdf->Cell(47,4,'RID',$borderNo,$newLineNo,'L');
	$pdf->Cell(5,4,' : ',$borderNo,$newLineNo,'L');
	$pdf->SetFont("Arial","B","9");
	$pdf->Cell(60,4,$rid,$borderNo,$newLineYes,'L');
	
	$pdf->SetFont("Arial","","8");
   	$pdf->Cell(47,4,'PATIENT NAME',$borderNo,$newLineNo,'L');
	$pdf->Cell(5,4,' : ',$borderNo,$newLineNo,'L');
	$pdf->SetFont("Arial","B","9");
    $pdf->Cell(60,4,$pat_name,$borderNo,$newLineYes,'L');
	
	$pdf->SetFont("Arial","","8");
   	$pdf->Cell(47,4,'RADIOLOGY SECTION',$borderNo,$newLineNo,'L');
	$pdf->Cell(5,4,' : ',$borderNo,$newLineNo,'L');
	$pdf->SetFont("Arial","B","9");
	$pdf->Cell(60,4,$dept_name." (".$dept_name_short.")",$borderNo,$newLineYes,'L');
	
	$pdf->SetFont("Arial","","8");
   	$pdf->Cell(47,4,'SERVICE REQUEST',$borderNo,$newLineNo,'L');
	$pdf->Cell(5,4,' : ',$borderNo,$newLineNo,'L');
	$pdf->SetFont("Arial","B","9");
	$request = $service_name." (".$service_code.")";
	$pdf->Cell(60,4,$request,$borderNo,$newLineYes,'L');
	
	$pdf->SetFont("Arial","","8");
   	$pdf->Cell(47,4,'DATE SCHEDULED',$borderNo,$newLineNo,'L');
	$pdf->Cell(5,4,' : ',$borderNo,$newLineNo,'L');
	$pdf->SetFont("Arial","B","9");
	$pdf->Cell(60,4,date("F d, Y", strtotime($scheduled_dt)),$borderNo,$newLineYes,'L');
	
	$pdf->SetFont("Arial","","8");
   	$pdf->Cell(47,4,'TIME SCHEDULED',$borderNo,$newLineNo,'L');
	$pdf->Cell(5,4,' : ',$borderNo,$newLineNo,'L');
	$pdf->SetFont("Arial","B","9");
	$pdf->Cell(60,4,date("h:i A", strtotime($scheduled_time)),$borderNo,$newLineYes,'L');
	
	$pdf->SetFont("Arial","","8");
   	$pdf->Cell(47,4,'PLEASE BRING THE FOLLOWING',$borderNo,$newLineNo,'L');
	$pdf->Cell(5,4,' : ',$borderNo,$newLineYes,'L');
	$pdf->SetFont("Arial","B","9");
	#print_r($instructions);
	$instruction = unserialize($instructions);
	#print_r($instruction);
	$j=1;
	$pdf->Ln(2);
	/*
	for ($i=0; $i<sizeof($instruction); $i++){
		$pdf->Cell(50,4,'',$borderNo,$newLineNo,'L');
		#$pdf->Cell(60,4,$instruction[$i],$borderNo,$newLineYes,'L');
		$instruct = $obj_radio->getRadioInstructionsInfo($sub_dept_nr,$instruction[$i]);
		$pdf->Cell(60,4,$j.".) ".$instruct['instruction'],$borderNo,$newLineYes,'L');
		$j++;
	}
	*/
	#edited by VAN 06-28-08
	for ($i=0; $i<sizeof($instruction); $i++){
		$pdf->Cell(50,4,'',$borderNo,$newLineNo,'L');
		#$pdf->Cell(60,4,$instruction[$i],$borderNo,$newLineYes,'L');
		#echo "<br>".$instruction[$i];
		$index = strpos($instruction[$i], ' ');
		$ins_nr = trim(substr($instruction[$i],0,$index));
		if ($ins_nr == '0'){
			$pdf->Cell(60,4,$j.".) ".trim(substr($instruction[$i],$index)),$borderNo,$newLineYes,'L');
		}else{
			$instruct = $obj_radio->getRadioInstructionsInfo($sub_dept_nr,$instruction[$i]);
			#echo "<br><br>".$obj_radio->sql;
			$pdf->Cell(60,4,$j.".) ".$instruct['instruction'],$borderNo,$newLineYes,'L');
		}	
		$j++;
	}
	#--------------------
			
	$pdf->Ln(3);
	$pdf->SetFont("Arial","","8");
   	$pdf->Cell(47,4,'INSTRUCTIONS',$borderNo,$newLineNo,'L');
	$pdf->Cell(5,4,' : ',$borderNo,$newLineNo,'L');
	$pdf->SetFont("Arial","B","9");
	$pdf->Cell(60,4,$remarks,$borderNo,$newLineYes,'L');
	
	$pdf->Ln($space*10);
    $pdf->SetFont('Arial','',8);
	$pdf->Cell(130,4,'',"",0,'C');	
	$pdf->Cell(5,4,'',"",0,'C');
    $pdf->Cell(50,4,mb_strtoupper($modify_id),"B",1,'C');
    $pdf->Cell(190,4,'Scheduled By (Signature Over Printed Name)',"",1,'R');

	$pdf->Ln(8);
	$pdf->Cell(131, 3 , '', "", 0,'');
	#$pdf->Cell(0, 3 ,"SPMC-F-RAD-13   ".date("F d, o")." REVO", "", 0,'');commented by art 02/12/2014
	#$pdf->Cell(0, 3 ,"SPMC-F-RAD-13   October 1, 2013  Rev.0", "", 0,'');#added by art 02/12/2014

  }
   	
   $pdf->Output();	
?>