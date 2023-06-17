<?php
	include("roots.php");
	require('./roots.php');
	
	#include_once($root_path."/classes/fpdf/fpdf.php");
	include_once($root_path."/classes/fpdf/pdf.class.php");
	require_once($root_path.'include/inc_environment_global.php');
	include_once($root_path.'include/inc_date_format_functions.php');
	
	#require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
	#$srvObj=new SegLab;
	require_once($root_path.'include/care_api_classes/class_radiology.php');
	$radio_Obj=new SegRadio;
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
	
	require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
	$objInfo = new Hospital_Admin();
	
	require($root_path.'classes/adodb/adodb.inc.php');
	include($root_path.'include/inc_init_hclab_main.php');
	include($root_path.'include/inc_seg_mylib.php');
	
	#require_once($root_path.'include/care_api_classes/class_hclab_oracle.php');
	#$hclabObj = new HCLAB;
	
	global $db;
	
	$pdf = new PDF("P",'mm','Letter');
	$pdf->AliasNbPages();   #--added
	$pdf->AddPage("P");
		
	$borderYes="1";
	$borderNo="0";
	$newLineYes="1";
	$newLineNo="0";
	$space=2;
	
	$pdf->Image($root_path.'gui/img/logos/dmc_logo.jpg',20,10,20,20);
	
	if ($row = $objInfo->getAllHospitalInfo()) {			
		$row['hosp_agency'] = strtoupper($row['hosp_agency']);
		$row['hosp_name']   = strtoupper($row['hosp_name']);
	}
	else {
		$row['hosp_country'] = "Republic of the Philippines";
		$row['hosp_agency']  = "DEPARTMENT OF HEALTH";
		$row['hosp_name']    = "Gonzales Maranan Medical Center Incorporated";
		$row['hosp_addr1']   = "Quezon Ave., Digos City";			
	}
		
	$pdf->SetFont("Times","B","10");
   #$pdf->Cell(0,4,'Republic of the Philippines',$borderNo,$newLineYes,'C');
	$pdf->Cell(0,4,$row['hosp_country'],$borderNo,$newLineYes,'C');
	$pdf->Ln(1);
	#$pdf->Cell(0,4,'DEPARTMENT OF HEALTH', $border_0,1,'C');
	$pdf->Cell(0,4,$row['hosp_agency'], $border_0,1,'C');
	$pdf->Ln(2);
	#$pdf->Cell(0,4,'Gonzales Maranan Medical Center Incorporated',$borderNo,$newLineYes,'C');
	$pdf->Cell(0,4,$row['hosp_name'],$borderNo,$newLineYes,'C');
	#$pdf->Cell(0,4,'OUTPATIENT and PREVENTIVE CARE CENTER',$borderNo,$newLineYes,'C');
	$pdf->Ln(2);
	$pdf->SetFont("Times","B","8");
   #$pdf->Cell(0,4,'JICA Bldg., JP Laurel Avenue, Davao City',$borderNo,$newLineYes,'C');
	$pdf->Cell(0,4,$row['hosp_addr1'],$borderNo,$newLineYes,'C');
   $pdf->Ln(2);
	$pdf->SetFont("Times","B","10");
	$pdf->Cell(0,4,'DEPARTMENT OF RADIOLOGICAL & IMAGING SCIENCES',$borderNo,$newLineYes,'C');
	$pdf->Ln(2);
	$pdf->SetFont("Times","I","10");
	$pdf->Cell(0,4,'ROENTGENOLOGICAL STATISTICS REPORT',$borderNo,$newLineYes,'C');
	$pdf->Ln(2);
	
	$datefrom = $_GET['fromdate'];
	$dateto = $_GET['todate'];
	
	$pdf->SetFont("Times","","10");
	$pdf->Cell(15,4,'Date',"",0,'L');
	$pdf->Cell(5,4,':',"",0,'C');
	$pdf->Cell(20,4,date("F d, Y "),"",0,'L');
	$pdf->Ln($space*2);
	$pdf->Cell(15,4,'Time',"",0,'L');
	$pdf->Cell(5,4,':',"",0,'C');
	$pdf->Cell(20,4,date("h:i:s A"),"",0,'L');
	
	if (($datefrom)&&($dateto)){
		$pdf->Ln($space*2);
		$pdf->Cell(15,4,'Start Date',"",0,'L');
		$pdf->Cell(5,4,':',"",0,'C');
		$pdf->Cell(20,4,date("F d, Y ", strtotime($datefrom)),"",0,'L');
		$pdf->Ln($space*2);
		$pdf->Cell(15,4,'End Date',"",0,'L');
		$pdf->Cell(5,4,':',"",0,'C');
		$pdf->Cell(20,4,date("F d, Y ", strtotime($dateto)),"",0,'L');
	}
	$pdf->Ln($space*4);
	
	$totalcount = 0;
	$totalyear = 0;
	#echo "from = ".$datefrom;
	#echo "<br>to = ".$dateto;
	#$pdf->SetFont('Arial','B',8);	
	$report_info = $radio_Obj->getStatRadioServices($datefrom, $dateto);
	#echo "<br>".$radio_Obj->sql;
	$totalcount = $radio_Obj->count;
	#echo "total = ".$totalcount;
	
	if ($totalcount){
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(40,6,'Code',"TBLR",0,'C');
		$pdf->Cell(100,6,'Service\'s Name ',"TBLR",0,'C');
		$pdf->Cell(40,6,'No. of Requests',"TBLR",1,'C');
		while ($row=$report_info->FetchRow()){
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(40,6,$row['service_code'],"TBLR",0,'L');
			$pdf->Cell(100,6,$row['name'],"TBLR",0,'L');
			$pdf->Cell(40,6,$row['no_of_request'],"TBLR",1,'C');
		}	
	}else{
		$pdf->SetFont('Times','',10);	
		$pdf->Ln($space*4);
		$pdf->Cell(150,4,'No query results available at this time...',"",0,'C');
	}
	
	$pdf->Output();	
?>