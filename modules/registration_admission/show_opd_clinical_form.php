<?php
	#edited by VAN 05-01-08
	include("roots.php");
	#commented by Art 01/16/2014
	//include_once($root_path."/classes/fpdf/fpdf.php");
	//include_once($root_path."/classes/fpdf/pdf.class.php"); 
	include_once($root_path."/classes/fpdf/cert-pdf-nocode.php"); #added by Art 01/16/2014
	require_once($root_path.'include/inc_environment_global.php');
	include_once($root_path.'include/inc_date_format_functions.php');

	require_once($root_path.'/include/care_api_classes/class_drg.php');
	$objDRG= new DRG;

	include_once($root_path.'include/care_api_classes/class_encounter.php');
	$enc_obj=new Encounter;

	#Added by Genz
	include_once($root_path.'include/care_api_classes/class_person.php');
	$person_obj=& new Person($pid);

	require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
	$objInfo = new Hospital_Admin();

	#added by Genesis Ortiz (06-04-2014)
	require_once($root_path.'include/care_api_classes/class_vitalsign.php');
	$vitals_obj = new SegVitalsign();

	
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
	#$fontSizeLabel=9;
	$fontSizeLabel=10;
	$fontSizeInput=12;
	$fontSizeHeading=13;

	//Cell(float w [, float h [, string txt [, mixed border [, int ln [, string align [, int fill [, mixed link]]]]]]])
	//s$pdf = new FPDF();
	#$pdf = new PDF();
	$pdf  = new PDF("P","mm","Letter");//added by art 01/16/2014
	$pdf->AliasNbPages();   #--added
	#$pdf->SetAutoPageBreak('true','10');
	$pdf->AddPage("P");
	/* commented by art 01/18/2014
	$pdf->SetFont('Arial','',$fontSizeLabel+3);
	$pdf->Cell(150,4,'HRN : ',"$border2",0,'R');
	$pdf->SetFont('Arial','B',$fontSizeHeading+8);
	$pdf->Cell(0,4	,$pid,"$border2",0,'R');
	$pdf->Ln(1); */
/*
	$pdf->SetFont("Arial","","11");
	$pdf->Cell(0,4,'Republic of the Philippines',$border2,1,'C');
	#$pdf->SetFont("Arial","","11");
	$pdf->Cell(0,4,'DEPARTMENT OF HEALTH',$border2,1,'C');

	$pdf->Ln(1);
	$pdf->SetFont("Arial","B","12");
	$pdf->Cell(0,4,'Gonzales Maranan Medical Center Incorporated',$border2,1,'C');

	$pdf->SetFont("Arial","","11");
	$pdf->Cell(0,4,'Bajada, Davao City',$border2,1,'C');
*/
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

	$pdf->SetFont("Arial","","11");
	// $pdf->Cell(0,4,$row['hosp_country'],$border2,1,'C');
	#$pdf->SetFont("Arial","","11");
	// $pdf->Cell(0,4,$row['hosp_agency'],$border2,1,'C');

	$pdf->Ln(1);
	$pdf->SetFont("Arial","B","14");
	$pdf->Cell(0,4,$row['hosp_name'],$border2,1,'C');

	$pdf->SetFont("Arial","","11");
	$pdf->Cell(0,4,$row['hosp_addr1'],$border2,1,'C');
	
	$pdf->SetFont('Arial','B',"14");
	$pdf->Ln(5);
	$pdf->Cell(0,5,'Outpatient Preventive Care Center Clinical Form',$border2,1,'C');

	//added by art 01/18/2014
	// $pdf->Image('image/logo_doh.jpg',25,10,20,20);
	$pdf->Image('image/dmc_logo.jpg',15,5,30,30);
	//end art

	#$pdf->Ln($space*2);
	$pdf->Ln($space*6);
	
	$pdf->SetFont('Arial','',$fontSizeLabel+2);
	$pdf->Cell(15,4,'HRN : ',"$border2",0,'L');
	$pdf->SetFont('Arial','B',$fontSizeHeading+5);
	$pdf->Cell(15,4,$pid,"$border2",0,'L');
	$pdf->SetFont('Arial','',$fontSizeLabel+3);
	$pdf->Cell(45,4,'Case No. : ',"$border2",0,'R');
	$pdf->SetFont('Arial','B',$fontSizeHeading+5);
	$pdf->Cell(40,4	,$encounter_nr,"$border2",0,'L');

	$pdf->SetFont('Arial','',$fontSizeLabel);
	$pdf->Cell(25,4,'Department : ',"$border2",0,'R');
	$pdf->SetFont('Arial','B',12);
	$pdf->Cell(60,4	,mb_strtoupper($name_formal),"$border2",0,'L');

	$pdf->Ln($space*3);
	#$pdf->SetFont('Arial','',$fontSizeLabel+3);
	#$pdf->Cell(27,4,'Department : ',"",0,'L');
	#$pdf->SetFont('Arial','B',$fontSizeHeading+3);
	#$pdf->MultiCell(80, 4,mb_strtoupper($name_formal), '0', 'L','');
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(48,4,'Last Name',"TR",0,'L');
	$pdf->Cell(48,4,'First Name',"TLR",0,'L');
	$pdf->Cell(48,4,'Middle Name',"TLR",0,'L');
	$pdf->Cell(48,4,'Maiden Name',"TL",0,'L');
	
	$pdf->Ln();	
	
	$x = $pdf->GetX();
	$y = $pdf->GetY();
	
	$pdf->SetFont('Arial','B',$fontSizeLabel+3);
	$pdf->Cell(48,12,'',"RB",0,'L');
	$pdf->Cell(48,12,'',"LRB",0,'L');
	$pdf->Cell(48,12,'',"LRB",0,'L');
	$pdf->Cell(48,12,'',"LB",0,'L');
	
	$pdf->SetXY($x, $y);
	$pdf->MultiCell(47, 4, mb_strtoupper($name_last), '', 'L','');
	
	$pdf->SetXY($x+48, $y);
	$pdf->MultiCell(47, 4, mb_strtoupper($name_first), '0', 'L','');
	
	$pdf->SetXY($x+96, $y);
	$pdf->MultiCell(47, 4, mb_strtoupper($name_middle), '0', 'L','');
	
	$pdf->SetXY($x+144, $y);
	$pdf->MultiCell(47, 4, mb_strtoupper($name_maiden), '0', 'L','');
	#$pdf->Ln($space*2);
	$pdf->SetY($y+12);
	#$pdf->Ln($space*2);
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(10,8,'Age : ',"",0,'L');
	$pdf->SetFont('Arial','',$fontSizeLabel);
	#if ($age)
	#	$age = $age." old ";
	
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
			
			$age = $age." ".$labelyear;
		}else
			$age = "0 day";		
	}
	
	$pdf->Cell(38, 8, $age." old", '', 0,'L');
	
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(10,8,'Sex : ',"",0,'L');
	$pdf->SetFont('Arial','',$fontSizeLabel);
	
	if ($sex=='f')
		$gender = 'FEMALE';
	elseif ($sex=='m')
		$gender = 'MALE';	
	
	$pdf->Cell(38, 8, mb_strtoupper($gender), '', 0,'L');
	
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(22,8,'Civil Status : ',"",0,'L');
	$pdf->SetFont('Arial','',$fontSizeLabel);
	$pdf->Cell(28, 8, mb_strtoupper($civil_status), '', 0,'L');
	
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(27,8,'Contact Number : ',"",0,'L');
	$pdf->SetFont('Arial','',$fontSizeLabel);
	
	// $contact = $phone_1_nr;
	$contact = $person_obj->getcontactno($pid);
	if (!isset($contact) || empty($contact)) $contact = $cellphone_1_nr;
	if (!isset($contact) || empty($contact)) $contact = $phone_2_nr;
	if (!isset($contact) || empty($contact)) $contact = $cellphone_2_nr;
	
	$pdf->Cell(22, 8, $contact, '', 1,'L');
	
	$pdf->SetY($y+17);
	
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(20,8,'Address : ',"",0,'L');
	
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	
	$x = $pdf->GetX();
	$y = $pdf->GetY();
	
	$pdf->SetFont('Arial','',$fontSizeLabel);
	$pdf->SetXY($x-2, $y+2);
	/*
	if ($street_name)
		$street_name = "$street_name, ";
	else
		$street_name = "";	
		
	if (!($brgy_name))
		$brgy_name = "NOT PROVIDED";
		
	if(stristr(trim($mun_name), 'city') === FALSE){
		if (!empty($mun_name)){
			$province = ", ".trim($prov_name);
		}else{
			$province = trim($prov_name);;
		}
	}	
	
	#$address = trim($street_name)." ".trim($brgy_name).", ".trim($mun_name)." ".trim($zipcode)." ".trim($prov_name);
	$address = trim($street_name)." ".trim($brgy_name).", ".trim($mun_name)." ".$province;
	*/
	
	if ($street_name){
		if ($brgy_name!="NOT PROVIDED")
			$street_name = $street_name.", ";
		else
			$street_name = $street_name.", ";	
	}#else
		#$street_name = "";	
				
				
		
	if ((!($brgy_name)) || ($brgy_name=="NOT PROVIDED"))
		$brgy_name = "";
	else 
		$brgy_name  = $brgy_name.", ";	
					
	if ((!($mun_name)) || ($mun_name=="NOT PROVIDED"))
		$mun_name = "";		
	else{	
		if ($brgy_name)
			$mun_name = $mun_name;	
		#else
			#$mun_name = $mun_name;		
	}			
	
	if ((!($prov_name)) || ($prov_name=="NOT PROVIDED"))
		$prov_name = "";		
	#else
	#	$prov_name = $prov_name;			
				
	if(stristr(trim($mun_name), 'city') === FALSE){
		if ((!empty($mun_name))&&(!empty($prov_name))){
			if ($prov_name!="NOT PROVIDED")	
				$prov_name = ", ".trim($prov_name);
			else
				$prov_name = "";	
		}else{
			#$province = trim($prov_name);
			$prov_name = "";
		}
	}else
		$prov_name = " ";	
				
	$address = $street_name.$brgy_name.$mun_name.$prov_name;
	
	$pdf->MultiCell(170, 4,mb_strtoupper($address), '0', 'L','');
	
	$pdf->Ln($space);
	#$x = $pdf->GetX();
	#$y = $pdf->GetY();
	#$pdf->SetXY($x, $y);
	#$pdf->SetY($y+13);
	$pdf->SetY($y+5);
	
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(55,8,'Country of Nationality : ',"",0,'L');
	$pdf->Cell(55,8,'Religion : ',"",0,'L');
	$pdf->Cell(55,8,'Occupation : ',"",0,'L');
	$pdf->Ln($space*2);
	
	$pdf->SetFont('Arial','',$fontSizeLabel);
	$pdf->Cell(55, 8, mb_strtoupper($citizenship), '', 0,'L');
	$pdf->Cell(55, 8, mb_strtoupper($religion), '', 0,'L');
	$pdf->Cell(55, 8, mb_strtoupper($occupation), '', 0,'L');
	
	$pdf->Ln($space);
	#$pdf->SetY($y+22);
	$pdf->SetY($y+13);
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(110,8,'Birth Place : ',"",0,'L');
	$pdf->Cell(55,8,'Birth Date : ',"",0,'L');
	#$pdf->Cell(55,8,'Department : ',"",0,'L');
	
	$pdf->Ln($space*3);
	
	$pdf->SetFont('Arial','',$fontSizeLabel);
	#$pdf->Cell(55, 4, mb_strtoupper($place_birth), '', 0,'L');
	$x = $pdf->GetX();
	$y = $pdf->GetY();
	
	$pdf->SetXY($x, $y);
	
	$pdf->MultiCell(110, 4,mb_strtoupper($place_birth), '', 'L','');
	
	$pdf->SetXY($x+110, $y);
	if ($date_birth)
		#$pdf->Cell(55,4,@formatDate2Local($date_birth,$date_format),"",0,'L');
		$pdf->MultiCell(50, 4,@formatDate2Local($date_birth,$date_format), '0', 'L','');
	else
		#$pdf->Cell(55,4,'',"",0,'L');
		$pdf->MultiCell(50, 4,'', '', 'L','');
		
	#$pdf->SetXY($x+110, $y);	
	#$pdf->Cell(55, 4, mb_strtoupper($name_formal), '', 0,'L');
	#$pdf->MultiCell(80, 4,mb_strtoupper($name_formal), '0', 'L','');
    
    #added by Genesis D. Ortiz
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(70,12,'Informant\'s Name : ',"",0,'L');
	#$pdf->Cell(70,8,'Informant\'s Address : ',"",0,'L');
	$pdf->Cell(70,12,'Relation to Patient : ',"",0,'L');
	//$pdf->SetX($x);
	$pdf->Cell(52,60,'Discharge Date/Time :',"",0,'L');

	$pdf->SetX($x);
	$pdf->Cell(70,35,'BEEN ADMITTED LATELY :  ________________       _______________ ',"",0,'L');
	$pdf->SetX($x+15);
	$pdf->Cell(70,60,'____________________',"",0,'L');
	$pdf->SetX($x+76);
	$pdf->Cell(70,60,'____________________',"",0,'L');

	
	#$pdf->Ln($space*4);
	$pdf->Ln($space);
	
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	// $pdf->Cell(70,8,'Name of Father : ',"T",0,'L');
	// $pdf->Cell(70,8,'Name of Mother : ',"T",0,'L');
	// $pdf->Cell(52,8,'Name of Guardian :',"T",0,'L');

	#added by Genesis Ortiz(06-07-2014)
	$pdf->Cell(70,65,'',"T",0,'L');
	$pdf->Cell(70,80,'',"T",0,'L');
	$pdf->Cell(50,8,'Hospitalization Plan : ',"T",0,'L');
	
	$pdf->Ln($space*3);
	
	$x = $pdf->GetX();
	$y = $pdf->GetY();
	
	$pdf->SetX($x+57);
	$pdf->Cell(68, 29,"YES", '', 'L','');
	$pdf->SetX($x+95);
	$pdf->Cell(68, 29,"NO", '', 'L','');
	$pdf->SetX($x);
	$pdf->Cell(68, 45,"WHEN:", '', 'L','');
	$pdf->SetX($x+60);
	$pdf->Cell(68, 45,"WHERE:", '', 'L','');
	$pdf->Ln($space*10);

	#end added by Genesis Ortiz(06-07-2014)
	$pdf->SetFont('Arial','',$fontSizeLabel);
	
	$FmiddleInitial = "";
		/*if (trim($father_mname)!=""){
			$thisMI=split(" ",$father_mname);
			$pdf->SetXY($x+70, $y+4);	
			foreach($thisMI as $value){
				if (!trim($value)=="")
					$FmiddleInitial .= $value[0];
			}
			if (trim($FmiddleInitial)!="")
				$FmiddleInitial = " ".$FmiddleInitial.".";	
		}
	
	$father_name = $father_fname." ".$FmiddleInitial." ".$father_lname;
	
	if(strlen($father_name) > 2 OR strlen($mother_name) > 2){
		$pdf->MultiCell(70, -35,mb_strtoupper($father_name), '', 'L','');
		$pdf->SetX($x+70);
		$pdf->Cell(50, 35,mb_strtoupper("father"), '', 'L','');

		if(strlen($mother_name) > 3){
		$pdf->MultiCell(70, -35,'', '', 'L','');
		$pdf->SetX($x+70);
		$pdf->Cell(50, 35,'', '', 'L','');	
		}
	}*/

	if($father_fname){
		$informant_name = $father_fname." ".$father_lname;
		$relation = "father";
	}

	else if($mother_fname){
		$informant_name = $mother_fname." ".$mother_lname;
		$relation = "mother";
	}

	else if($spouse_name){
		$informant_name = $spouse_name;
		$relation = "spouse";
	}

	else if($guardian_name){
		$informant_name = $guardian_name;
		$relation = "guardian";
	}
	else{
		$informant_name = "";
		$relation = "";
	}

	$pdf->MultiCell(70, -35,mb_strtoupper($informant_name), '', 'L','');
	$pdf->SetX($x+70);
	$pdf->Cell(50, 35,mb_strtoupper($relation), '', 'L','');

	//$pdf->SetXY($x+70, $y);	
	#$pdf->Cell(55, 4, mb_strtoupper($name_formal), '', 0,'L');
	$MmiddleInitial = "";
		if (trim($mother_mname)!=""){
			$thisMI=split(" ",$mother_mname);	
			foreach($thisMI as $value){
				if (!trim($value)=="")
					$MmiddleInitial .= $value[0];
			}
			if (trim($MmiddleInitial)!="")
				$MmiddleInitial = " ".$MmiddleInitial.".";
		}
	
	$mother_name = $mother_fname." ".$MmiddleInitial." ".$mother_lname;


	// $pdf->SetXY($x+140, $y);	
	// #$pdf->Cell(55, 4, mb_strtoupper($name_formal), '', 0,'L');
	// $pdf->MultiCell(55, 4,mb_strtoupper($guardian_name), '', 'L','');
	
	#$pdf->SetY($y+8);
	//$pdf->SetY($y+3);
	
	// $pdf->SetFont('Arial','I',$fontSizeLabel);
	// $pdf->Cell(70,8,'Name of Spouse : ',"",0,'L');
	// $pdf->Cell(70,8,'Employer : ',"",0,'L');
	
	// if ($parent_mss_id)
	// 	$mss_classification = $parent_mss_id." (".$mss_id.")";
	// else
	// 	$mss_classification = $mss_id;	
	// $pdf->Cell(25,8,'MSS CLASS : ',"",0,'L');
	
	// if ($encounter_status=='phs')
	// 	$mss_classification = 'CLASS D';
	
	// $pdf->SetFont('Arial','',$fontSizeLabel+2);
	// $pdf->Cell(40,8,$mss_classification,"",0,'L');
	
	// $pdf->Ln($space);
	
	// $x = $pdf->GetX();
	// $y = $pdf->GetY();
	
	// $pdf->SetXY($x, $y+4);
	
	// $pdf->SetFont('Arial','',$fontSizeLabel);
	// $pdf->MultiCell(68, 4,mb_strtoupper($spouse_name), '', 'L','');
	
	// $pdf->SetXY($x+70, $y+4);	
	// #$pdf->Cell(55, 4, mb_strtoupper($name_formal), '', 0,'L');
	// $pdf->MultiCell(68, 4,mb_strtoupper($employer), '', 'L','');

	#added By Genesis D. Ortiz (06-03-2014)
	$pdf->SetXY($x+140, $y);
	$infoResult4 = $objDRG->getinsurance($pid);

        while($res = $infoResult4->FetchRow()){
			$firm_desc = $res['firm_id'];
				  if ( $res['hcare_id'] == 18)  {
				  	$if_phic = 1;
				  	$if_phic_desc = "PHIC";
				  } else{
				  	$if_nonphic = 1;
				  	$if_notphic_desc =  $res['firm_id'];
				  }                     
        }
		if ($if_phic == 1 && $if_nonphic == 1){
			$str_ins = "PHIC(".$if_notphic_desc.")";
		}
		else if ($if_phic == 1){
			$str_ins = "PHIC";
		}
		else if ($if_nonphic == 1){
			$str_ins = $if_notphic_desc;
		}

		 if ($firm_desc)
		        $pdf->MultiCell(55, 4,$str_ins, '', 'L','');
		    else
		        $pdf->MultiCell(55, 4,"NON-PHIC", '', 'L','');
#end Added By Genesis D. Ortiz

	
	$pdf->Ln($space);
	
	$pdf->SetY($y+7);
	
	// $pdf->SetFont('Arial','I',$fontSizeLabel);
	// $pdf->Cell(70,8,'Name of Spouse : ',"",0,'L');
	// $pdf->Cell(70,8,'Employer : ',"",0,'L');
	// $pdf->Cell(52,8,'MSS CLASS : ',"",0,'L');

	$pdf->SetFont('Arial','I',$fontSizeLabel);
	// $pdf->Cell(70,8,'Name of Spouse : ',"",0,'L');
	// $pdf->Cell(70,8,'Employer : ',"",0,'L');
	// $pdf->Cell(52,8,'Admission Date/Time : ',"",0,'L');

	$pdf->Cell(70,8,'',"",0,'L');
	$pdf->Cell(70,8,'',"",0,'L');
	$pdf->Cell(52,8,'Consultation Date/Time : ',"",0,'L');
	$pdf->setX($x+145);
	$pdf->SetFont('Arial','B',$fontSizeLabel+1);
	$pdf->Cell(52,15,@formatDate2Local($er_opd_datetime,$date_format,1),"",0,'L');
	
	// $y = $pdf->GetY();
	// #$pdf->SetY($y+4);	
	// $pdf->SetY($y);

	$pdf->Ln(23);	
	
	$pdf->Cell(192,2,'',"T",1,'L');
		
	#$pdf->Ln($space);
	#echo "nd = ".$er_opd_datetime;
	//$patient_OR = $enc_obj->getPatientOPDORNoforAnEncounter($pid, $er_opd_datetime);
	#echo "sql = ".$enc_obj->sql;
	#echo "count = ".$enc_obj->count;
	#print_r($patient_OR);
	#added by VAN 05-08-08 #Edited by Jarel 07/17/2013
	// if($official_receipt_nr && $official_receipt_nr != 'WCPU'){
	// 	$ornum = $official_receipt_nr;
	// }elseif ($enc_obj->count){
	// 	$ornum = trim($patient_OR['or_no']);
	// }else{
	// 	if ($senior_ID){
	// 		$ornum = "SENIOR CITIZEN";
	// 	}elseif($personnelID){
	// 		$ornum = "DMC PERSONNEL";
	// 	}elseif ($encounter_status=='phs')
	// 		$ornum = "PERSONNEL DEPENDENT";	
	// }
	#---------------------------
	
	
	// $pdf->SetFont('Arial','I',$fontSizeLabel);
	// $pdf->Cell(25,4,'OR Number : ',"",0,'L');
	// $pdf->SetFont('Arial','B',$fontSizeLabel);
	// $pdf->Cell(50,4,$ornum,"",0,'L');
	// $pdf->Cell(25,4,'',"",0,'L');
	// $pdf->SetFont('Arial','I',$fontSizeLabel);
	// $pdf->Cell(25,4,'Amount Paid : ',"",0,'L');
	// $pdf->SetFont('Arial','',$fontSizeLabel);
	// $pdf->Cell(10,4,'Php ',"",0,'L');
	// $pdf->Cell(50,4,number_format($patient_OR['amount_paid'],2),"",1,'L');
     $pdf->Ln($space);     
    #added by VAN 10-12-2011
    $pdf->SetFont('Arial','I',$fontSizeLabel);
    $pdf->Cell(35,4,'History of Smoking : ',"",0,'L');
    $pdf->SetFont('Arial','B',$fontSizeLabel);
    
    if ($smoker_history=='yes'){
        $smoker_yes = '/';
        $smoker_no = '';
        $smoker_na = '';
    }elseif ($smoker_history=='no'){
        $smoker_yes = '';
        $smoker_no = '/';
        $smoker_na = '';
    }elseif ($smoker_history=='na'){
        $smoker_yes = '';
        $smoker_no = '';
        $smoker_na = '/';
    }else{
        $smoker_yes = '';
        $smoker_no = '';
        $smoker_na = '';
    }
            
    $pdf->Cell(5,4,$smoker_yes,1,"","C");
    $pdf->SetFont('Arial','',$fontSizeLabel);
    $pdf->Cell(10,4,"YES","","","L");
    $pdf->SetFont('Arial','B',$fontSizeLabel);
    $pdf->Cell(5,4,$smoker_no,1,"","C");
    $pdf->SetFont('Arial','',$fontSizeLabel);
    $pdf->Cell(10,4," NO","","","L");
    $pdf->SetFont('Arial','B',$fontSizeLabel);
    $pdf->Cell(5,4,$smoker_na,1,"","C");
    $pdf->SetFont('Arial','',$fontSizeLabel);
    $pdf->Cell(10,4,"N/A","",1,"L");
    $pdf->SetFont('Arial','I',$fontSizeLabel);
	
	$pdf->Ln($space);
	
    $pdf->Cell(35,4,'Alcohol Drinker : ',"",0,'L');
    $pdf->SetFont('Arial','B',$fontSizeLabel);
    
    if ($drinker_history=='yes'){
        $drinker_yes = '/';
        $drinker_no = '';
        $drinker_na = '';
    }elseif ($smoker_history=='no'){
        $drinker_yes = '';
        $drinker_no = '/';
        $drinker_na = '';
    }elseif ($smoker_history=='na'){
        $drinker_yes = '';
        $drinker_no = '';
        $drinker_na = '/';
    }else{
        $drinker_yes = '';
        $drinker_no = '';
        $drinker_na = '';
    }
            
    $pdf->Cell(5,4,$drinker_yes,1,"","C");
    $pdf->SetFont('Arial','',$fontSizeLabel);
    $pdf->Cell(10,4,"YES","","","L");
    $pdf->SetFont('Arial','B',$fontSizeLabel);
    $pdf->Cell(5,4,$drinker_no,1,"","C");
    $pdf->SetFont('Arial','',$fontSizeLabel);
    $pdf->Cell(10,4," NO","","","L");
    $pdf->SetFont('Arial','B',$fontSizeLabel);
    $pdf->Cell(5,4,$drinker_na,1,"","C");
    $pdf->SetFont('Arial','',$fontSizeLabel);
    $pdf->Cell(10,4,"N/A","",1,"L");
    #--------------
    
	$pdf->Ln($space*2);

	// $pdf->SetFont('Arial','',8);
	// $pdf->Cell(50,4,"Registered By : ".$registered_by,"",1,'L');
	/* commented out by art 01/16/2014
	$pdf->Ln(3);
	$pdf->Cell(50,4,"Encoded By : ".$encoded_by,"",0,'L');
	*/

	#Added By Genesis Ortiz (06-04-2014)
	$pdf->setY(-147);

	## populates the vital sign details of encounter with the oldest ##
	$rowvitaldataResult = $vitals_obj->getOldestVitalDetailsbyPid($pid,$encounter_nr);
	$rowvitaldata = $rowvitaldataResult->FetchRow();
	$pdf->Cell(110,3,'',"",0,'L');
	$pdf->SetFont('Arial','I',9);
	$pdf->Cell(50,3,"Temperature(T) : ".$rowvitaldata['temp'],"",1,'L');
	$pdf->Ln($space);
	$pdf->Cell(110,3,'',"",0,'L');
	$pdf->SetFont('Arial','I',9);
	$pdf->Cell(50,3,"Pulse Rate(PR) : ".$rowvitaldata['pulse_rate']." bpm","",1,'L');
	$pdf->Ln($space);
	$pdf->Cell(110,3,'',"",0,'L');
	$pdf->SetFont('Arial','I',9);
	$pdf->Cell(50,3,"Resp. Rate(RR) : ".$rowvitaldata['resp_rate']." bpm","",1,'L');
	$pdf->Ln($space);
	$pdf->Cell(110,4,'',"",0,'L');
	$pdf->SetFont('Arial','I',9);
	$pdf->Cell(50,3,"Blood Pressure(BP) : ".$rowvitaldata['systole']." / ".$rowvitaldata['diastole']." mmHg","",0,'L');
	$pdf->setY(5);
	$pdf->SetFont('Arial','B',10);
	#end Added by Genesis Ortiz (06-04-2014)


	#added By Genesis D. Ortiz (06-03-2014)	
	$pdf->setY(-135);
	// if ((($allow_opd_user)&&($ptype=='opd'))||(($allow_er_user)&&($ptype=='er'))){	
	// 	$pdf->SetFont('Arial','I',10);
	// 	$pdf->Cell(50,4,"Cheif Complaint : ","",1,'L');
	// 	$pdf->Cell(50,4, trim($chief_complaint),"",1,'L');
	// 	$pdf->Ln($space);
	// }
	// $pdf->SetFont('Arial','I',10);
	// 	$pdf->Cell(50,4,"Cheif Complaint : ","",1,'L');
	// 	$pdf->Cell(50,4, trim($chief_complaint),"",1,'L');
	// 	$pdf->Ln($space);
	#end added By Genesis D. Ortiz (06-03-2014)

	$pdf->SetFont('Arial','B',$fontSizeLabel);
	$pdf->Cell(0,13,'  S',"",1,'L');
	$pdf->Cell(0,13,'  O',"",1,'L');
	$pdf->Cell(0,13,'  A',"",1,'L');
	$pdf->Cell(0,13,'  P',"",1,'L');

	//added by art 01/18/2014
	$pdf->setY(-83);
	// $pdf->SetFont('Arial','I',8);
	// $pdf->Cell(50,4,"Encoded By : ".$encoded_by,"",0,'L');
	// $pdf->setY(5);
	// $pdf->SetFont('Arial','B',12);
	// $pdf->setY(35);
	// $pdf->Cell(0,4,"SPMC-F-MRO-01", "",0, 'R');
	//end art
$dr_attending = "";
$items = $enc_obj->getEncounterDRAddList($encounter_nr);
if ($items) {
	foreach ($items as $row) {
		if ($row['is_admitting'] == '1') {
			$dr_admitting = "DR. ".$row['name']."\n";
		}else{
			$dr_attending.="DR. ".$row['name']."\n";
		}
	}
}

#Added By Geensis Ortiz (06-04-2014)
	// if (isset($is_discharged) && $is_discharged){
	// 	if ( ($encounter_type==3) || ($encounter_type==4) ){
	// #		$admitting_dr=$er_opd_admitting_physician_nr;
	// #		$attending_dr=$attending_physician_nr;
	// 		$admitting_dr=$er_opd_admitting_physician_name;
	// 		$attending_dr=$attending_physician_name;
	// 	}else{
	// #		$attending_dr=$attending_physician_nr;
	// 		$attending_dr=$attending_physician_name;
	// 	}
	// }else{
	// 		# assuming that ONLY ecnounters with encounter_type==3 or 4
	// 	#$attending_dr='';
	// 	$attending_dr=$attending_physician_name;
	// 	$admitting_dr=$er_opd_admitting_physician_name;	
	// }

#end added By Genesis D. Ortiz (06-03-2014)
	
#added By Genesis D. Ortiz (06-03-2014)
	$pdf->SetFont('Arial','',$fontSizeLabel);
	$pdf->Cell(70,4,'Condition at OPD',"TLR",0,'L');
	$pdf->Cell(60,4,'Disposition',"TLR",0,'L');
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(0,4,'Result',"TLR",1,'L');
	
	$pdf->SetFont('Arial','',$fontSizeLabel);
	$resx=$pdf->GetX();
	$resy=$pdf->GetY();
	$pdf->Cell(7,4,'[   ]',"L",0,'R');
	$pdf->SetY($resy);
	$pdf->SetX($resx);
	$pdf->SetFont('Arial','B',$fontSizeLabel+2);
	$pdf->Cell(7,4,$recovered_mark,"",0,'C');
	$pdf->SetFont('Arial','',$fontSizeLabel);
	$pdf->Cell(21,4,'A. Conscious',"0",0,'L');

	$resx=$pdf->GetX();
	$resy=$pdf->GetY();
	$pdf->Cell(7,4,'[   ]',"0",0,'R');
	$pdf->SetY($resy);
	$pdf->SetX($resx);
	$pdf->SetFont('Arial','B',$fontSizeLabel+2);
	$pdf->Cell(7,4,$died_mark,"",0,'C');
	$pdf->SetFont('Arial','',$fontSizeLabel);
	$pdf->Cell(35,4,'B. Semi-Conscious',"R",0,'L');

	$resx=$pdf->GetX();
	$resy=$pdf->GetY();
	$pdf->Cell(7,4,'[   ]',"L",0,'R');
	$pdf->SetY($resy);
	$pdf->SetX($resx);
	$pdf->SetFont('Arial','B',$fontSizeLabel+2);
	$pdf->Cell(7,4,$discharged_mark,"",0,'C');
	$pdf->SetFont('Arial','',$fontSizeLabel);
	$pdf->Cell(25,4,'Discharged',"0",0,'L');

	$resx=$pdf->GetX();
	$resy=$pdf->GetY();
	$pdf->Cell(7,4,'[   ]',"0",0,'R');
	$pdf->SetY($resy);
	$pdf->SetX($resx);
	$pdf->SetFont('Arial','B',$fontSizeLabel+2);
	$pdf->Cell(7,4,$absconded_mark,"",0,'C');
	$pdf->SetFont('Arial','',$fontSizeLabel);
	$pdf->Cell(21,4,'Absconded',"R",0,'L');

	$resx=$pdf->GetX();
	$resy=$pdf->GetY();
	$pdf->Cell(7,4,'[   ]',"L",0,'R');
	$pdf->SetY($resy);
	$pdf->SetX($resx);
	$pdf->SetFont('Arial','B',$fontSizeLabel+2);
	$pdf->Cell(7,4,$concious_mark,"",0,'C');
	$pdf->SetFont('Arial','',$fontSizeLabel);
    $pdf->Cell(22,4,'Recovered',"0",0,'L');

    $resx=$pdf->GetX();
	$resy=$pdf->GetY();
    $pdf->Cell(7,4,'[   ]',"0",0,'R');
    $pdf->SetY($resy);
	$pdf->SetX($resx);
	$pdf->SetFont('Arial','B',$fontSizeLabel+2);
	$pdf->Cell(7,4,$semiconcious_mark,"",0,'C');
	$pdf->SetFont('Arial','',$fontSizeLabel);
    $pdf->Cell(0,4,'Died',"R",1,'L');

    $pdf->SetFont('Arial','',$fontSizeLabel);
	$resx=$pdf->GetX();
	$resy=$pdf->GetY();
    // $pdf->Cell(7,4,'[   ]',"L",0,'R');
    $pdf->Cell(7,4,'',"L",0,'R');
	$pdf->SetY($resy);
	$pdf->SetX($resx);
	$pdf->SetFont('Arial','B',$fontSizeLabel+2);
	$pdf->Cell(7,4,$improved_mark,"",0,'C');
	$pdf->SetFont('Arial','',$fontSizeLabel);
	$pdf->Cell(31,4,'',"0",0,'L');

	$resx=$pdf->GetX();
	$resy=$pdf->GetY();
	// $pdf->Cell(7,4,'[   ]',"0",0,'R');
	$pdf->Cell(7,4,'',"0",0,'R');
	$pdf->SetY($resy);
	$pdf->SetX($resx);
	$pdf->SetFont('Arial','B',$fontSizeLabel+2);
	$pdf->Cell(7,4,$autopsy_mark,"",0,'C');
	$pdf->SetFont('Arial','',$fontSizeLabel);
	$pdf->Cell(25,4,'',"R",0,'L');

	$resx=$pdf->GetX();
	$resy=$pdf->GetY();
	$pdf->Cell(7,4,'[   ]',"L",0,'R');
	$pdf->SetY($resy);
	$pdf->SetX($resx);
	$pdf->SetFont('Arial','B',$fontSizeLabel+2);
	$pdf->Cell(7,4,$transfered_mark,"",0,'C');
	$pdf->SetFont('Arial','',$fontSizeLabel);
	$pdf->Cell(21,4,'Transferred',"0",0,'L');
	$pdf->Cell(32,4,'',"R",0,'L');
//	$pdf->Cell(0,4,'Discharge Date/Time',"R",0,'L');

	$resx=$pdf->GetX();
	$resy=$pdf->GetY();
    $pdf->Cell(7,4,'[   ]',"L",0,'R');
    $pdf->SetY($resy);
	$pdf->SetX($resx);
	$pdf->SetFont('Arial','B',$fontSizeLabel+2);
	$pdf->Cell(7,4,$ambulatory_mark,"",0,'C');
	$pdf->SetFont('Arial','',$fontSizeLabel);
    $pdf->Cell(22,4,'Improved',"0",0,'L');
    //$pdf->Cell(22,4,'',"0",0,'L');

    $resx=$pdf->GetX();
	$resy=$pdf->GetY();
    $pdf->Cell(7,4,'[   ]',"0",0,'R');
    //$pdf->Cell(7,4,'',"0",0,'R');
    $pdf->SetY($resy);
	$pdf->SetX($resx);
	$pdf->SetFont('Arial','B',$fontSizeLabel+2);
	$pdf->Cell(7,4,$unconscious_mark,"",0,'C');
	$pdf->SetFont('Arial','',$fontSizeLabel);
    //$pdf->Cell(0,4,'E. Unconscious',"R",1,'L');
    $pdf->Cell(0,4,'Autopsy',"R",1,'L');

    $resx=$pdf->GetX();
	$resy=$pdf->GetY();
	//$pdf->Cell(7,4,'[   ]',"L",0,'R');
	$pdf->Cell(7,4,'',"L",0,'R');
	$pdf->SetY($resy);
	$pdf->SetX($resx);
	$pdf->SetFont('Arial','B',$fontSizeLabel+2);
	$pdf->Cell(7,4,$unimproved_mark,"",0,'C');
	$pdf->SetFont('Arial','',$fontSizeLabel);
	$pdf->Cell(21,4,'',"0",0,'L');

	$resx=$pdf->GetX();
	$resy=$pdf->GetY();
	//$pdf->Cell(7,4,'[   ]',"0",0,'R');
	$pdf->Cell(7,4,'',"0",0,'R');
	$pdf->SetY($resy);
	$pdf->SetX($resx);
	$pdf->SetFont('Arial','B',$fontSizeLabel+2);
	$pdf->Cell(7,4,$no_autopsy_mark,"",0,'C');
	$pdf->SetFont('Arial','',$fontSizeLabel);
	$pdf->Cell(35,4,'',"R",0,'L');

	$resx=$pdf->GetX();
	$resy=$pdf->GetY();
	$pdf->Cell(7,4,'[   ]',"L",0,'R');
	$pdf->SetY($resy);
	$pdf->SetX($resx);
	$pdf->SetFont('Arial','B',$fontSizeLabel+2);
	$pdf->Cell(7,4,$HAMA_mark,"",0,'C');
	$pdf->SetFont('Arial','',$fontSizeLabel);
	$pdf->Cell(21,4,'HAMA',"0",0,'L');
	$pdf->Cell(32,4,'',"R",0,'L');

	$resx=$pdf->GetX();
	$resy=$pdf->GetY();
    $pdf->Cell(7,4,'[   ]',"L",0,'R');
    //$pdf->Cell(7,4,'',"L",0,'R');
	$pdf->SetY($resy);
	$pdf->SetX($resx);
	$pdf->SetFont('Arial','B',$fontSizeLabel+2);
	$pdf->Cell(7,4,$wheelchair_mark,"",0,'C');
	$pdf->SetFont('Arial','',$fontSizeLabel);
    //$pdf->Cell(22,4,'C. Wheelchair',"0",0,'L');
    $pdf->Cell(22,4,'No Autopsy',"0",0,'L');

    $resx=$pdf->GetX();
	$resy=$pdf->GetY();
    $pdf->Cell(7,4,'[   ]',"0",0,'R');
    //$pdf->Cell(7,4,'',"0",0,'R');
    $pdf->SetY($resy);
	$pdf->SetX($resx);
	$pdf->SetFont('Arial','B',$fontSizeLabel+2);
	$pdf->Cell(7,4,$stretcher_mark,"",0,'C');
	$pdf->SetFont('Arial','',$fontSizeLabel);
    //$pdf->Cell(0,4,'F. Stretcher',"R",1,'L');
    $pdf->Cell(0,4,'Unimproved',"R",1,'L');


	
	$pdf->SetFont('Arial','',$fontSizeLabel);
	$pdf->Cell(70,2,'',"BLR",0,'R');
	$pdf->Cell(60,2,'',"BLR	",0,'R');
	$pdf->Cell(0,2,'',"BLR",1,'L');

	$pdf->Ln($space);

	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$note="I have reviewed this record and found it to be accurate and complete.";
	$pdf->Cell(0,8,$note,"0",1,'C');
	
	$pdf->Ln($space*1);
	
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(40,5,"THUMB MARK","0",0,'L');

	$x=$pdf->GetX();
	$y=$pdf->GetY();
	#echo "x, y here = ".$x." , ".$y;
	$pdf->Rect($x-37, $y+6, 20, 20);

	$pdf->SetFont('Arial','',$fontSizeLabel);
	$x = $pdf->GetX();
	$pdf->setX($x+80);
	$pdf->MultiCell(65,4,mb_strtoupper($dr_attending),"B",1,"C");
	$x = $pdf->GetX();
	$y = $pdf->GetY();
	$pdf->setXY($x+133,$y);
	$pdf->MultiCell(50,5,"ATTENDING PHYSICIAN","",1,'L');

	$x = $pdf->GetX();
	$y = $pdf->GetY();
	$pdf->setXY($x+40,$y-10);
	$pdf->Cell(50,5,'',"B",1,'L');
	#$pdf->Ln($space*2);
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(40,5,"","0",0,'L');	
	$pdf->Cell(55,5,"Informant / Patient's Signature","0",0,'L');
	
	$pdf->Ln($space*.05);
	
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	
	//added by art 01/10/2014
	$pdf->SetFont('Arial','B',12);
	$pdf->setY(33);
	$pdf->Cell(0,4,  "", "",0, 'R');

	#end Added By Geensis Ortiz (06-04-2014)

	#$pdf->Ln($space*60);
	#commented by VAN 09-19-08
	/*
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(25,5,'Principal Diagnosis:',"0",1,'L');
	
	
	$pdf->SetFont('Arial','B',$fontSizeLabel);
	$result_diagnosis = array();
	if (isset($is_discharged) && $is_discharged){
		if ($rs_diagnosis = $objDRG->getDiagnosisCodes($_GET['encounter_nr'])){
			$rowsDiagnosis = $rs_diagnosis->RecordCount();
			while($temp=$rs_diagnosis->FetchRow()){
				$temp_diagnosis = array();
				$temp_diagnosis['type'] = $temp['type'];
				$temp_diagnosis['code'] = $temp['code'];
				$temp_diagnosis['diagnosis'] = $temp['diagnosis'];
				array_push($result_diagnosis,$temp_diagnosis);
			}			
		}
	}
	
	if (isset($is_discharged) && ($rowsDiagnosis)){
		$count=0;
		foreach ($result_diagnosis as $value) {
			if ($value['type']==1){
				$pdf->Cell(10,5,"","0",0,'L');
				$pdf->Cell(71,5,$value['code']." : ".$value['diagnosis'],"0",1,'L');
				$count++;
			}
		}
		if (1-$count){
			$pdf->Ln($space*(1-$count));
		}
	}else{
		$pdf->Ln($space*26);
	}
	
	$pdf->Ln($space);
	
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(25,5,'Other Diagnosis:',"0",1,'L');
	
	
	$pdf->SetFont('Arial','B',$fontSizeLabel);
	
	if (isset($is_discharged) && ($rowsDiagnosis)){
		$count=0;
		foreach ($result_diagnosis as $value) {
			if ($value['type']==0){
				$pdf->Cell(10,5,"","0",0,'L');
				$pdf->Cell(71,5,$value['code']." : ".$value['diagnosis'],"0",1,'L');
				$count++;
			}
		}
		if (1-$count){
			$pdf->Ln($space*(1-$count));
		}
	}else{
		$pdf->Ln($space*26);
	}
	
	$pdf->Ln($space);
	
	#if ($is_discharged){
		$pdf->SetFont('Arial','I',$fontSizeLabel);
		$pdf->Cell(10,5,"","0",0,'L');
		$pdf->Cell(45,5,$attending_dr,"0",0,'C');
		$pdf->Ln(1);
		$pdf->Cell(13,3,"","0",0,'L');
		$pdf->Cell(90,3,"________________________","0",1,'L');
		
		$pdf->Cell(15,5,"","0",0,'L');
		$pdf->Cell(85,5,"ATTENDING PHYSICIAN","0",1,'L');

	#}
	*/


	$pdf->Output();	
?>