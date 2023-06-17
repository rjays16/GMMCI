<?php
	//include("roots.php");
	require('./roots.php');
	
	#include_once($root_path."/classes/fpdf/fpdf.php");
	
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
	$ward_obj=new Ward;
	
	require($root_path.'classes/adodb/adodb.inc.php');
	include($root_path.'include/inc_init_hclab_main.php');
	#include($root_path.'include/inc_seg_mylib.php');
	
	require_once($root_path.'include/care_api_classes/class_hclab_oracle.php');
	$hclabObj = new HCLAB;
	
	global $db;
	
	$datefrom = $_GET['fromdate'];
	$dateto = $_GET['todate'];
	
	$fromtime = $_GET['fromtime'];
	$totime = $_GET['totime'];
	
	$pat_type = $_GET['pat_type'];
	
	$grp_kind = $_GET['report_kind'];
	$grp_code = $_GET['report_group'];
	$discountID = $_GET['report_class'];
	
	if (($fromtime=='00:00:00 AM')||($fromtime=='00:00:00 PM')){
		$fromtime = '00:00:00';
	}else{
		$fromtime = date("H:i:s",strtotime($fromtime));
	}	
		
	if (($totime=='00:00:00 AM')||($totime=='00:00:00 PM')){
		$totime = '00:00:00';
	}else{
		$totime = date("H:i:s",strtotime($totime));	
	}	
	
	$HTTP_SESSION_VARS['fromtime'] = $fromtime;
	$HTTP_SESSION_VARS['totime'] = $totime;
	
	if ($datefrom){
		$datefrom = date("Y-m-d",strtotime($datefrom));
		$datefromSession = date("m/d/Y",strtotime($datefrom));
	}else{
		$datefrom = "";	
		$datefromSession = "";
	}
	
	if ($dateto){	
		$dateto = date("Y-m-d",strtotime($dateto));
		$datetoSession = date("m/d/Y",strtotime($dateto));
	}else{
		$dateto = "";	
		$datetoSession = "";
	}	
	
	#$HTTP_SESSION_VARS['fromdate'] = date("m/d/Y",strtotime($datefrom));
	#$HTTP_SESSION_VARS['todate'] = date("m/d/Y",strtotime($dateto));
	$HTTP_SESSION_VARS['fromdate'] = $datefromSession;
	$HTTP_SESSION_VARS['todate'] = $datetoSession;
	
	if ($pat_type==1){
		#ER PATIENT
		$enctype = " AND encounter_type IN (1)";
		$patient_type = "ER";
	}elseif ($pat_type==2){
		#ADMITTED PATIENT
		$enctype = " AND encounter_type IN (3,4)";
		$patient_type = "IPD";
	}elseif ($pat_type==3){
		#OUT PATIENT
		$enctype = " AND encounter_type IN (2)";
		$patient_type = "OPD";
	}elseif ($pat_type==4){
		#WALK-IN PATIENT
		$enctype = " AND r.encounter_nr=''";
		$patient_type = "Walkin";
	}elseif($pat_type==5){
		$enctype = " AND (encounter_type IN(2) OR r.encounter_nr='')";
		$patient_type = "OPD & Walkin";
	}else{	
		$enctype = "";
		$patient_type = "All";
	}	
		
	$HTTP_SESSION_VARS['patient_type'] = $patient_type;
	
	include_once($root_path."/classes/fpdf/pdf-lab.class.php");
	$pdf = new PDF("L",'mm','Legal');
	$pdf->AliasNbPages();   #--added
	$pdf->AddPage("L");
	
	$pdf->SetLeftMargin(26);
	$pdf->SetAutoPageBreak("auto");
			
	$borderYes="1";
	$borderNo="0";
	$newLineYes="1";
	$newLineNo="0";
	$fontsizeInput = 10;
	$space=2;
	
	/*
	$pdf->Image($root_path.'gui/img/logos/dmc_logo.jpg',50,10,20,20);
		
	$pdf->SetFont("Times","B",$fontsizeInput);
    $pdf->Cell(0,4,'Republic of the Philippines',$borderNo,$newLineYes,'C');
	$pdf->Ln(1);
	$pdf->Cell(0,4,'DEPARTMENT OF HEALTH', $border_0,1,'C');
	$pdf->Ln(2);
	$pdf->Cell(0,4,'DAVAO MEDICAL CENTER',$borderNo,$newLineYes,'C');
	#$pdf->Cell(0,4,'OUTPATIENT and PREVENTIVE CARE CENTER',$borderNo,$newLineYes,'C');
	$pdf->Ln(2);
	$pdf->SetFont("Times","B",$fontsizeInput-2);
    $pdf->Cell(0,4,'JICA Bldg., JP Laurel Avenue, Davao City',$borderNo,$newLineYes,'C');
    $pdf->Ln(2);
	$pdf->SetFont("Times","B",$fontsizeInput);
    $pdf->Cell(0,4,'DEPARTMENT OF PATHOLOGY AND CLINICAL LABORATORIES',$borderNo,$newLineYes,'C');
	$pdf->Ln(4);
	*/
	
	/*
	$pdf->SetFont("Times","B",$fontsizeInput+2);
	#$pdf->Cell(0,4,'Patients\' List '.$datefrom." ".$fromtime." - ".$dateto." ".$totime,$borderNo,$newLineYes,'C');
	$pdf->Cell(0,4,'OPD Patients\' List '.date("m/d/Y",strtotime($datefrom))." ".date("h:i A",strtotime($fromtime))." - ".date("m/d/Y",strtotime($dateto))." ".date("h:i A",strtotime($totime)),$borderNo,$newLineYes,'C');
	$pdf->Ln(4);
	*/
	#$report_info = $srvObj->getPatientList($datefrom, $dateto, $fromtime, $totime,0);
	#$totalcount = $srvObj->count;
	
	#$report_info_grp = $srvObj->getPatientList($datefrom, $dateto, $fromtime, $totime, $enctype,1);
	$report_info_grp = $srvObj->getPatientList($datefrom, $dateto, $fromtime, $totime, $grp_kind, $grp_code, $discountID, $enctype, 1);
	#echo $srvObj->sql;
	$totalcount2 = $srvObj->count;
	
	$pdf->SetFont("Times","",$fontsizeInput+2);
	/*
	$pdf->Ln($space*4);
	$pdf->SetFont('Arial','B',$fontsizeInput-1);
	$pdf->Cell(10,4,"","",0,'L');	
	$pdf->Cell(35,8,'DATE/TIME',"TB",0,'L');
	$pdf->Cell(25,8,'REFERENCE',"TB",0,'C');
	$pdf->Cell(60,8,'PATIENT\'S NAME',"TB",0,'L');
	$pdf->Cell(25,8,'HOSP. NO.',"TB",0,'L');
	$pdf->Cell(25,8,'BIRTH DATE',"TB",0,'C');
	$pdf->Cell(10,8,'AGE',"TB",0,'C');
	$pdf->Cell(10,8,'SEX',"TB",0,'C');
	$pdf->Cell(10,8,'QTY',"TB",0,'C');
	$pdf->Cell(65,8,'PROCEDURE',"TB",0,'L');
	$pdf->Cell(25,8,'FROM',"TB",0,'L');
	$pdf->Cell(20,8,'PAID',"TB",0,'C');
	
	$pdf->Ln($space*6);
	*/
	#
	
	$pdf->SetFont('Arial','B',$fontsizeInput-1);
	if ($totalcount2){
		while ($row=$report_info_grp->FetchRow()){
		
			$pdf->Cell(10,4,"","",0,'L');	
			$pdf->Cell(32,5,date("m/d/Y",strtotime(trim($row['serv_dt'])))." ".date("h:i A",strtotime(trim($row['serv_tm']))),"",0,'L');
			$pdf->Cell(25,5,trim($row['refno']),"",0,'C');
			#$pdf->Cell(50,5,strtoupper($row['ordername']),"",0,'L');
			$x = $pdf->GetX();
 			$y = $pdf->GetY();
			$pdf->SetXY($x, $y);
			$pdf->MultiCell(50, 5, mb_strtoupper($row['ordername']), '', 'L','');
			$pdf->SetXY($x+50, $y);
			$pdf->Cell(25,5,trim($row['pid']),"",0,'L');
			
			if ($row['date_birth']!='0000-00-00'){
				$bdate = date("m/d/Y",strtotime(trim($row['date_birth'])));
				$trans = array("years" => "yrs", "year" => "yr");
				$age = strtr($row['agebydbate'],$trans);
				#$age = $row['agebydbate'];
				#echo "<br>age = ".$age;
			}else{
				$bdate = "unknown";
				if($row['age'])	{
					if ($row['age']==1)
						$age = $row['age']." yr";	
					else
						$age = $row['age']." yrs";		
				}else	
					$age = "unknown";
			}
			
			$pdf->Cell(25,5,$bdate,"",0,'C');
			
			
			$pdf->Cell(12,5,trim($age),"",0,'C');
			$pdf->Cell(10,5,strtoupper($row['sex']),"",0,'C');
			
			#$z = $pdf->GetY();
					
			$report_info_details = $srvObj->getPatientListDetails($row['refno']);
			#echo $srvObj->sql;
			$totalcount = $srvObj->count;
			$i=1;
			if ($totalcount){
				while ($row2=$report_info_details->FetchRow()){
					#$pdf->Cell(10,5,number_format($row2['qty'],0),"",0,'C');
					$pdf->Cell(10,5,"1","",0,'C');
					$x = $pdf->GetX();
					$y = $pdf->GetY();
					#$pdf->Cell(65,5,$row2['service_name'],"",0,'L');
					$pdf->SetXY($x, $y);
					$pdf->MultiCell(55, 5, mb_strtoupper($row2['service_name']), '', 'L','');
					#$pdf->Cell(60,5,mb_strtoupper($row2['service_name']),"1",0,'L');
					#$x = $pdf->GetX();
					#$y = $pdf->GetY();
					#$pdf->SetXY($x+65, $y);
					#$pdf->Cell(25,5,$row['dept_name'],"",0,'L');
					#echo "type = ".$row['encounter_type'];
					if ($row['encounter_type']==1){
						$location = "ER";
					}elseif ($row['encounter_type']==2){
						$dept = $dept_obj->getDeptAllInfo($row['current_dept_nr']);
						$location = $dept['id'];
					}elseif (($row['encounter_type']==3)||($row['encounter_type']==4)){
						#echo "wad = ".$row['current_ward_nr'];
						$ward = $ward_obj->getWardInfo($row['current_ward_nr']);
						#echo "<br>sql = ".$ward_obj->sql;
						$location = $ward['ward_id']." : Rm.#".$row['current_room_nr'];
					}else{
						$location = 'Walkin';
					}
					$pdf->SetXY($x+60, $y);
					#$pdf->SetX($x+65);
					#$pdf->MultiCell(30, 4, $location, '', 'L','');
					if ($i==1)
						#$pdf->Cell(40,5,$location,"",0,'L');
						$pdf->MultiCell(40, 4, $location, '', 'L','');
					
					$pdf->SetXY($x+100, $y);
					$pdf->Cell(7,5,'Php',"",0,'L');
					$pdf->Cell(15,5,number_format($row2['paid'],2,".",","),"",1,'R');
					$paid = $paid + $row2['paid'];
					#$pdf->Ln($space*2);
					if (strlen($row2['service_name'])>42)
						$pdf->Ln($space*5);
					else	
						$pdf->Ln($space*2);
					$pdf->Cell(189,5,"","",0,'L');
					$i++;
				}
				
			}
			$pdf->Ln($space*1);
		}
		
		$pdf->SetY(-20);
		
		$pdf->SetFont("Times","B",$fontsizeInput+4);
		$pdf->Cell(10,4,"","",0,'L');	
		
		$pdf->Cell(315,4,"","T",1,'L');
		$pdf->Cell(10,4,"","",0,'L');	
		$pdf->Cell(170,4,"TOTAL AMOUNT COLLECTED (".date("h:i A",strtotime($fromtime))." - ".date("m/d/Y",strtotime($dateto))." ".date("h:i A",strtotime($totime)).")","",0,'L');	
		$pdf->Cell(10,4," : Php","",0,'L');	
		$pdf->Cell(8,4," ","",0,'L');	
		$pdf->Cell(25,4,number_format($paid, 2),"",1,'R');	
		$pdf->Ln(2);
		$pdf->Cell(10,4,"","",0,'L');	
		$pdf->Cell(50,4,"Total Number of Records : ".$totalcount2,"",0,'L');	
	}else{
		$pdf->SetFont('Times','',$fontsizeInput);	
		$pdf->Ln($space*4);
		$pdf->Cell(337,4,'No query results available at this time...',"",0,'C');
	}
	
	
	$pdf->Output();	
?>