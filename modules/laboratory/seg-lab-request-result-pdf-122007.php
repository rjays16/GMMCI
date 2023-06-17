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
	
	require($root_path.'classes/adodb/adodb.inc.php');
	include($root_path.'include/inc_init_hclab_main.php');
	include($root_path.'include/inc_seg_mylib.php');
	
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
	
	//echo "status = ".$status;
	/*
	if ($status==0){
		echo " \n <script type=\"text/javascript\">alert(\"The request is not yet paid or not in the list of the granted request. Pls. settle this request accounts. Thank you.!\")</script>";
		exit();
	}
	*/
	
	$infoResult = $srvObj->getOrderInfo($refno);
	$saved_discounts = $srvObj->getOrderDiscounts($refno);
	if ($infoResult)	$info = $infoResult->FetchRow();
	
	#get service info
	$labrequestObj = $srvObj->getServiceRequestInfo($refno, $service_code);
	$person = $person_obj->getAllInfoArray($info["pid"]);
	
	# Connect to HCLAB through ORACLE
	#echo "dsn = ".$dsn;
	$objconn = $hclabObj->ConnecttoDest($dsn);
	/*
	if ($info["pid"]==" "){
		$request_name = $info['ordername'];
		$request_address = $info['orderaddress'];
	}else{
		$person = $person_obj->getAllInfoArray($info["pid"]);
		#echo "sql = ".$person_obj->sql;
		$request_name = $person['name_first']." ".$person["name_2"]." ".$person["name_last"];
		$request_name = ucwords(strtolower($request_name));
		$request_name = htmlspecialchars($request_name);
		
		$request_address = $person['street_name']." ".$person['brgy_name']." ".$person['mun_name'].", ".$person['prov_name'].", ".$person['region_name']." ".$person['zipcode'];
		#$request_address = ucwords(strtolower(htmlspecialchars($request_address)));
		$request_address = htmlspecialchars($request_address);
	}
	
	if ($info['serv_dt']) {
			$time = strtotime($info['serv_dt']);
			$requestDate = date("m/d/Y",$time);
	}
	
	#Location
	if (is_numeric($labrequestObj['request_dept'])){	
		$dept = $dept_obj->getDeptAllInfo($labrequestObj['request_dept']);
		$dept_name = stripslashes($dept['name_formal']);
	}else{
		$dept_name = stripslashes($row['request_dept']);
	}
	
	#Doctor
	#echo "doctor = ".$labrequestObj['request_doctor'];
	if (is_numeric($labrequestObj['request_doctor'])){
		$doctor = $pers_obj->getPersonellInfo($labrequestObj['request_doctor']);
		$doctor_name = stripslashes($doctor["name_first"])." ".stripslashes($doctor["name_2"])." ".stripslashes($doctor["name_last"]);
		$doctor_name = "Dr. ".ucwords(strtolower($doctor_name));
		$doctor_name = htmlspecialchars($doctor_name);
	}else{
		$doctor_name = $row['request_doctor'];
	}	
	
	if ($info["is_urgent"])
		$priority = "Routine/Normal";
	else
		$priority = "Urgent/STAT";	
		
	$encounter = $enc_obj->getPatientEncounter($info['encounter_nr']);
	#echo "patient_type = ".$patient_type['encounter_type'];
	if ($encounter['encounter_type']==1)
		$patient_type = "ER Patient";
	elseif ($encounter['encounter_type']==2)
		$patient_type = "Outpatient";
	else
		$patient_type = "Inpatient";
		
	#Birth Date
	if ($person['date_birth']) {
			$time = strtotime($person['date_birth']);
			$birthDate = date("m/d/Y",$time);
	}
	
	#Sex
	if ($person['sex']=='m'){
		$gender = "Male";
	}elseif ($person['sex']=='f'){
		$gender = "Female";
	}
	
	#social service classification
	#$info["discountid"]
	
	$pdf->Ln($space*2);
	$pdf->SetFont('Times','B',10);	
	$pdf->Cell(168,4,'Patient Demographic Information',"",0,'L');
	$pdf->Cell(169,4,'Patient Request Details',"",0,'L');
	
	$pdf->Ln($space*2);
	$pdf->SetFont('Times','',8);	
	$pdf->Cell(18,4,'Patient ID : ',"",0,'L');
	$pdf->Cell(150,4,$info["pid"],"",0,'L');
	$pdf->Cell(26,4,'Order No. : ',"",0,'L');
	$pdf->Cell(142,4,$refno,"",0,'L');

	$pdf->Ln($space*2);
	$pdf->SetFont('Times','',8);	
	$pdf->Cell(18,4,'Name : ',"",0,'L');
	$pdf->Cell(150,4,$request_name,"",0,'L');
	$pdf->Cell(26,4,'Order Date : ',"",0,'L');
	$pdf->Cell(142,4,$requestDate,"",0,'L');

	$pdf->Ln($space*2);
	$pdf->SetFont('Times','',8);	
	$pdf->Cell(18,4,'Patient Type : ',"",0,'L');
	$pdf->Cell(150,4,$info["pid"],"",0,'L');
	$pdf->Cell(26,4,'Location : ',"",0,'L');
	$pdf->Cell(142,4,$dept_name,"",0,'L');

	$pdf->Ln($space*2);
	$pdf->SetFont('Times','',8);	
	$pdf->Cell(18,4,'Birth Date : ',"",0,'L');
	$pdf->Cell(150,4,$birthDate,"",0,'L');
	$pdf->Cell(26,4,'Doctor : ',"",0,'L');
	$pdf->Cell(142,4,$doctor_name,"",0,'L');

	$pdf->Ln($space*2);
	$pdf->SetFont('Times','',8);	
	$pdf->Cell(18,4,'Sex : ',"",0,'L');
	$pdf->Cell(150,4,$gender,"",0,'L');
	$pdf->Cell(26,4,'Priority : ',"",0,'L');
	$pdf->Cell(142,4,$priority,"",0,'L');

	$pdf->Ln($space*2);
	$pdf->SetFont('Times','',8);	
	$pdf->Cell(18,4,'Address : ',"",0,'L');
	$pdf->Cell(150,4,$request_address,"",0,'L');
	$pdf->Cell(26,4,'Clinical Info : ',"",0,'L');
	$pdf->Cell(142,4,$labrequestObj["clinical_info"],"",0,'L');
	
	$pdf->Ln($space*2);
	$pdf->SetFont('Times','',8);	
	$pdf->Cell(18,4,'Classification : ',"",0,'L');
	$pdf->Cell(150,4,$info["discountid"],"",0,'L');
	$pdf->Cell(26,4,'Case / Visitation No. : ',"",0,'L');
	$pdf->Cell(142,4,'',"",0,'L');

	$pdf->Ln($space*2);
	$pdf->SetFont('Times','',8);	
	$pdf->Cell(18,4,'',"",0,'L');
	$pdf->Cell(150,4,'',"",0,'L');
	$pdf->Cell(26,4,'Lab No. : ',"",0,'L');
	$pdf->Cell(142,4,'',"",0,'L');

	$pdf->Ln($space*2);
	$pdf->SetFont('Times','',8);	
	$pdf->Cell(18,4,'',"",0,'L');
	$pdf->Cell(150,4,'',"",0,'L');
	$pdf->Cell(26,4,'Test : ',"",0,'L');
	$pdf->Cell(142,4,$labrequestObj["name"],"",0,'L');

	$pdf->Ln($space*2);
	$pdf->SetFont('Times','',8);	
	$pdf->Cell(18,4,'',"",0,'L');
	$pdf->Cell(150,4,'',"",0,'L');
	$pdf->Cell(26,4,'Test Type : ',"",0,'L');
	$pdf->Cell(142,4,$patient_type,"",0,'L');
	
	$pdf->Ln($space*2);
	$pdf->SetFont('Times','',8);	
	$pdf->Cell(18,4,'',"",0,'L');
	$pdf->Cell(150,4,'',"",0,'L');
	$pdf->Cell(26,4,'Test Group : ',"",0,'L');
	$pdf->Cell(142,4,$labrequestObj["group_name"],"",0,'L');

	$pdf->Ln($space*2);
	$pdf->SetFont('Times','',8);	
	$pdf->Cell(18,4,'',"",0,'L');
	$pdf->Cell(150,4,'',"",0,'L');
	$pdf->Cell(26,4,'Ctl. Seq. No. : ',"",0,'L');
	$pdf->Cell(142,4,'',"",0,'L');
	
	$pdf->Ln($space*4);
	$pdf->SetFont('Times','B',8);	
	$pdf->Cell(20,4,'TEST CODE ',"TBRL",0,'C');
	$pdf->Cell(35,4,'TEST NAME',"TBRL",0,'C');
	$pdf->Cell(15,4,'RESULT',"TBRL",0,'C');
	$pdf->Cell(12,4,'UNIT',"TBRL",0,'C');
	$pdf->Cell(25,4,'NORMAL RANGE',"TBRL",0,'C');
	$pdf->Cell(15,4,'FLAG',"TBRL",0,'C');
	$pdf->Cell(15,4,'STATUS',"TBRL",0,'C');
	$pdf->Cell(30,4,'REPORTED ON',"TBRL",0,'C');
	$pdf->Cell(40,4,'MLT Responsible',"TBRL",0,'C');
	$pdf->Cell(30,4,'Performed Lab',"TBRL",0,'C');
	$pdf->Cell(50,4,'Test Comments',"TBRL",0,'C');
	$pdf->Cell(30,4,'Parent Item',"TBRL",0,'C');
	$pdf->Cell(20,4,'Line No.',"TBRL",1,'C');
	*/
	if ($objconn) {
		if (($refno == "2007000001") || ($refno == "2007000004") || ($refno == "2007000012"))
			$patient_info = $hclabObj->getResultHeader_to_HCLAB('641297');
		else
			$patient_info = $hclabObj->getResultHeader_to_HCLAB('8724061');	
		
		#$patient_info = $hclabObj->getResultHeader_to_HCLAB($refno);
		
		if ($patient_info){
			/*
			$sql = "SELECT ref_no AS refno, ref_source, service_code 
		        FROM seg_pay_request
				  WHERE ref_source = 'LD'
				  AND ref_no = '".$refno."'
				  AND service_code = '".$service_code."'
				  UNION
				  SELECT ref_no AS refno, ref_source, service_code 
				  FROM seg_granted_request
   			  WHERE ref_source = 'LD'
			     AND ref_no = '".$refno."'
   			  AND service_code = '".$service_code."'";
			$res = $db->Execute($sql);
			$row=$res->RecordCount();
			
			if ($row==0){
				echo '<em class="warn">Sorry but the result can\'t be displayed. The request is not yet paid or not in the list of the granted request. Pls. settle this request accounts before you can view the Lab Results. Thank you.!</em>';
				exit();
			}else{
			*/
			$pdf->Ln($space*2);
			$pdf->SetFont('Times','B',10);	
			$pdf->Cell(168,4,'Patient Demographic Information',"",0,'L');
			$pdf->Cell(169,4,'Patient Request Details',"",0,'L');
	
			$pdf->Ln($space*2);
			$pdf->SetFont('Times','',8);	
			$pdf->Cell(18,4,'Patient ID : ',"",0,'L');
			$pdf->Cell(150,4,$patient_info["PRH_PAT_ID"],"",0,'L');
			$pdf->Cell(26,4,'Order No. : ',"",0,'L');
			$pdf->Cell(142,4,$refno,"",0,'L');
		
			$request_name = stripslashes($patient_info["PRH_PAT_NAME"]);
			$request_name = ucwords(strtolower($request_name));
			$request_name = htmlspecialchars($request_name);
		
			$pdf->Ln($space*2);
			$pdf->SetFont('Times','',8);	
			$pdf->Cell(18,4,'Name : ',"",0,'L');
			$pdf->Cell(150,4,$request_name,"",0,'L');
			$pdf->Cell(26,4,'Order Date : ',"",0,'L');
			$pdf->Cell(142,4,$patient_info["PRH_ORDER_DT"],"",0,'L');
		
			if ($patient_info["PRH_PAT_TYPE"]=="IN"){
				$patient_type = "Inpatient";
			}elseif ($patient_info["PRH_PAT_TYPE"]=="OP"){
				$patient_type = "Outpatient";
			}elseif ($patient_info["PRH_PAT_TYPE"]=="ER"){
				$patient_type = "ER Patient";
			}		
			
			$pdf->Ln($space*2);
			$pdf->SetFont('Times','',8);	
			$pdf->Cell(18,4,'Patient Type : ',"",0,'L');
			$pdf->Cell(150,4,$patient_type,"",0,'L');
			$pdf->Cell(26,4,'Location : ',"",0,'L');
			$pdf->Cell(142,4,$patient_info["PRH_LOC_NAME"],"",0,'L');
		
			if ($patient_info["PRH_PAT_DOB"]) {
				$time = strtotime($patient_info["PRH_PAT_DOB"]);
				$birthDate = date("m/d/Y",$time);
			}
			$doctor_name = stripslashes($patient_info["PRH_DR_NAME"]);
			#$doctor_name = "Dr. ".ucwords(strtolower($doctor_name));
			$doctor_name = ucwords(strtolower($doctor_name));
			$doctor_name = htmlspecialchars($doctor_name);
		
			$pdf->Ln($space*2);
			$pdf->SetFont('Times','',8);	
			$pdf->Cell(18,4,'Birth Date : ',"",0,'L');
			$pdf->Cell(150,4,$birthDate,"",0,'L');
			$pdf->Cell(26,4,'Doctor : ',"",0,'L');
			$pdf->Cell(142,4,$doctor_name,"",0,'L');
		
			if ($patient_info["PRH_PAT_SEX"]==1){
				$gender = "Male";
			}elseif($patient_info["PRH_PAT_SEX"]==2){
				$gender = "Female";
			}elseif($patient_info["PRH_PAT_SEX"]==0){	
				$gender = "Unknown";
			}
			
			if ($patient_info["PRH_PRIORITY"]=="R")
				$priority = "Routine/Normal";
			elseif ($patient_info["PRH_PRIORITY"]=="U")
				$priority = "Urgent/STAT";	
		
			$pdf->Ln($space*2);
			$pdf->SetFont('Times','',8);	
			$pdf->Cell(18,4,'Sex : ',"",0,'L');
			$pdf->Cell(150,4,$gender,"",0,'L');
			$pdf->Cell(26,4,'Priority : ',"",0,'L');
			$pdf->Cell(142,4,$priority,"",0,'L');
		
			$request_address = $person['street_name']." ".$person['brgy_name']." ".$person['mun_name'].", ".$person['prov_name'].", ".$person['region_name']." ".$person['zipcode'];
			$request_address = htmlspecialchars($request_address);
		
			$pdf->Ln($space*2);
			$pdf->SetFont('Times','',8);	
			$pdf->Cell(18,4,'Address : ',"",0,'L');
			$pdf->Cell(150,4,$request_address,"",0,'L');
			$pdf->Cell(26,4,'Clinical Info : ',"",0,'L');
			$pdf->Cell(142,4,$patient_info["PRH_CLI_INFO"],"",0,'L');
	
			$pdf->Ln($space*2);
			$pdf->SetFont('Times','',8);	
			$pdf->Cell(18,4,'Classification : ',"",0,'L');
			$pdf->Cell(150,4,$info["discountid"],"",0,'L');
			$pdf->Cell(26,4,'Case / Visitation No. : ',"",0,'L');
			$pdf->Cell(142,4,$patient_info["PRH_PAT_CASENO"],"",0,'L');

			$pdf->Ln($space*2);
			$pdf->SetFont('Times','',8);	
			$pdf->Cell(18,4,'',"",0,'L');
			$pdf->Cell(150,4,'',"",0,'L');
			$pdf->Cell(26,4,'Lab No. : ',"",0,'L');
			$pdf->Cell(142,4,$patient_info["PRH_LAB_NO"],"",0,'L');

			$pdf->Ln($space*2);
			$pdf->SetFont('Times','',8);	
			$pdf->Cell(18,4,'',"",0,'L');
			$pdf->Cell(150,4,'',"",0,'L');
			$pdf->Cell(26,4,'Test : ',"",0,'L');
			$pdf->Cell(142,4,$patient_info["PRH_TEST_NAME"],"",0,'L');

			$pdf->Ln($space*2);
			$pdf->SetFont('Times','',8);	
			$pdf->Cell(18,4,'',"",0,'L');
			$pdf->Cell(150,4,'',"",0,'L');
			$pdf->Cell(26,4,'Test Type : ',"",0,'L');
			$pdf->Cell(142,4,$patient_info["PRH_TEST_TYPE"],"",0,'L');
	
			$pdf->Ln($space*2);
			$pdf->SetFont('Times','',8);	
			$pdf->Cell(18,4,'',"",0,'L');
			$pdf->Cell(150,4,'',"",0,'L');
			$pdf->Cell(26,4,'Test Group : ',"",0,'L');
			$pdf->Cell(142,4,$patient_info["PRH_TG_NAME"],"",0,'L');

			$pdf->Ln($space*2);
			$pdf->SetFont('Times','',8);	
			$pdf->Cell(18,4,'',"",0,'L');
			$pdf->Cell(150,4,'',"",0,'L');
			$pdf->Cell(26,4,'Ctl. Seq. No. : ',"",0,'L');
			$pdf->Cell(142,4,$patient_info["PRH_CTL_SEQNO"],"",0,'L');
	
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
	
			if (($refno == "2007000001") || ($refno == "2007000004") || ($refno == "2007000012"))
				$result = $hclabObj->getResult_to_HCLAB('641297', 'CBCPLT');
				
			else
				$result = $hclabObj->getResult_to_HCLAB('8724061', 'ER');	
			
			#$result = $hclabObj->getResult_to_HCLAB($refno, $service_code);
			$rowcount = $hclabObj->count;
		
			if ($rowcount){
				$i=1;
				$pdf->SetFont('Times','',8);	
				while ($row=$result->FetchRow()) {
					$pdf->Cell(20,4,$row['PRD_TEST_CODE'],"TBRL",0,'C');
					$pdf->Cell(35,4,$row['PRD_TEST_NAME'],"TBRL",0,'C');
					$pdf->Cell(15,4,$row['PRD_RESULT_VALUE'],"TBRL",0,'C');
					$pdf->Cell(12,4,$row['PRD_UNIT'],"TBRL",0,'C');
					$pdf->Cell(25,4,$row['PRD_RANGE'],"TBRL",0,'C');
					$pdf->Cell(10,4,$row['PRD_RESULT_FLAG'],"TBRL",0,'C');
					$pdf->Cell(15,4,$row['PRD_RESULT_STATUS'],"TBRL",0,'C');
					$pdf->Cell(35,4,$row['PRD_REPORTED_DT'],"TBRL",0,'C');
					$pdf->Cell(40,4,$row['PRD_MLT_NAME'],"TBRL",0,'C');
					$pdf->Cell(30,4,$row['PRD_PERFORMED_LAB_NAME'],"TBRL",0,'C');
					$pdf->Cell(55,4,$row['PRD_TEST_COMMENT'],"TBRL",0,'C');
					$pdf->Cell(30,4,$row['PRD_PARENT_ITEM'],"TBRL",0,'C');
					$pdf->Cell(15,4,$row['PRD_LINE_NO'],"TBRL",1,'C');	
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