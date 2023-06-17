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
   $pdf->Cell(0,4,'Quezon Ave., Digos City',$borderNo,$newLineYes,'C');
   $pdf->Ln(2);
	$pdf->SetFont("Times","B","10");
	$pdf->Cell(0,4,'DEPARTMENT OF RADIOLOGICAL & IMAGING SCIENCES',$borderNo,$newLineYes,'C');
	$pdf->Ln(2);
	$pdf->SetFont("Times","I","10");
	$pdf->Cell(0,4,'RADIOLOGICAL STATISTICS REPORT',$borderNo,$newLineYes,'C');
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
	$report_info = $radio_Obj->getStatReport($datefrom, $dateto);
	#echo "<br>".$radio_Obj->sql;
	$totalcount = $radio_Obj->count;
	
	if ($totalcount){
		while ($row=$report_info->FetchRow()){
			$pdf->SetFont('Arial','B',8);
					
			#$pdf->Cell(10,4,'YEAR : '.$row['year'],"",0,'L');
			#$pdf->Cell(5,4,'',"",1,'L');
			$report_year = $radio_Obj->getStatReportByYear($row['year'], $datefrom, $dateto);
			$report_year2 = $radio_Obj->getStatReportByYear($row['year'], $datefrom, $dateto);
			#echo "<br>".$radio_Obj->sql;
			$totalyear = $radio_Obj->count;
			/*
			$pdf->Cell(20,4,'',"",0,'L');
			$pdf->Cell(45,4,'GROUP',"TBLR",0,'L');
			$pdf->Cell(50,4,'TOTAL NUMBER OF REQUESTS',"LRTB",0,'C');
			$pdf->Cell(65,4,'NUMBER OF REQUESTS WITHOUT RESULTS',"LRTB",0,'C');
			$pdf->Cell(60,4,'NUMBER OF REQUESTS WITH RESULTS',"LRTB",0,'C');
			*/
			$pdf->Ln($space*4);
			
			
			
			if ($totalyear){
				$pdf->Cell(70,4,'',"",0,'L');
				
				while ($row_yr2=$report_year2->FetchRow()){
					$pdf->SetFont('Arial','B',8);
					$month = $radio_Obj->getMonth($row_yr2['month']);
					$pdf->Cell(30,4,strtoupper($month)." ,".$row['year'],"TBLR",0,'C');
					#$pdf->Cell(5,4,'',"",1,'L');	
					
				}
				
				while ($row_yr=$report_year->FetchRow()){
					$pdf->SetFont('Arial','B',8);
					$month = $radio_Obj->getMonth($row_yr['month']);
					#$pdf->Cell(10,4,'',"",0,'L');
					#$pdf->Cell(20,4,strtoupper($month)." ,".$row['year'],"",0,'L');
					#$pdf->Cell(5,4,'',"",1,'L');	
					$pdf->Ln($space*4);
					$report_mnth = $radio_Obj->getStatReportByMonth($row['year'], $row_yr['month'], $fromdate, $todate);
					#echo "<br>".$radio_Obj->sql;
					$totalmnth = $radio_Obj->count;
					if ($totalmnth){
						while ($row_mnth=$report_mnth->FetchRow()){
							$pdf->SetFont('Arial','B',10);
							$pdf->Cell(20,4,'',"",0,'L');
							$pdf->Cell(45,4,strtoupper($row_mnth['grp_name']),"",0,'L');
							
							$pdf->SetFont('Arial','',8);
							$pdf->Cell(5,4,'',"",1,'L');
							$pdf->Cell(25,4,'',"",0,'L');
							$pdf->Cell(50,4,'Number of Examinations',"",1,'L');
							
							$pdf->Cell(25,4,'',"",0,'L');
							$enctype = '1';
							$er_stat_wo = $radio_Obj->getStatByResultEncType($row['year'], $row_yr['month'], $fromdate, $todate, $row_mnth['grp_code'], $enctype, 0);
							
							if ($er_stat_wo['stat_result'])
								$er_wo = $er_stat_wo['stat_result'];
							else
								$er_wo = 0;
								
							$pdf->Cell(45,4,'ER ',"TBLR",0,'R');
							$pdf->Cell(30,4,$er_wo,"TBLR",0,'R');
							$pdf->Cell(30,4,$er_wo,"TBLR",1,'R');
							
							$pdf->Cell(25,4,'',"",0,'L');
							$enctype = '2';
							$opd_stat_wo = $radio_Obj->getStatByResultEncType($row['year'], $row_yr['month'], $fromdate, $todate, $row_mnth['grp_code'], $enctype, 0);
							
							if ($opd_stat_wo['stat_result'])
								$opd_wo = $opd_stat_wo['stat_result'];
							else
								$opd_wo = 0;
							
							$pdf->Cell(45,4,'OPD ',"TBLR",0,'R');
							$pdf->Cell(30,4,$opd_wo,"TBLR",0,'R');
							$pdf->Cell(30,4,$opd_wo,"TBLR",1,'R');
							
							$pdf->Cell(25,4,'',"",0,'L');
							$enctype = '0';
							$in_stat_wo = $radio_Obj->getStatByResultEncType($row['year'], $row_yr['month'], $fromdate, $todate, $row_mnth['grp_code'], $enctype, 0);
							
							if ($in_stat_wo['stat_result'])
								$in_wo = $in_stat_wo['stat_result'];
							else
								$in_wo = 0;
							
							$pdf->Cell(45,4,'Industrial ',"TBLR",0,'R');
							$pdf->Cell(30,4,$in_wo,"TBLR",0,'R');
							$pdf->Cell(30,4,$in_wo,"TBLR",1,'R');
							
							$pdf->Cell(25,4,'',"",0,'L');
							$enctype = '3,4';
							$ipd_stat_wo = $radio_Obj->getStatByResultEncType($row['year'], $row_yr['month'], $fromdate, $todate, $row_mnth['grp_code'], $enctype, 0);
							
							if ($ipd_stat_wo['stat_result'])
								$ipd_wo = $ipd_stat_wo['stat_result'];
							else
								$ipd_wo = 0;
							
							$pdf->Cell(45,4,'In-patient ',"TBLR",0,'R');
							$pdf->Cell(30,4,$ipd_wo,"TBLR",0,'R');
							$pdf->Cell(30,4,$ipd_wo,"TBLR",1,'R');
							
							$total_wo = $er_wo + $opd_wo + $in_wo + $ipd_wo;
							
							$pdf->Cell(25,4,'',"",0,'L');
							$pdf->SetFont('Arial','B',8);
							$pdf->Cell(45,4,'Total ',"TBLR",0,'R');
							$pdf->Cell(30,4,$total_wo,"TBLR",0,'R');
							$pdf->Cell(30,4,$total_wo,"TBLR",1,'R');
							
							$pdf->Cell(5,4,'',"",1,'L');
							$pdf->Cell(25,4,'',"",0,'L');
							$pdf->Cell(50,4,'Number of Patients served',"",1,'L');
							
							$enctype = '1';
							$er_stat_w = $radio_Obj->getStatByResultEncType($row['year'], $row_yr['month'], $fromdate, $todate, $row_mnth['grp_code'], $enctype, 1);
							
							if ($er_stat_w['stat_result'])
								$er_w = $er_stat_w['stat_result'];
							else
								$er_w = 0;
							
							$pdf->Cell(25,4,'',"",0,'L');
							$pdf->Cell(45,4,'ER ',"TBLR",0,'R');
							$pdf->Cell(30,4,$er_w,"TBLR",0,'R');
							$pdf->Cell(30,4,$er_w,"TBLR",1,'R');
							
							$enctype = '2';
							$opd_stat_w = $radio_Obj->getStatByResultEncType($row['year'], $row_yr['month'], $fromdate, $todate, $row_mnth['grp_code'], $enctype, 1);
							
							if ($opd_stat_w['stat_result'])
								$opd_w = $opd_stat_w['stat_result'];
							else
								$opd_w = 0;
							
							$pdf->Cell(25,4,'',"",0,'L');
							$pdf->Cell(45,4,'OPD ',"TBLR",0,'R');
							$pdf->Cell(30,4,$opd_w,"TBLR",0,'R');
							$pdf->Cell(30,4,$opd_w,"TBLR",1,'R');
							
							$enctype = '0';
							$in_stat_w = $radio_Obj->getStatByResultEncType($row['year'], $row_yr['month'], $fromdate, $todate, $row_mnth['grp_code'], $enctype, 1);
							if ($in_stat_w['stat_result'])
								$in_w = $in_stat_w['stat_result'];
							else
								$in_w = 0;
								
							$pdf->Cell(25,4,'',"",0,'L');
							$pdf->Cell(45,4,'Industrial ',"TBLR",0,'R');
							$pdf->Cell(30,4,$in_w,"TBLR",0,'R');
							$pdf->Cell(30,4,$in_w,"TBLR",1,'R');
							
							$enctype = '3,4';
							$ipd_stat_w = $radio_Obj->getStatByResultEncType($row['year'], $row_yr['month'], $fromdate, $todate, $row_mnth['grp_code'], $enctype, 1);
							
							if ($ipd_stat_w['stat_result'])
								$ipd_w = $ipd_stat_w['stat_result'];
							else
								$ipd_w = 0;
								
							$pdf->Cell(25,4,'',"",0,'L');
							$pdf->Cell(45,4,'In-patient ',"TBLR",0,'R');
							$pdf->Cell(30,4,$ipd_w,"TBLR",0,'R');
							$pdf->Cell(30,4,$ipd_w,"TBLR",1,'R');
							
							$total_w = $er_w + $opd_w + $in_w + $ipd_w;
							
							$pdf->Cell(25,4,'',"",0,'L');
							$pdf->SetFont('Arial','B',8);
							$pdf->Cell(45,4,'Total ',"TBLR",0,'R');
							$pdf->Cell(30,4,$total_w,"TBLR",0,'R');
							$pdf->Cell(30,4,$total_w,"TBLR",1,'R');
							
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