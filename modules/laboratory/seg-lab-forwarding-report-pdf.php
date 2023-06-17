<?php
	//include("roots.php");
	require('./roots.php');

	#include_once($root_path."/classes/fpdf/fpdf.php");

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

	require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
	$objInfo = new Hospital_Admin();

	require($root_path.'classes/adodb/adodb.inc.php');
	include($root_path.'include/inc_init_hclab_main.php');
	#include($root_path.'include/inc_seg_mylib.php');

	require_once($root_path.'include/care_api_classes/class_hclab_oracle.php');
	$hclabObj = new HCLAB;

	global $db;

	$datefrom = $_GET['fromdate'];
	$dateto = $_GET['todate'];

	$fromtime = $_GET['fromtime'];
	$totime = $_GET['totime'];

	$pat_type = $_GET['pat_type'];

	$ward_nr = $_GET['ward_nr'];

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

	#$HTTP_SESSION_VARS['fromtime'] = $fromtime;
	#$HTTP_SESSION_VARS['totime'] = $totime;
	/*
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
	*/


	#$HTTP_SESSION_VARS['fromdate'] = $datefromSession;
	#$HTTP_SESSION_VARS['todate'] = $datetoSession;
	#echo 'type = '.$pat_type;
	if ($pat_type==1){
		#ER PATIENT
		$enctype = " AND encounter_type IN (1)";
		$patient_type = "ER";
	}elseif ($pat_type==2){
		#OUT PATIENT
		$enctype = " AND (encounter_type IN (2) OR r.encounter_nr='')";
		$patient_type = "OPD";
	}elseif ($pat_type==3){
		#ADMITTED PATIENT
		$enctype = " AND encounter_type IN (3,4)";
		$patient_type = "IPD";
	}else{
		$enctype = "";
		$patient_type = "All";
	}

	#$HTTP_SESSION_VARS['patient_type'] = $patient_type;

	#include_once($root_path."/classes/fpdf/pdf-lab.class.php");
	include_once($root_path."/classes/fpdf/pdf.class.php");
	$pdf = new PDF("L",'mm','Legal');
	$pdf->AliasNbPages();   #--added
	$pdf->AddPage("L");

	$pdf->SetLeftMargin(5);
	$pdf->SetAutoPageBreak("auto");

	$borderYes="1";
	$borderNo="0";
	$newLineYes="1";
	$newLineNo="0";
	$fontsizeInput = 10;
	$space=2;


	$pdf->Image($root_path.'gui/img/logos/dmc_logo.jpg',50,10,20,20);

	if ($row = $objInfo->getAllHospitalInfo()) {
		$row['hosp_agency'] = strtoupper($row['hosp_agency']);
		$row['hosp_name']   = strtoupper($row['hosp_name']);
	}
	else {
		$row['hosp_country'] = "Republic of the Philippines";
		$row['hosp_agency']  = "DEPARTMENT OF HEALTH";
		$row['hosp_name']    = "Gonzales Maranan Medical Center Incorporated";
		$row['hosp_addr1']   = "Quezon Ave., Digos City, Davao del Sur";
	}

	$pdf->SetFont("Times","B",$fontsizeInput);
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
	$pdf->SetFont("Times","B",$fontsizeInput-2);
	 #$pdf->Cell(0,4,'JICA Bldg., JP Laurel Avenue, Davao City',$borderNo,$newLineYes,'C');
	$pdf->Cell(0,4,$row['hosp_addr1'],$borderNo,$newLineYes,'C');
		$pdf->Ln(2);
	$pdf->SetFont("Times","B",$fontsizeInput);
		$pdf->Cell(0,4,'DEPARTMENT OF PATHOLOGY AND CLINICAL LABORATORIES',$borderNo,$newLineYes,'C');
	$pdf->Ln(4);

	$pdf->SetFont("Times","B",$fontsizeInput+2);

	if ($fromtime!='00:00:00')
		$fromtime = date("h:i A",strtotime($fromtime));
	else
		$fromtime = "";

	if ($totime!='00:00:00')
		$totime = date("h:i A",strtotime($totime));
	else
		$totime= "";

	if ((trim($datefrom))&&(trim($dateto)))
		$pdf->Cell(0,4,$patient_type.' Patients\' For Warding List '.$datefromSession." ".$fromtime." - ".$datetoSession." ".$totime,$borderNo,$newLineYes,'C');
	else
		$pdf->Cell(0,4,$patient_type.' Patients\' For Warding List (All Records)',$borderNo,$newLineYes,'C');

	#$pdf->Cell(0,4,'PATIENTS\' FOR WARDING LIST '.date("m/d/Y",strtotime($datefrom))." ".date("h:i A",strtotime($fromtime))." - ".date("m/d/Y",strtotime($dateto))." ".date("h:i A",strtotime($totime)),$borderNo,$newLineYes,'C');
	$pdf->Ln(4);

	#$report_info = $srvObj->getPatientList($datefrom, $dateto, $fromtime, $totime,0);
	#$totalcount = $srvObj->count;

	#$report_info_grp = $srvObj->getPatientList($datefrom, $dateto, $fromtime, $totime, $enctype,1);
	if ($fromtime)
		$fromtime = date("H:i:s", strtotime($fromtime));

	if ($totime)
		$totime = date("H:i:s", strtotime($totime));

	$report_info_grp = $srvObj->getPatientForWardList($datefrom, $dateto, $fromtime, $totime, $enctype,$ward_nr, 1);
	#echo $srvObj->sql;
	$totalcount2 = $srvObj->count;

	$pdf->Cell(50,4,"Total Number of Records : ".$totalcount2,"",0,'L');

	$pdf->SetFont("Times","",$fontsizeInput+2);

	$pdf->Ln($space*4);
	$pdf->SetFont('Arial','B',$fontsizeInput-1);
	$pdf->Cell(10,4,"","",0,'L');
	$pdf->Cell(30,8,'DATE/TIME',"TB",0,'L');
	$pdf->Cell(30,8,'REFERENCE',"TB",0,'C');
	$pdf->Cell(40,8,'PATIENT\'S NAME',"TB",0,'L');
	$pdf->Cell(30,8,'HOSP. NO.',"TB",0,'L');
	$pdf->Cell(15,8,'SEX',"TB",0,'C');
	$pdf->Cell(50,8,'LOCATION',"TB",0,'L');
	$pdf->Cell(90,8,'PROCEDURE',"TB",0,'L');
	$pdf->Cell(25,8,'OR NO.',"TB",0,'L');
	$pdf->Cell(15,8,'DONE',"TB",0,'C');

	$pdf->Ln($space*6);


	$pdf->SetFont('Arial','B',$fontsizeInput-1);
	if ($totalcount2){
		$j = 1;
		while ($row=$report_info_grp->FetchRow()){

			$pdf->Cell(10,4,$j,"",0,'L');
			$pdf->Cell(30,5,date("m/d/Y",strtotime(trim($row['serv_dt'])))." ".date("h:i A",strtotime(trim($row['serv_tm']))),"",0,'L');
			$pdf->Cell(30,5,trim($row['refno']),"",0,'C');
			#$pdf->Cell(50,5,strtoupper($row['ordername']),"",0,'L');
			$x = $pdf->GetX();
			$y = $pdf->GetY();
			$pdf->SetXY($x, $y);
			$pdf->MultiCell(40, 5, mb_strtoupper($row['ordername']), '', 'L','');
			$pdf->SetXY($x+40, $y);
			$pdf->Cell(30,5,trim($row['pid']),"",0,'L');

			$pdf->Cell(15,5,strtoupper($row['sex']),"",0,'C');

			if ($row['encounter_type']==1){
				$location = "ER";
			}elseif ($row['encounter_type']==2){
				$dept = $dept_obj->getDeptAllInfo($row['current_dept_nr']);
				$location = $dept['id'];
			}elseif (($row['encounter_type']==3)||($row['encounter_type']==4)){
				$bed = $ward_obj->getBedNr($row['encounter_nr']);
				$ward = $ward_obj->getWardInfo($row['current_ward_nr']);
				if ($ward['ward_id'])
					$location = $ward['ward_id']." : Rm.#".$row['current_room_nr']." ,Bed #".$bed['location_nr'];
				else
					$location = "ER";
			}else{
				$location = 'Walkin';
			}

			$pdf->Cell(60,5,$location,"",0,'L');

			if ($row['is_cash']){
				if ($row['or_no']){
					$orno = $row['or_no'];
				}elseif($row['grant_no']){
					#$orno = "Subsidized";
					$orno = "Charity";
				}
			}else{
				$orno = "Charge";
			}
			#$pdf->Cell(30,5,$orno,"",0,'L');
			#$z = $pdf->GetY();

			$report_info_details = $srvObj->getPatientListDetails($row['refno']);
			#echo "<br><br>sql = ".$srvObj->sql;
			$totalcount = $srvObj->count;
			$i=1;

			if ($totalcount){
				while ($row2=$report_info_details->FetchRow()){
					$x = $pdf->GetX();
					$y = $pdf->GetY();
					$pdf->SetXY($x-10, $y);
					$pdf->MultiCell(90, 5, mb_strtoupper($row2['service_name']), '', 'L','');
					#$pdf->SetXY($x+80, $y-1);
					$pdf->SetXY($x+80, $y+1);
					$pdf->Cell(25,5,$orno,"",0,'L');
					#$pdf->SetXY($x+105, $y-1);
					$pdf->SetXY($x+105, $y+1);
					$pdf->Cell(15,5,"______","",1,'C');
					$pdf->Ln($space*1);
					$pdf->Cell(215,5,"","",0,'L');

					$i++;
				}

			}
			$j++;
			$pdf->Ln($space*3);
		}


	}else{
		$pdf->SetFont('Times','',$fontsizeInput);
		$pdf->Ln($space*4);
		$pdf->Cell(337,4,'No query results available at this time...',"",0,'C');
	}


	$pdf->Output();
?>