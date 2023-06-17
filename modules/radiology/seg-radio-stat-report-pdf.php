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
	
	$pdf = new PDF("L",'mm','Letter');
	$pdf->AliasNbPages();   #--added
	$pdf->AddPage("L");
		
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
	$report_info = $radio_Obj->getStatReport($datefrom, $dateto);
	#echo "<br>".$radio_Obj->sql;
	$totalcount = $radio_Obj->count;
	#echo "total = ".$totalcount;
	if ($totalcount){
		while ($row=$report_info->FetchRow()){
			$pdf->SetFont('Arial','B',8);
					
			$report_year = $radio_Obj->getStatReportByYear($row['year'], $datefrom, $dateto);
			$report_year2 = $radio_Obj->getStatReportByYear($row['year'], $datefrom, $dateto);
			
			$report_year3 = $radio_Obj->getStatReportByYear($row['year'], $datefrom, $dateto);
			
			$report_year4 = $radio_Obj->getStatReportByYear($row['year'], $datefrom, $dateto);
			$report_year5 = $radio_Obj->getStatReportByYear($row['year'], $datefrom, $dateto);
			
			$report_year6 = $radio_Obj->getStatReportByYear($row['year'], $datefrom, $dateto);
			$report_year7 = $radio_Obj->getStatReportByYear($row['year'], $datefrom, $dateto);
			
			$report_year8 = $radio_Obj->getStatReportByYear($row['year'], $datefrom, $dateto);
			$report_year9 = $radio_Obj->getStatReportByYear($row['year'], $datefrom, $dateto);
			
			$report_year10 = $radio_Obj->getStatReportByYear($row['year'], $datefrom, $dateto);
			$report_year11 = $radio_Obj->getStatReportByYear($row['year'], $datefrom, $dateto);
			#echo "<br>".$radio_Obj->sql;
			$totalyear = $radio_Obj->count;
			$pdf->Ln($space*4);
			
			if ($totalyear){
				$pdf->Cell(70,4,'',"",0,'L');
				
				$buf = array();
				$buf_er_wo = array();
				$buf_opd_wo = array();
				$buf_in_wo = array();
				$buf_ipd_wo = array();
				
				$buf_er_w = array();
				$buf_opd_w = array();
				$buf_in_w = array();
				$buf_ipd_w = array();
				
				$i=0;
				while ($row_yr2=$report_year2->FetchRow()){
					$pdf->SetFont('Arial','B',8);
					$month = $radio_Obj->getMonth($row_yr2['month']);
					$pdf->Cell(30,4,strtoupper($month)." , ".$row['year'],"TBLR",0,'C');
					
					#echo "<br><br>total = ".$totalyear;
					#echo "<br>year = ".$row_yr2['year'];
					#echo "<br>monthe = ".$row_yr2['month'];
					
					#$buf[$i]['year'] = $row_yr2['year'];
					#$buf[$i]['month'] = $row_yr2['month'];
					$buf[$row_yr2['year']][] = $row_yr2['month'];
					$i++;
				}
				
				#$pdf->Ln($space*4);
				#$report_group = $radio_Obj->getRadioServiceGroups("status NOT IN('deleted','hidden','inactive','void')");
				$report_group = $radio_Obj->getRadioServiceGroups();
				$totalgrp = $radio_Obj->count;
				
				while($row_yr=$report_year->FetchRow()){
					#echo "<br><br>total = ".$totalyear;
					#echo "<br>year = ".$row_yr['year'];
					#echo "<br>monthe = ".$row_yr['month'];
					#$pdf->Cell(30,4,"Hello = ".$row_yr['year'].' , '.$row_yr['month'],"TBLR",1,'R');
					#$report_mnth = $radio_Obj->getStatReportByMonth($row['year'], $row_yr['month'], $fromdate, $todate);
					if ($totalgrp){
							while ($row_mnth=$report_group->FetchRow()){
								$pdf->SetFont('Arial','B',10);
								$pdf->Ln($space*4);
								$pdf->Cell(20,4,'',"",0,'L');
								$pdf->Cell(45,4,strtoupper($row_mnth['name']),"",0,'L');
								$pdf->Ln(2);
								$pdf->SetFont('Arial','',8);
								$pdf->Cell(5,4,'',"",1,'L');
								$pdf->Cell(25,4,'',"",0,'L');
								$pdf->Cell(50,4,'Number of Examinations',"",1,'L');
								$pdf->Cell(25,4,'',"",0,'L');
								$pdf->Cell(45,4,'ER ',"TBLR",0,'R');
								
								$enctype = '1';
								for ($i=0; $i<$totalyear;$i++){
									$er_stat_wo = $radio_Obj->getStatByResultEncType($row_yr['year'], $buf[$row_yr['year']][$i], $fromdate, $todate, $row_mnth['group_code'], $enctype, 0);
									if ($er_stat_wo['stat_result'])
										$er_wo = $er_stat_wo['stat_result'];
									else
										$er_wo = 0;
									
									$buf_er_wo[$i] = $er_wo;
									$total_er_wo[$i] = $total_er_wo[$i] + $buf_er_wo[$i];
									$pdf->Cell(30,4,$er_wo,"TBLR",0,'R');
								}
								
								$pdf->Ln(4);
								$pdf->Cell(25,4,'',"",0,'L');
								$pdf->Cell(45,4,'OPD ',"TBLR",0,'R');
								
								$enctype = '2';
								for ($i=0; $i<$totalyear;$i++){
									$opd_stat_wo = $radio_Obj->getStatByResultEncType($row_yr['year'], $buf[$row_yr['year']][$i], $fromdate, $todate, $row_mnth['group_code'], $enctype, 0);
									if ($opd_stat_wo['stat_result'])
										$opd_wo = $opd_stat_wo['stat_result'];
									else
										$opd_wo = 0;
									
									$buf_opd_wo[$i] = $opd_wo;
									$total_opd_wo[$i] = $total_opd_wo[$i] + $buf_opd_wo[$i];
									$pdf->Cell(30,4,$opd_wo,"TBLR",0,'R');
								}
								
								$pdf->Ln(4);
								$pdf->Cell(25,4,'',"",0,'L');
								$pdf->Cell(45,4,'Industrial ',"TBLR",0,'R');
								
								$enctype = '0';
								for ($i=0; $i<$totalyear;$i++){
									$in_stat_wo = $radio_Obj->getStatByResultEncType($row_yr['year'], $buf[$row_yr['year']][$i], $fromdate, $todate, $row_mnth['group_code'], $enctype, 0);
									if ($in_stat_wo['stat_result'])
										$in_wo = $in_stat_wo['stat_result'];
									else
										$in_wo = 0;
									
									$buf_in_wo[$i] = $in_wo;
									$total_in_wo[$i] = $total_in_wo[$i] + $buf_in_wo[$i];
									$pdf->Cell(30,4,$in_wo,"TBLR",0,'R');
								}
								
								$pdf->Ln(4);
								$pdf->Cell(25,4,'',"",0,'L');
								$pdf->Cell(45,4,'In-patient ',"TBLR",0,'R');
								
								$enctype = '3,4';
								for ($i=0; $i<$totalyear;$i++){
									$ipd_stat_wo = $radio_Obj->getStatByResultEncType($row_yr['year'], $buf[$row_yr['year']][$i], $fromdate, $todate, $row_mnth['group_code'], $enctype, 0);
									if ($ipd_stat_wo['stat_result'])
										$ipd_wo = $ipd_stat_wo['stat_result'];
									else
										$ipd_wo = 0;
									
									$buf_ipd_wo[$i] = $ipd_wo;
									$total_ipd_wo[$i] = $total_ipd_wo[$i] + $buf_ipd_wo[$i];
									$pdf->Cell(30,4,$ipd_wo,"TBLR",0,'R');
								}
								
								$pdf->Ln(4);
								$pdf->Cell(25,4,'',"",0,'L');
								$pdf->Cell(45,4,'Total ',"TBLR",0,'R');
								
								for ($i=0; $i<$totalyear;$i++){
									$total_wo = $buf_er_wo[$i] + $buf_opd_wo[$i] + $buf_in_wo[$i] + $buf_ipd_wo[$i];
									$pdf->Cell(30,4,$total_wo,"TBLR",0,'R');
								}
								
								#$pdf->Cell(25,4,'',"",0,'L');
								$pdf->Ln(4);
								$pdf->Cell(5,4,'',"",1,'L');
								$pdf->Cell(25,4,'',"",0,'L');
								$pdf->Cell(50,4,'Number of Patients served',"",1,'L');
								$pdf->Cell(25,4,'',"",0,'L');
								$pdf->Cell(45,4,'ER ',"TBLR",0,'R');
								
								$enctype = '1';
								for ($i=0; $i<$totalyear;$i++){
									$er_stat_w = $radio_Obj->getStatByResultEncType($row_yr['year'], $buf[$row_yr['year']][$i], $fromdate, $todate, $row_mnth['group_code'], $enctype, 1);
									if ($er_stat_w['stat_result'])
										$er_w = $er_stat_w['stat_result'];
									else
										$er_w = 0;
									
									$buf_er_w[$i] = $er_w;
									$total_er_w[$i] = $total_er_w[$i] + $buf_er_w[$i];
									$pdf->Cell(30,4,$er_w,"TBLR",0,'R');
								}
								
								$pdf->Ln(4);
								$pdf->Cell(25,4,'',"",0,'L');
								$pdf->Cell(45,4,'OPD ',"TBLR",0,'R');
								
								$enctype = '2';
								for ($i=0; $i<$totalyear;$i++){
									$opd_stat_w = $radio_Obj->getStatByResultEncType($row_yr['year'], $buf[$row_yr['year']][$i], $fromdate, $todate, $row_mnth['group_code'], $enctype, 1);
									if ($opd_stat_w['stat_result'])
										$opd_w = $opd_stat_w['stat_result'];
									else
										$opd_w = 0;
									
									$buf_opd_w[$i] = $opd_w;
									$total_opd_w[$i] = $total_opd_w[$i] + $buf_opd_w[$i];
									$pdf->Cell(30,4,$opd_w,"TBLR",0,'R');
								}
								
								$pdf->Ln(4);
								$pdf->Cell(25,4,'',"",0,'L');
								$pdf->Cell(45,4,'Industrial ',"TBLR",0,'R');
								
								$enctype = '0';
								for ($i=0; $i<$totalyear;$i++){
									$in_stat_w = $radio_Obj->getStatByResultEncType($row_yr['year'], $buf[$row_yr['year']][$i], $fromdate, $todate, $row_mnth['group_code'], $enctype, 1);
									if ($in_stat_w['stat_result'])
										$in_w = $in_stat_w['stat_result'];
									else
										$in_w = 0;
									
									$buf_in_w[$i] = $in_w;
									$total_in_w[$i] = $total_in_w[$i] + $buf_in_w[$i];
									$pdf->Cell(30,4,$in_w,"TBLR",0,'R');
								}
								
								$pdf->Ln(4);
								$pdf->Cell(25,4,'',"",0,'L');
								$pdf->Cell(45,4,'In-patient ',"TBLR",0,'R');
								
								$enctype = '3,4';
								for ($i=0; $i<$totalyear;$i++){
									$ipd_stat_w = $radio_Obj->getStatByResultEncType($row_yr['year'], $buf[$row_yr['year']][$i], $fromdate, $todate, $row_mnth['group_code'], $enctype, 1);
									if ($ipd_stat_w['stat_result'])
										$ipd_w = $ipd_stat_w['stat_result'];
									else
										$ipd_w = 0;
									
									$buf_ipd_w[$i] = $ipd_w;
									$total_ipd_w[$i] = $total_ipd_w[$i] + $buf_ipd_w[$i];
									$pdf->Cell(30,4,$ipd_w,"TBLR",0,'R');
								}
								
								$pdf->Ln(4);
								$pdf->Cell(25,4,'',"",0,'L');
								$pdf->Cell(45,4,'Total ',"TBLR",0,'R');
								
								for ($i=0; $i<$totalyear;$i++){
									$total_w = $buf_er_w[$i] + $buf_opd_w[$i] + $buf_in_w[$i] + $buf_ipd_w[$i];
									$pdf->Cell(30,4,$total_w,"TBLR",0,'R');
								}
							}
					}
				}
			}
			/*
			$pdf->SetFont('Arial','B',10);
			$pdf->Ln($space*4);
			$pdf->Cell(20,4,'',"",0,'L');
			$pdf->Cell(45,4,'Average No. of Persons/day : ',"",0,'R');
			$pdf->Cell(10,4,'',"",0,'R');
			*/
			$pdf->SetFont('Arial','',10);
			$pdf->Ln($space*4);
			$pdf->Cell(20,4,'',"",0,'L');
			$pdf->Cell(50,4,'Average No. of Persons/day : ',"1",0,'L');
			
			while ($row=$report_year3->FetchRow()){
				$totalinfo = $radio_Obj->getStatByYearMonth($row['year'], $row['month']);
				#echo "<br>".$radio_Obj->sql;
				$pdf->SetFont('Arial','',10);	
				$pdf->Cell(15,4,$totalinfo['totalpat']."/30 : ","LBT",0,'L');
				$pdf->SetFont('Arial','B',10);
				$pdf->Cell(15,4,round($totalinfo['totalpat']/30),"RBT",0,'R');
			}
			#$buf_er_wo[$i] + $buf_opd_wo[$i] + $buf_in_wo[$i] + $buf_ipd_wo[$i];
			#ER PATIENT	
			$pdf->Ln($space*2);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(20,4,'',"",0,'L');
			$pdf->Cell(50,4,'Total ERPx Not Served : ',"1",0,'L');
			#print_r($total_er_wo);
			$cnt = 0;
			while ($row=$report_year4->FetchRow()){
				#$totalinfo = $radio_Obj->getStatByYearMonth($row['year'], $row['month']);
				$pdf->SetFont('Arial','B',10);
				$pdf->Cell(30,4,$total_er_wo[$cnt],"LRBT",0,'R');
				$cnt++;
			}	
			
			$pdf->Ln($space*2);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(20,4,'',"",0,'L');
			$pdf->Cell(50,4,'Total ERPx Served : ',"1",0,'L');
			$cnt = 0;
			while ($row=$report_year5->FetchRow()){
				#$totalinfo = $radio_Obj->getStatByYearMonth($row['year'], $row['month']);
				$pdf->SetFont('Arial','B',10);
				$pdf->Cell(30,4,$total_er_w[$cnt],"LRBT",0,'R');
				$cnt++;
			}	
			
			#OPD PATIENT	
			$pdf->Ln($space*2);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(20,4,'',"",0,'L');
			$pdf->Cell(50,4,'Total OPDPx Not Served : ',"1",0,'L');
			$cnt = 0;
			while ($row=$report_year6->FetchRow()){
				#$totalinfo = $radio_Obj->getStatByYearMonth($row['year'], $row['month']);
				$pdf->SetFont('Arial','B',10);
				$pdf->Cell(30,4,$total_opd_wo[$cnt],"LRBT",0,'R');
				$cnt++;
			}	
			
			$pdf->Ln($space*2);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(20,4,'',"",0,'L');
			$pdf->Cell(50,4,'Total OPDPx Served : ',"1",0,'L');
			$cnt = 0;
			while ($row=$report_year7->FetchRow()){
				#$totalinfo = $radio_Obj->getStatByYearMonth($row['year'], $row['month']);
				$pdf->SetFont('Arial','B',10);
				$pdf->Cell(30,4,$total_opd_w[$cnt],"LRBT",0,'R');
				$cnt++;
			}	
			
			#IPD PATIENT	
			$pdf->Ln($space*2);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(20,4,'',"",0,'L');
			$pdf->Cell(50,4,'Total IPDPx Not Served : ',"1",0,'L');
			$cnt = 0;
			while ($row=$report_year8->FetchRow()){
				#$totalinfo = $radio_Obj->getStatByYearMonth($row['year'], $row['month']);
				$pdf->SetFont('Arial','B',10);
				$pdf->Cell(30,4,$total_ipd_wo[$cnt],"LRBT",0,'R');
				$cnt++;
			}	
			
			$pdf->Ln($space*2);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(20,4,'',"",0,'L');
			$pdf->Cell(50,4,'Total IPDPx Served : ',"1",0,'L');
			$cnt = 0;
			while ($row=$report_year9->FetchRow()){
				#$totalinfo = $radio_Obj->getStatByYearMonth($row['year'], $row['month']);
				$pdf->SetFont('Arial','B',10);
				$pdf->Cell(30,4,$total_ipd_w[$cnt],"LRBT",0,'R');
				$cnt++;
			}	
			
			#IC PATIENT	
			$pdf->Ln($space*2);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(20,4,'',"",0,'L');
			$pdf->Cell(50,4,'Total ICPx Not Served : ',"1",0,'L');
			$cnt = 0;
			while ($row=$report_year10->FetchRow()){
				#$totalinfo = $radio_Obj->getStatByYearMonth($row['year'], $row['month']);
				$pdf->SetFont('Arial','B',10);
				$pdf->Cell(30,4,$total_in_wo[$cnt],"LRBT",0,'R');
				$cnt++;
			}	
			
			$pdf->Ln($space*2);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(20,4,'',"",0,'L');
			$pdf->Cell(50,4,'Total ICPx Served : ',"1",0,'L');
			$cnt = 0;
			while ($row=$report_year11->FetchRow()){
				#$totalinfo = $radio_Obj->getStatByYearMonth($row['year'], $row['month']);
				$pdf->SetFont('Arial','B',10);
				$pdf->Cell(30,4,$total_in_w[$cnt],"LRBT",0,'R');
				$cnt++;
			}
		}	
	}else{
		$pdf->SetFont('Times','',10);	
		$pdf->Ln($space*4);
		$pdf->Cell(250,4,'No query results available at this time...',"",0,'C');
	}
	
	$pdf->Output();	
?>