<?php
	include("roots.php");
	require('./roots.php');
	
	#include_once($root_path."/classes/fpdf/fpdf.php");
	include_once($root_path."/classes/fpdf/pdf.class.php");
	require_once($root_path.'include/inc_environment_global.php');
	include_once($root_path.'include/inc_date_format_functions.php');
	
	#require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
	#$srvObj=new SegLab;
	require_once($root_path.'include/care_api_classes/class_oproom.php');
	$oproom_Obj = new OPRoom;
	
	require_once($root_path.'include/care_api_classes/billing/class_ops.php');
	$srvObj=new SegOps;
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
	
	$pdf->Image($root_path.'gui/img/logos/dmc_logo.jpg',80,10,20,20);
	
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
	$pdf->Cell(0,4,$row['hosp_addr1'],$borderNo,$newLineYes,'C');
   $pdf->Ln(2);
	$pdf->SetFont("Times","B","10");
	$pdf->Cell(0,4,'SURGICAL DEPARTMENT',$borderNo,$newLineYes,'C');
	$pdf->Ln(2);
	$pdf->SetFont("Times","I","10");
	$pdf->Cell(0,4,'SURGICAL PROCEDURE STATISTICS REPORT',$borderNo,$newLineYes,'C');
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
	
	$report_info = $srvObj->getStatReport($datefrom, $dateto);
	#echo "<br>".$srvObj->sql;
	$totalcount = $srvObj->count;
	#echo "total = ".$totalcount;
	if ($totalcount){
		while ($row=$report_info->FetchRow()){
			$pdf->SetFont('Arial','B',8);
					
			$report_year = $srvObj->getStatReportByYear($row['year'], $datefrom, $dateto);
			$report_year2 = $srvObj->getStatReportByYear($row['year'], $datefrom, $dateto);
			#echo "<br>".$srvObj->sql;
			$totalyear = $srvObj->count;
			$pdf->Ln($space*4);
			
			if ($totalyear){
				$pdf->Cell(70,4,'',"",0,'L');
				
				$buf = array();
				$buf_er_wo = array();
				$buf_opd_wo = array();
				#$buf_in_wo = array();
				$buf_ipd_wo = array();
				
				$buf_er_w = array();
				$buf_opd_w = array();
				#$buf_in_w = array();
				$buf_ipd_w = array();
				
				$i=0;
				while ($row_yr2=$report_year2->FetchRow()){
					$pdf->SetFont('Arial','B',8);
					#echo "<br>".$row_yr2['month'];
					$month = $srvObj->getMonth($row_yr2['month']);
					$pdf->Cell(30,4,strtoupper($month)." , ".$row['year'],"TBLR",0,'C');
					
					$buf[$row_yr2['year']][] = $row_yr2['month'];
					$i++;
				}
				
				$report_dept = $oproom_Obj->AllORInfo();
				$totaldept = $srvObj->count;
				
				while($row_yr=$report_year->FetchRow()){
					if ($totaldept){
							
							while ($row_dept=$report_dept->FetchRow()){
								$pdf->SetFont('Arial','B',10);
								$pdf->Ln($space*4);
								$pdf->Cell(20,4,'',"",0,'L');
								$pdf->Cell(45,4,strtoupper($row_dept['deptname']),"",0,'L');
								$pdf->Ln(2);
								$pdf->SetFont('Arial','',8);
								$pdf->Cell(5,4,'',"",1,'L');
								$pdf->Cell(25,4,'',"",0,'L');
								$pdf->Cell(50,4,'Number of Requests',"",1,'L');
								$pdf->Cell(25,4,'',"",0,'L');
								$pdf->Cell(45,4,'ER ',"TBLR",0,'R');
								
								$enctype = '1';
								for ($i=0; $i<$totalyear;$i++){
									
									$er_stat_wo = $srvObj->getStatByResultEncType($row_yr['year'], $buf[$row_yr['year']][$i], $fromdate, $todate, $row_dept['dept_nr'], $enctype, 0);
									#echo "<br>sql = ".$srvObj->sql;
									if ($er_stat_wo['stat_result'])
										$er_wo = $er_stat_wo['stat_result'];
									else
										$er_wo = 0;
									
									$buf_er_wo[$i] = $er_wo;
									$pdf->Cell(30,4,$er_wo,"TBLR",0,'R');
								}
								
								$pdf->Ln(4);
								$pdf->Cell(25,4,'',"",0,'L');
								$pdf->Cell(45,4,'OPD ',"TBLR",0,'R');
								
								$enctype = '2';
								for ($i=0; $i<$totalyear;$i++){
									
									$opd_stat_wo = $srvObj->getStatByResultEncType($row_yr['year'], $buf[$row_yr['year']][$i], $fromdate, $todate, $row_dept['dept_nr'], $enctype, 0);
									if ($opd_stat_wo['stat_result'])
										$opd_wo = $opd_stat_wo['stat_result'];
									else
										$opd_wo = 0;
									
									$buf_opd_wo[$i] = $opd_wo;
									$pdf->Cell(30,4,$opd_wo,"TBLR",0,'R');
								}
								/*
								$pdf->Ln(4);
								$pdf->Cell(25,4,'',"",0,'L');
								$pdf->Cell(45,4,'Industrial ',"TBLR",0,'R');
								
								$enctype = '0';
								for ($i=0; $i<$totalyear;$i++){
									$in_stat_wo = $srvObj->getStatByResultEncType($row_yr['year'], $buf[$row_yr['year']][$i], $fromdate, $todate, $row_dept['dept_nr'], $enctype, 0);
									if ($in_stat_wo['stat_result'])
										$in_wo = $in_stat_wo['stat_result'];
									else
										$in_wo = 0;
									
									$buf_in_wo[$i] = $in_wo;
									$pdf->Cell(30,4,$in_wo,"TBLR",0,'R');
								}
								*/
								$pdf->Ln(4);
								$pdf->Cell(25,4,'',"",0,'L');
								$pdf->Cell(45,4,'In-patient ',"TBLR",0,'R');
								
								$enctype = '3,4';
								for ($i=0; $i<$totalyear;$i++){
									
									$ipd_stat_wo = $srvObj->getStatByResultEncType($row_yr['year'], $buf[$row_yr['year']][$i], $fromdate, $todate, $row_dept['dept_nr'], $enctype, 0);
									if ($ipd_stat_wo['stat_result'])
										$ipd_wo = $ipd_stat_wo['stat_result'];
									else
										$ipd_wo = 0;
									
									$buf_ipd_wo[$i] = $ipd_wo;
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
								$pdf->Cell(50,4,'Number of Operations Done',"",1,'L');
								$pdf->Cell(25,4,'',"",0,'L');
								$pdf->Cell(45,4,'ER ',"TBLR",0,'R');
								
								$enctype = '1';
								for ($i=0; $i<$totalyear;$i++){
									
									$er_stat_w = $srvObj->getStatByResultEncType($row_yr['year'], $buf[$row_yr['year']][$i], $fromdate, $todate, $row_dept['dept_nr'], $enctype, 1);
									#echo "<br>sql = ".$srvObj->sql;
									if ($er_stat_w['stat_result'])
										$er_w = $er_stat_w['stat_result'];
									else
										$er_w = 0;
									
									$buf_er_w[$i] = $er_w;
									$pdf->Cell(30,4,$er_w,"TBLR",0,'R');
								}
								
								$pdf->Ln(4);
								$pdf->Cell(25,4,'',"",0,'L');
								$pdf->Cell(45,4,'OPD ',"TBLR",0,'R');
								
								$enctype = '2';
								for ($i=0; $i<$totalyear;$i++){
									
									$opd_stat_w = $srvObj->getStatByResultEncType($row_yr['year'], $buf[$row_yr['year']][$i], $fromdate, $todate, $row_dept['dept_nr'], $enctype, 1);
									if ($opd_stat_w['stat_result'])
										$opd_w = $opd_stat_w['stat_result'];
									else
										$opd_w = 0;
									
									$buf_opd_w[$i] = $opd_w;
									$pdf->Cell(30,4,$opd_w,"TBLR",0,'R');
								}
								/*
								$pdf->Ln(4);
								$pdf->Cell(25,4,'',"",0,'L');
								$pdf->Cell(45,4,'Industrial ',"TBLR",0,'R');
								
								$enctype = '0';
								for ($i=0; $i<$totalyear;$i++){
									$in_stat_w = $srvObj->getStatByResultEncType($row_yr['year'], $buf[$row_yr['year']][$i], $fromdate, $todate, $row_dept['dept_nr'], $enctype, 1);
									if ($in_stat_w['stat_result'])
										$in_w = $in_stat_w['stat_result'];
									else
										$in_w = 0;
									
									$buf_in_w[$i] = $in_w;
									$pdf->Cell(30,4,$in_w,"TBLR",0,'R');
								}
								*/
								$pdf->Ln(4);
								$pdf->Cell(25,4,'',"",0,'L');
								$pdf->Cell(45,4,'In-patient ',"TBLR",0,'R');
								
								$enctype = '3,4';
								for ($i=0; $i<$totalyear;$i++){
									
									$ipd_stat_w = $srvObj->getStatByResultEncType($row_yr['year'], $buf[$row_yr['year']][$i], $fromdate, $todate, $row_dept['dept_nr'], $enctype, 1);
									if ($ipd_stat_w['stat_result'])
										$ipd_w = $ipd_stat_w['stat_result'];
									else
										$ipd_w = 0;
									
									$buf_ipd_w[$i] = $ipd_w;
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
			$pdf->SetFont('Arial','B',10);
			$pdf->Ln($space*4);
			$pdf->Cell(20,4,'',"",0,'L');
			$pdf->Cell(45,4,'Average No. of Persons/day : ',"",0,'R');
			$pdf->Cell(10,4,'',"",0,'R');
		}	
	}else{
		$pdf->SetFont('Times','',10);	
		$pdf->Ln($space*4);
		$pdf->Cell(337,4,'No query results available at this time...',"",0,'C');
	}
	
	$pdf->Output();	
?>