<?php
	#include("roots.php");
	require('./roots.php');
	
	#include_once($root_path."/classes/fpdf/fpdf.php");
	#include_once($root_path."/classes/fpdf/pdf.class.php");
	
	require_once($root_path.'include/inc_environment_global.php');
	include_once($root_path.'include/inc_date_format_functions.php');
	
	require_once($root_path.'include/care_api_classes/class_radiology.php');
	$srvObj=new SegRadio();
	require_once($root_path.'include/care_api_classes/class_department.php');
	$dept_obj=new Department;
	require_once($root_path.'include/care_api_classes/class_person.php');
	$person_obj=new Person;
	require_once($root_path.'include/care_api_classes/class_encounter.php');
	$enc_obj=new Encounter;
	require_once($root_path.'include/care_api_classes/class_personell.php');
	$pers_obj=new Personell;
	require_once($root_path.'include/care_api_classes/class_ward.php');
	$ward_obj=new Ward;
	
	#echo "here";
	require($root_path.'classes/adodb/adodb.inc.php');
	
	global $db;
	
	$req_date = $_GET['req_date'];
	$fromtime = $_GET['fromtime'];
	$totime = $_GET['totime'];
	$user = $_GET['user'];
	
	$section = $_GET['rpt_group'];
	#echo "section - ".$section;
	$service_info = $srvObj->getAllRadioDeptInfo($section);
			
	$report_info = $srvObj->getAllPatientRequestByShift($req_date, $fromtime, $totime, $section, $user);
	#echo $srvObj->sql;
	$totalcount = $srvObj->count;
	
	$HTTP_SESSION_VARS['section_name'] = strtoupper($service_info['name_formal']);
	$HTTP_SESSION_VARS['section_id'] = strtoupper($service_info['dept_name']);
	
	$HTTP_SESSION_VARS['user'] = mb_strtoupper($user);
	
	$HTTP_SESSION_VARS['date_req'] = mb_strtoupper(date("F d, Y",strtotime($req_date)));
	$HTTP_SESSION_VARS['shift'] = date("h:i A",strtotime($fromtime))." - ".date("h:i A",strtotime($totime));
	
	include_once($root_path."/classes/fpdf/pdf-radio-report.class.php");
	
	$pdf = new PDF("L",'mm','Legal');
	$pdf->AliasNbPages();   #--added
	$pdf->AddPage("L");
	/*
	$pdf->SetLeftMargin(5);
		
	$borderYes="1";
	$borderNo="0";
	$newLineYes="1";
	$newLineNo="0";
	$space=2;
	
	$pdf->Image($root_path.'gui/img/logos/dmc_logo.jpg',20,10,20,20);
		
	$pdf->SetFont("Times","B","10");
   $pdf->Cell(0,4,'Republic of the Philippines',$borderNo,$newLineYes,'C');
	$pdf->Ln(1);
	$pdf->Cell(0,4,'DEPARTMENT OF HEALTH', $border_0,1,'C');
	$pdf->Ln(2);
	$pdf->Cell(0,4,'Gonzales Maranan Medical Center Incorporated',$borderNo,$newLineYes,'C');
	#$pdf->Cell(0,4,'OUTPATIENT and PREVENTIVE CARE CENTER',$borderNo,$newLineYes,'C');
	$pdf->Ln(2);
	$pdf->SetFont("Times","B","8");
   $pdf->Cell(0,4,'JICA Bldg., JP Laurel Avenue, Davao City',$borderNo,$newLineYes,'C');
   $pdf->Ln(2);
	$pdf->SetFont("Times","B","10");
   $pdf->Cell(0,4,'DEPARTMENT OF RADIOLOGICAL & IMAGING SCIENCES',$borderNo,$newLineYes,'C');
	$pdf->Ln(2);
	
	$section = $_GET['rpt_group'];
	#echo "section - ".$section;
	$service_info = $srvObj->getAllRadioDeptInfo($section);
	
	$pdf->SetFont("Times","B","10");
   $pdf->Cell(0,4,strtoupper($service_info['name_formal'])." (".strtoupper($service_info['dept_name']).")",$borderNo,$newLineYes,'C');
	$pdf->Ln(2);
	$pdf->SetFont("Times","I","10");
	$pdf->Cell(0,4,'OPS & IN-PATIENT REGISTER',$borderNo,$newLineYes,'C');
	$pdf->Ln(2);
	
	$req_date = $_GET['req_date'];
	$fromtime = $_GET['fromtime'];
	$totime = $_GET['totime'];
	$user = $_GET['user'];
	
	
	$pdf->SetFont("Times","B","10");
   $pdf->Cell(0,4,'RAD. TECH. ON DUTY : '.mb_strtoupper($user),0,$newLineNo,'C');
	$pdf->Ln(6);
	#echo $fromtime." - ".$totime;
	
	$fromtime = date("H:i:s",strtotime($fromtime));
	$totime = date("H:i:s",strtotime($totime));
	
	if (($fromtime=='00:00:00 AM')||($fromtime=='00:00:00 PM'))
		$fromtime = '00:00:00';
	if (($totime=='00:00:00 AM')||($totime=='00:00:00 PM'))
		$totime = '00:00:00';
	
	$pdf->Cell(10,4,'',0,$newLineNo,'L');	
	$pdf->SetFont("Times","","10");
   $pdf->Cell(15,4,'DATE : ',0,$newLineNo,'L');
	$pdf->SetFont("Times","B","12");
   $pdf->Cell(10,4,mb_strtoupper(date("F d, Y",strtotime($req_date))),0,$newLineNo,'L');
	
	$pdf->Cell(230,4,'',0,$newLineNo,'');
	
	$pdf->SetFont("Times","","10");
   $pdf->Cell(15,4,'SHIFT : ',0,$newLineNo,'L');
	$pdf->SetFont("Times","B","12");
   $pdf->Cell(10,4,date("h:i A",date("h:i A",strtotime($fromtime))." - ".date("h:i A",strtotime($totime)),0,$newLineNo,'L');
	
	$pdf->Ln(2);
	
	
	$pdf->SetFont("Times","B","10");
	
	#$fromtime = '00:00:00';
	#$totime = '00:00:00';
	
	$report_info = $srvObj->getAllPatientRequestByShift($req_date, $fromtime, $totime, $section, $user);
	#echo $srvObj->sql;
	$totalcount = $srvObj->count;
	
	$pdf->Ln($space*4);
	$pdf->SetFont('Arial','B',8);
	#$pdf->Cell(10,4,"","",0,'L');	
	
	$pdf->Cell(5,6,'',"TBRL",0,'C');
	$pdf->Cell(22,6,'HOSPITAL NO.',"TBRL",0,'C');
	$pdf->Cell(18,6,'OR NO.',"TBRL",0,'C');
	$pdf->Cell(13,6,'AMOUNT',"TBRL",0,'C');
	$pdf->Cell(18,6,'FILM NO.',"TBV",0,'C');
	$pdf->Cell(18,6,'REF. NO.',"TBRL",0,'C');
	$pdf->Cell(14,6,'TIME',"TBRL",0,'C');
	$pdf->Cell(50,6,'NAME OF PATIENT',"TBRL",0,'C');
	$pdf->Cell(10,6,'AGE',"TBRL",0,'C');
	$pdf->Cell(7,6,'SEX',"TBRL",0,'C');
	$pdf->Cell(16,6,'BIRTHDAY',"TBRL",0,'C');
	$pdf->Cell(40,6,'ADDRESS',"TBRL",0,'C');
	$pdf->Cell(40,6,'ADMITTING DIAGNOSIS',"TBRL",0,'C');
	$pdf->Cell(25,6,'PHYSICIAN',"TBRL",0,'C');
	$pdf->Cell(20,6,'WARD',"TBRL",0,'C');
	$pdf->Cell(25,6,'EXAMINATION',"TBRL",1,'C');
	#$pdf->Ln(2);
	#$pdf->Cell(20,4,'TIME',"TB",0,'L');
	*/
	#$pdf->Cell(50,4,'',"TB",1,'L');
	if ($totalcount){
			$i=1;
			$pdf->SetFont('Times','',8);	
			while ($row=$report_info->FetchRow()){
				$pdf->Cell(7,8,$i,"TBRL",0,'C');
				$pdf->Cell(22,8,$row["pid"],"TBRL",0,'C');
				$pdf->Cell(18,8,$row["or_no"],"TBRL",0,'C');
				$pdf->Cell(13,8,number_format($row["amount_due"],2,".",","),"TBRL",0,'C');
				$pdf->Cell(18,8,$row["film_no"],"TBV",0,'C');
				$pdf->Cell(18,8,$row["refno"],"TBRL",0,'C');
				$pdf->Cell(14,8,date("h:i A",strtotime($row["request_time"])),"TBRL",0,'C');
				
				$patient = mb_strtoupper($row["name_last"]).", ".mb_strtoupper($row["name_first"])." ".mb_strtoupper($row["name_middle"]);
				
				$pdf->Cell(50,8,$patient,"TBRL",0,'L');
				$pdf->Cell(10,8,$row["age"],"TBRL",0,'C');
				$pdf->Cell(7,8,mb_strtoupper($row["sex"]),"TBRL",0,'C');
				$pdf->Cell(16,8,date("m/d/Y",strtotime($row["date_birth"])),"TBRL",0,'C');
				
				#$address = $row["street_name"]."".$row["mun_name"];
				#$address = $row["mun_name"];
				$address = $row["brgy_name"]." ".$row["mun_name"];
				
				$pdf->Cell(40,8,$address,"TBRL",0,'L');
				$pdf->Cell(40,8,$row["adm_diagnosis"],"TBRL",0,'L');
				
				$pdf->Cell(26,8,$row["dr_name"],"TBRL",0,'L');
				
				if ($row["encounter_nr"]){
					if ($row['encounter_type'] == 1){
						$loc_name = "ER";
					}elseif (($row['encounter_type'] == 3)||($row['encounter_type'] == 4)){
						$loc_code = $patient['current_ward_nr'];
						if ($loc_code)
							$ward = $ward_obj->getWardInfo($loc_code);
						$loc_name = stripslashes($ward['ward_id']);
					}else{
						$loc_code = $row['current_dept_nr'];
						if ($loc_code)
							$dept = $dept_obj->getDeptAllInfo($loc_code);
						$loc_name = stripslashes($dept['id']);
					}
				}else{
					$loc_name = "WALK-IN";
				}	
				
				$pdf->Cell(22,8,$loc_name,"TBRL",0,'L');
				$pdf->Cell(25,8,$row["service_code"],"TBRL",1,'L');
				#$pdf->Ln(4);
				$i++;
			}	
		
	}else{
		$pdf->SetFont('Times','',10);	
		#$pdf->Ln(10);
		$pdf->Cell(346,20,'No query results available at this time...',"TRLB",0,'C');
	}
	
	$pdf->Output();	
?>