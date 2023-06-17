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
	$pdf->AddPage("L","Legal");

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
//	echo "</p>".$radio_Obj->sql."</p>";
	#echo "<br>".$radio_Obj->sql;
	$totalcount = $radio_Obj->count;
	#echo "total = ".$totalcount;
	if ($totalcount){
		while ($row=$report_info->FetchRow()){
			$pdf->SetFont('Arial','B',8);
				#echo "report year=".$row['year']."<br>";
			$report_year = $radio_Obj->getStatReportByYear($row['year'], $datefrom, $dateto);
//			echo "</p>".$radio_Obj->sql."</p>";
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
			#echo "</p> STAT REPORT BETWEEN THE DATE SQL.. <br>".$radio_Obj->sql."</p>";

			$totalyear = $radio_Obj->count;
			#echo $totalyear."</p>";

			$pdf->Ln($space*4);
//			die("here");

			if ($totalyear){
				$pdf->Cell(50,4,'',"",0,'L');

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

					$month = $radio_Obj->getMonth($row_yr2['month']);
					$row_yr=$row_yr2['year'];
					$buf[$row_yr2['year']][] = $row_yr2['month'];
					$i++;
				}
				$pdf->SetFont('Arial','B',8);

				//added by cha 07-13-09
				$pdf->Cell(50,4,'1st qrt.','TBLR',0,'C');
				$pdf->Cell(50,4,'2nd qrt.','TBLR',0,'C');
				$pdf->Cell(50,4,'3rd qrt.','TBLR',0,'C');
				$pdf->Cell(50,4,'4th qrt.','TBLR',0,'C');
				$pdf->Ln(4);
				$pdf->Cell(50,4,'',"",0,'L');
				$pdf->Cell(12.5,4,'Jan','TBLR',0,'C');
				$pdf->Cell(12.5,4,'Feb','TBLR',0,'C');
				$pdf->Cell(12.5,4,'Mar','TBLR',0,'C');
				$pdf->Cell(12.5,4,'Total','TBLR',0,'C');
				$pdf->Cell(12.5,4,'Apr','TBLR',0,'C');
				$pdf->Cell(12.5,4,'May','TBLR',0,'C');
				$pdf->Cell(12.5,4,'Jun','TBLR',0,'C');
				$pdf->Cell(12.5,4,'Total','TBLR',0,'C');
				$pdf->Cell(12.5,4,'Jul','TBLR',0,'C');
				$pdf->Cell(12.5,4,'Aug','TBLR',0,'C');
				$pdf->Cell(12.5,4,'Sep','TBLR',0,'C');
				$pdf->Cell(12.5,4,'Total','TBLR',0,'C');
				$pdf->Cell(12.5,4,'Oct','TBLR',0,'C');
				$pdf->Cell(12.5,4,'Nov','TBLR',0,'C');
				$pdf->Cell(12.5,4,'Dec','TBLR',0,'C');
				$pdf->Cell(12.5,4,'Total','TBLR',0,'C');

				$report_dept = $radio_Obj->getMainRadioDepartment();
				#echo "report_group=".$report_group;
				#echo "</p>FOR DEPT. SQL..<br> ".$radio_Obj->sql."</p>";

				$totaldept = $radio_Obj->count;
				#echo "<br>totalgrp=".$totalgrp;
				for($i=0;$i<16;$i++)
				{ $total_er_w_ar[$i]=0;
					$total_opd_w_ar[$i]=0;
					$total_in_w_ar[$i]=0;
					$total_ipd_w_ar[$i]=0;

					$total_er_wo[$i]=0;
					$total_opd_wo[$i]=0;
					$total_in_wo[$i]=0;
					$total_ipd_wo[$i]=0;

					$total_er_w[$i]=0;
					$total_opd_w[$i]=0;
					$total_in_w[$i]=0;
					$total_ipd_w[$i]=0; }
				while($row_yr=$report_year->FetchRow())
				{
						if($totaldept)
						{
							while ($row_dept=$report_dept->FetchRow())
							{
								$pdf->SetFont('Arial','B',10);
								$pdf->Ln($space*4);
								$pdf->Cell(1,4,'',"",0,'L');
								$pdf->Cell(25,4,strtoupper($row_dept['name_formal']),"",0,'L');
								$pdf->Ln(2);

								$report_group = $radio_Obj->getRadioServiceGroupsbyDept($row_dept['nr']);
								#echo $radio_Obj->sql."</p>";
								#echo "</p> SERVICES BY DEPT. SQL... <br>".$radio_Obj->sql."</p>";
								#die("here");
								#die("here");
								#echo"<br>department=".$row_dept['name_formal'];

								for($i=0;$i<16;$i++)
								{
									$er_wo_ar[$i]=0;
									$opd_wo_ar[$i]=0;
									$in_wo_ar[$i]=0;
									$ipd_wo_ar[$i]=0;
									$total_wo_ar[$i]=0;

									$er_w_ar[$i]=0;
									$opd_w_ar[$i]=0;
									$in_w_ar[$i]=0;
									$ipd_w_ar[$i]=0;
									$total_w_ar[$i]=0;

									$total_er_wo_ar[$i]=0;
									$total_opd_wo_ar[$i]=0;
									$total_in_wo_ar[$i]=0;
									$total_ipd_wo_ar[$i]=0;
								}

								while($row_mnth=$report_group->FetchRow())
								{

										#echo"<br>group=".$row_mnth['name'];
										#echo "<br>Number of Patients to be served<br>";
										//number of patients to be served
										$enctype = '1';
										$buf_er_wo=array();
										$er_wo=0;
										for ($i=0; $i<$totalyear;$i++)
										{
											$er_stat_wo = $radio_Obj->getStatByResultEncType($row_yr['year'], $buf[$row_yr['year']][$i], $datefrom, $dateto, $row_mnth['group_code'], $enctype, 0);
											if ($er_stat_wo['stat_result'])
												$er_wo = $er_stat_wo['stat_result'];
											else
												$er_wo = 0;

											$buf_er_wo[$i] = $er_wo;
											#$total_er_wo[$i] = $total_er_wo[$i] + $buf_er_wo[$i];
											#switch($buf[$row_yr['year']][$i])
											#echo "</p>".print_r($buf[$row_yr['month']]);
											#echo "</p>Examination SQL... <br>".$radio_Obj->sql."</p>";
											#die("</p>here");
											switch($buf[$row_yr['year']][$i])
											{
												case 1: $er_wo_ar[0]+=$er_wo; $total_er_wo_ar[0]+=$buf_er_wo[$i]; break;
												case 2: $er_wo_ar[1]+=$er_wo; $total_er_wo_ar[1]+=$buf_er_wo[$i]; break;
												case 3: $er_wo_ar[2]+=$er_wo; $total_er_wo_ar[2]+=$buf_er_wo[$i]; break;
												case 4: $er_wo_ar[4]+=$er_wo; $total_er_wo_ar[4]+=$buf_er_wo[$i]; break;
												case 5: $er_wo_ar[5]+=$er_wo; $total_er_wo_ar[5]+=$buf_er_wo[$i]; break;
												case 6: $er_wo_ar[6]+=$er_wo; $total_er_wo_ar[6]+=$buf_er_wo[$i]; break;
												case 7: $er_wo_ar[8]+=$er_wo; $total_er_wo_ar[8]+=$buf_er_wo[$i]; break;
												case 8: $er_wo_ar[9]+=$er_wo; $total_er_wo_ar[9]+=$buf_er_wo[$i]; break;
												case 9: $er_wo_ar[10]+=$er_wo; $total_er_wo_ar[10]+=$buf_er_wo[$i]; break;
												case 10: $er_wo_ar[12]+=$er_wo; $total_er_wo_ar[12]+=$buf_er_wo[$i]; break;
												case 11: $er_wo_ar[13]+=$er_wo; $total_er_wo_ar[13]+=$buf_er_wo[$i]; break;
												case 12: $er_wo_ar[14]+=$er_wo; $total_er_wo_ar[14]+=$buf_er_wo[$i]; break;
											}

										}
										$er_wo_ar[3]=($er_wo_ar[0]+$er_wo_ar[1]+$er_wo_ar[2]);
										$er_wo_ar[7]=($er_wo_ar[4]+$er_wo_ar[5]+$er_wo_ar[6]);
										$er_wo_ar[11]=($er_wo_ar[8]+$er_wo_ar[9]+$er_wo_ar[10]);
										$er_wo_ar[15]=($er_wo_ar[12]+$er_wo_ar[13]+$er_wo_ar[14]);
										$total_er_wo_ar[3]=($total_er_wo_ar[0]+$total_er_wo_ar[1]+$total_er_wo_ar[2]);
										$total_er_wo_ar[7]=($total_er_wo_ar[4]+$total_er_wo_ar[5]+$total_er_wo_ar[6]);
										$total_er_wo_ar[11]=($total_er_wo_ar[8]+$total_er_wo_ar[9]+$total_er_wo_ar[10]);
										$total_er_wo_ar[15]=($total_er_wo_ar[12]+$total_er_wo_ar[13]+$total_er_wo_ar[14]);


										$enctype = '2';
										$buf_opd_wo=array();
										$opd_wo=0;
										for ($i=0; $i<$totalyear;$i++){
											$opd_stat_wo = $radio_Obj->getStatByResultEncType($row_yr['year'], $buf[$row_yr['year']][$i], $datefrom, $dateto, $row_mnth['group_code'], $enctype, 0);
											if ($opd_stat_wo['stat_result'])
												$opd_wo = $opd_stat_wo['stat_result'];
											else
												$opd_wo = 0;

											$buf_opd_wo[$i] = $opd_wo;
											#$total_opd_wo[$i] = $total_opd_wo[$i] + $buf_opd_wo[$i];
											switch($buf[$row_yr['year']][$i])
											{
												case 1: $opd_wo_ar[0]+=$opd_wo; $total_opd_wo_ar[0]+=$buf_opd_wo[$i]; break;
												case 2: $opd_wo_ar[1]+=$opd_wo; $total_opd_wo_ar[1]+=$buf_opd_wo[$i]; break;
												case 3: $opd_wo_ar[2]+=$opd_wo; $total_opd_wo_ar[2]+=$buf_opd_wo[$i]; break;
												case 4: $opd_wo_ar[4]+=$opd_wo; $total_opd_wo_ar[4]+=$buf_opd_wo[$i]; break;
												case 5: $opd_wo_ar[5]+=$opd_wo; $total_opd_wo_ar[5]+=$buf_opd_wo[$i]; break;
												case 6: $opd_wo_ar[6]+=$opd_wo; $total_opd_wo_ar[6]+=$buf_opd_wo[$i]; break;
												case 7: $opd_wo_ar[8]+=$opd_wo; $total_opd_wo_ar[8]+=$buf_opd_wo[$i]; break;
												case 8: $opd_wo_ar[9]+=$opd_wo; $total_opd_wo_ar[9]+=$buf_opd_wo[$i]; break;
												case 9: $opd_wo_ar[10]+=$opd_wo; $total_opd_wo_ar[10]+=$buf_opd_wo[$i]; break;
												case 10: $opd_wo_ar[12]+=$opd_wo; $total_opd_wo_ar[12]+=$buf_opd_wo[$i]; break;
												case 11: $opd_wo_ar[13]+=$opd_wo; $total_opd_wo_ar[13]+=$buf_opd_wo[$i]; break;
												case 12: $opd_wo_ar[14]+=$opd_wo; $total_opd_wo_ar[14]+=$buf_opd_wo[$i]; break;
											}
										}
										$opd_wo_ar[3]=($opd_wo_ar[0]+$opd_wo_ar[1]+$opd_wo_ar[2]);
										$opd_wo_ar[7]=($opd_wo_ar[4]+$opd_wo_ar[5]+$opd_wo_ar[6]);
										$opd_wo_ar[11]=($opd_wo_ar[8]+$opd_wo_ar[9]+$opd_wo_ar[10]);
										$opd_wo_ar[15]=($opd_wo_ar[12]+$opd_wo_ar[13]+$opd_wo_ar[14]);
										$total_opd_wo_ar[3]=($total_opd_wo_ar[0]+$total_opd_wo_ar[1]+$total_opd_wo_ar[2]);
										$total_opd_wo_ar[7]=($total_opd_wo_ar[4]+$total_opd_wo_ar[5]+$total_opd_wo_ar[6]);
										$total_opd_wo_ar[11]=($total_opd_wo_ar[8]+$total_opd_wo_ar[9]+$total_opd_wo_ar[10]);
										$total_opd_wo_ar[15]=($total_opd_wo_ar[12]+$total_opd_wo_ar[13]+$total_opd_wo_ar[14]);

										$enctype = '0';
										$buf_in_wo=array();
										$in_wo=0;
										for ($i=0; $i<$totalyear;$i++){
											$in_stat_wo = $radio_Obj->getStatByResultEncType($row_yr['year'], $buf[$row_yr['year']][$i], $datefrom, $dateto, $row_mnth['group_code'], $enctype, 0);
											#echo $radio_Obj->sql."</p>";
											if ($in_stat_wo['stat_result'])
												$in_wo = $in_stat_wo['stat_result'];
											else
												$in_wo = 0;

											$buf_in_wo[$i] = $in_wo;
											$$total_in_wo[$i] = $total_in_wo[$i] + $buf_in_wo[$i];
											switch($buf[$row_yr['year']][$i])
											{
												case 1: $in_wo_ar[0]+=$in_wo; $total_in_wo_ar[0]+=$buf_in_wo[$i]; break;
												case 2: $in_wo_ar[1]+=$in_wo; $total_in_wo_ar[1]+=$buf_in_wo[$i]; break;
												case 3: $in_wo_ar[2]+=$in_wo; $total_in_wo_ar[2]+=$buf_in_wo[$i]; break;
												case 4: $in_wo_ar[4]+=$in_wo; $total_in_wo_ar[4]+=$buf_in_wo[$i]; break;
												case 5: $in_wo_ar[5]+=$in_wo; $total_in_wo_ar[5]+=$buf_in_wo[$i]; break;
												case 6: $in_wo_ar[6]+=$in_wo; $total_in_wo_ar[6]+=$buf_in_wo[$i]; break;
												case 7: $in_wo_ar[8]+=$in_wo; $total_in_wo_ar[8]+=$buf_in_wo[$i]; break;
												case 8: $in_wo_ar[9]+=$in_wo; $total_in_wo_ar[9]+=$buf_in_wo[$i]; break;
												case 9: $in_wo_ar[10]+=$in_wo; $total_in_wo_ar[10]+=$buf_in_wo[$i]; break;
												case 10: $in_wo_ar[12]+=$in_wo; $total_in_wo_ar[12]+=$buf_in_wo[$i]; break;
												case 11: $in_wo_ar[13]+=$in_wo; $total_in_wo_ar[13]+=$buf_in_wo[$i]; break;
												case 12: $in_wo_ar[14]+=$in_wo; $total_in_wo_ar[14]+=$buf_in_wo[$i]; break;
											}
										}
										$in_wo_ar[3]=($in_wo_ar[0]+$in_wo_ar[1]+$in_wo_ar[2]);
										$in_wo_ar[7]=($in_wo_ar[4]+$in_wo_ar[5]+$in_wo_ar[6]);
										$in_wo_ar[11]=($in_wo_ar[8]+$in_wo_ar[9]+$in_wo_ar[10]);
										$in_wo_ar[15]=($in_wo_ar[12]+$in_wo_ar[13]+$in_wo_ar[14]);
										$total_in_wo_ar[3]=($total_in_wo_ar[0]+$total_in_wo_ar[1]+$total_in_wo_ar[2]);
										$total_in_wo_ar[7]=($total_in_wo_ar[4]+$total_in_wo_ar[5]+$total_in_wo_ar[6]);
										$total_in_wo_ar[11]=($total_in_wo_ar[8]+$total_in_wo_ar[9]+$total_in_wo_ar[10]);
										$total_in_wo_ar[15]=($total_in_wo_ar[12]+$total_in_wo_ar[13]+$total_in_wo_ar[14]);

										$enctype = '3,4';
										$buf_ipd_wo=array();
										$ipd_wo=0;
										for ($i=0; $i<$totalyear;$i++){
											$ipd_stat_wo = $radio_Obj->getStatByResultEncType($row_yr['year'], $buf[$row_yr['year']][$i], $datefrom, $dateto, $row_mnth['group_code'], $enctype, 0);
											if ($ipd_stat_wo['stat_result'])
												$ipd_wo = $ipd_stat_wo['stat_result'];
											else
												$ipd_wo = 0;

											$buf_ipd_wo[$i] = $ipd_wo;
											#$total_ipd_wo[$i] = $total_ipd_wo[$i] + $buf_ipd_wo[$i];
											switch($buf[$row_yr['year']][$i])
											{
												case 1: $ipd_wo_ar[0]+=$ipd_wo; $total_ipd_wo_ar[0]+=$buf_ipd_wo[$i]; break;
												case 2: $ipd_wo_ar[1]+=$ipd_wo; $total_ipd_wo_ar[1]+=$buf_ipd_wo[$i]; break;
												case 3: $ipd_wo_ar[2]+=$ipd_wo; $total_ipd_wo_ar[2]+=$buf_ipd_wo[$i]; break;
												case 4: $ipd_wo_ar[4]+=$ipd_wo; $total_ipd_wo_ar[4]+=$buf_ipd_wo[$i]; break;
												case 5: $ipd_wo_ar[5]+=$ipd_wo; $total_ipd_wo_ar[5]+=$buf_ipd_wo[$i]; break;
												case 6: $ipd_wo_ar[6]+=$ipd_wo; $total_ipd_wo_ar[6]+=$buf_ipd_wo[$i]; break;
												case 7: $ipd_wo_ar[8]+=$ipd_wo; $total_ipd_wo_ar[8]+=$buf_ipd_wo[$i]; break;
												case 8: $ipd_wo_ar[9]+=$ipd_wo; $total_ipd_wo_ar[9]+=$buf_ipd_wo[$i]; break;
												case 9: $ipd_wo_ar[10]+=$ipd_wo; $total_ipd_wo_ar[10]+=$buf_ipd_wo[$i]; break;
												case 10: $ipd_wo_ar[12]+=$ipd_wo; $total_ipd_wo_ar[12]+=$buf_ipd_wo[$i]; break;
												case 11: $ipd_wo_ar[13]+=$ipd_wo; $total_ipd_wo_ar[13]+=$buf_ipd_wo[$i]; break;
												case 12: $ipd_wo_ar[14]+=$ipd_wo; $total_ipd_wo_ar[14]+=$buf_ipd_wo[$i]; break;
											}
										}
										$ipd_wo_ar[3]=($ipd_wo_ar[0]+$ipd_wo_ar[1]+$ipd_wo_ar[2]);
										$ipd_wo_ar[7]=($ipd_wo_ar[4]+$ipd_wo_ar[5]+$ipd_wo_ar[6]);
										$ipd_wo_ar[11]=($ipd_wo_ar[8]+$ipd_wo_ar[9]+$ipd_wo_ar[10]);
										$ipd_wo_ar[15]=($ipd_wo_ar[12]+$ipd_wo_ar[13]+$ipd_wo_ar[14]);
										$total_ipd_wo_ar[3]=($total_ipd_wo_ar[0]+$total_ipd_wo_ar[1]+$total_ipd_wo_ar[2]);
										$total_ipd_wo_ar[7]=($total_ipd_wo_ar[4]+$total_ipd_wo_ar[5]+$total_ipd_wo_ar[6]);
										$total_ipd_wo_ar[11]=($total_ipd_wo_ar[8]+$total_ipd_wo_ar[9]+$total_ipd_wo_ar[10]);
										$total_ipd_wo_ar[15]=($total_ipd_wo_ar[12]+$total_ipd_wo_ar[13]+$total_ipd_wo_ar[14]);

										for ($i=0; $i<$totalyear;$i++){
											$total_wo = $buf_er_wo[$i] + $buf_opd_wo[$i] + $buf_in_wo[$i] + $buf_ipd_wo[$i];
										}

										for($i=0;$i<16;$i++)
										{
											$total_wo_ar[$i]=($total_er_wo_ar[$i]+$total_opd_wo_ar[$i]+$total_in_wo_ar[$i]+$total_ipd_wo_ar[$i]);
										}

										//number of patients served
										$enctype = '1';
										$buf_er_w=array();
										$er_w=0;
										for ($i=0; $i<$totalyear;$i++){
											$er_stat_w = $radio_Obj->getStatByResultEncType($row_yr['year'], $buf[$row_yr['year']][$i], $datefrom, $dateto, $row_mnth['group_code'], $enctype, 1);
											#echo "</p>"."sql angelo ..".$radio_Obj->sql."</p>";
											if ($er_stat_w['stat_result'])
												$er_w = $er_stat_w['stat_result'];
											else
												$er_w = 0;

											$buf_er_w[$i] = $er_w;
											#$total_er_w[$i] = $total_er_w[$i] + $buf_er_w[$i];
											switch($buf[$row_yr['year']][$i])
											{
												case 1: $er_w_ar[0]+=$er_w; $total_er_w_ar[0]+=$buf_er_w[$i]; break;
												case 2: $er_w_ar[1]+=$er_w; $total_er_w_ar[1]+=$buf_er_w[$i]; break;
												case 3: $er_w_ar[2]+=$er_w; $total_er_w_ar[2]+=$buf_er_w[$i]; break;
												case 4: $er_w_ar[4]+=$er_w; $total_er_w_ar[4]+=$buf_er_w[$i]; break;
												case 5: $er_w_ar[5]+=$er_w; $total_er_w_ar[5]+=$buf_er_w[$i]; break;
												case 6: $er_w_ar[6]+=$er_w; $total_er_w_ar[6]+=$buf_er_w[$i]; break;
												case 7: $er_w_ar[8]+=$er_w; $total_er_w_ar[8]+=$buf_er_w[$i]; break;
												case 8: $er_w_ar[9]+=$er_w; $total_er_w_ar[9]+=$buf_er_w[$i]; break;
												case 9: $er_w_ar[10]+=$er_w; $total_er_w_ar[10]+=$buf_er_w[$i]; break;
												case 10: $er_w_ar[12]+=$er_w; $total_er_w_ar[12]+=$buf_er_w[$i]; break;
												case 11: $er_w_ar[13]+=$er_w; $total_er_w_ar[13]+=$buf_er_w[$i]; break;
												case 12: $er_w_ar[14]+=$er_w; $total_er_w_ar[14]+=$buf_er_w[$i]; break;
											}
										}
										$er_w_ar[3]=($er_w_ar[0]+$er_w_ar[1]+$er_w_ar[2]);
										$er_w_ar[7]=($er_w_ar[4]+$er_w_ar[5]+$er_w_ar[6]);
										$er_w_ar[11]=($er_w_ar[8]+$er_w_ar[9]+$er_w_ar[10]);
										$er_w_ar[15]=($er_w_ar[12]+$er_w_ar[13]+$er_w_ar[14]);
										$total_er_w_ar[3]=($total_er_w_ar[0]+$total_er_w_ar[1]+$total_er_w_ar[2]);
										$total_er_w_ar[7]=($total_er_w_ar[4]+$total_er_w_ar[5]+$total_er_w_ar[6]);
										$total_er_w_ar[11]=($total_er_w_ar[8]+$total_er_w_ar[9]+$total_er_w_ar[10]);
										$total_er_w_ar[15]=($total_er_w_ar[12]+$total_er_w_ar[13]+$total_er_w_ar[14]);


										$enctype = '2';
										$buf_opd_w=array();
										$opd_w=0;
										for ($i=0; $i<$totalyear;$i++){
											$opd_stat_w = $radio_Obj->getStatByResultEncType($row_yr['year'], $buf[$row_yr['year']][$i], $datefrom, $dateto, $row_mnth['group_code'], $enctype, 1);
											if ($opd_stat_w['stat_result'])
												$opd_w = $opd_stat_w['stat_result'];
											else
												$opd_w = 0;

											$buf_opd_w[$i] = $opd_w;

											#$total_opd_w[$i] = $total_opd_w[$i] + $buf_opd_w[$i];
											switch($buf[$row_yr['year']][$i])
											{
												case 1: $opd_w_ar[0]+=$opd_w; $total_opd_w_ar[0]+=$buf_opd_w[$i]; break;
												case 2: $opd_w_ar[1]+=$opd_w; $total_opd_w_ar[1]+=$buf_opd_w[$i]; break;
												case 3: $opd_w_ar[2]+=$opd_w; $total_opd_w_ar[2]+=$buf_opd_w[$i]; break;
												case 4: $opd_w_ar[4]+=$opd_w; $total_opd_w_ar[4]+=$buf_opd_w[$i]; break;
												case 5: $opd_w_ar[5]+=$opd_w; $total_opd_w_ar[5]+=$buf_opd_w[$i]; break;
												case 6: $opd_w_ar[6]+=$opd_w; $total_opd_w_ar[6]+=$buf_opd_w[$i]; break;
												case 7: $opd_w_ar[8]+=$opd_w; $total_opd_w_ar[8]+=$buf_opd_w[$i]; break;
												case 8: $opd_w_ar[9]+=$opd_w; $total_opd_w_ar[9]+=$buf_opd_w[$i]; break;
												case 9: $opd_w_ar[10]+=$opd_w; $total_opd_w_ar[10]+=$buf_opd_w[$i]; break;
												case 10: $opd_w_ar[12]+=$opd_w; $total_opd_w_ar[12]+=$buf_opd_w[$i]; break;
												case 11: $opd_w_ar[13]+=$opd_w; $total_opd_w_ar[13]+=$buf_opd_w[$i]; break;
												case 12: $opd_w_ar[14]+=$opd_w; $total_opd_w_ar[14]+=$buf_opd_w[$i]; break;
											}
										}
										$opd_w_ar[3]=($opd_w_ar[0]+$opd_w_ar[1]+$opd_w_ar[2]);
										$opd_w_ar[7]=($opd_w_ar[4]+$opd_w_ar[5]+$opd_w_ar[6]);

										$opd_w_ar[11]=($opd_w_ar[8]+$opd_w_ar[9]+$opd_w_ar[10]);
										$opd_w_ar[15]=($opd_w_ar[12]+$opd_w_ar[13]+$opd_w_ar[14]);
										$total_opd_w_ar[3]=($total_opd_w_ar[0]+$total_opd_w_ar[1]+$total_opd_w_ar[2]);
										$total_opd_w_ar[7]=($total_opd_w_ar[4]+$total_opd_w_ar[5]+$total_opd_w_ar[6]);
										$total_opd_w_ar[11]=($total_opd_w_ar[8]+$total_opd_w_ar[9]+$total_opd_w_ar[10]);
										$total_opd_w_ar[15]=($total_opd_w_ar[12]+$total_opd_w_ar[13]+$total_opd_w_ar[14]);
										#echo $radio_Obj->sql;


										$enctype = '0';
										$buf_in_w=array();
										$in_w=0;
										for ($i=0; $i<$totalyear;$i++){
											$in_stat_w = $radio_Obj->getStatByResultEncType($row_yr['year'], $buf[$row_yr['year']][$i], $datefrom, $dateto, $row_mnth['group_code'], $enctype, 1);
											if ($in_stat_w['stat_result'])
												$in_w = $in_stat_w['stat_result'];
											else
												$in_w = 0;

											$buf_in_w[$i] = $in_w;
											#$total_in_w[$i] = $total_in_w[$i] + $buf_in_w[$i];
											switch($buf[$row_yr['year']][$i])
											{
												case 1: $in_w_ar[0]+=$in_w; $total_in_w_ar[0]+=$buf_in_w[$i]; break;
												case 2: $in_w_ar[1]+=$in_w; $total_in_w_ar[1]+=$buf_in_w[$i]; break;
												case 3: $in_w_ar[2]+=$in_w; $total_in_w_ar[2]+=$buf_in_w[$i]; break;
												case 4: $in_w_ar[4]+=$in_w; $total_in_w_ar[4]+=$buf_in_w[$i]; break;
												case 5: $in_w_ar[5]+=$in_w; $total_in_w_ar[5]+=$buf_in_w[$i]; break;
												case 6: $in_w_ar[6]+=$in_w; $total_in_w_ar[6]+=$buf_in_w[$i]; break;
												case 7: $in_w_ar[8]+=$in_w; $total_in_w_ar[8]+=$buf_in_w[$i]; break;
												case 8: $in_w_ar[9]+=$in_w; $total_in_w_ar[9]+=$buf_in_w[$i]; break;
												case 9: $in_w_ar[10]+=$in_w; $total_in_w_ar[10]+=$buf_in_w[$i]; break;
												case 10: $in_w_ar[12]+=$in_w; $total_in_w_ar[12]+=$buf_in_w[$i]; break;
												case 11: $in_w_ar[13]+=$in_w; $total_in_w_ar[13]+=$buf_in_w[$i]; break;
												case 12: $in_w_ar[14]+=$in_w; $total_in_w_ar[14]+=$buf_in_w[$i]; break;
											}
										}
										$in_w_ar[3]=($in_w_ar[0]+$in_w_ar[1]+$in_w_ar[2]);
										$in_w_ar[7]=($in_w_ar[4]+$in_w_ar[5]+$in_w_ar[6]);
										$in_w_ar[11]=($in_w_ar[8]+$in_w_ar[9]+$in_w_ar[10]);
										$in_w_ar[15]=($in_w_ar[12]+$in_w_ar[13]+$in_w_ar[14]);
										$total_in_w_ar[3]=($total_in_w_ar[0]+$total_in_w_ar[1]+$total_in_w_ar[2]);
										$total_in_w_ar[7]=($total_in_w_ar[4]+$total_in_w_ar[5]+$total_in_w_ar[6]);
										$total_in_w_ar[11]=($total_in_w_ar[8]+$total_in_w_ar[9]+$total_in_w_ar[10]);
										$total_in_w_ar[15]=($total_in_w_ar[12]+$total_in_w_ar[13]+$total_in_w_ar[14]);

										$enctype = '3,4';
										$buf_ipd_w=array();
										$ipd_w=0;
										for ($i=0; $i<$totalyear;$i++){
											$ipd_stat_w = $radio_Obj->getStatByResultEncType($row_yr['year'], $buf[$row_yr['year']][$i], $datefrom, $dateto, $row_mnth['group_code'], $enctype, 1);
											if ($ipd_stat_w['stat_result'])
												$ipd_w = $ipd_stat_w['stat_result'];
											else
												$ipd_w = 0;

											$buf_ipd_w[$i] = $ipd_w;
											#$total_ipd_w[$i] = $total_ipd_w[$i] + $buf_ipd_w[$i];
											switch($buf[$row_yr['year']][$i])
											{
												case 1: $ipd_w_ar[0]+=$ipd_w; $total_ipd_w_ar[0]+=$buf_ipd_w[$i]; break;
												case 2: $ipd_w_ar[1]+=$ipd_w; $total_ipd_w_ar[1]+=$buf_ipd_w[$i]; break;
												case 3: $ipd_w_ar[2]+=$ipd_w; $total_ipd_w_ar[2]+=$buf_ipd_w[$i]; break;
												case 4: $ipd_w_ar[4]+=$ipd_w; $total_ipd_w_ar[4]+=$buf_ipd_w[$i]; break;
												case 5: $ipd_w_ar[5]+=$ipd_w; $total_ipd_w_ar[5]+=$buf_ipd_w[$i]; break;
												case 6: $ipd_w_ar[6]+=$ipd_w; $total_ipd_w_ar[6]+=$buf_ipd_w[$i]; break;
												case 7: $ipd_w_ar[8]+=$ipd_w; $total_ipd_w_ar[8]+=$buf_ipd_w[$i]; break;
												case 8: $ipd_w_ar[9]+=$ipd_w; $total_ipd_w_ar[9]+=$buf_ipd_w[$i]; break;
												case 9: $ipd_w_ar[10]+=$ipd_w; $total_ipd_w_ar[10]+=$buf_ipd_w[$i]; break;
												case 10: $ipd_w_ar[12]+=$ipd_w; $total_ipd_w_ar[12]+=$buf_ipd_w[$i]; break;
												case 11: $ipd_w_ar[13]+=$ipd_w; $total_ipd_w_ar[13]+=$buf_ipd_w[$i]; break;
												case 12: $ipd_w_ar[14]+=$ipd_w; $total_ipd_w_ar[14]+=$buf_ipd_w[$i]; break;
											}
										}
										$ipd_w_ar[3]=($ipd_w_ar[0]+$ipd_w_ar[1]+$ipd_w_ar[2]);
										$ipd_w_ar[7]=($ipd_w_ar[4]+$ipd_w_ar[5]+$ipd_w_ar[6]);
										$ipd_w_ar[11]=($ipd_w_ar[8]+$ipd_w_ar[9]+$ipd_w_ar[10]);
										$ipd_w_ar[15]=($ipd_w_ar[12]+$ipd_w_ar[13]+$ipd_w_ar[14]);
										$total_ipd_w_ar[3]=($total_ipd_w_ar[0]+$total_ipd_w_ar[1]+$total_ipd_w_ar[2]);
										$total_ipd_w_ar[7]=($total_ipd_w_ar[4]+$total_ipd_w_ar[5]+$total_ipd_w_ar[6]);
										$total_ipd_w_ar[11]=($total_ipd_w_ar[8]+$total_ipd_w_ar[9]+$total_ipd_w_ar[10]);
										$total_ipd_w_ar[15]=($total_ipd_w_ar[12]+$total_ipd_w_ar[13]+$total_ipd_w_ar[14]);


										for ($i=0; $i<$totalyear;$i++){
											$total_w = $buf_er_w[$i] + $buf_opd_w[$i] + $buf_in_w[$i] + $buf_ipd_w[$i];
										}

										for($i=0;$i<16;$i++)
										{
											$total_w_ar[$i]=($total_er_w_ar[$i]+$total_opd_w_ar[$i]+$total_in_w_ar[$i]+$total_ipd_w_ar[$i]);
										}
								}

										$pdf->Ln(2);
										$pdf->SetFont('Arial','',8);
										$pdf->Cell(5,4,'',"",1,'L');
										$pdf->Cell(5,4,'',"",0,'L');
										$pdf->Cell(50,4,'Number of Examinations',"",1,'L');
										$pdf->Cell(5,4,'',"",0,'L');
										$pdf->Cell(45,4,'ER ',"TBLR",0,'R');
										#$pdf->Cell(12.5,4,$er_wo,"TBLR",0,'R');  //loop
										for($i=0;$i<16;$i++)
										{
											 $pdf->Cell(12.5,4,$total_er_wo_ar[$i],"TBLR",0,'R');
											 $total_er_wo[$i]+=$total_er_wo_ar[$i];
											 #echo "<br>".$total_er_wo[$i];
										}

										$pdf->Ln(4);
										$pdf->Cell(5,4,'',"",0,'L');
										$pdf->Cell(45,4,'OPD ',"TBLR",0,'R');
										#$pdf->Cell(12.5,4,$opd_wo,"TBLR",0,'R');
										for($i=0;$i<16;$i++)
										{
											 $pdf->Cell(12.5,4,$total_opd_wo_ar[$i],"TBLR",0,'R');
											 $total_opd_wo[$i]+=$total_opd_wo_ar[$i];
											 #echo "<br>".$total_opd_wo[$i];
										}

										$pdf->Ln(4);
										$pdf->Cell(5,4,'',"",0,'L');
										$pdf->Cell(45,4,'Industrial ',"TBLR",0,'R');
										#$pdf->Cell(12.5,4,$in_wo,"TBLR",0,'R');
										for($i=0;$i<16;$i++)
										{
											 $pdf->Cell(12.5,4,$total_in_wo_ar[$i],"TBLR",0,'R');
											 $total_in_wo[$i]+=$total_in_wo_ar[$i];
											# echo "<br>".$total_in_wo[$i];
										}

										$pdf->Ln(4);
										$pdf->Cell(5,4,'',"",0,'L');
										$pdf->Cell(45,4,'In-patient ',"TBLR",0,'R');
										#$pdf->Cell(12.5,4,$ipd_wo,"TBLR",0,'R');
										for($i=0;$i<16;$i++)
										{
											 $pdf->Cell(12.5,4,$total_ipd_wo_ar[$i],"TBLR",0,'R');
											 $total_ipd_wo[$i]+=$total_ipd_wo_ar[$i];
											 #echo "<br>".$total_ipd_wo[$i];
										}

										$pdf->Ln(4);
										$pdf->Cell(5,4,'',"",0,'L');
										$pdf->Cell(45,4,'Total ',"TBLR",0,'R');
										#$pdf->Cell(12.5,4,$total_wo,"TBLR",0,'R');
										for($i=0;$i<16;$i++)
										{
											 $pdf->Cell(12.5,4,$total_wo_ar[$i],"TBLR",0,'R');
											 #echo "<br>".$er_wo_ar[$i];
										}

										$pdf->Ln(4);
										$pdf->Cell(5,4,'',"",1,'L');
										$pdf->Cell(5,4,'',"",0,'L');
										$pdf->Cell(50,4,'Number of Patients served',"",1,'L');
										$pdf->Cell(5,4,'',"",0,'L');
										$pdf->Cell(45,4,'ER ',"TBLR",0,'R');
										#$pdf->Cell(12.5,4,$er_w,"TBLR",0,'R');
										for($i=0;$i<16;$i++)
										{
											 $pdf->Cell(12.5,4,$total_er_w_ar[$i],"TBLR",0,'R');
											 $total_er_w[$i]+=$total_er_w_ar[$i];
											 #echo "<br>".$total_er_w[$i];
										}

										$pdf->Ln(4);
										$pdf->Cell(5,4,'',"",0,'L');
										$pdf->Cell(45,4,'OPD ',"TBLR",0,'R');
										#$pdf->Cell(12.5,4,$opd_w,"TBLR",0,'R');

										for($i=0;$i<16;$i++)
										{
											 $pdf->Cell(12.5,4,$total_opd_w_ar[$i],"TBLR",0,'R');
											 $total_opd_w[$i]+=$total_opd_w_ar[$i];
											 #echo "<br>".$total_opd_w[$i];
										}



										$pdf->Ln(4);
										$pdf->Cell(5,4,'',"",0,'L');
										$pdf->Cell(45,4,'Industrial ',"TBLR",0,'R');
										#$pdf->Cell(12.5,4,$in_w,"TBLR",0,'R');
										for($i=0;$i<16;$i++)
										{
											 $pdf->Cell(12.5,4,$total_in_w_ar[$i],"TBLR",0,'R');
											 $total_in_w[$i]+=$total_in_w_ar[$i];
											 #echo "<br>".$total_in_w[$i];
										}

										$pdf->Ln(4);
										$pdf->Cell(5,4,'',"",0,'L');
										$pdf->Cell(45,4,'In-patient ',"TBLR",0,'R');
										#$pdf->Cell(12.5,4,$ipd_w,"TBLR",0,'R');
										for($i=0;$i<16;$i++)
										{
											 $pdf->Cell(12.5,4,$total_ipd_w_ar[$i],"TBLR",0,'R');
											 $total_ipd_w[$i]+=$total_ipd_w_ar[$i];
											 #echo "<br>".$total_opd_w[$i];
										}

										$pdf->Ln(4);
										$pdf->Cell(5,4,'',"",0,'L');
										$pdf->Cell(45,4,'Total ',"TBLR",0,'R');
										#$pdf->Cell(12.5,4,$total_w,"TBLR",0,'R');
										for($i=0;$i<16;$i++)
										{
											 $pdf->Cell(12.5,4,$total_w_ar[$i],"TBLR",0,'R');
											 #echo "<br>".$er_wo_ar[$i];
										}
										#echo print_r($buf_opd_w);
										#die("here");
										for($i=0;$i<16;$i++)
										{ $total_er_w_ar[$i]=0;
											$total_opd_w_ar[$i]=0;
											$total_in_w_ar[$i]=0;
											$total_ipd_w_ar[$i]=0;

											$total_er_w[$i]=0;
											$total_opd_w[$i]=0;
											$total_in_w[$i]=0;
											$total_ipd_w[$i]=0; }
							}
						}
				}
			}

			$pdf->SetFont('Arial','',9);
			$pdf->Ln($space*4);
			$pdf->Cell(5,4,'',"",0,'L');
			$pdf->Cell(45,4,'Average No. of Persons/day : ',"1",0,'L');

			$average_px=array();
			while ($row=$report_year3->FetchRow()){
				$totalinfo = $radio_Obj->getStatByYearMonth($row['year'], $row['month']);
				switch($row['month'])
				{
				case 1: $average_px[0]=$totalinfo['totalpat']; break;
				case 2: $average_px[1]=$totalinfo['totalpat']; break;
				case 3: $average_px[2]=$totalinfo['totalpat']; break;
				case 4: $average_px[4]=$totalinfo['totalpat']; break;
				case 5: $average_px[5]=$totalinfo['totalpat']; break;
				case 6: $average_px[6]=$totalinfo['totalpat']; break;
				case 7: $average_px[8]=$totalinfo['totalpat']; break;
				case 8: $average_px[9]=$totalinfo['totalpat']; break;
				case 9: $average_px[10]=$totalinfo['totalpat']; break;
				case 10: $average_px[12]=$totalinfo['totalpat']; break;
				case 11: $average_px[13]=$totalinfo['totalpat']; break;
				case 12: $average_px[14]=$totalinfo['totalpat']; break;
				}
				$average_px[3]=round(($average_px[0]/30)+($average_px[1]/30)+($average_px[2]/30));
				$average_px[7]=round(($average_px[4]/30)+($average_px[5]/30)+($average_px[6]/30));
				$average_px[11]=round(($average_px[8]/30)+($average_pxr[9]/30)+($average_px[10]/30));
				$average_px[15]=round(($average_px[12]/30)+($average_px[13]/30)+($average_px[14]/30));
			}
			$cnt=0;
			while($cnt<16)
			{
				if($average_px[$cnt])
				{
					$pdf->SetFont('Arial','B',8);
					$pdf->Cell(12.5,4,$average_px[$cnt]."/30 : ".round($avergae_px[$cnt]/30),"LRBT",0,'R');
				}
				else
				{
					$pdf->Cell(12.5, 4, "0","LBRT",0,'R');
				}
				$cnt++;
			}

			#$buf_er_wo[$i] + $buf_opd_wo[$i] + $buf_in_wo[$i] + $buf_ipd_wo[$i];
			#ER PATIENT
			$pdf->Ln($space*2);
			$pdf->SetFont('Arial','',9);
			$pdf->Cell(5,4,'',"",0,'L');
			$pdf->Cell(45,4,'Total ERPx Not Served : ',"1",0,'L');
			#print_r($total_er_wo);
			$cnt = 0;
			while ($cnt<16){
				#$totalinfo = $radio_Obj->getStatByYearMonth($row['year'], $row['month']);
				$pdf->SetFont('Arial','B',10);
				$pdf->Cell(12.5,4,$total_er_wo[$cnt],"LRBT",0,'R');
				$cnt++;
			}

			$pdf->Ln($space*2);
			$pdf->SetFont('Arial','',9);
			$pdf->Cell(5,4,'',"",0,'L');
			$pdf->Cell(45,4,'Total ERPx Served : ',"1",0,'L');
			$cnt = 0;
			while ($cnt<16){
				#$totalinfo = $radio_Obj->getStatByYearMonth($row['year'], $row['month']);
				$pdf->SetFont('Arial','B',10);
				$pdf->Cell(12.5,4,$total_er_w[$cnt],"LRBT",0,'R');
				$cnt++;
			}

			#OPD PATIENT
			$pdf->Ln($space*2);
			$pdf->SetFont('Arial','',9);
			$pdf->Cell(5,4,'',"",0,'L');
			$pdf->Cell(45,4,'Total OPDPx Not Served : ',"1",0,'L');
			$cnt = 0;
			while ($cnt<16){
				#$totalinfo = $radio_Obj->getStatByYearMonth($row['year'], $row['month']);
				$pdf->SetFont('Arial','B',10);
				$pdf->Cell(12.5,4,$total_opd_wo[$cnt],"LRBT",0,'R');
				$cnt++;
			}

			$pdf->Ln($space*2);
			$pdf->SetFont('Arial','',9);
			$pdf->Cell(5,4,'',"",0,'L');
			$pdf->Cell(45,4,'Total OPDPx Served : ',"1",0,'L');
			$cnt = 0;
			while ($cnt<16){
				#$totalinfo = $radio_Obj->getStatByYearMonth($row['year'], $row['month']);
				$pdf->SetFont('Arial','B',10);
				$pdf->Cell(12.5,4,$total_opd_w[$cnt],"LRBT",0,'R');
				$cnt++;
			}

			#IPD PATIENT
			$pdf->Ln($space*2);
			$pdf->SetFont('Arial','',9);
			$pdf->Cell(5,4,'',"",0,'L');
			$pdf->Cell(45,4,'Total IPDPx Not Served : ',"1",0,'L');
			$cnt = 0;
			while ($cnt<16){
				#$totalinfo = $radio_Obj->getStatByYearMonth($row['year'], $row['month']);
				$pdf->SetFont('Arial','B',10);
				$pdf->Cell(12.5,4,$total_ipd_wo[$cnt],"LRBT",0,'R');
				$cnt++;
			}

			$pdf->Ln($space*2);
			$pdf->SetFont('Arial','',9);
			$pdf->Cell(5,4,'',"",0,'L');
			$pdf->Cell(45,4,'Total IPDPx Served : ',"1",0,'L');
			$cnt = 0;
			while ($cnt<16){
				#$totalinfo = $radio_Obj->getStatByYearMonth($row['year'], $row['month']);
				$pdf->SetFont('Arial','B',10);
				$pdf->Cell(12.5,4,$total_ipd_w[$cnt],"LRBT",0,'R');
				$cnt++;
			}

			#IC PATIENT
			$pdf->Ln($space*2);
			$pdf->SetFont('Arial','',9);
			$pdf->Cell(5,4,'',"",0,'L');
			$pdf->Cell(45,4,'Total ICPx Not Served : ',"1",0,'L');
			$cnt = 0;
			while ($cnt<16){
				#$totalinfo = $radio_Obj->getStatByYearMonth($row['year'], $row['month']);
				$pdf->SetFont('Arial','B',10);
				$pdf->Cell(12.5,4,$total_in_wo[$cnt],"LRBT",0,'R');
				$cnt++;
			}

			$pdf->Ln($space*2);
			$pdf->SetFont('Arial','',9);
			$pdf->Cell(5,4,'',"",0,'L');
			$pdf->Cell(45,4,'Total ICPx Served : ',"1",0,'L');
			$cnt = 0;
			while ($cnt<16){
				#$totalinfo = $radio_Obj->getStatByYearMonth($row['year'], $row['month']);
				$pdf->SetFont('Arial','B',10);
				$pdf->Cell(12.5,4,$total_in_w[$cnt],"LRBT",0,'R');
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