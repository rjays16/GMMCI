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
	
	$pdf = new PDF();
	$pdf->AliasNbPages();   #--added
	$pdf->AddPage("P");
	
	$borderYes="1";
	$borderNo="0";
	$newLineYes="1";
	$newLineNo="0";
	$space=2;
	
	#$pdf->Image($root_path.'gui/img/logos/dmc_logo.jpg',20,10,20,20);
		
	$pdf->SetFont("Arial","B","10");
   $pdf->Cell(0,4,'Republic of the Philippines',$borderNo,$newLineYes,'C');
	$pdf->Cell(0,4,'Department of Health',$borderNo,$newLineYes,'C');
	$pdf->Cell(0,4,'Gonzales Maranan Medical Center Incorporated',$borderNo,$newLineYes,'C');
   $pdf->Cell(0,4,'DEPARTMENT OF PATHOLOGY AND CLINICAL LABORATORIES',$borderNo,$newLineYes,'C');
   
	
	$refno = $_GET['refno'];
	$service_code = $_GET['service_code'];
	$status = $_GET['status'];
	
	$infoResult = $srvObj->getOrderInfo($refno);
	$saved_discounts = $srvObj->getOrderDiscounts($refno);
	if ($infoResult)	$info = $infoResult->FetchRow();
	$patient = $enc_obj->getEncounterInfo($info['encounter_nr']);
	#added by VAN 08-10-09
	$rsRef = $srvObj->getLabOrderNo($refno);
	
	$rowRef = $rsRef->FetchRow();
	
	$rsCode = $srvObj->getTestCode($service_code);
	
	if (($patient['encounter_type'] == 1) || ($patient['encounter_type'] == 3) || ($patient['encounter_type'] == 4))
		#$code = $rsCode['service_code'];
        $code = $rsCode['ipdservice_code'];
	else	
		$code = $rsCode['oservice_code'];
	#------------------------	
	
	#get service info
	$labrequestObj = $srvObj->getServiceRequestInfo($refno, $service_code);
	#echo $srvObj->sql;
	$person = $person_obj->getAllInfoArray($info["pid"]);
	
	#$labresult = $srvObj->hasResult($refno);
	#$labresult = $srvObj->hasResult($refno, $service_code);
	$labresult = $srvObj->hasResult2($rowRef['lis_order_no'], $code);
	#if ($labresult) {
	if ($srvObj->count){
		#$patient_info = $srvObj->getLabResultHeader($refno, $service_code);
		$patient_info = $srvObj->getLabResultHeader($labresult['refno'], $code);
		#echo $srvObj->sql;
		if ($patient_info){
			$request_name = stripslashes($person['name_first'])." ".stripslashes($person['name_2'])." ".stripslashes($person['name_middle'])." ".stripslashes($person['name_last']);
			$request_name = ucwords(strtolower($request_name));
			$request_name = htmlspecialchars($request_name);
						
			$pdf->Ln($space*2);
			$pdf->SetFont('Arial','',8);	
			$pdf->Cell(22,4,'Name : ',"",0,'L');
			$pdf->Cell(85,4,strtoupper($request_name),"",0,'L');
			$pdf->Cell(15,4,'Lab No. : ',"",0,'L');
			$pdf->Cell(80,4,$patient_info["lab_no"],"",0,'L');
			
			if ((is_numeric($patient_info["dr_code"])) && !($patient_info["dr_code"])){
			   $doctor = $pers_obj->getPersonellInfo($patient_info["dr_code"]);
				$doctor_name = stripslashes($doctor["name_first"])." ".stripslashes($doctor["name_2"])." ".stripslashes($doctor["name_last"]);
				$doctor_name = "Dr. ".ucwords(strtolower($doctor_name));
				$doctor_name = htmlspecialchars($doctor_name);
			}else{
				$doctor_name = "no doctor specified";
			}
			
			$pdf->Ln($space*2);
			$pdf->SetFont('Arial','',8);	
			$pdf->Cell(22,4,'HRN : ',"",0,'L');
			$pdf->Cell(85,4,$patient_info["pid"],"",0,'L');
			$pdf->Cell(15,4,'Clinician : ',"",0,'L');
			$pdf->Cell(80,4,'Dr. '.$doctor_name,"",0,'L');
			
			if ($person['sex']=="m"){
				$gender = "Male";
			}elseif($person['sex']=="f"){
				$gender = "Female";
			}else{	
				$gender = "Unknown";
			}
			
			if ($person['date_birth']) {
				$time = strtotime($person['date_birth']);
				$birthDate = date("m/d/Y",$time);
				$age = $person_obj->getAge($birthDate,2,date("m/d/Y"));
				$age = round($age,1);
			}else{
				$birthDate = " ";
				$age = "Undefined";
			}
			
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
			$pdf->SetFont('Arial','',8);	
			$pdf->Cell(22,4,'Age : ',"",0,'L');
			$pdf->Cell(30,4,$age.' yrs. old',"",0,'L');
			$pdf->Cell(10,4,'',"",0,'L');
			$pdf->Cell(15,4,'Sex : ',"",0,'L');
			$pdf->Cell(30,4,$gender,"",0,'L');
			$pdf->Cell(15,4,'Location : ',"",0,'L');
			$pdf->Cell(80,4,$loc_name,"",0,'L');
			
			$pdf->Ln($space*2);
			$pdf->SetFont('Arial','',8);	
			$pdf->Cell(22,4,'Date Received : ',"",0,'L');
			$pdf->Cell(30,4,date("m-d-Y",strtotime($patient_info["order_dt"])),"",0,'L');
			$pdf->Cell(10,4,'',"",0,'L');
			$pdf->Cell(15,4,'Reported : ',"",0,'L');
			$pdf->Cell(30,4,date("m-d-Y",strtotime($patient_info["trx_dt"])),"",0,'L');
			$pdf->Cell(15,4,'Printed : ',"",0,'L');
			$pdf->Cell(40,4,date("m-d-Y h:i:s"),"",0,'L');
			$pdf->Cell(10,4,'Page : ',"",0,'L');
			$pdf->Cell(5,4,$pdf->PageNo().' / {nb}',"",0,'L');
			
			#$labservObj = $srvObj->getServiceInfo($patient_info["service_code"]);
			$result = $srvObj->getLabResult($labresult['refno'], $code);
			#echo $srvObj->sql;
			$pdf->Ln($space*4);
			$pdf->SetFont('Arial','B',8);	
			$pdf->Cell(60,4,'        TEST ',"TB",0,'L');
			$pdf->Cell(40,4,'RESULT',"TB",0,'C');
			$pdf->Cell(40,4,'UNIT',"TB",0,'C');
			$pdf->Cell(50,4,'REFERENCE RANGES',"TB",1,'C');
			$pdf->Ln($space*2);
			$pdf->Cell(40,4,'     '.$labservObj['group_name'],"",0,'L');
			
			$pdf->Ln($space*4);
			$pdf->Cell(40,4,'          '.$labservObj['name'],"",0,'L');
			
			$pdf->Ln($space*2);
			
			#$result = $srvObj->getLabResult($refno, $service_code);
			$result = $srvObj->getLabResult($labresult['refno'], $code);
			$rowcount = $srvObj->count;
			
			if ($rowcount){
				$i=1;
				$pdf->SetFont('Times','',8);	
				while ($row=$result->FetchRow()) {
					if ($row['parent_item']==$service_code)
						$pdf->Cell(60,4,'               '.$row['test_name'],"",0,'L');
					else
					 	$pdf->Cell(60,4,'                    '.$row['test_name'],"",0,'L');
						
					$pdf->Cell(40,4,$row['result_value'],"",0,'C');
					$pdf->Cell(40,4,$row['unit'],"",0,'C');
					$pdf->Cell(50,4,$row['ranges'],"",1,'C');
					$i++;
					
					$examiner = $row['mlt_name'];
				}
				
			}else{
				$pdf->SetFont('Times','',10);	
				$pdf->Cell(337,4,'No laboratory results available at this time...',"TBRL",0,'C');
			}

			$pdf->Ln($space*6);
			$pdf->Cell(20,4,'',"",0,'L');
			$pdf->SetFont('Arial','U',8);	
			$pdf->Cell(100,4,'     '.$examiner.', RMT     ',"",0,'L');
			$pdf->Cell(100,4,'                                               ',"",0,'L');
			#$pdf->SetFont('Arial','',8);	
			
			$pdf->Ln($space*2);
			$pdf->Cell(20,4,'',"",0,'L');
			$pdf->SetFont('Arial','',8);	
			$pdf->Cell(30,4,'Examiner',"",0,'C');
			$pdf->Cell(75,4,'',"",0,'C');
			$pdf->Cell(30,4,'Pathologist',"",0,'C');
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