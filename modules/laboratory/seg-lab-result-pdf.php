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
	$ward_obj = new Ward;

	require($root_path.'classes/adodb/adodb.inc.php');
	include($root_path.'include/inc_init_hclab_main.php');
	require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

	require_once($root_path.'include/care_api_classes/class_hclab_oracle.php');
	global $db;
	$hclabObj = new HCLAB;
	$objInfo = new Hospital_Admin();

	if ($row = $objInfo->getAllHospitalInfo()) {
		 #$row['hosp_agency'] = strtoupper($row['hosp_agency']);
		 $row['hosp_name']   = strtoupper($row['hosp_name']);
	}else {
		 $row['hosp_country'] = "Republic of the Philippines";
		 $row['hosp_agency']  = "DEPARTMENT OF HEALTH";
		 $row['hosp_name']    = "Gonzales Maranan Medical Center Incorporated";
		 $row['hosp_addr1']   = "Quezon Ave., Digos City, Davao del Sur";
	}

	# echo $objInfo->sql;
	$pdf = new PDF();
	$pdf->AliasNbPages();   #--added
	$pdf->AddPage("P");

	$borderYes="1";
	$borderNo="0";
	$newLineYes="1";
	$newLineNo="0";
	$space=2;

	$itemLISWResult = array();
	$itemSEGWResult = array();

	$pdf->Image($root_path.'gui/img/logos/dmc_logo.jpg',30,10,15,15);

	$pdf->SetFont("Arial","B","10");
	$pdf->Cell(0,4,$row['hosp_country'],$borderNo,$newLineYes,'C');
	$pdf->Cell(0,4,$row['hosp_name'],$borderNo,$newLineYes,'C');
	$pdf->Cell(0,4,$row['hosp_agency'],$borderNo,$newLineYes,'C');
	$pdf->Cell(0,4,'DEPARTMENT OF PATHOLOGY AND CLINICAL LABORATORIES',$borderNo,$newLineYes,'C');


	$refno = $_GET['refno'];
	$service_code = $_GET['service_code'];
	$status = $_GET['status'];

	$infoResult = $srvObj->getOrderInfo($refno);
	#echo $srvObj->sql;

	if ($infoResult)	$info = $infoResult->FetchRow();

	$patient = $info;
	#added by VAN 08-10-09
	$rsRef = $srvObj->getLabListOrderNo($refno);
	#echo $srvObj->sql;
	$rowRef = $rsRef->FetchRow();

	$rsCode = $srvObj->getTestCode($service_code);

	if (($patient['encounter_type'] == 1) || ($patient['encounter_type'] == 3) || ($patient['encounter_type'] == 4))
		#$code = $rsCode['service_code'];
        $code = $rsCode['ipdservice_code'];
	else
		$code = $rsCode['oservice_code'];
	#------------------------

	#get service info
	$labrequestObj = $srvObj->getServiceRequestInfo($refno, 0);
	#echo $srvObj->sql;

	$labresult = $srvObj->hasResult2($rowRef['lis_order_no'],'',$patient['encounter_nr'],$patient['pid'],$refno);
	#echo $srvObj->sql;
	$count =  $srvObj->FoundRows();

	if ($count){
		$patient_info = $srvObj->getLabResultHeader($rowRef['lis_order_no'],'',$patient['encounter_nr'],$patient['pid'],$refno);
		#echo $srvObj->sql;
		if ($patient_info){
			$request_name = stripslashes($patient['name_first'])." ".stripslashes($patient['name_2'])." ".stripslashes($patient['name_middle'])." ".stripslashes($patient['name_last']);
			$request_name = ucwords(strtolower($request_name));
			$request_name = htmlspecialchars($request_name);

			$pdf->Ln($space*2);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(22,4,'Name : ',"",0,'L');
			$pdf->Cell(85,4,strtoupper($request_name),"",0,'L');
			#$pdf->Cell(15,4,'Lab No. : ',"",0,'L');
			$pdf->Cell(15,4,'Case No. :',"",0,'L');
			#$pdf->Cell(80,4,$patient_info["lab_no"],"",0,'L');
			if ($is_walkin)
				$encounter_nr = "Walkin";
			else
				$encounter_nr = $info['encounter_nr'];

			$pdf->Cell(80,4,$encounter_nr,"",0,'L');

			#if ((is_numeric($patient_info["dr_code"])) && !($patient_info["dr_code"])){
			if (is_numeric($patient_info["dr_code"])){
				$doctor = $pers_obj->getPersonellInfo($patient_info["dr_code"]);
				$doctor_name = stripslashes($doctor["name_first"])." ".stripslashes($doctor["name_2"])." ".stripslashes($doctor["name_last"]);
				$doctor_name = mb_strtoupper(strtolower($doctor_name));
				$doctor_name = htmlspecialchars($doctor_name);
			}else{
				$doctor_name = "no doctor specified";
			}

			$pdf->Ln($space*2);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(22,4,'HRN : ',"",0,'L');
			$pdf->Cell(85,4,$patient["pid"],"",0,'L');
			$pdf->Cell(15,4,'Clinician : ',"",0,'L');
			$pdf->Cell(80,4,'Dr. '.$doctor_name,"",0,'L');

			if ($patient['sex']=="m"){
				$gender = "Male";
			}elseif($patient['sex']=="f"){
				$gender = "Female";
			}else{
				$gender = "Unknown";
			}

			if ($patient['date_birth']) {
				$time = strtotime($patient['date_birth']);
				$birthDate = date("m/d/Y",$time);
				$age = $person_obj->getAge($birthDate,2,date("m/d/Y"));
				$age = round($age,1);
			}else{
				$birthDate = " ";
				$age = "Undefined";
			}

			if (stristr($age,'years')){
				$age = substr($age,0,-5);
				if ($age>1)
					$labelyear = "years";
				else
					$labelyear = "year";

					$age = floor($age)." ".$labelyear;
			}elseif (stristr($age,'year')){
				 $age = substr($age,0,-4);
				 if ($age>1)
						$labelyear = "years";
				 else
						$labelyear = "year";

						$age = floor($age)." ".$labelyear;

			}elseif (stristr($age,'months')){
				 $age = substr($age,0,-6);
				 if ($age>1)
						$labelmonth = "months";
				 else
						$labelmonth = "month";

				 $age = floor($age)." ".$labelmonth;

			}elseif (stristr($age,'month')){
				 $age = substr($age,0,-5);

				 if ($age>1)
						$labelmonth = "months";
				 else
						$labelmonth = "month";

				 $age = floor($age)." ".$labelmonth;

			}elseif (stristr($age,'days')){
				 $age = substr($age,0,-4);

				 if ($age>30){
						 $age = $age/30;
						 if ($age>1)
								$label = "months";
						 else
								$label = "month";

				 }else{
						 if ($age>1)
								$label = "days";
						 else
								$label = "day";
				 }

				 $age = floor($age).' '.$label;

			}elseif (stristr($age,'day')){
				 $age = substr($age,0,-3);

				 if ($age>1)
						$labelday = "days";
				 else
						$labelday = "day";

				 $age = floor($age)." ".$labelday;
			}else{
				 if ($age){
						if ($age>1)
							 $labelyear = "years";
						else
							 $labelyear = "year";

						$age = floor($age)." ".$labelyear;
				 }else
						$age = "0 day";
			 }

			#$patient = $enc_obj->getEncounterInfo($info['encounter_nr']);
			if ($patient['encounter_type'] == 1){
				$patient_type = "ER Patient";
				#$loc_code = "ER";
				#$loc_name = "Emergency Room";
				$loc_code = $patient['current_dept_nr'];
				if ($loc_code)
					$dept = $dept_obj->getDeptAllInfo($loc_code);

				$ptype = "ER";
				$loc_name = "$ptype - ".stripslashes($dept['name_formal']);

			}elseif (($patient['encounter_type'] == 3)||($patient['encounter_type'] == 4)){
				$patient_type = "Inpatient";
				$loc_code = $patient['current_ward_nr'];
				if ($loc_code)
					$ward = $ward_obj->getWardInfo($loc_code);

				$ptype = "IPD";
				$loc_name = "$ptype - ".stripslashes($ward['name']);

			}elseif ($patient['encounter_type'] == 2){
				$patient_type = "Outpatient";
				
                #edited by VAN 12-30-2011
                #$loc_code = $patient['current_dept_nr'];
                #$loc_code = $patient_info['loc_code'];
                $loc_code = $labrequestObj['request_dept'];
				if ($loc_code)
					$dept = $dept_obj->getDeptAllInfo($loc_code);

				$ptype = "OPD";
				$loc_name = "$ptype - ".stripslashes($dept['name_formal']);

			}else{
				$patient_type = "Walkin";
				$ptype = "Walkin";
				$loc_name = "Walkin";
			}

			$pdf->Ln($space*2);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(22,4,'Age : ',"",0,'L');
			$pdf->Cell(30,4,$age.' old',"",0,'L');
			$pdf->Cell(10,4,'',"",0,'L');
			$pdf->Cell(15,4,'Sex : ',"",0,'L');
			$pdf->Cell(30,4,$gender,"",0,'L');
			$pdf->Cell(15,4,'Location : ',"",0,'L');
			$pdf->Cell(80,4,$loc_name,"",0,'L');

			$pdf->Ln($space*2);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(22,4,'Date Received : ',"",0,'L');
			$pdf->Cell(30,4,date("m-d-Y",strtotime($patient_info["order_dt"])),"",0,'L');
			$pdf->Cell(10,4,'',"",0,'L');
			$pdf->Cell(15,4,'Reported : ',"",0,'L');
			$pdf->Cell(30,4,date("m-d-Y",strtotime($patient_info["trx_dt"])),"",0,'L');
			$pdf->Cell(15,4,'Printed : ',"",0,'L');
			$pdf->Cell(40,4,date("m-d-Y h:i:s"),"",0,'L');
			$pdf->Cell(10,4,'Page : ',"",0,'L');
			$pdf->Cell(5,4,$pdf->PageNo().' / {nb}',"",0,'L');

			#$labservObj = $srvObj->getServiceInfo($patient_info["service_code"]);
			#$result = $srvObj->getLabResult_order($rowRef['lis_order_no'], 0);
			#echo $srvObj->sql;
			$pdf->Ln($space*4);

			#$pdf->Ln(2);
			#$pdf->Ln($space*2);
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(65,4,'        TEST ',"TB",0,'L');
			$pdf->Cell(40,4,'RESULT',"TB",0,'C');
			$pdf->Cell(35,4,'UNIT',"TB",0,'C');
			$pdf->Cell(50,4,'REFERENCE RANGES',"TB",1,'C');

			$pdf->Ln(2);

			#$result = $srvObj->getLabResult($refno, $service_code);
			$result = $srvObj->getLabResult_order($patient['pid'], $rowRef['lis_order_no'], 0, $refno);
			#echo "<br><br>".$srvObj->sql;
			$rowcount = $srvObj->count;

			if ($rowcount){
				$i=1;

				$section = '';
				$parent = '';
				$s_name = '';
				$x = $pdf->GetX();
				$pdf->Cell(60,4,'FROM AUTOMATED LIS',"",1,'L');
				while ($row=$result->FetchRow()) {
					$minus_width = 0;

					if ($row['name']!=$section){
						$pdf->SetFont('Times','B',8);
						$pdf->Ln(2);
						$pdf->Cell(60,4,$row['name'],"",1,'L');
					}

					if (($row['parent_item'])&&($row['parent_item']!=$parent)){
							if (($row['service_code']==$row['parent_item'])&&($row['parent_item'])){
								$pdf->SetFont('Times','',8);
								$pdf->Cell(5,4,"","",0,'L');
								if (empty($parent)){
										if ($row['parent_item']==$row['oservice_code'])
											$parent_item = substr($row['parent_item'],1);
                                        #added by VAN 08-07-2012
                                        elseif ($row['parent_item']==$row['ipdservice_code'])
                                            $parent_item = substr($row['parent_item'],1);
                                        else
										    $parent_item = $row['parent_item'];

									#$parent_item = $row['parent_item'];
									$pdf->Cell(60,4,$parent_item,"",1,'L');
								}
							}
					}
					#echo "<br>".$row['parent_item']."==".$row['service_code_lab']." - ".$row['oservice_code'];
					if ($row['service_code_lab']!=$service_code_lab){
						$itemLISWResult[] = $row['service_code_lab'];
					}

					$pdf->SetFont('Times','',8);
					if ($row['parent_item']==$code){
						$pdf->Cell(5,4,"","",0,'L');
							if ((($row['tg_code']!=$row['grp']))&&(($row['test_name']==$row['test_code'])))
								$test_name = substr($row['test_name'],1);
							else
							$test_name = $row['test_name'];

						$pdf->Cell(60,4,$test_name,"",0,'L');
					}else{
						if (($row['service_code']!=$row['parent_item'])&&($row['parent_item'])){
							$pdf->Cell(15,4,"","",0,'L');
							if ((($row['tg_code']!=$row['grp']))&&(($row['test_name']==$row['test_code'])))
								$test_name = substr($row['test_name'],1);
							else
								$test_name = $row['test_name'];

							$pdf->Cell(50,4,$test_name,"",0,'L');
						}else{
							$pdf->SetX($x);
							$pdf->Cell(10,4,"","",0,'L');
							if ((($row['tg_code']!=$row['grp']))&&(($row['test_name']==$row['test_code'])))
								$test_name = substr($row['test_name'],1);
							else
								$test_name = $row['test_name'];

							$pdf->Cell(55,4,$test_name,"",0,'L');
						}
					}

					$pdf->Cell(40,4,$row['result_value'],"",0,'C');
					#$pdf->Cell(20,4,'',"1",0,'L');
					$pdf->Cell(35,4,$row['unit'],"",0,'C');
					$pdf->Cell(50,4,$row['ranges'],"",1,'C');
					$examiner = $row['mlt_name'];

					$section = $row['name'];
					$parent = $row['parent_item'];
					$s_name = $row['service_code'];
					$service_code_lab = $row['service_code_lab'];

					$i++;
				}

			}/*else{
				$pdf->SetFont('Times','',10);
				$pdf->Cell(190,4,'No laboratory results available at this time...',"",0,'C');
			}*/

			#-----------
			$pdf->Ln(4);
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(60,4,'FROM SEGHIS',"",1,'L');
			#-------SEGHIS
			$result = $srvObj->getAllSegHisLabResultGroup($refno);
			#echo "<br><br>".$srvObj->sql;
			$rowcount2 = $srvObj->count;

			if ($rowcount2){
				$i=1;

				$section = '';
				$parent = '';
				$s_name = '';
				$x = $pdf->GetX();
				while ($row=$result->FetchRow()) {
					$minus_width = 0;

					if ($row['section_name']!=$section){
						$pdf->SetFont('Times','B',8);
						$pdf->Ln(2);
						$pdf->Cell(60,4,$row['section_name'],"",1,'L');
					}

					if (($row['parent_item'])&&($row['parent_item']!=$parent)){
							if (($row['test_code']==$row['parent_item'])&&($row['parent_item'])){
								$pdf->SetFont('Times','',8);
								$pdf->Cell(5,4,"","",0,'L');
								$parent_item = $row['parent_item'];
								$pdf->Cell(60,4,$parent_item,"",1,'L');
								$itemSEGWResult[] = $row['service_code'];
							}
					}

					if ($row['test_gr_name']!=$test_gp_name){
						$pdf->SetFont('Times','',8);
						$pdf->Cell(10,4,"","",0,'L');
						$param_group = $row['test_gr_name'];
						$pdf->Cell(60,4,mb_strtoupper($param_group),"",1,'L');
						#$c = 1;
						$result_d = $srvObj->getAllSegHisLabResultParam($refno,$row['param_group_id'],$row['group_id']);

						#echo "<br><br>".$srvObj->sql;
						$count_d = $srvObj->count;

						if ($count_d){
							while($row_d=$result_d->FetchRow()){
									$pdf->SetFont('Times','',8);
									$pdf->Cell(15,4,"","",0,'L');
									$test_name = $row_d['test_name'];
									$pdf->Cell(50,4,$test_name,"",0,'L');

									$pdf->Cell(40,4,$row_d['result_value'],"",0,'C');
									$pdf->Cell(35,4,$row_d['unit'],"",0,'C');
									$pdf->Cell(50,4,$row_d['ranges'],"",1,'C');
									$examiner = $row_d['mlt_name'];
							}
						}
					}

					$section = $row['section_name'];
					$parent = $row['parent_item'];
					$test_gp_name = $row['test_gr_name'];
					$i++;
				}
				$no_result = 0;
			}/*else{
				$pdf->SetFont('Times','',10);
				$pdf->Cell(190,4,'No laboratory results available at this time...',"",0,'C');
				$no_result = 1;
			}*/

			#-------------- SEGHIS
			#print_r($itemSEGWResult);
			#echo "<br>";
			#print_r($itemLISWResult);
			if (!$no_result){
				$itemWResult = array_unique(array_merge($itemLISWResult,$itemSEGWResult));
				#print_r($itemWResult);
				$sql_request = "SELECT service_code FROM seg_lab_servdetails WHERE refno='$refno'";
				$rs_request = $db->Execute($sql_request);
				#echo $sql_request;
				$itemRequest = array();
				while($row_request = $rs_request->FetchRow()){
					$itemRequest[] = $row_request['service_code'];
				}
				#print_r($itemRequest);
				$pdf->Ln(4);
				$pdf->SetFont('Arial','B',8);
				$pdf->Cell(60,4,'LABORATORY REQUEST WITH NO RESULT YET',"",1,'L');
				$pdf->Ln(2);
				#print_r($itemWResult);
				$pdf->SetFont('Times','',8);
				foreach($itemRequest as $i=>$v){
					#echo "<br>".$itemWResult[$i];
					if (in_array($itemRequest[$i],$itemWResult)){
						#do nothing
					}else{
							$pdf->Cell(5,4,'',"",0,'L');
							#$pdf->Cell(5,4,'*',"",0,'L');
							#$pdf->Cell(20,4,$itemRequest[$i],"",0,'L');
							#$pdf->Cell(10,4,':',"",0,'C');

							$sql_service = "SELECT name FROM seg_lab_services WHERE service_code='".$itemRequest[$i]."'";
							$rs_service = $db->Execute($sql_service);
							$row_service = $rs_service->FetchRow();

							$pdf->Cell(20,4,$row_service['name'],"",1,'L');
					}
				}
			}

			$pdf->Ln($space*6);
			$pdf->Cell(20,4,'',"",0,'L');
			$pdf->SetFont('Arial','U',8);
			#$pdf->Cell(100,4,'     '.$examiner.', RMT     ',"",0,'L');
			$pdf->Cell(100,4,'',"",0,'L');

			$pathologist_name = $pers_obj->getPathologist();

			$pdf->Cell(100,4,mb_strtoupper($pathologist_name['name']),"",0,'L');
			#$pdf->SetFont('Arial','',8);

			$pdf->Ln($space*2);
			$pdf->Cell(20,4,'',"",0,'L');
			$pdf->SetFont('Arial','',8);
			#$pdf->Cell(30,4,'Examiner',"",0,'C');
			$pdf->Cell(30,4,'',"",0,'C');
			$pdf->Cell(75,4,'',"",0,'C');
			$pdf->Cell(30,4,'Pathologist',"",0,'C');
			#}
		}else{
			echo '<em class="warn">Sorry but the page cannot be displayed! There is no result at all. Pending Status..</em>';
			exit();
		}
	}else{
		echo '<em class="warn">Sorry but the page cannot be displayed! There is no result at all. Pending Status..</em>';
		exit();
	}
	$pdf->Output();
?>
