<?php
	include("roots.php");
	require('./roots.php');
	
	#include_once($root_path."/classes/fpdf/fpdf.php");
	include_once($root_path."/classes/fpdf/pdf.class.php");
	require_once($root_path.'include/inc_environment_global.php');
	include_once($root_path.'include/inc_date_format_functions.php');
	
	require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
	$srvObj=new SegLab;
	require_once($root_path.'include/care_api_classes/class_department.php');
	$dept_obj=new Department;
	require_once($root_path.'include/care_api_classes/class_person.php');
	$person_obj=new Person;
	require_once($root_path.'include/care_api_classes/class_encounter.php');
	$enc_obj=new Encounter;
	require_once($root_path.'include/care_api_classes/class_personell.php');
	$pers_obj=new Personell;
	require_once($root_path.'include/care_api_classes/class_ward.php');
	$ward_obj = new Ward;
	
	require($root_path.'classes/adodb/adodb.inc.php');
	include($root_path.'include/inc_init_hclab_main.php');
	//include($root_path.'include/inc_seg_mylib.php');
	
	require_once($root_path.'include/care_api_classes/class_hclab_oracle.php');
	$hclabObj = new HCLAB;
	
	global $db;
	
	$pdf = new PDF("L",'mm','Legal');
	$pdf->AliasNbPages();   #--added
	$pdf->AddPage("L");
	
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
	$pdf->Ln(2);
	$pdf->SetFont("Times","B","8");
   $pdf->Cell(0,4,'Quezon Ave., Digos City, Davao del Sur',$borderNo,$newLineYes,'C');
   $pdf->Ln(2);
	$pdf->SetFont("Times","B","10");
   $pdf->Cell(0,4,'DEPARTMENT OF PATHOLOGY AND CLINICAL LABORATORIES',$borderNo,$newLineYes,'C');
	$pdf->Ln(2);
	$pdf->SetFont("Times","I","10");
	$pdf->Cell(0,4,'PATIENT OBSERVATION REPORT',$borderNo,$newLineYes,'C');
   #$pdf->Ln($space*2);
	
	$refno = $_GET['refno'];
	$service_code = $_GET['service_code'];
	$status = $_GET['status'];
	
	$infoResult = $srvObj->getOrderInfo($refno);
	$saved_discounts = $srvObj->getOrderDiscounts($refno);
	if ($infoResult)	$info = $infoResult->FetchRow();
	
	#get service info
	$labrequestObj = $srvObj->getServiceRequestInfo($refno, $service_code);
	$person = $person_obj->getAllInfoArray($info["pid"]);
	$patient = $enc_obj->getEncounterInfo($info['encounter_nr']);
	# Connect to HCLAB through ORACLE
	#$objconn = $hclabObj->ConnecttoDest($dsn);

	#$labresult = $srvObj->hasResult($refno);
	#$labresult = $srvObj->hasResult($refno, $service_code);
	#added by VAN 08-10-09
	$rsRef = $srvObj->getLabOrderNo($refno);
	
	$rowRef = $rsRef->FetchRow();
	
	$rsCode = $srvObj->getTestCode($service_code);
	
	if (($patient['encounter_type'] == 1) || ($patient['encounter_type'] == 3) || ($patient['encounter_type'] == 4))
		#$code = $rsCode['service_code'];
        $code = $rsCode['ipdservice_code'];
	else	
		$code = $rsCode['oservice_code'];
	
	#$labresult = $srvObj->hasResult($Ref, $service_code);
	$labresult = $srvObj->hasResult2($rowRef['lis_order_no'], $code);
	
	if ($srvObj->count){
	#if ($labresult) {
		#$patient_info = $srvObj->getLabResultHeader($refno, $service_code);
		$patient_info = $srvObj->getLabResultHeader($labresult['refno'], $code);
		
		if ($patient_info){
			
			$pdf->Ln($space*2);
			$pdf->SetFont('Times','B',10);	
			$pdf->Cell(168,4,'Patient Demographic Information',"",0,'L');
			$pdf->Cell(169,4,'Patient Request Details',"",0,'L');
	
			$pdf->Ln($space*2);
			$pdf->SetFont('Times','',8);	
			$pdf->Cell(18,4,'Patient ID : ',"",0,'L');
			$pdf->Cell(150,4,$patient_info["pid"],"",0,'L');
			$pdf->Cell(26,4,'Order No. : ',"",0,'L');
			$pdf->Cell(142,4,$refno,"",0,'L');
		
			$request_name = stripslashes($person['name_first'])." ".stripslashes($person['name_2'])." ".stripslashes($person['name_middle'])." ".stripslashes($person['name_last']);
			$request_name = ucwords(strtolower($request_name));
			$request_name = htmlspecialchars($request_name);
		
			$pdf->Ln($space*2);
			$pdf->SetFont('Times','',8);	
			$pdf->Cell(18,4,'Name : ',"",0,'L');
			$pdf->Cell(150,4,$request_name,"",0,'L');
			$pdf->Cell(26,4,'Order Date : ',"",0,'L');
			$pdf->Cell(142,4,$patient_info["order_dt"],"",0,'L');
			
			/*
			if ($patient_info["patient_type"]=="IN"){
				$patient_type = "Inpatient";
			}elseif ($patient_info["patient_type"]=="OP"){
				$patient_type = "Outpatient";
			}elseif ($patient_info["patient_type"]=="ER"){
				$patient_type = "ER Patient";
			}	
			*/
			
			#$patient = $enc_obj->getEncounterInfo($info['encounter_nr']);
			if ($patient['encounter_type'] == 1){
				$patient_type = "ER Patient";
				$loc_code = "ER";
				$loc_name = "Emergency Room";
			}elseif (($patient['encounter_type'] == 3)||($patient['encounter_type'] == 4)){
				$patient_type = "Inpatient";	
				$loc_code = $patient['current_ward_nr'];
				if ($loc_code)
					$ward = $ward_obj->getWardInfo($loc_code);
					
				$loc_name = stripslashes($ward['name']);
			}else{
				$patient_type = "Outpatient";	
				$loc_code = $patient['current_dept_nr'];
				if ($loc_code)
					$dept = $dept_obj->getDeptAllInfo($loc_code);
				$loc_name = stripslashes($dept['name_formal']);
			}
			
			$pdf->Ln($space*2);
			$pdf->SetFont('Times','',8);	
			$pdf->Cell(18,4,'Patient Type : ',"",0,'L');
			$pdf->Cell(150,4,$patient_type,"",0,'L');
			$pdf->Cell(26,4,'Location : ',"",0,'L');
			
			#$dept = $dept_obj->getDeptAllInfo($patient_info["loc_code"]);
			$pdf->Cell(142,4,$loc_name,"",0,'L');
		
			if ($person['date_birth']) {
				$time = strtotime($person['date_birth']);
				$birthDate = date("m/d/Y",$time);
			}else{
				$birthDate = " ";
			}
			
			if ((is_numeric($patient_info["dr_code"])) && !($patient_info["dr_code"])){
			   $doctor = $pers_obj->getPersonellInfo($patient_info["dr_code"]);
				$doctor_name = stripslashes($doctor["name_first"])." ".stripslashes($doctor["name_2"])." ".stripslashes($doctor["name_last"]);
				$doctor_name = "Dr. ".ucwords(strtolower($doctor_name));
				$doctor_name = htmlspecialchars($doctor_name);
			}else{
				$doctor_name = "no doctor specified";
			}
		
			$pdf->Ln($space*2);
			$pdf->SetFont('Times','',8);	
			$pdf->Cell(18,4,'Birth Date : ',"",0,'L');
			$pdf->Cell(150,4,$birthDate,"",0,'L');
			$pdf->Cell(26,4,'Doctor : ',"",0,'L');
			$pdf->Cell(142,4,$doctor_name,"",0,'L');
		
			if ($person['sex']=="m"){
				$gender = "Male";
			}elseif($person['sex']=="f"){
				$gender = "Female";
			}else{	
				$gender = "Unknown";
			}
			
			if ($patient_info["priority"]=="R")
				$priority = "Routine/Normal";
			elseif ($patient_info["priority"]=="U")
				$priority = "Urgent/STAT";		
		
			$pdf->Ln($space*2);
			$pdf->SetFont('Times','',8);	
			$pdf->Cell(18,4,'Sex : ',"",0,'L');
			$pdf->Cell(150,4,$gender,"",0,'L');
			$pdf->Cell(26,4,'Priority : ',"",0,'L');
			$pdf->Cell(142,4,$priority,"",0,'L');
		
			$request_address = stripslashes(trim($person['street_name']))." ".stripslashes(trim($person['brgy_name']))." ".stripslashes(trim($person['mun_name'])).", ".stripslashes(trim($person['prov_name'])); #.", ".stripslashes(trim($person['region_name']))." ".stripslashes(trim($person['zipcode']));
			$request_address = htmlspecialchars($request_address);
		
			$pdf->Ln($space*2);
			$pdf->SetFont('Times','',8);	
			$pdf->Cell(18,4,'Address : ',"",0,'L');
			$pdf->Cell(150,4,$request_address,"",0,'L');
			$pdf->Cell(26,4,'Clinical Info : ',"",0,'L');
			$pdf->Cell(142,4,$patient_info["clinical_info"],"",0,'L');
	
			$pdf->Ln($space*2);
			$pdf->SetFont('Times','',8);	
			$pdf->Cell(18,4,'Classification : ',"",0,'L');
			$pdf->Cell(150,4,$info["discountid"],"",0,'L');
			$pdf->Cell(26,4,'Case / Visitation No. : ',"",0,'L');
			$pdf->Cell(142,4,$patient_info["patient_caseNo"],"",0,'L');

			$pdf->Ln($space*2);
			$pdf->SetFont('Times','',8);	
			$pdf->Cell(18,4,'',"",0,'L');
			$pdf->Cell(150,4,'',"",0,'L');
			$pdf->Cell(26,4,'Lab No. : ',"",0,'L');
			$pdf->Cell(142,4,$patient_info["lab_no"],"",0,'L');
			
			#$labservObj = $srvObj->getServiceInfo($patient_info["service_code"]);
			$labservObj = $srvObj->getServiceInfo($code);
			
			$pdf->Ln($space*2);
			$pdf->SetFont('Times','',8);	
			$pdf->Cell(18,4,'',"",0,'L');
			$pdf->Cell(150,4,'',"",0,'L');
			$pdf->Cell(26,4,'Test : ',"",0,'L');
			$pdf->Cell(142,4,$labservObj['name'],"",0,'L');

			$pdf->Ln($space*2);
			$pdf->SetFont('Times','',8);	
			$pdf->Cell(18,4,'',"",0,'L');
			$pdf->Cell(150,4,'',"",0,'L');
			$pdf->Cell(26,4,'Test Type : ',"",0,'L');
			$pdf->Cell(142,4,$patient_info["test_type"],"",0,'L');
	
			$pdf->Ln($space*2);
			$pdf->SetFont('Times','',8);	
			$pdf->Cell(18,4,'',"",0,'L');
			$pdf->Cell(150,4,'',"",0,'L');
			$pdf->Cell(26,4,'Test Group : ',"",0,'L');
			$pdf->Cell(142,4,$labservObj['group_name'],"",0,'L');

			$pdf->Ln($space*2);
			$pdf->SetFont('Times','',8);	
			$pdf->Cell(18,4,'',"",0,'L');
			$pdf->Cell(150,4,'',"",0,'L');
			$pdf->Cell(26,4,'Ctl. Seq. No. : ',"",0,'L');
			$pdf->Cell(142,4,$patient_info["ctl_seqNo"],"",0,'L');
	
			$pdf->Ln($space*4);
			$pdf->SetFont('Times','B',8);	
			$pdf->Cell(20,4,'TEST CODE ',"TBRL",0,'C');
			$pdf->Cell(35,4,'TEST NAME',"TBRL",0,'C');
			$pdf->Cell(15,4,'RESULT',"TBRL",0,'C');
			$pdf->Cell(12,4,'UNIT',"TBRL",0,'C');
			$pdf->Cell(25,4,'NORMAL RANGE',"TBRL",0,'C');
			$pdf->Cell(10,4,'FLAG',"TBRL",0,'C');
			$pdf->Cell(15,4,'STATUS',"TBRL",0,'C');
			$pdf->Cell(35,4,'REPORTED ON',"TBRL",0,'C');
			$pdf->Cell(40,4,'MLT Responsible',"TBRL",0,'C');
			$pdf->Cell(30,4,'Performed Lab',"TBRL",0,'C');
			$pdf->Cell(55,4,'Test Comments',"TBRL",0,'C');
			$pdf->Cell(30,4,'Parent Item',"TBRL",0,'C');
			$pdf->Cell(15,4,'Line No.',"TBRL",1,'C');
	
			#$result = $srvObj->getLabResult($refno, $service_code);
			$result = $srvObj->getLabResult($labresult['refno'], $code);
			$rowcount = $srvObj->count;
		
			if ($rowcount){
				$i=1;
				$pdf->SetFont('Times','',8);	
				while ($row=$result->FetchRow()) {
					$pdf->Cell(20,4,$row['test_code'],"TBRL",0,'C');
					$pdf->Cell(35,4,$row['test_name'],"TBRL",0,'C');
					$pdf->Cell(15,4,$row['result_value'],"TBRL",0,'C');
					$pdf->Cell(12,4,$row['unit'],"TBRL",0,'C');
					$pdf->Cell(25,4,$row['ranges'],"TBRL",0,'C');
					$pdf->Cell(10,4,$row['result_flag'],"TBRL",0,'C');
					$pdf->Cell(15,4,$row['result_status'],"TBRL",0,'C');
					$pdf->Cell(35,4,$row['reported_dt'],"TBRL",0,'C');
					$pdf->Cell(40,4,$row['mlt_name'],"TBRL",0,'C');
					$pdf->Cell(30,4,$row['performed_lab_name'],"TBRL",0,'C');
					$pdf->Cell(55,4,$row['test_comment'],"TBRL",0,'C');
					$pdf->Cell(30,4,$row['parent_item'],"TBRL",0,'C');
					$pdf->Cell(15,4,$row['line_no'],"TBRL",1,'C');	
					$i++;
				}
			
			}else{
				$pdf->SetFont('Times','',10);	
				$pdf->Cell(337,4,'No laboratory results available at this time...',"TBRL",0,'C');
			}

		$pdf->Ln($space*6);
		$pdf->Cell(337,4,'________________________________________',"",1,'R');
		$pdf->Cell(337,4,'Person In-Charge (Signature Over Printed Name)',"",1,'R');
		#}
		}else{
			echo '<em class="warn">Sorry but the page cannot be displayed! There is no result at all. Pending Status..</em>';
			exit();
		}
		
	}else{
		echo '<em class="warn">Sorry but the page cannot be displayed! There is no result at all. Pending Status..</em>';
		exit();
	}
	
	$pdf->Output();	
?>