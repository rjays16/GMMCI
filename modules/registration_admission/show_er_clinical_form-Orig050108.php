<?php
/*		
		$this->sql ="SELECT cp.pid, enc.encounter_nr, 
							cp.name_last, cp.name_first, cp.name_2, cp.name_3, cp.name_middle,
							enc.encounter_date AS er_opd_datetime, 
							dept.name_formal,
							cp.street_name, sb.brgy_name, sm.zipcode, sm.mun_name, sp.prov_name, sr.region_name,
							cp.phone_1_nr, cp.phone_2_nr, cp.cellphone_1_nr, cp.cellphone_2_nr, cp.sex, cp.civil_status,
							fn_get_age(enc.encounter_date,cp.date_birth) AS age,
							cp.date_birth, cp.place_birth,
							sc.country_name AS citizenship, 
							sreli.religion_name AS religion, 
							so.occupation_name AS occupation, 
							cp.mother_name, cp.father_name, cp.spouse_name, cp.guardian_name,							
							enc.informant_name, enc.info_address, enc.relation_informant, 
							enc.encounter_type, 
							enc.referrer_dr AS opd_admitting_physician,
							enc.current_dept_nr,							
							enc.consulting_dr AS admitting_physician,
							enc.modify_id AS admitting_clerk,
							enc.create_id AS admitting_clerk_er_opd,
							enc.referrer_diagnosis AS admitting_diagnosis
						FROM $this->tb_person AS cp, $this->tb_enc AS enc, 
							$this->tb_dept AS dept,
							$this->tb_barangays AS sb, $this->tb_municity AS sm, 
							$this->tb_provinces AS sp, $this->tb_regions AS sr, 
							$this->tb_country AS sc, $this->tb_religion AS sreli, $this->tb_occupation AS so
						WHERE enc.encounter_nr='$encounter_nr'
							AND cp.pid=enc.pid AND dept.nr=enc.current_dept_nr
							AND sr.region_nr=sp.region_nr AND sp.prov_nr=sm.prov_nr 
							AND sm.mun_nr=sb.mun_nr AND sb.brgy_nr=cp.brgy_nr
							AND sc.country_code=cp.citizenship AND sreli.religion_nr = cp.religion 
							AND so.occupation_nr = cp.occupation " ;
*/							
							
	include("roots.php");
	include_once($root_path."/classes/fpdf/fpdf.php");
	
	#added by VAN 02-12-08
	#include_once($root_path."/classes/fpdf/pdf.class.php");
	
	require_once($root_path.'include/inc_environment_global.php');
	include_once($root_path.'include/inc_date_format_functions.php');
	include_once($root_path.'include/care_api_classes/class_encounter.php');
	$enc_obj=new Encounter;
	if ($_GET['encounter_nr']) {
		if (!($enc_info = $enc_obj->getEncounterInfo($_GET['encounter_nr']))){
			echo '<em class="warn">Sorry but the page cannot be displayed!</em>';
			exit();
		}
		#echo "enc_obj->sql = '".$enc_obj->sql."' <br> \n";		
		extract($enc_info);
	}else{
			echo '<em class="warn">Sorry but the page cannot be displayed! <br>Invalid Case Number! </em>';
			exit();	
	}
	$border="1";
	$border2="0";
	$space=2;
	$fontSizeLabel=9;
	$fontSizeInput=12;
	$fontSizeHeading=13;

	//Cell(float w [, float h [, string txt [, mixed border [, int ln [, string align [, int fill [, mixed link]]]]]]])
	#edited by VAN 02-12-08
	$pdf = new FPDF();
	#$pdf = new PDF();
	
	$pdf->AddPage("P");
	$pdf->SetFont("Arial","B",$fontSizeLabel-2);
	$pdf->Cell(0,3,'MRFI 05-1',$border2,1,'R');	

	$pdf->SetFont("Arial","I","11");
	$pdf->Cell(0,4,'Republic of the Philippines',$border2,1,'C');
	$pdf->Cell(0,4,'DEPARTMENT OF HEALTH',$border2,1,'C');

	$pdf->Ln(2);
	$pdf->SetFont("Arial","B","12");
	$pdf->Cell(0,4,'Gonzales Maranan Medical Center Incorporated',$border2,1,'C');

	$pdf->SetFont("Arial","","11");
	$pdf->Cell(0,4,'Digos City',$border2,1,'C');

	$pdf->SetFont('Arial','B',13);
	$pdf->Cell(0,5,'Emergency Room Clinical Form',$border2,1,'C');

	$pdf->Ln(3);
	$pdf->SetFont('Arial','',$fontSizeLabel+2);
	$pdf->Cell(30,4,'Case No.',"$border2",0,'R');
	$pdf->SetFont('Arial','B',$fontSizeHeading);
	$pdf->Cell(65,4,$encounter_nr,"$border2",0,'L');
	$pdf->SetFont('Arial','',$fontSizeLabel+2);
	$pdf->Cell(65,4,'Patient ID',"$border2",0,'R');
	$pdf->SetFont('Arial','B',$fontSizeHeading);
	$pdf->Cell(30,4	,$pid,"$border2",1,'L');

	$pdf->SetFont('Arial','I',$fontSizeLabel);	
	$pdf->Cell(45,4,'Last Name',"TLR",0,'L');
	$pdf->Cell(45,4,'First Name',"TLR",0,'L');
	$pdf->Cell(45,4,'Middle Name',"TLR",0,'L');
	$pdf->Cell(17,4,'Date/Time: ',"TLB",0,'L');
	$pdf->SetFont('Arial','','8',$fontSizeInput);
	$pdf->Cell(38,4,@formatDate2Local($er_opd_datetime,$date_format,1),"TRB",0,'L');
	$pdf->Ln();	

	$pdf->SetFont('Arial','B',$fontSizeInput);
	$pdf->Cell(45,4,mb_strtoupper($name_last),"BLR",0,'L');
	$pdf->Cell(45,4,mb_strtoupper($name_first),"BLR",0,'L');
	$pdf->Cell(45,4,mb_strtoupper($name_middle),"BLR",0,'L');
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(17,4,'Department: ',"TLB",0,'L');
	#$pdf->SetFont('Arial','',$fontSizeInput);
	$pdf->SetFont('Arial','B',9);
	#$pdf->Cell(38,4,' '.$name_formal,"TRB",1,'L');
	$pdf->Cell(38,4,' '.$name_formal,"TRB",1,'L');
	
	#$pdf->Cell(0,4,$pdf->Write(5, $name_formal),"R",1,'L');
	#$pdf->Cell(38,4,'',"TRB",1,'L');

		$address = "$street_name, $brgy_name, $mun_name $zipcode $prov_name";
		#echo "address = '".$address."' <br> \n";
		#echo " strlen(address) = '".strlen($address)."'<br> \n";
		$index = strlen($address);
		if (strlen($address)>65){
			$temp = substr($address,0,65);
		#	echo "temp = '".$temp."' <br> \n";
			$index = strrpos($temp," ");
		}
		#echo "index = '".$index."' <br> \n";
		$address1 = trim(substr($address,0,$index));
		$address2 = trim(substr($address,$index));
		#echo "address1 = '".$address1."' <br> \n";
		#echo "address2 = '".$address2."' <br> \n";

#  $pdf->SetFont('Arial','I',$fontSizeLabel);
#  $pdf->Cell(135,4,'Address ',"TLR",0,'L');
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(15,12,'Address: ',"TL",0,'L');
	$pdf->SetFont('Arial','',$fontSizeInput);
	#edited by VAN 02-12-08
	#$newtext = wordwrap($text, 8, "\n", 1);
	#$add1wrap = wordwrap($address1.'sasddddddddssssssdsssd',10,"\n",1);
	#echo $add1wrap;
	#$address1 = str_repeat($address1,2);
	#$address2 = str_repeat($address2,2);
	$pdf->Cell(120,4,"$address1","TR",0,'L');
	#$pdf->Cell(120,4,$add1wrap,"TR",0,'L');
	#$pdf->Cell(120,4,$pdf->Write(4, $address1).'sddddddddd',"TR",0,'L');
	#$add1 = $pdf->MultiCell(120, 4, $address1, '', 'J','');
	#$pdf->Cell(120,4,$add1,"TR",0,'L');
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(27,4,'Contact Number',"TLR",0,'L');
	$pdf->Cell(7,4,'Sex',"TLR",0,'L');
	$pdf->Cell(21,4,'Civil Status',"TLR",1,'L');
	$pdf->SetFont('Arial','',$fontSizeInput);
#	$pdf->Cell(135,4,"$brgy_name, $mun_name $zipcode $prov_name","LBR",0,'L');
	$pdf->Cell(5,4,'',"BL",0,'L');
#	$pdf->Cell(130,4,"$brgy_name, $mun_name $zipcode $prov_name","BR",0,'L');
	#edited by VAN 02-12-08
	$pdf->Cell(130,4,"$address2","BR",0,'L');
	#$pdf->Cell(130,4,$pdf->Write(4, $address2),"BR",0,'L');
	#$add2 = $pdf->MultiCell(120, 4, $address2, '', 'J','');
	#$pdf->Cell(130,4,$add2,"BR",0,'L');
	
#	$pdf->SetFont('Arial','',$fontSizeInput-1);
	$contact = $phone_1_nr;
	if (!isset($contact) || empty($contact)) $contact = $cellphone_1_nr;
	if (!isset($contact) || empty($contact)) $contact = $phone_2_nr;
	if (!isset($contact) || empty($contact)) $contact = $cellphone_2_nr;
	$pdf->Cell(27,4,$contact,"LBR",0,'L');
#	$pdf->Cell(27,4,"09191234567","LBR",0,'L');
	$pdf->Cell(7,4,mb_strtoupper($sex),"LBR",0,'L');
	$pdf->SetFont('Arial','',$fontSizeInput-2);
	$pdf->Cell(21,4,mb_strtoupper($civil_status),"LBR",1,'L');

	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(22,4,'Birth Date',"TLR",0,'L');
	$pdf->Cell(20,4,'Age',"TLR",0,'L');
	$pdf->Cell(52,4,'Birth Place',"TLR",0,'L');
	$pdf->Cell(32,4,'Citizenship',"TLR",0,'L');
	$pdf->Cell(32,4,'Religion',"TLR",0,'L');
	$pdf->Cell(32,4,'Occupation',"TLR",1,'L');

	$pdf->SetFont('Arial','',$fontSizeInput);
#	$pdf->Cell(31,4,$date_birth,"LBR",0,'L');
	if ($date_birth)
		$pdf->Cell(22,4,@formatDate2Local($date_birth,$date_format),"LBR",0,'L');
	else
		$pdf->Cell(22,4,'',"LBR",0,'L');
	$pdf->Cell(20,4,$age,"LBR",0,'L');
	$pdf->Cell(52,4,$place_birth,"LBR",0,'L');
	$pdf->Cell(32,4,$citizenship,"LBR",0,'L');
	$pdf->Cell(32,4,$religion,"LBR",0,'L');
	$pdf->Cell(32,4,$occupation,"LBR",1,'L');

	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(24,6,'Father\'s Name',"TLB",0,'L');
	$pdf->SetFont('Arial','',$fontSizeInput);
	$pdf->Cell(70,6,$father_name,"TRB",0,'L');
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(25,6,'Spouse\'s Name',"TLB",0,'L');
	$pdf->SetFont('Arial','',$fontSizeInput);
	$pdf->Cell(71,6,$spouse_name,"TRB",1,'L');

	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(24,6,'Mother\'s Name',"TLB",0,'L');
	$pdf->SetFont('Arial','',$fontSizeInput);
	$pdf->Cell(70,6,$mother_name,"TRB",0,'L');
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(25,6,'Guardian\'s Name',"TLB",0,'L');
	$pdf->SetFont('Arial','',$fontSizeInput);
	$pdf->Cell(71,6,$guardian_name,"TRB",1,'L');

	$pdf->Ln($space);
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(64,4,'Date Furnished By',"TLR",0,'L');
	$pdf->Cell(63,4,'Signature of Informant',"TLR",0,'L');
	$pdf->Cell(63,4,'Relation to Patient',"TLR",1,'L');
	$pdf->SetFont('Arial','',$fontSizeLabel);
	$pdf->Cell(64,4,'',"BLR",0,'L');
	$pdf->Cell(63,4,mb_strtoupper($informant_name),"BLR",0,'L');
	$pdf->Cell(63,4,'',"BLR",1,'L');

	$pdf->Ln($space);
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(40,6,'Social Service Classification', "TLB",0,'L');
	$pdf->SetFont('Arial','',$fontSizeInput);
	$pdf->Cell(0,6,'[ A ]   [ B ]  [ C ]  [ D ]',"TRB",1,'L');

	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(40,6,'Date and Time Examined', "TLB",0,'L');
	$pdf->SetFont('Arial','',$fontSizeInput);
	$pdf->Cell(0,6,'',"TRB",1,'L');
	$pdf->Cell(0,26,'  S',1,1,'L');
	$pdf->Cell(0,26,'  O',1,1,'L');
	$pdf->Cell(0,26,'  A',1,1,'L');
	$pdf->Cell(0,26,'  P',1,1,'L');
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(150,5,'Diagnosis',"TLR",0,'L');
	$pdf->Cell(40,5,'ICD Code',"TLR",1,'L');
	$pdf->Cell(150,20,'',"LBR",0,'L');
	$pdf->Cell(40,20,'',"LBR",1,'L');
	$pdf->Ln($space);
	$pdf->Cell(50,4,'Condition at ER',"TLR",0,'L');
	$pdf->Cell(50,4,'Disposition',"TLR",0,'L');
	$pdf->Cell(0,4,'',"TLR",1,'L');
	$pdf->SetFont('Arial','',$fontSizeLabel);
	$pdf->Cell(7,4,'[   ]',"L",0,'R');
	$pdf->Cell(43,4,'Conscious',"R",0,'L');
	$pdf->Cell(7,4,'[   ]',"L",0,'R');
	$pdf->Cell(43,4,'Admitted',"R",0,'L');
	$pdf->Cell(0,4,'',"R",1,'L');
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
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(50,4,'Results',"TLR",0,'L');
	$pdf->SetFont('Arial','',$fontSizeLabel);
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
	$pdf->Cell(78,4,mb_strtoupper($er_opd_admitting_physician_name),"B",0,'C');
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
	$pdf->Output();	
?>