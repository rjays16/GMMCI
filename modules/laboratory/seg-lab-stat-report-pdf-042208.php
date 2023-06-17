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
	
	$pdf = new PDF("L",'mm','Letter');
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
	$pdf->Ln(1);
	$pdf->Cell(0,4,'DEPARTMENT OF HEALTH', $border_0,1,'C');
	$pdf->Ln(2);
	$pdf->Cell(0,4,'Gonzales Maranan Medical Center Incorporated',$borderNo,$newLineYes,'C');
	#$pdf->Cell(0,4,'OUTPATIENT and PREVENTIVE CARE CENTER',$borderNo,$newLineYes,'C');
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
	
	$datefrom = $_GET['fromdate'];
	$dateto = $_GET['todate'];
	
	$pdf->SetFont("Times","B","10");

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
	
	#$pdf->SetFont('Arial','B',8);	
	$report_info = $srvObj->getStatReport($datefrom, $dateto);
	#echo $srvObj->sql;
	$totalcount = $srvObj->count;
	
	if ($totalcount){
		while ($row=$report_info->FetchRow()){
			$pdf->SetFont('Arial','B',8);
					
			$pdf->Cell(10,4,'YEAR : '.$row['year'],"",0,'L');
			$pdf->Cell(5,4,'',"",1,'L');
			$report_year = $srvObj->getStatReportByYear($row['year'], $datefrom, $dateto);
			$totalyear = $srvObj->count;
			
			$pdf->Cell(20,4,'',"",0,'L');
			$pdf->Cell(45,4,'GROUP',"TBLR",0,'L');
			$pdf->Cell(50,4,'TOTAL NUMBER OF REQUESTS',"LRTB",0,'C');
			$pdf->Cell(65,4,'NUMBER OF REQUESTS WITHOUT RESULTS',"LRTB",0,'C');
			$pdf->Cell(60,4,'NUMBER OF REQUESTS WITH RESULTS',"LRTB",0,'C');
			
			$pdf->Ln($space*4);
			if ($totalyear){
				while ($row_yr=$report_year->FetchRow()){
					$pdf->SetFont('Arial','B',8);
					$month = $srvObj->getMonth($row_yr['month']);
					$pdf->Cell(10,4,'',"",0,'L');
					$pdf->Cell(20,4,strtoupper($month),"",0,'L');
					$pdf->Cell(5,4,'',"",1,'L');	
					
					$report_mnth = $srvObj->getStatReportByMonth($row['year'], $row_yr['month'], $fromdate, $todate);
					$totalmnth = $srvObj->count;
					if ($totalmnth){
						while ($row_mnth=$report_mnth->FetchRow()){
							$pdf->SetFont('Arial','B',8);
							$pdf->Cell(20,4,'',"",0,'L');
							#$pdf->Cell(45,4,strtoupper($row_mnth['grp_name']),"LRTB",0,'L');
							$pdf->Cell(45,4,strtoupper($row_mnth['grp_name']),"LRTB",0,'L');
							$pdf->Cell(50,4,$row_mnth['stat'].'  ',"LRTB",0,'R');
							
							#without result
							$report_grp_wo = $srvObj->getStatByResult($row['year'], $row_yr['month'], $fromdate, $todate, $row_mnth['grp_code'], 0);
							if ($report_grp_wo['stat_result'])
								$stat_wo_result = $report_grp_wo['stat_result'];
							else	
								$stat_wo_result = 0;
								
							$pdf->Cell(65,4,$stat_wo_result.'  ',"LRTB",0,'R');
							#with result
							$report_grp_w = $srvObj->getStatByResult($row['year'], $row_yr['month'], $fromdate, $todate, $row_mnth['grp_code'], 1);
							if ($report_grp_w['stat_result'])
								$stat_w_result = $report_grp_w['stat_result'];
							else	
								$stat_w_result = 0;
								
							$pdf->Cell(60,4,$stat_w_result.'  ',"LRTB",0,'R');
							$pdf->Cell(5,4,'',"",1,'L');
						}
					}
					$pdf->Ln($space*2);
				}
			}
			$pdf->Ln($space*2);
		}	

	}else{
		$pdf->SetFont('Times','',10);	
		$pdf->Ln($space*4);
		$pdf->Cell(337,4,'No query results available at this time...',"",0,'C');
	}
	
	$pdf->Output();	
?>