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
		
	$borderYes="1";
	$borderNo="0";
	$newLineYes="1";
	$newLineNo="0";
	$space=2;
	
	$pdf->Image($root_path.'gui/img/logos/dmc_logo.jpg',20,10,20,20);
		
	$pdf->SetFont("Times","B","10");
   $pdf->Cell(0,4,'Republic of the Philippines',$borderNo,$newLineYes,'C');
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
	$pdf->Cell(0,4,'LABORATORY STATISTICS REPORT',$borderNo,$newLineYes,'C');
	$pdf->Ln(2);
	
	$grp_kind = $_GET['report_kind'];
	$grp_code = $_GET['report_group'];
	$discountID = $_GET['report_class'];
	$datefrom = $_GET['fromdate'];
	$dateto = $_GET['todate'];
	$grpview = $_GET['grpview'];
	
	$pdf->SetFont("Times","B","10");
	if ($grp_kind == 'all'){
		$pdf->Cell(0,4,'ALL LABORATORY REQUESTS',$borderNo,$newLineYes,'C');
		$pdf->Ln(4);
	}elseif ($grp_kind == 'wo_result'){
		$pdf->Cell(0,4,'LABORATORY REQUEST WITHOUT RESULTS',$borderNo,$newLineYes,'C');
		$pdf->Ln(4);
	}elseif ($grp_kind == 'w_result'){
		$pdf->Cell(0,4,'LABORATORY REQUEST WITH RESULTS',$borderNo,$newLineYes,'C');
		$pdf->Ln(4);
	}
	
	#echo "grpview = ".$grpview;
	#$report_info = $srvObj->getListLabSectionRequest($grp_kind, $grpview, $grp_code, $datefrom, $dateto, $discountID,1);
	$report_info = $srvObj->getListLabSectionRequest_Stat($grp_kind, $grpview, $grp_code, $datefrom, $dateto, $discountID);
	#echo $srvObj->sql;
	$totalcount = $srvObj->count;
	
	#echo "sql = ".$srvObj->sql;
	#echo "<br>totalcount = ".$totalcount;
	$service_info = $srvObj->getAllLabGroupInfo($grp_code);
	
	$pdf->SetFont("Times","","10");
	if ($grp_code!='all'){
		$pdf->Cell(270,4,'Laboratory Section : '.$service_info['name'],"",0,'L');
	}else{
		$pdf->Cell(270,4,'Laboratory Section : ALL SECTION',"",0,'L');
	}
	$pdf->Cell(60,4,'Date : '.date("M. d, Y "),"",0,'L');
	$pdf->Ln($space*2);
	if ($discountID!='all'){
		$pdf->Cell(270,4,'Classification : '.$discountID,"",0,'L');
	}else{
		$pdf->Cell(270,4,'Classification : ALL CLASS',"",0,'L');
	}
	
	$pdf->Cell(60,4,'Time : '.date("h:i:s A"),"",0,'L');
	$pdf->Ln($space*2);
	$pdf->Cell(270,4,'Start Date : '.$datefrom,"",0,'L');
	$pdf->Ln($space*2);
	$pdf->Cell(270,4,'End Date : '.$dateto,"",0,'L');
	$pdf->Ln($space*2);
	#$pdf->Cell(270,4,'Number of Records : '.$totalcount,"",0,'L');
	#$pdf->Ln($space*2);
#	$pdf->Cell(270,4,'Currency : Philippine Peso (Php)',"",0,'L');
	#$pdf->Cell(60,4,'Page : '.$pdf->PageNo().' / {nb}',"",0,'L');
	
	$pdf->Ln($space*4);

	$pdf->SetFont('Arial','B',8);	
	#$pdf->Cell(20,4,'PATIENT ID',"TB",0,'L');

	if ($totalcount){
			$i=1;
			#$pdf->SetFont('Times','',8);	
			#$mnt_array = array();
			while ($row=$report_info->FetchRow()){
				$pdf->SetFont('Arial','B',8);	
				#$mnt_array[] = $row['month'];
				
				$month = $srvObj->getMonth($row['month']);
				#echo "<br>month = ".$month;
				$year = date('Y',strtotime($row['serv_dt']));
				#$pdf->Cell(35,4,$month.", ".$year,"",1,'L');
				
				$pdf->Cell(10,4,'',"",0,'L');
				$pdf->Cell(40,4,strtoupper($row['grp_name']).' : ',"",0,'L');
				$pdf->Cell(5,4,$row['stat'],"",0,'R');
				$pdf->Cell(35,4,$month.", ".$year,"",1,'R');
				$pdf->Cell(5,4,'',"",1,'L');
				
			}	

	}else{
		$pdf->SetFont('Times','',10);	
		$pdf->Ln($space*4);
		$pdf->Cell(337,4,'No query results available at this time...',"",0,'C');
	}
	
	$pdf->Output();	
?>