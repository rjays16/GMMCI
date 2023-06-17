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
	
	#get service info
	$labrequestObj = $srvObj->getServiceRequestInfo($refno, $service_code);
	$person = $person_obj->getAllInfoArray($info["pid"]);
	
	# Connect to HCLAB through ORACLE
	$objconn = $hclabObj->ConnecttoDest($dsn);
	if ($objconn) {
		#$result_info = $hclabObj->getResultHeader_to_HCLAB($refno);
		#if ($refno == "2007000001")
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
			$request_name = stripslashes($patient_info["PRH_PAT_NAME"]);
			$request_name = ucwords(strtolower($request_name));
			$request_name = htmlspecialchars($request_name);
						
			$pdf->Ln($space*2);
			$pdf->SetFont('Arial','',8);	
			$pdf->Cell(22,4,'Name : ',"",0,'L');
			$pdf->Cell(85,4,strtoupper($request_name),"",0,'L');
			$pdf->Cell(15,4,'Lab No. : ',"",0,'L');
			$pdf->Cell(80,4,$patient_info["PRH_LAB_NO"],"",0,'L');
			
			$doctor_name = stripslashes($patient_info["PRH_DR_NAME"]);
			#$doctor_name = "Dr. ".ucwords(strtolower($doctor_name));
			$doctor_name = ucwords(strtolower($doctor_name));
			$doctor_name = htmlspecialchars($doctor_name);
			
			$pdf->Ln($space*2);
			$pdf->SetFont('Arial','',8);	
			$pdf->Cell(22,4,'PID : ',"",0,'L');
			$pdf->Cell(85,4,$patient_info["PRH_PAT_ID"],"",0,'L');
			$pdf->Cell(15,4,'Clinician : ',"",0,'L');
			$pdf->Cell(80,4,'Dr. '.$doctor_name,"",0,'L');
			
			if ($patient_info["PRH_PAT_SEX"]==1){
				$gender = "Male";
			}elseif($patient_info["PRH_PAT_SEX"]==2){
				$gender = "Female";
			}elseif($patient_info["PRH_PAT_SEX"]==0){	
				$gender = "Unknown";
			}
			
			if ($patient_info["PRH_PAT_DOB"]) {
				$time = strtotime($patient_info["PRH_PAT_DOB"]);
				$birthDate = date("m/d/Y",$time);
			}
			
			$age = $person_obj->getAge($birthDate,2,date("m/d/Y"));
			$age = round($age,1);
			
			$pdf->Ln($space*2);
			$pdf->SetFont('Arial','',8);	
			$pdf->Cell(22,4,'Age : ',"",0,'L');
			$pdf->Cell(30,4,$age.' yrs. old',"",0,'L');
			$pdf->Cell(10,4,'',"",0,'L');
			$pdf->Cell(15,4,'Sex : ',"",0,'L');
			$pdf->Cell(30,4,$gender,"",0,'L');
			$pdf->Cell(15,4,'Location : ',"",0,'L');
			$pdf->Cell(80,4,$patient_info["PRH_LOC_NAME"],"",0,'L');
			
			$pdf->Ln($space*2);
			$pdf->SetFont('Arial','',8);	
			$pdf->Cell(22,4,'Date Received : ',"",0,'L');
			$pdf->Cell(30,4,date("m-d-Y h:i:s",$patient_info["PRH_ORDER_DT"]),"",0,'L');
			$pdf->Cell(10,4,'',"",0,'L');
			$pdf->Cell(15,4,'Reported : ',"",0,'L');
			$pdf->Cell(30,4,date("m-d-Y h:i:s",$patient_info["PRH_TRX_DT"]),"",0,'L');
			$pdf->Cell(15,4,'Printed : ',"",0,'L');
			$pdf->Cell(40,4,date("m-d-Y h:i:s"),"",0,'L');
			$pdf->Cell(10,4,'Page : ',"",0,'L');
			$pdf->Cell(5,4,$pdf->PageNo().' / {nb}',"",0,'L');
			
			$pdf->Ln($space*4);
			$pdf->SetFont('Arial','B',8);	
			$pdf->Cell(60,4,'        TEST ',"TB",0,'L');
			$pdf->Cell(40,4,'RESULT',"TB",0,'C');
			$pdf->Cell(40,4,'UNIT',"TB",0,'C');
			$pdf->Cell(50,4,'REFERENCE RANGES',"TB",1,'C');
			$pdf->Ln($space*2);
			$pdf->Cell(40,4,'     '.$patient_info["PRH_TG_NAME"],"",0,'L');
			
			$pdf->Ln($space*4);
			$pdf->Cell(40,4,'          '.$patient_info["PRH_TEST_NAME"],"",0,'L');
			
			$pdf->Ln($space*2);
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
					#if ($row['PRD_PARENT_ITEM']==$service_code)
					if ($row['PRD_PARENT_ITEM']=='CBCPLT')
						$pdf->Cell(60,4,'               '.$row['PRD_TEST_NAME'],"",0,'L');
					else
					 	$pdf->Cell(60,4,'                    '.$row['PRD_TEST_NAME'],"",0,'L');
						
					$pdf->Cell(40,4,$row['PRD_RESULT_VALUE'],"",0,'C');
					$pdf->Cell(40,4,$row['PRD_UNIT'],"",0,'C');
					$pdf->Cell(50,4,$row['PRD_RANGE'],"",1,'C');
					$i++;
					
					$examiner = $row['PRD_MLT_NAME'];
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