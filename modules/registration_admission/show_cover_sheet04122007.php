<?php
	include("roots.php");
	include_once($root_path."/classes/fpdf/fpdf.php");
	require_once($root_path.'include/inc_environment_global.php');
	include_once($root_path.'include/inc_date_format_functions.php');

	require_once($root_path.'/include/care_api_classes/class_drg.php');
	$objDRG= new DRG;

	include_once($root_path.'include/care_api_classes/class_encounter.php');
	$enc_obj=new Encounter;

	if ($_GET['encounter_nr']) {
		$enc_info = $enc_obj->getEncounterInfo($_GET['encounter_nr']);
		extract($enc_info);
	}
	$space=2;
	$pdf = new FPDF();
	$pdf->AddPage("P");
	$pdf->SetFont("Arial","B",8);
  $pdf->Cell(0,3,'MRFI 05-1',$border2,1,'R');	
	$pdf->SetFont("Arial","I","9");
	$pdf->Cell(0,4,'Republic of the Philippines',$border2,1,'C');
	$pdf->Cell(0,4,'DEPARTMENT OF HEALTH',$border2,1,'C');
	$pdf->Ln(2);
	$pdf->SetFont("Arial","B","10");
	$pdf->Cell(0,4,'Gonzales Maranan Medical Center Incorporated',$border2,1,'C');
	$pdf->SetFont("Arial","","9");
  $pdf->Cell(0,4,'Digos City',$border2,1,'C');
	$pdf->SetFont('Arial','B',11);
	$pdf->Cell(0,5,'Clinical Cover Sheet',$border2,1,'C');

	$pdf->Ln($space);
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(30,4,'Case No.',"$border2",0,'R');
	$pdf->SetFont('Arial','B',10);
	$pdf->Cell(65,4,$encounter_nr,"$border2",0,'L');
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(65,4,'Patient ID',"$border2",0,'R');
	$pdf->SetFont('Arial','B',10);
	$pdf->Cell(30,4	,$pid,"$border2",1,'L');
	$pdf->SetFont('Arial','I',8);	
	$pdf->Cell(45,4,'Last Name',"TLR",0,'L');
	$pdf->Cell(45,4,'First Name',"TLR",0,'L');
	$pdf->Cell(45,4,'Middle Name',"TLR",0,'L');
	$pdf->Cell(20,4,'Date/Time: ',"TLB",0,'L');
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(35,4," ".@formatDate2Local($admission_dt,$date_format,1),"TRB",0,'L');

	$pdf->Ln();	

	$pdf->SetFont('Arial','',9);
	$pdf->Cell(45,4,mb_strtoupper($name_last),"BLR",0,'L');
	$pdf->Cell(45,4,mb_strtoupper($name_first),"BLR",0,'L');
	$pdf->Cell(45,4,mb_strtoupper($name_middle),"BLR",0,'L');
	$pdf->SetFont('Arial','I',8);
	$pdf->Cell(20,4,'Department: ',"TLB",0,'L');
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(35,4,$name_formal,"TRB",1,'L');
	$pdf->SetFont('Arial','I',8);
	$pdf->Cell(120,4,'Address ',"TLR",0,'L');
	$pdf->Cell(30,4,'Contact',"TLR",0,'L');
	$pdf->Cell(20,4,'Sex',"TLR",0,'L');
	$pdf->Cell(20,4,'Civil Status',"TLR",1,'L');
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(120,4,"$street_name, $brgy_name, $mun_name $zipcode $prov_name ","LBR",0,'L');
	$contact = $phone_1_nr;
	if (!isset($contact)) $contact = $cellphone_1_nr;
	if (!isset($contact)) $contact = $phone_2_nr;
	if (!isset($contact)) $contact = $cellphone_2_nr;
	$pdf->Cell(30,4,$contact,"LBR",0,'L');
	$pdf->Cell(20,4,mb_strtoupper($sex),"LBR",0,'L');
	$pdf->Cell(20,4,mb_strtoupper($civil_status),"LBR",1,'L');
	$pdf->SetFont('Arial','I',8);
	$pdf->Cell(31,4,'Birth Date',"TLR",0,'L');
	$pdf->Cell(31,4,'Age',"TLR",0,'L');
	$pdf->Cell(32,4,'Birth Place',"TLR",0,'L');
	$pdf->Cell(32,4,'Citizenship',"TLR",0,'L');
	$pdf->Cell(32,4,'Religion',"TLR",0,'L');
	$pdf->Cell(32,4,'Occupation',"TLR",1,'L');
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(31,4,$date_birth,"LBR",0,'L');
	$pdf->Cell(31,4,$age,"LBR",0,'L');
	$pdf->Cell(32,4,$place_birth,"LBR",0,'L');
	$pdf->Cell(32,4,$citizenship,"LBR",0,'L');
	$pdf->Cell(32,4,$religion,"LBR",0,'L');
	$pdf->Cell(32,4,$occupation,"LBR",1,'L');
	$pdf->SetFont('Arial','I',8);
	$pdf->Cell(29,8,'Father\'s Name',"TLB",0,'L');
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(65,8,$father_name,"TRB",0,'L');
	$pdf->SetFont('Arial','I',8);
	$pdf->Cell(30,8,'Spouses\' Name',"TLB",0,'L');
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(66,8,$spouse_name,"TRB",1,'L');
	$pdf->SetFont('Arial','I',8);
	$pdf->Cell(29,8,'Mother\'s Name',"TLB",0,'L');
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(65,8,$mother_name,"TRB",0,'L');
	$pdf->SetFont('Arial','I',8);
	$pdf->Cell(30,8,'Guardian\'s Name',"TLB",0,'L');
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(66,8,$guardian_name,"TRB",1,'L');
	$pdf->SetFont('Arial','I',8);
	$pdf->Cell(29,8,'Informant\'s Name',"TLB",0,'L');
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(65,8,$informant_name,"TRB",0,'L');
	$pdf->SetFont('Arial','I',8);
	$pdf->Cell(30,8,'Informant\'s Address',"TLB",0,'L');
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(66,8,$info_address,"TRB",1,'L');
	
	$pdf->Ln($space);	
	
	$pdf->SetFont('Arial','I',8);
	$pdf->Cell(24,8,'Attending Dr.',"TLB",0,'L');
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(70,8,'',"TRB",0,'L');
	$pdf->SetFont('Arial','I',8);
	$pdf->Cell(25,8,'Admitting Dr.',"TLB",0,'L');
	$pdf->SetFont('Arial','',9);
	if ($encounter_type==4){
		$admitting_dr=$opd_admitting_physician;
	}else{
		$admitting_dr=$admitting_physician;
	}
	$pdf->Cell(71,8,$admitting_dr,"TRB",1,'L');
	$pdf->SetFont('Arial','I',8);
	$pdf->Cell(24,8,'Consultant Dr.',"TLB",0,'L');
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(70,8,'',"TRB",0,'L');
	$pdf->SetFont('Arial','I',8);
	$pdf->Cell(25,8,'Admitting Clerk',"TLB",0,'L');
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(71,8,$admitting_clerk_er_opd,"TRB",1,'L');
	
	#$pdf->Ln($space);	

	$pdf->SetFont('Arial','I',8);
	$pdf->Cell(25,8,'Admitting Diagnosis:',"0",0,'L');
	$pdf->Ln($space*2);	
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(71,8,"\t\t\t\t".$admitting_diagnosis,"0",1,'L');

	$pdf->Ln($space);	

	$result_diagnosis = array();
	$final=1;
	if (isset($final)){
		if ($rs_diagnosis = $objDRG->getDiagnosisCodes($_GET['encounter_nr'])){
			$rowsDiagnosis = $rs_diagnosis->RecordCount();
			while($temp=$rs_diagnosis->FetchRow()){
				#echo $temp['code']." : ".$temp['diagnosis']." <br> \n";
				$temp_diagnosis = array();
				$temp_diagnosis['type'] = $temp['type'];
				$temp_diagnosis['code'] = $temp['code'];
				$temp_diagnosis['diagnosis'] = $temp['diagnosis'];
				array_push($result_diagnosis,$temp_diagnosis);
			}			
		}
	}
#	echo "result_diagnosis :  <br> \n"; print_r($result_diagnosis); echo "<br> \n";
/*
	echo "Principal Diagnosis <br> \n";
	foreach ($result_diagnosis as $value) {
		if ($value['type']==1)
			echo "Value: type = ".$value['type']."; code = ".$value['code']."; diagnosis = ".$value['diagnosis']." <br/>\n";
	}
	echo "Other Diagnosis <br> \n";
	foreach ($result_diagnosis as $value) {
		if ($value['type']==0)
			echo "Value: type = ".$value['type']."; code = ".$value['code']."; diagnosis = ".$value['diagnosis']." <br/>\n";
	}
*/

	$result_therapy = array();
	if (isset($final)){
		if ($rs_therapy = $objDRG->getProcedureCodes($_GET['encounter_nr'])){
			$rowsTherapy = $rs_therapy->RecordCount();
			while($temp=$rs_therapy->FetchRow()){
				#echo $temp['code']." : ".$temp['diagnosis']." <br> \n";
				$temp_therapy = array();
				$temp_therapy['type'] = $temp['type'];
				$temp_therapy['code'] = $temp['code'];
				$temp_therapy['therapy'] = $temp['therapy'];
				array_push($result_therapy,$temp_therapy);
			}			
		}
	}
#	echo "result_therapy :  <br> \n"; print_r($result_therapy); echo "<br> \n";
/*
	echo "Principal Procedure <br> \n";
	foreach ($result_therapy as $value) {
		if ($value['type']==1)
			echo "Value: type = ".$value['type']."; code = ".$value['code']."; therapy = ".$value['therapy']." <br/>\n";
	}
	echo "Other Procedure <br> \n";
	foreach ($result_therapy as $value) {
		if ($value['type']==0)
			echo "Value: type = ".$value['type']."; code = ".$value['code']."; therapy = ".$value['therapy']." <br/>\n";
	}
*/

	$pdf->SetFont('Arial','I',8);
	$pdf->Cell(25,5,'Principal Diagnosis:',"0",1,'L');
	$pdf->SetFont('Arial','',9);

	if (isset($final) && ($rowsDiagnosis)){
		$count=0;
		foreach ($result_diagnosis as $value) {
			if ($value['type']==1){
#				echo "Value: type = ".$value['type']."; code = ".$value['code']."; diagnosis = ".$value['diagnosis']." <br/>\n";
				$pdf->Cell(71,5,"\t\t\t\t".$value['code']." : ".$value['diagnosis'],"0",1,'L');
				$count++;
			}
		}
		if (5-$count){
			$pdf->Ln($space*(5-$count));
		}
	}else{
		$pdf->Ln($space*5);
	}

	$pdf->SetFont('Arial','I',8);
	$pdf->Cell(25,5,'Other Diagnosis:',"0",1,'L');

	if (isset($final) && ($rowsDiagnosis)){
		$count=0;
		foreach ($result_diagnosis as $value) {
			if ($value['type']==0){
#				echo "Value: type = ".$value['type']."; code = ".$value['code']."; diagnosis = ".$value['diagnosis']." <br/>\n";
				$pdf->Cell(71,4,"\t\t\t\t".$value['code']." : ".$value['diagnosis'],"0",1,'L');
				$count++;
			}
		}
		if (15-$count){
			$pdf->Ln($space*(15-$count));
		}
	}else{
		$pdf->Ln($space*15);
	}

	$pdf->SetFont('Arial','I',8);
	$pdf->Cell(25,5,'Operations:',"0",1,'L');

	if (isset($final) && ($rowsTherapy)){
		$count=0;
		foreach ($result_therapy as $value) {
			#if ($value['type']==0){
#				echo "Value: type = ".$value['type']."; code = ".$value['code']."; diagnosis = ".$value['diagnosis']." <br/>\n";
				$pdf->Cell(71,4,"\t\t\t\t".$value['code']." : ".$value['therapy'],"0",1,'L');
				$count++;
			#}
		}
		if (15-$count){
			$pdf->Ln($space*(15-$count));
		}
	}else{
		$pdf->Ln($space*15);
	}

	$pdf->SetFont('Arial','I',8);
	$note="Note: Always indicate diagnosis/procedure in order of importance, also indicate if procedure is Minor/Major.";
	$pdf->Cell(25,8,$note,"0",1,'L');

	$pdf->SetFont('Arial','B',8);
	$pdf->Cell(60,4,'Result',"TLR",0,'L');
	$pdf->Cell(60,4,'Disposition',"TLR",0,'L');
	$pdf->SetFont('Arial','I',8);
	$pdf->Cell(0,4,'Admision Date/Time',"TLR",1,'L');
	
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(7,4,'[   ]',"L",0,'R');
	$pdf->Cell(21,4,'Recovered',"0",0,'L');
	$pdf->Cell(7,4,'[   ]',"0",0,'R');
	$pdf->Cell(25,4,'Died',"R",0,'L');

	$pdf->Cell(7,4,'[   ]',"L",0,'R');
	$pdf->Cell(21,4,'Discharged',"0",0,'L');
	$pdf->Cell(7,4,'[   ]',"0",0,'R');
	$pdf->Cell(25,4,'Absconded',"R",0,'L');
#	$pdf->Cell(0,4,'',"BR",1,'L');
	   # burn added: March 29, 2007
	$pdf->Cell(0,4,"     ".@formatDate2Local($admission_dt,$date_format,1),"BR",1,'L');

	$pdf->Cell(7,4,'[   ]',"L",0,'R');
	$pdf->Cell(21,4,'Improved',"0",0,'L');
	$pdf->Cell(7,4,'[   ]',"0",0,'R');
	$pdf->Cell(25,4,'Autopsy',"R",0,'L');

	$pdf->Cell(7,4,'[   ]',"L",0,'R');
	$pdf->Cell(21,4,'Transferred',"0",0,'L');
	$pdf->Cell(32,4,'',"R",0,'L');
	$pdf->Cell(0,4,'Discharge Date/Time',"R",1,'L');
	
	$pdf->Cell(7,4,'[   ]',"L",0,'R');
	$pdf->Cell(21,4,'Unimproved',"0",0,'L');
	$pdf->Cell(7,4,'[   ]',"0",0,'R');
	$pdf->Cell(25,4,'No Autopsy',"R",0,'L');

	$pdf->Cell(7,4,'[   ]',"L",0,'R');
	$pdf->Cell(21,4,'DAMA',"0",0,'L');
	$pdf->Cell(32,4,'',"R",0,'L');
	$pdf->Cell(0,4,'',"R",1,'L');
	
	$pdf->Cell(60,2,'',"BLR",0,'R');
	$pdf->Cell(60,2,'',"BLR	",0,'R');
	$pdf->Cell(0,2,'',"BLR",1,'L');

	$pdf->Ln($space*2);

	$pdf->SetFont('Arial','I',8);
	$note="I have reviewed this record and found it to be accurate and complete.";
	$pdf->Cell(0,8,$note,"0",1,'C');
	
#	$pdf->Ln($space);

	$pdf->SetFont('Arial','I',8);
	$pdf->Cell(0,5,"THUMB MARK","0",1,'L');
	$x=$pdf->GetX();
	$y=$pdf->GetY();
	$pdf->Rect($x+1.5, $y, 18, 18);
	$pdf->SetFont('Arial','I',8);
	$pdf->Cell(40,5,"","0",0,'R');
#	$pdf->Cell(50,5,"Bernard Klinch Sabucdalao Clarito II","0",0,'C');
#	$pdf->Cell(50,5,$informant_name,"0",0,'C');
	$pdf->Cell(50,5,'',"0",0,'C');
#	$pdf->Cell(50,5,"x = '$x' ; y = '$y' ","0",0,'C');
	$pdf->Cell(10,5,"","0",0,'L');
#	$pdf->Cell(45,5,"Bernard Klinch Sabucdalao Clarito II","0",0,'C');
	$pdf->Cell(45,5,'',"0",0,'C');

	$pdf->Ln(1);

	$pdf->Cell(90,3,"______________________________","0",0,'R');
	$pdf->Cell(13,3,"","0",0,'L');
	$pdf->Cell(90,3,"________________________","0",1,'L');

	$pdf->Cell(85,5,"Informant / Patient's Signature","0",0,'R');
	$pdf->Cell(20,5,"","0",0,'L');
	$pdf->Cell(85,5,"ATTENDING PHYSICIAN","0",1,'L');
	
	/*
	$pdf->Cell(7,4,'[   ]',"L",0,'R');
	$pdf->Cell(43,4,'Semi-conscious',"R",0,'L');
	$pdf->Cell(7,4,'[   ]',"L",0,'R');
	$pdf->Cell(43,4,'Discharged',"R",0,'L');
	$pdf->Cell(0,4,'',"R",1,'L');
	$pdf->Cell(7,4,'',"L",0,'R');
	$pdf->Cell(43,4,'',"R",0,'L');
	$pdf->Cell(7,4,'[   ]',"L",0,'R');
	$pdf->Cell(43,4,'Transferred',"R",0,'L');
	$pdf->Cell(0,4,'',"R",1,'L');
  $pdf->SetFont('Arial','I',8);
	$pdf->Cell(50,4,'Results',"TLR",0,'L');
  $pdf->SetFont('Arial','',8);
	$pdf->Cell(7,4,'[   ]',"L",0,'R');
	$pdf->Cell(43,4,'HAMA',"R",0,'L');
	$pdf->Cell(0,4,'',"R",1,'L');
	$pdf->Cell(7,4,'[   ]',"L",0,'R');
	$pdf->Cell(43,4,'Recovered',"R",0,'L');
	$pdf->Cell(7,4,'[   ]',"L",0,'R');
	$pdf->Cell(43,4,'Absconded/PNF',"R",0,'L');
	$pdf->Cell(0,4,'',"R",1,'L');
	$pdf->Cell(7,4,'[   ]',"L",0,'R');
	$pdf->Cell(43,4,'Improved',"R",0,'L');
	$pdf->Cell(7,4,'[   ]',"L",0,'R');
	$pdf->Cell(43,4,'For OPD',"R",0,'L');
	$pdf->Cell(6,4,'',"L",0,'');
	$pdf->Cell(78,4,mb_strtoupper($attending_physician),"B",0,'C');
	$pdf->Cell(6,4,'',"R",1,'');
	$pdf->Cell(7,4,'[   ]',"L",0,'R');
	$pdf->Cell(43,4,'Unimproved',"R",0,'L');
	$pdf->Cell(7,4,'',"L",0,'R');
	$pdf->Cell(43,4,'',"R",0,'L');
	$pdf->Cell(0,4,'Name & Signature of Attending Physician',"R",1,'C');
	$pdf->Cell(7,4,'[   ]',"L",0,'R');
	$pdf->Cell(43,4,'Died',"R",0,'L');
	$pdf->Cell(7,4,'',"L",0,'R');
	$pdf->Cell(43,4,'',"R",0,'L');
	$pdf->Cell(0,4,'',"R",1,'L');
	$pdf->Cell(7,4,'',"LB",0,'R');
	$pdf->Cell(43,4,'',"RB",0,'L');
	$pdf->Cell(7,4,'',"LB",0,'R');
	$pdf->Cell(43,4,'',"RB",0,'L');
	$pdf->Cell(0,4,'',"RB",1,'L');
	*/
	$pdf->Output();	
?>