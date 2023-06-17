<?php
	//include("roots.php");
	require('./roots.php');

	#include_once($root_path."/classes/fpdf/fpdf.php");

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

	global $db;

	$datefrom = $_GET['fromdate'];
	$dateto = $_GET['todate'];

	$fromtime = $_GET['fromtime'];
	$totime = $_GET['totime'];

	$pat_type = $_GET['pat_type'];

	$grp_kind = $_GET['report_kind'];
	$grp_code = $_GET['report_group'];
	$discountID = $_GET['report_class'];

	$doctor_nr = $_GET['doctor_nr'];

	/*
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
	*/

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


	include_once($root_path."/classes/fpdf/pdf-radio.class.php");
	$pdf = new PDF("L",'mm','Legal');
	$pdf->AliasNbPages();   #--added
	$pdf->AddPage("L");

	$pdf->SetTopMargin(2);
	#$pdf->SetLeftMargin(5);
	#$pdf->SetAutoPageBreak("auto");
	$pdf->SetAutoPageBreak(TRUE);

	$borderYes="1";
	$borderNo="0";
	$newLineYes="1";
	$newLineNo="0";
	$fontsizeInput = 10;
	$space=2;

	$fromtime = '00:00:00';
	$totime = '00:00:00';

	$report_info_grp = $srvObj->getPatientList($datefrom, $dateto, $fromtime, $totime, $grp_kind, $grp_code, $discountID, $enctype,1,$doctor_nr);
	#echo $srvObj->sql;
	$totalcount2 = $srvObj->count;

	$pdf->SetFont("Times","",$fontsizeInput+2);


	$pdf->SetFont('Arial','B',$fontsizeInput-1);
	if ($totalcount2){
		$cntindex=1;
		while ($row=$report_info_grp->FetchRow()){

			$pdf->Cell(10,4,$cntindex,"",0,'L');
			$pdf->Cell(32,5,date("m/d/Y",strtotime(trim($row['request_date'])))." ".date("h:i A",strtotime(trim($row['request_time']))),"",0,'L');
			$pdf->Cell(25,5,trim($row['refno']),"",0,'C');
			#$pdf->Cell(50,5,strtoupper($row['ordername']),"",0,'L');
			$x = $pdf->GetX();
			$y = $pdf->GetY();
			$pdf->SetXY($x, $y);
			$pdf->MultiCell(40, 4, mb_strtoupper($row['ordername']), '', 'L','');
			$pdf->SetXY($x+40, $y);
			$pdf->Cell(20,5,trim($row['pid']),"",0,'L');

			if ($row['date_birth']!='0000-00-00'){
				$bdate = date("m/d/Y",strtotime(trim($row['date_birth'])));
				#$trans = array("years" => "yrs", "year" => "yr");
				#$age = strtr($row['agebydbate'],$trans);
				#$age = $row['agebydbate'];
				#echo "<br>age = ".$age;
			}else{
				$bdate = "unknown";
			}

			$pdf->Cell(20,5,$bdate,"",0,'C');

			#$age = $row['age'];

			 if (stristr($row['age'],'years')){
					$age = substr($row['age'],0,-5);
					$age = floor($age).' y';
				}elseif (stristr($row['age'],'year')){
					$age = substr($row['age'],0,-4);
					$age = floor($age).' y';
				}elseif (stristr($row['age'],'months')){
					$age = substr($row['age'],0,-6);
					$age = floor($age).' m';
				}elseif (stristr($row['age'],'month')){
					$age = substr($row['age'],0,-5);
					$age = floor($age).' m';
				}elseif (stristr($row['age'],'days')){
					$age = substr($row['age'],0,-4);

					if ($age>30){
						$age = $age/30;
						$label = 'm';
					}else
						$label = 'd';

					$age = floor($age).' '.$label;
				}elseif (stristr($row['age'],'day')){
					$age = substr($row['age'],0,-3);
					$age = floor($age).' d';
				}else{
					$age = floor($row['age']).' y';
				}

			$pdf->Cell(12,5,trim($age),"",0,'C');
			$pdf->Cell(10,5,strtoupper($row['sex']),"",0,'C');

			#$z = $pdf->GetY();

			$report_info_details = $srvObj->getPatientListDetails($row['refno']);
			#echo  "<br>".$srvObj->sql;
			$totalcount = $srvObj->count;
			$cnt=1;
			if ($totalcount){
				while ($row2=$report_info_details->FetchRow()){
					#$pdf->Cell(10,5,number_format($row2['qty'],0),"",0,'C');
					#$pdf->Cell(10,5,"1","",0,'C');
					$x = $pdf->GetX();
					$y = $pdf->GetY();
					#$pdf->Cell(65,5,$row2['service_name'],"",0,'L');
					$pdf->SetXY($x, $y);
					$pdf->MultiCell(55, 4, mb_strtoupper($row2['service_name']), '', 'L','');
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
						$pdf->MultiCell(30, 4, $location, '', 'L','');

					$pdf->SetXY($x+100, $y);

					if ($row['is_cash']==0){
							$paid = "Charge";
					}elseif ($row["request_flag"]=='paid'){
							$sql_paid = "SELECT pr.or_no, pr.ref_no,pr.service_code, pr.amount_due, pr.qty
													 FROM seg_pay_request AS pr
													 INNER JOIN seg_pay AS p ON p.or_no=pr.or_no AND p.pid='".$row["pid"]."'
													 WHERE pr.ref_source = 'RD' AND pr.ref_no = '".trim($row["refno"])."'
													 AND (ISNULL(p.cancel_date) OR p.cancel_date='0000-00-00 00:00:00') LIMIT 1";
							$rs_paid = $db->Execute($sql_paid);
							if ($rs_paid){
									$result2 = $rs_paid->FetchRow();
									$paid_total = $paid_total + $result2['amount_due'];
									$paid = number_format($result2['amount_due'],2,".",",");
									#$pdf->Cell(3,5,'Php',"",0,'L');
									$paid = "Php ".$paid;
							}
					}elseif (($row["request_flag"]!=NULL)||($row["request_flag"]!="")){
							#$or_no = $result_paid["charge_name"];
							$paid = mb_strtoupper($row["request_flag"]);
					}

					$pdf->Cell(15,5,$paid,"",0,'R');

					#$radio_impression_array = unserialize($radio_impression);
					#$radio_impression_final = $radio_impression_array[count($radio_impression_array)-1];

					$doctors_array = unserialize($row['doctor_in_charge']);
					$doctors_final = $doctors_array[count($doctors_array)-1];
					if(!is_array($doctors_final) && $doctors_final != ''){
						if (stristr($doctors_final," / ")){
							$doctor_array = explode(" / ",$doctors_final);
							$doctor = '';
							for ($i=0;$i<sizeof($doctor_array);$i++){
								if (stristr($doctor_array[$i],","))
									$pos = stripos($doctor_array[$i],",");
								else
									$pos = stripos($doctor_array[$i],"MD");

								$dr_list = substr($doctor_array[$i],0,$pos);
								$doctor .= trim($dr_list).",";
							}

							$dr = trim($doctor);
							$doctors = substr($dr,0,strlen($dr)-1);

						}else{
							$pos = stripos($doctors_final,",");
							$doctors = substr($doctors_final,0,$pos);
						}
					}else{
							$docs =  $row['doc_nr'];
		                    $doctor_final2 = '';
		                    $nr = explode(',',$docs);
		                    foreach($nr as $key => $value){
		                        if($value!=''){
		                            $row_pr=$pers_obj->get_Person_name($value);
		                            $dr_name = mb_strtoupper($row_pr['dr_name']);		     
		                            $doctor_final2 .= $dr_name."\n";
		                        }
		                    } 
				            $doctors = $doctor_final2;
					}

					#$pdf->Cell(40,5,$doctors_final,"",1,'R');
					$x = $pdf->GetX();
					$y = $pdf->GetY();
					$pdf->SetXY($x, $y);
					$pdf->MultiCell(50, 4, $doctors, '', 'L','');

					#echo "<br>len = ".$row2['service_name']." - ".strlen($row2['service_name']);
					if (strlen($row2['service_name'])>42)
						$pdf->Ln($space*2);
					else
						$pdf->Ln($space*2);

					$pdf->Cell(169,5,"","",0,'L');
					$i++;
				}

			}
			#$pdf->Ln($space*1);
			$pdf->Ln(0.5);
			$cntindex++;
		}

		$pdf->SetFont("Times","B",$fontsizeInput+1);
		$pdf->Cell(10,4,"","",0,'L');

		$pdf->Cell(310,4,"","T",1,'L');
		$pdf->Cell(10,4,"","",0,'L');
		$pdf->Cell(170,4,"TOTAL AMOUNT COLLECTED (".date("h:i A",strtotime($fromtime))." - ".date("m/d/Y",strtotime($dateto))." ".date("h:i A",strtotime($totime)).")","",0,'L');
		$pdf->Cell(10,4," : Php","",0,'L');
		$pdf->Cell(8,4," ","",0,'L');
		$pdf->Cell(25,4,number_format($paid_total, 2),"",1,'R');
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