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
	$ward_obj=new Ward;
	
	
	require($root_path.'classes/adodb/adodb.inc.php');
	include($root_path.'include/inc_init_hclab_main.php');
	include($root_path.'include/inc_seg_mylib.php');
	
	require_once($root_path.'include/care_api_classes/class_hclab_oracle.php');
	$hclabObj = new HCLAB;
	
	global $db;
	
	$pdf = new PDF("L",'mm','Legal');
	$pdf->AliasNbPages();   #--added
	$pdf->AddPage("L");
	
	$pdf->SetLeftMargin(26);
		
	$borderYes="1";
	$borderNo="0";
	$newLineYes="1";
	$newLineNo="0";
	$fontsizeInput = 10;
	$space=2;
	
	$pdf->Image($root_path.'gui/img/logos/dmc_logo.jpg',50,10,20,20);
		
	$pdf->SetFont("Times","B",$fontsizeInput);
    $pdf->Cell(0,4,'Republic of the Philippines',$borderNo,$newLineYes,'C');
	$pdf->Ln(1);
	$pdf->Cell(0,4,'DEPARTMENT OF HEALTH', $border_0,1,'C');
	$pdf->Ln(2);
	$pdf->Cell(0,4,'Gonzales Maranan Medical Center Incorporated',$borderNo,$newLineYes,'C');
	#$pdf->Cell(0,4,'OUTPATIENT and PREVENTIVE CARE CENTER',$borderNo,$newLineYes,'C');
	$pdf->Ln(2);
	$pdf->SetFont("Times","B",$fontsizeInput-2);
    $pdf->Cell(0,4,'Quezon Ave., Digos City, Davao del Sur',$borderNo,$newLineYes,'C');
    $pdf->Ln(2);
	$pdf->SetFont("Times","B",$fontsizeInput);
    $pdf->Cell(0,4,'DEPARTMENT OF PATHOLOGY AND CLINICAL LABORATORIES',$borderNo,$newLineYes,'C');
	$pdf->Ln(4);
	
	$datefrom = $_GET['fromdate'];
	$dateto = $_GET['todate'];
	
	$fromtime = $_GET['fromtime'];
	$totime = $_GET['totime'];
	
	$fromtime = date("H:i:s",strtotime($fromtime));
	$totime = date("H:i:s",strtotime($totime));
	
	if (($fromtime=='00:00:00 AM')||($fromtime=='00:00:00 PM'))
		$fromtime = '00:00:00';
	if (($totime=='00:00:00 AM')||($totime=='00:00:00 PM'))
		$totime = '00:00:00';
	
	$datefrom = date("Y-m-d",strtotime($datefrom));
	$dateto = date("Y-m-d",strtotime($dateto));
	
	$pdf->SetFont("Times","B",$fontsizeInput+2);
	#$pdf->Cell(0,4,'Patients\' List '.$datefrom." ".$fromtime." - ".$dateto." ".$totime,$borderNo,$newLineYes,'C');
	$pdf->Cell(0,4,'OPD Patients\' List '.$datefrom." ".$fromtime." - ".$dateto." ".$totime,$borderNo,$newLineYes,'C');
	$pdf->Ln(4);
	
	#$report_info = $srvObj->getPatientList($datefrom, $dateto, $fromtime, $totime,0);
	#$totalcount = $srvObj->count;
	$report_info_grp = $srvObj->getPatientList($datefrom, $dateto, $fromtime, $totime,1);
	#echo $srvObj->sql;
	$totalcount2 = $srvObj->count;
	
	$pdf->SetFont("Times","",$fontsizeInput+2);
	
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
	if ($totalcount2){
		while ($row=$report_info_grp->FetchRow()){
		
			$pdf->Cell(10,4,"","",0,'L');	
			$pdf->Cell(35,5,date("m/d/Y",strtotime(trim($row['serv_dt'])))." ".date("h:i A",strtotime(trim($row['serv_tm']))),"",0,'L');
			$pdf->Cell(25,5,trim($row['refno']),"",0,'C');
			$pdf->Cell(60,5,strtoupper($row['ordername']),"",0,'L');
			$pdf->Cell(25,5,trim($row['pid']),"",0,'L');
			
			if ($row['date_birth']!='0000-00-00'){
				$bdate = date("m/d/Y",strtotime(trim($row['date_birth'])));
				$age = substr($row['agebydbate'],0,3);
			}else{
				$bdate = "unknown";
				if($row['age'])	
					$age = $row['age'];	
				else	
					$age = 0;
			}
			
			$pdf->Cell(25,5,$bdate,"",0,'C');
			
			
			$pdf->Cell(10,5,trim($age),"",0,'C');
			$pdf->Cell(10,5,strtoupper($row['sex']),"",0,'C');
					
			$report_info_details = $srvObj->getPatientListDetails($row['refno']);
			$totalcount = $srvObj->count;
			
			if ($totalcount){
				while ($row2=$report_info_details->FetchRow()){
					#$pdf->Cell(10,5,number_format($row2['qty'],0),"",0,'C');
					$pdf->Cell(10,5,"1","",0,'C');
					$pdf->Cell(65,5,$row2['service_name'],"",0,'L');
					$pdf->Cell(25,5,$row['dept_name'],"",0,'L');
					$pdf->Cell(7,5,'Php',"",0,'L');
					$pdf->Cell(15,5,number_format($row2['paid'],2,".",","),"",1,'R');
					$paid = $paid + $row2['paid'];
					$pdf->Cell(200,5,"","",0,'L');
				}
			}
			$pdf->Ln($space*2);
		}
		$pdf->SetFont("Times","B",$fontsizeInput+4);
		$pdf->Cell(10,4,"","",0,'L');	
		
		$pdf->Cell(170,4,"TOTAL AMOUNT COLLECTED (".$datefrom." ".$fromtime." - ".$dateto." ".$totime.")","",0,'L');	
		$pdf->Cell(10,4," : Php","",0,'L');	
		$pdf->Cell(2,4,"","",0,'L');	
		$pdf->Cell(20,4,number_format($paid, 2),"",1,'R');	
	}else{
		$pdf->SetFont('Times','',$fontsizeInput);	
		$pdf->Ln($space*4);
		$pdf->Cell(337,4,'No query results available at this time...',"",0,'C');
	}
	
	
	$pdf->Output();	
?>