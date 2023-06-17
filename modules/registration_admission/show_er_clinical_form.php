<?php
	#edited by VAN 05-01-08
	include("roots.php");
	include_once($root_path."/classes/fpdf/fpdf.php");
	#include_once($root_path."/classes/fpdf/pdf.class.php"); commented by art 01/18/2014
	include_once($root_path."/classes/fpdf/cert-pdf-nocode.php"); #added by art 01/18/2014
	
	#added by VAN 02-12-08
	#include_once($root_path."/classes/fpdf/pdf.class.php");
	
	require_once($root_path.'include/inc_environment_global.php');
	include_once($root_path.'include/inc_date_format_functions.php');
	
	include_once($root_path.'include/care_api_classes/class_encounter.php');
	$enc_obj=new Encounter;

	#Added by Genz
	include_once($root_path.'include/care_api_classes/class_person.php');
	$person_obj=& new Person($pid);

	#Added By Genesis Ortiz	(06-03-2014)
	require_once($root_path.'/include/care_api_classes/class_drg.php');
	$objDRG= new DRG;
	
	require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
	$objInfo = new Hospital_Admin();
	
	#echo "enc = ".$_GET['encounter_nr'];
	if ($_GET['encounter_nr']) {
		if (!($enc_info = $enc_obj->getEncounterInfo($_GET['encounter_nr']))){
			echo '<em class="warn">Sorry but the page cannot be displayed!</em>';
			exit();
		}
		#echo "enc_obj = '".$enc_obj->sql."' <br> \n";		
		extract($enc_info);
	}else{
			echo '<em class="warn">Sorry but the page cannot be displayed! <br>Invalid Case Number! </em>';
			exit();	
	}
	
	#echo "enc_obj = '".$enc_obj->sql."' <br> \n";	
	
	$border="1";
	$border2="0";
	$space=2;
	#$fontSizeLabel=9;
	$fontSizeLabel=10;
	$fontSizeInput=12;
	$fontSizeHeading=13;

	// Added by Robert 05/17/2015
	$width = 0;
	$actual_width = 0;
	$computed_width = 0;
	$count_attending_dr = 0;
	// End add by Robert

	//Cell(float w [, float h [, string txt [, mixed border [, int ln [, string align [, int fill [, mixed link]]]]]]])
	#edited by VAN 02-12-08
	#$pdf = new FPDF();
	#$pdf = new PDF(); commented by art 01/18/2014
	$pdf  = new PDF("P","mm","Letter"); #added by art 01/18/2014
	$pdf->AliasNbPages();   #--added
	$pdf->AddPage("P");
	
	$pdf->setY(5); #added by art 01/18/2014
	#added by VAN 06-13-08
	$pdf->SetDisplayMode(real,'default');
	$pdf->SetAutoPageBreak('true','10');
	/* commented by art 01/18/2014
	$pdf->SetFont('Arial','',$fontSizeLabel+3);
	$pdf->Cell(150,4,'HRN : ',"$border2",0,'R');
	$pdf->SetFont('Arial','B',$fontSizeHeading+8);
	$pdf->Cell(0,4	,$pid,"$border2",0,'R');
	$pdf->Ln(1);*/
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
		//$row['hosp_agency'] = strtoupper($row['hosp_agency']);
		$row['hosp_name']   = strtoupper($row['hosp_name']);
	}
	else {
		//$row['hosp_country'] = "Republic of the Philippines";
		//$row['hosp_agency']  = "DEPARTMENT OF HEALTH";
		$row['hosp_name']    = "GONZALES MARANAN MEDICAL CENTER INC.";
		$row['hosp_addr1']   = "Quezon Ave., Digos City, Davao Del Sur";	
	}

	//added by art 01/18/2014
	//$pdf->Image('image/logo_doh.jpg',25,7,20,20);
	//$pdf->Image('image/dmc_logo.jpg',170,7,20,20);
	$pdf->Image('image/dmc_logo.jpg',10,2.5,30,30);
	//end art
	//$pdf->SetFont("Arial","","11");
	//$pdf->Cell(0,4,$row['hosp_country'],$border2,1,'C');
	#$pdf->SetFont("Arial","","11");
	//$pdf->Cell(0,4,$row['hosp_agency'],$border2,1,'C');

	$pdf->Ln(1);
	$pdf->SetFont("Arial","B","16");
    $pdf->Cell(0,4,$row['hosp_name'],$border2,1,'C');

	$pdf->Ln(1);
    $pdf->SetFont("Arial","","11");
    $pdf->Cell(0,4,$row['hosp_addr1'],$border2,1,'C');
	
    $pdf->Ln(1);
	$pdf->SetFont('Arial','B',"16");
	$pdf->Cell(0,5,'Emergency Room Clinical Form',$border2,1,'C');
	//added by art 01/18/2014
	 $pdf->Ln(15);
	// $pdf->SetFont('Arial','I',$fontSizeLabel);
	// $category_name = $enc_obj->getTriageCategoryInfo($category);
	// $pdf->Cell(30,4,'Triage Category : ', "",0,'L');
	// $pdf->SetFont('Arial','B',$fontSizeHeading);
	// $pdf->Cell(30,3,"".mb_strtoupper($category_name['roman_id']), "$border2",0,'L');
	// $pdf->SetFont('Arial','B',12);
	//$pdf->Cell(0,6,  "SPMC-F-MRI-10A", "",0, 'R');
	//end art


	#$pdf->Ln($space*2);
	$pdf->Ln($space*2);
	#added by VAN 06-13-08
	// $pdf->SetFont('Arial','I',$fontSizeLabel);
	// $category_name = $enc_obj->getTriageCategoryInfo($category);
	// $pdf->Cell(30,4,'Triage Category : ', "",0,'L');
	// $pdf->SetFont('Arial','B',$fontSizeHeading);
	// $pdf->Cell(30,4,"".mb_strtoupper($category_name['roman_id']), "$border2",0,'L');
	// $pdf->SetFont('Arial','',$fontSizeLabel+3);
	// $pdf->Cell(50,4,'Department : ',"$border2",0,'R');
	// $pdf->SetFont('Arial','B',$fontSizeHeading+5);
	// $pdf->Cell(60,4	,mb_strtoupper($name_formal),"$border2",0,'L');
	// #--------------------
	$pdf->SetFont('Arial','',$fontSizeLabel+2);
	$pdf->Cell(15,4,'HRN : ',"$border2",0,'L');
	$pdf->SetFont('Arial','B',$fontSizeHeading+5);
	$pdf->Cell(15,4,$pid,"$border2",0,'L');
	$pdf->SetFont('Arial','',$fontSizeLabel+3);
	$pdf->Cell(50,4,'Case No. : ',"$border2",0,'R');
	$pdf->SetFont('Arial','B',$fontSizeHeading+5);
	$pdf->Cell(40,4	,$encounter_nr,"$border2",0,'L');

	$pdf->SetFont('Arial','',$fontSizeLabel);
	$pdf->Cell(30,4,'Department : ',"$border2",0,'R');
	$pdf->SetFont('Arial','B',12);
	$pdf->Cell(60,4	,mb_strtoupper($name_formal),"$border2",0,'L');
	#--------------------

	// $pdf->Ln($space*3);
	// $pdf->SetFont('Arial','',$fontSizeLabel);
	// $pdf->Cell(30,4,'Date/Time : ',"$border2",0,'L');
	// $pdf->SetFont('Arial','B',$fontSizeHeading);
	// $pdf->Cell(50,4	,@formatDate2Local($er_opd_datetime,$date_format,1),"$border2",0,'L');	
	// //added by art 01/18/2014
	// $pdf->SetFont('Arial','B',$fontSizeHeading);
	// $pdf->Cell(0,4	,'HRN : '.$pid,"$border2",0,'R');
	// //end

	$pdf->Ln($space*3);
	/*
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(52,4,'Admitted Date and Time at ER : ',"",0,'L');
	$pdf->SetFont('Arial','B',$fontSizeLabel);
	$pdf->Cell(48,4,@formatDate2Local($er_opd_datetime,$date_format,1),"",1,'L');
	*/
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(48,4,'Last Name',"TR",0,'L');
	$pdf->Cell(48,4,'First Name',"TLR",0,'L');
	$pdf->Cell(48,4,'Middle Name',"TLR",0,'L');
	$pdf->Cell(48,4,'Maiden Name',"TL",0,'L');
	
	$pdf->Ln();	
	
	$x = $pdf->GetX();
	$y = $pdf->GetY();
	
	$pdf->SetFont('Arial','B',$fontSizeLabel);
	$pdf->Cell(48,8,'',"RB",0,'L');
	$pdf->Cell(48,8,'',"LRB",0,'L');
	$pdf->Cell(48,8,'',"LRB",0,'L');
	$pdf->Cell(48,8,'',"LB",0,'L');
	
	$pdf->SetXY($x, $y);
	$pdf->MultiCell(47, 4, mb_strtoupper($name_last), '', 'L','');
	
	$pdf->SetXY($x+47, $y);
	$pdf->MultiCell(47, 4, mb_strtoupper($name_first), '', 'L','');
	
	$pdf->SetXY($x+96, $y);
	$pdf->MultiCell(47, 4, mb_strtoupper($name_middle), '', 'L','');
	
	$pdf->SetXY($x+144, $y);
	$pdf->MultiCell(47, 4, mb_strtoupper($name_maiden), '', 'L','');
	
	$pdf->SetY($y+7);
	#$pdf->Ln($space*2);
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(10,8,'Age : ',"",0,'L');
	$pdf->SetFont('Arial','',$fontSizeLabel+2);
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
	$pdf->SetFont('Arial','',$fontSizeLabel+2);
	
	if ($sex=='f')
		$gender = 'FEMALE';
	elseif ($sex=='m')
		$gender = 'MALE';	
	
	$pdf->Cell(38, 8, mb_strtoupper($gender), '', 0,'L');
	
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(20,8,'Civil Status : ',"",0,'L');
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
	
	$pdf->SetY($y+12);
	
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
	
	#$address = "$street_name $brgy_name, $mun_name $zipcode $prov_name";
	if (!($brgy_name))
		$brgy_name = "NOT PROVIDED";
	
	#$address = trim($street_name)." ".trim($brgy_name).", ".trim($mun_name)." ".trim($zipcode)." ".trim($prov_name);
	if(stristr(trim($mun_name), 'city') === FALSE){
		if (!empty($mun_name)){
			$province = ", ".trim($prov_name);
		}else{
			$province = trim($prov_name);;
		}
	}	
	
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
	$pdf->SetY($y+13);
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(55,8,'Birth Place : ',"",0,'L');
	$pdf->Cell(55,8,'Birth Date : ',"",0,'L');
	#$pdf->Cell(55,8,'Department : ',"",0,'L');
	
	$pdf->Ln($space*3);
	
	$pdf->SetFont('Arial','',$fontSizeLabel);
	#$pdf->Cell(55, 4, mb_strtoupper($place_birth), '', 0,'L');
	$x = $pdf->GetX();
	$y = $pdf->GetY();
	
	$pdf->SetXY($x, $y);
	
	$pdf->MultiCell(55, 4,mb_strtoupper($place_birth), '', 'L','');
	
	$pdf->SetXY($x+55, $y);
	if ($date_birth)
		#$pdf->Cell(55,4,@formatDate2Local($date_birth,$date_format),"",0,'L');
		$pdf->MultiCell(50, 4,@formatDate2Local($date_birth,$date_format), '', 'L','');
	else
		#$pdf->Cell(55,4,'',"",0,'L');
		$pdf->MultiCell(50, 4,'', '', 'L','');
		
	#$pdf->SetXY($x+110, $y);	
	#$pdf->Cell(55, 4, mb_strtoupper($name_formal), '', 0,'L');
	#$pdf->MultiCell(80, 4,mb_strtoupper($name_formal), '', 'L','');

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


	$pdf->Ln($space);
	
	// $pdf->SetFont('Arial','I',$fontSizeLabel);
	// $pdf->Cell(70,8,'Name of Father : ',"T",0,'L');
	// $pdf->Cell(70,8,'Name of Mother : ',"T",0,'L');
	// $pdf->Cell(52,8,'Name of Guardian :',"T",0,'L');

	 $pdf->SetFont('Arial','',$fontSizeLabel);
	// $pdf->Cell(70,8,'Name of Father : ',"T",0,'L');
	// $pdf->Cell(70,8,'Name of Mother : ',"T",0,'L');
	// $pdf->Cell(50,8,'Hospitalization Plan : ',"T",0,'L');

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
	
	// $pdf->Ln($space*3);
	
	// $x = $pdf->GetX();
	// $y = $pdf->GetY();
	
	// $pdf->SetXY($x, $y);
	
	// $pdf->SetFont('Arial','',$fontSizeLabel);
	// $FmiddleInitial = "";
	// 	if (trim($father_mname)!=""){
	// 		$thisMI=split(" ",$father_mname);	
	// 		foreach($thisMI as $value){
	// 			if (!trim($value)=="")
	// 				$FmiddleInitial .= $value[0];
	// 		}
	// 		if (trim($FmiddleInitial)!="")
	// 			$FmiddleInitial = " ".$FmiddleInitial.".";
	// 	}
	
	// $father_name = $father_fname." ".$FmiddleInitial." ".$father_lname;
	// $pdf->MultiCell(70, 4,mb_strtoupper($father_name), '', 'L','');
	
	// $pdf->SetXY($x+70, $y);	
	// #$pdf->Cell(55, 4, mb_strtoupper($name_formal), '', 0,'L');
	// $MmiddleInitial = "";
	// 	if (trim($mother_mname)!=""){
	// 		$thisMI=split(" ",$mother_mname);	
	// 		foreach($thisMI as $value){
	// 			if (!trim($value)=="")
	// 				$MmiddleInitial .= $value[0];
	// 		}
	// 		if (trim($MmiddleInitial)!="")
	// 			$MmiddleInitial = " ".$MmiddleInitial.".";
	// 	}
	
	// $mother_name = $mother_fname." ".$MmiddleInitial." ".$mother_lname;
	// $pdf->MultiCell(68, 4,mb_strtoupper($mother_name), '', 'L','');
	
	// $pdf->SetXY($x+140, $y);	
	// #$pdf->Cell(55, 4, mb_strtoupper($name_formal), '', 0,'L');
	// $pdf->MultiCell(55, 4,mb_strtoupper($guardian_name), '', 'L','');

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
	$pdf->Cell(52,8,'Admission Date/Time : ',"",0,'L');
	
	// $pdf->Ln($space);
	
	// $x = $pdf->GetX();
	// $y = $pdf->GetY();
	
	// $pdf->SetXY($x, $y+4);
	
	// $pdf->SetFont('Arial','',$fontSizeLabel);
	// $pdf->MultiCell(68, 4,mb_strtoupper($spouse_name), '', 'L','');
	
	// $pdf->SetXY($x+70, $y+4);	
	// #$pdf->Cell(55, 4, mb_strtoupper($name_formal), '', 0,'L');
	// $pdf->MultiCell(68, 4,mb_strtoupper($employer), '', 'L','');
	
	// // $pdf->SetXY($x+140, $y+4);	
	// // #$pdf->Cell(55, 4, mb_strtoupper($name_formal), '', 0,'L');
	// // #$pdf->MultiCell(52, 4,$mss_id ." (".mb_strtoupper($mss_class).")", '', 'L','');
	// // $pdf->SetFont('Arial','',$fontSizeLabel+2);
	// // $pdf->MultiCell(55, 4,'     '.$mss_id, '', 'L','');

	
	// $pdf->Ln($space);
	// $pdf->Cell(192,4,'',"T",0,'L');
	
	// $y = $pdf->GetY();
	// $pdf->SetY($y+2);	
	
	// $pdf->Cell(190,1,'',"T",0,'L');
		
	// $pdf->Ln($space*0.5);


//added by Genesis Ortiz (06-03-2014)
	$pdf->Ln($space);
	
	$x = $pdf->GetX();
	$y = $pdf->GetY();
	
	//$pdf->SetXY($x, $y+4);
	
	// $pdf->SetFont('Arial','',$fontSizeLabel);
	// $pdf->MultiCell(68, 4,mb_strtoupper($spouse_name), '', 'L','');
	
	// $pdf->SetXY($x+70, $y+4);	
	// #$pdf->Cell(55, 4, mb_strtoupper($name_formal), '', 0,'L');
	// $pdf->MultiCell(68, 4,mb_strtoupper($employer), '', 'L','');
	
	$pdf->SetXY($x+140, $y+4);	
	#$pdf->Cell(55, 4, mb_strtoupper($name_formal), '', 0,'L');
	#$pdf->MultiCell(52, 4,$mss_id ." (".mb_strtoupper($mss_class).")", '', 'L','');
//	$pdf->SetFont('Arial','',$fontSizeLabel+2);
//	$pdf->MultiCell(55, 4,'     '.$mss_id, '', 'L','');

    $pdf->SetFont('Arial','B',$fontSizeInput);
    if ($er_opd_datetime)
        $pdf->Cell(0,4,"     ".@formatDate2Local($er_opd_datetime,$date_format,1),"",'L','');
    else
        $pdf->Cell(0,4,"","",'L','');
	
	$pdf->Ln($space);
	$pdf->SetY($y+10);
	
	// $pdf->SetFont('Arial','I',$fontSizeLabel);
	// $pdf->Cell(70,12,'Informant\'s Name : ',"",0,'L');
	// #$pdf->Cell(70,8,'Informant\'s Address : ',"",0,'L');
	// $pdf->Cell(70,12,'Relation to Patient : ',"",0,'L');
	// $pdf->Cell(52,8,'Discharge Date/Time',"",0,'L');
	
	$pdf->Ln($space);
	
	$x = $pdf->GetX();
	$y = $pdf->GetY();
	
	$pdf->SetXY($x, $y+4);
	
	$pdf->SetFont('Arial','',$fontSizeLabel);
	$pdf->MultiCell(70, -46,mb_strtoupper($informant_name), '', 'L','');
	
	$pdf->SetXY($x+70, $y+4);	
	#$pdf->Cell(55, 4, mb_strtoupper($name_formal), '', 0,'L');
	#$pdf->MultiCell(60, 4,mb_strtoupper($info_address), '', 'L','');
	$pdf->MultiCell(70, -46,mb_strtoupper($relation_informant), '', 'L','');

    $pdf->SetXY($x+140, $y+12);
    $pdf->SetFont('Arial','',$fontSizeInput);
    if (isset($is_discharged) && $is_discharged)
        $pdf->Cell(0,4,"     ".@formatDate2Local($discharge_dt,$date_format,1),"",'','L');
    else
        $pdf->Cell(0,4,'',"",1,'L');
	
	//$pdf->Ln($space);
	
	$pdf->Ln(-5);
	$pdf->Cell(192,4,'',"T",0,'L');
	
	$pdf->Ln($space);
	
	

	$items = $enc_obj->getEncounterDRAddList($encounter_nr);
	if($items){
		foreach($items AS $row){
			if($row['is_admitting']=='1'){
				$dr_admitting .= $row['name']."\n";
			}else{
				$dr_attending .=  $row['name']."\n";
				// Added by Robert 05/17/2015
				$actual_width = (int) (ceil($pdf->GetStringWidth($row['name'])/10))*10;
				$count_attending_dr++;
				// End add by Robert
			}
			// Added by Robert 05/17/2015
			$computed_width = ($actual_width >= $width ? $actual_width : $width);
			$width = $computed_width;
			// End add by Robert
		}//End Foreach
	}//End If

	if (isset($is_discharged) && $is_discharged){
		if ( ($encounter_type==3) || ($encounter_type==4) ){
	#		$admitting_dr=$er_opd_admitting_physician_nr;
	#		$attending_dr=$attending_physician_nr;
			$admitting_dr=$dr_admitting;
			$attending_dr=$dr_attending;

		}else{
	#		$attending_dr=$attending_physician_nr;
			$attending_dr=$dr_attending;
		}
	}else{
			# assuming that ONLY ecnounters with encounter_type==3 or 4
		#$attending_dr='';
		$attending_dr=$dr_attending;
		$admitting_dr=$dr_admitting;	
	}
			// 	echo $admitting_dr." == ".$dr_attending;
			// exit();
	$y = $pdf->GetY();
	$x = $pdf->GetX();
	
    $pdf->SetXY($x+92,$y);
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(27,8,'Attending Dr.   : ',"",0,'L');
	$pdf->SetFont('Arial','',$fontSizeLabel);
	//$pdf->Cell(65,8,mb_strtoupper($attending_dr),"",0,'L');
	
	$pdf->SetXY($x+122,$y+2);
	$pdf->multiCell(60, 3 ,strtoupper($attending_dr), "",1,'');
	
	$pdf->SetXY($x,$y);
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(27,8,'Admitting Dr.    : ',"",0,'L');
	$pdf->SetFont('Arial','',$fontSizeLabel);
	//$pdf->Cell(65,8,mb_strtoupper($admitting_dr),"",0,'L');
	$pdf->SetXY($x+28,$y+2);
	$pdf->multiCell(60, 3 ,strtoupper($admitting_dr), "",1,'');
	
	$pdf->Ln(2);
	$y = $pdf->GetY();
	$x = $pdf->GetX();
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(27,8,'Consultant Dr. : ',"",0,'L');
	$pdf->SetFont('Arial','',$fontSizeLabel);
	
	$consulting_dr = "";
	if(stristr(trim($ward_name), 'charity') === FALSE)
		$consulting_dr = mb_strtoupper($attending_dr);
				
	//$pdf->Cell(65,8,$consulting_dr,"",0,'L');
	$pdf->SetXY($x+27,$y+2.5);
	$pdf->multiCell(60, 3 ,strtoupper($consulting_dr), "",1,'');
	
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(27,8,'Admitting Clerk : ',"",0,'L');
	$pdf->SetFont('Arial','',$fontSizeLabel);
	#echo "<br>1".$admitting_clerk;
	#echo "<br>1".$admitting_clerk_er_opd;
	#if ($admitting_clerk)
	#	$clerk = mb_strtoupper($admitting_clerk);
	#else
		$clerk = mb_strtoupper($admitting_clerk_er_opd);
	
	$pdf->Cell(65,8,$clerk,"",0,'L');
	
	$pdf->Ln($space*3);
	
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(35,8,'Admitting Diagnosis : ',"",0,'L');
	
	$pdf->Ln($space*2);
	$pdf->Cell(10,8,'',"",0,'L');
	$x = $pdf->GetX();
	$y = $pdf->GetY();
	
	$pdf->SetXY($x, $y+2);
	$pdf->SetFont('Arial','',$fontSizeLabel);
	$pdf->MultiCell(180, 6, mb_strtoupper($admitting_diagnosis), '0', 'J','');
	
	$pdf->Ln($space);
	
	$pdf->SetXY($x-10, $y+15);
	
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(25,5,'Principal Diagnosis:',"0",1,'L');
	
	
	$pdf->SetFont('Arial','',$fontSizeLabel);
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
		$pdf->Ln($space*7);
	}
	
	$pdf->Ln($space);
	$pdf->SetXY($x-10, $y+27);
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(25,1,'Other Diagnosis:',"0",1,'L');
	
	
	$pdf->SetFont('Arial','',$fontSizeLabel);
	
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
		$pdf->Ln($space*7);
	}
	
	$pdf->Ln($space);
	$pdf->SetXY($x+62, $y+40);
	
	//$pdf->SetFont('Arial','I',$fontSizeLabel);
	//$pdf->Cell(35,8,'Operations : ',"",0,'L');
	
	$result_therapy = array();
	if (isset($is_discharged) && $is_discharged){
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
	$pdf->Ln($space*3);
	$pdf->SetFont('Arial','',$fontSizeLabel);
	if (isset($is_discharged) && ($rowsTherapy)){
		$count=0;
		foreach ($result_therapy as $value) {
				$pdf->Cell(10,5,"","0",0,'L');
				$pdf->Cell(71,4,$value['code']." : ".$value['therapy'],"0",1,'L');
				$count++;
		}
		#if (13-$count){
		#	$pdf->Ln($space*(13-$count));
		#}else{
		#	$pdf->Ln($space);		
		#}
	}else{
		$pdf->Ln($space*7);
	}
	
	$pdf->setY($y+35);
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(150,4,'Operations:',"",0,'L');
	$pdf->setX($x+90);
	$pdf->Cell(30,2,'Date:                                      RVU:',"",1,'L');
	$pdf->Cell(150,10,'',"",0,'L');
	$pdf->Cell(40,10,'',"",1,'L');

	$pdf->Ln(5);

#added By Genesis D. Ortiz (06-03-2014)
	$pdf->SetFont('Arial','',$fontSizeLabel);
	$pdf->Cell(70,4,'Condition at ER',"TLR",0,'L');
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
	
	// Added by Robert 05/17/2015
	$abscissa = $pdf->GetX(); // Abscissa
	$ordinate = $pdf->GetY(); // Ordinate
	// Verify width
	if($width === 0) {
		$width = 50;
	}
	/** Thumb mark label **/
	$pdf->SetXY($abscissa,$ordinate);
	$pdf->SetFont("Arial","I",$fontSizeLabel);
	$pdf->Cell(40,5,"THUMB MARK","",0,"L");
	/** Thumb mark **/
	$pdf->Rect($abscissa+3, $ordinate+5, 20, 20);
	/** Attending Physician data **/
	$pdf->SetX($abscissa+120);
	$pdf->SetFont("Arial","",$fontSizeLabel);
	$pdf->multiCell($width,3,mb_strtoupper($attending_dr),"","C","");
	/** Informant / Patient's Signature label **/
	$pdf->SetX($abscissa+40);
	$pdf->SetFont("Arial","I",$fontSizeLabel);
	$pdf->Cell(55,5,"Informant / Patient's Signature","T",0,"C");
	/** Attending Physician label **/
	$pdf->SetX($abscissa+120);
	$pdf->SetFont("Arial","I",$fontSizeLabel);
	$pdf->Cell($width,5,"ATTENDING PHYSICIAN","T",0,"C");
	// End add by Robert
	
	//added by art 01/10/2014
	$pdf->SetFont('Arial','B',12);
	$pdf->setY(33);
	$pdf->Cell(0,4,  "", "",0, 'R');
	//end art
#end added By Genesis D. Ortiz (06-03-2014)

	// $pdf->Cell(50,4,'Condition at ER',"",0,'L');
	// $pdf->Cell(50,4,'Disposition',"",0,'L');
	// #$pdf->Cell(50,4,'Disposition',"TLR",1,'L');
	// $pdf->Cell(0,4,'',"",1,'L');
	// $pdf->SetFont('Arial','',$fontSizeLabel);
	// $pdf->Cell(7,4,'[   ]',"",0,'R');
	// $pdf->Cell(43,4,'Conscious',"",0,'L');
	// $pdf->Cell(7,4,'[   ]',"",0,'R');
	// $pdf->Cell(43,4,'Admitted',"",0,'L');
	// $pdf->Cell(0,4,'',"",1,'L');
	// $pdf->Cell(7,4,'[   ]',"",0,'R');
	// $pdf->Cell(43,4,'Semi-conscious',"",0,'L');
	// $pdf->Cell(7,4,'[   ]',"",0,'R');
	// $pdf->Cell(43,4,'Discharged',"",0,'L');
	// $pdf->Cell(0,4,'',"",1,'L');
	// $pdf->Cell(7,4,'',"",0,'R');
	// $pdf->Cell(43,4,'',"",0,'L');
	// $pdf->Cell(7,4,'[   ]',"",0,'R');
	// $pdf->Cell(43,4,'Transferred',"",0,'L');
	// $pdf->Cell(0,4,'',"",1,'L');
	// $pdf->SetFont('Arial','I',$fontSizeLabel);
	// $pdf->Cell(50,4,'Results',"",0,'L');
	// $pdf->SetFont('Arial','',$fontSizeLabel);
	// $pdf->Cell(7,4,'[   ]',"",0,'R');
	// $pdf->Cell(43,4,'HAMA',"",0,'L');
	// $pdf->Cell(0,4,'',"",1,'L');
	// $pdf->Cell(7,4,'[   ]',"",0,'R');
	// $pdf->Cell(43,4,'Recovered',"",0,'L');
	// $pdf->Cell(7,4,'[   ]',"",0,'R');
	
	// $pdf->Cell(43,4,'Absconded/PNF',"",0,'L');
	// $pdf->Cell(0,4,'',"",1,'L');
	// $pdf->Cell(7,4,'[   ]',"",0,'R');
	// $pdf->Cell(43,4,'Improved',"",0,'L');
	// $pdf->Cell(7,4,'[   ]',"",0,'R');
	// $pdf->Cell(43,4,'For OPD',"",0,'L');
	// $pdf->Cell(6,4,'',"",0,'');
	// $pdf->Cell(78,4,mb_strtoupper($er_opd_admitting_physician_name),"",0,'C');
	// $pdf->Cell(6,4,'',"",1,'');
	// $pdf->Cell(7,4,'[   ]',"",0,'R');
	// $pdf->Cell(43,4,'Unimproved',"",0,'L');
	// $pdf->Cell(7,4,'',"",0,'R');
	// $pdf->Cell(43,4,'',"",0,'L');
	// $pdf->Cell(0,4,'Name & Signature of Attending Physician',"",1,'C');
	// $pdf->Cell(7,4,'[   ]',"",0,'R');
	// $pdf->Cell(43,4,'Died',"",0,'L');
	// $pdf->Cell(7,4,'',"",0,'R');
	// $pdf->Cell(43,4,'',"",0,'L');
	// $pdf->Cell(0,4,'',"",1,'L');
	// #$pdf->Cell(7,4,'',"LB",0,'R');
	#$pdf->Cell(43,4,'',"RB",0,'L');
	#$pdf->Cell(7,4,'',"LB",0,'R');
	#$pdf->Cell(43,4,'',"RB",0,'L');
	#$pdf->Cell(0,4,'',"RB",1,'L')
	//$pdf->Output();	

	//Added by Genesis D. Ortiz (06-03-2014)

	$fullname = $name_last.", ".$name_first." ".$name_middle;

	$pdf->AddPage("P");

		$row2['hosp_name']    = "GONZALES MARANAN MEDICAL CENTER INC.";
		$row2['hosp_addr1']   = "Quezon Ave., Digos City, Davao Del Sur";

	$pdf->Image('image/dmc_logo.jpg',10,5,30,30);

    $pdf->SetFont("Arial","B","16");

    $pdf->Cell(0,4,$row2['hosp_name'],$border2,1,'C');

	$pdf->Ln(1);

    $pdf->SetFont("Arial","","11");

    $pdf->Cell(0,4,$row2['hosp_addr1'],$border2,1,'C');

	$pdf->SetFont('Arial','B',"16");
    $pdf->Ln(15);

	$pdf->Cell(0,5,'CLEARANCE FOR DISCHARGE',$border2,1,'C');

    $pdf->Ln(4);

	$pdf->Ln($space*2.9);
	
    $pdf->SetFont("Arial","","11");

	$pdf->Cell(130,5,'',"",0,'R');
	$pdf->Cell(15,5,'DATE:______________________',"",0,'L');

	$pdf->Cell(5,5,@formatDate2Local($er_opd_datetime,$date_format,1),"$border2",0,'L');
    $pdf->Ln(8);
    $pdf->Ln(4);

	$text = "This is to certify patient below has no accountability and cleared from the following departments:";

	$pdf->Cell(10,5,'',"",0,'L');

	$pdf->Cell(5,5,$text,"",1,'L');
    $pdf->Ln(2);

	$pdf->Cell(20,5,"PATIENT:_______________________________________________________","",0,'L');
	$pdf->Cell(120,5,strtoupper($fullname),"",0,'L');

	$pdf->Cell(12,5,"AGE: _____________","",0,'L');
	$pdf->Cell(100,5, $age." old","",1,'L');
    $pdf->Ln(2);


	$pdf->Cell(20,5,"PHIC NO: ________________________","",0,'L');
	$pdf->Cell(50,5,"","",0,'L');

	$pdf->Cell(25,5,"ROOM NO: ________________________________________","",0,'L');
	$pdf->Cell(37,5,$ward,"",1,'L');
    $pdf->Ln(2);

	$pdf->Cell(24,5,"ADDRESS:_________________________________________________________________________","",0,'L');
	$pdf->Cell(115,5,$address,"",1,'L');
    $pdf->Ln(2);

    // Commented by Robert 05/17/2015 (ORIGINAL CODE)
	// $pdf->Cell(50,5,"ATTENDING PHYSICIAN:_____________________________________________________________","",0,'L');
	// $pdf->Cell(115,5,mb_strtoupper($attending_dr),"",1,'L');
	// End comment by Robert

    // Edited by Robert 05/17/2015
    $display = ($count_attending_dr > 1 ? "ATTENDING PHYSICIAN:" : "ATTENDING PHYSICIAN:_____________________________________________________________");
	$pdf->Cell(50,5,$display,0,0);
	$pdf->MultiCell(115,5,mb_strtoupper($attending_dr),0,"L");
	// End edit by Robert

    $pdf->Ln(15);

	$pdf->Cell(64,5,"_______________________","",0,'L');
	$pdf->Cell(64,5,"_______________________","",0,'L');
	$pdf->Cell(70,5,"_______________________","",1,'L');
	$pdf->Cell(64,5,"          1. PHILHEALTH","",0,'L');
	$pdf->Cell(64,5,"             INSURANCE ","",0,'L');
	$pdf->Cell(70,5,"          2. CASHIER   ","",1,'L');



	 $pdf->Ln(15);

	$pdf->Cell(64,5,"_______________________","",0,'L');
	$pdf->Cell(64,5,"_______________________","",0,'L');
	$pdf->Cell(70,5,"_______________________","",1,'L');
	$pdf->Cell(64,5,"          3. PHARMACY  ","",0,'L');
	$pdf->Cell(64,5,"          4. LABORATORY","",0,'L');
	$pdf->Cell(70,5,"     5. NURSE ON DUTY  ","",1,'L');
	 $pdf->Ln(10);


	$pdf->Cell(130,5,"Date Discharge: _______________________","",0,'L');
	$pdf->Cell(64,5,"Time: _____________A.M.","",1,'L');
	$pdf->Ln(2);
	$pdf->Cell(141,5,"","",0,'L');
	$pdf->Cell(64,5,"_____________P.M.","",0,'L');
	 $pdf->Ln(15);

	$pdf->Cell(95,5,"PHILHEALTH:_______________________","",0,'L');
	$pdf->Cell(70,5,"NON-PHILHEALTH:_______________________","",1,'L');

	 $pdf->Ln(15);
	$pdf->Cell(70,5,"PHYSICIAN CHARGES:","",1,'L');
	 $pdf->Ln(-5);

	$pdf->Cell(70,5,"____________________","",1,'L');
	 $pdf->Ln(4);
	$pdf->Cell(70,5,"Attending Physician____________________________","",1,'L');
	 $pdf->Ln(4);
	$pdf->Cell(70,5,"CP Clearance_________________________________","",1,'L');
	 $pdf->Ln(4);
	$pdf->Cell(70,5,"Referral______________________________________","",1,'L');
	 $pdf->Ln(4);
	$pdf->Cell(70,5,"Surgeon Fee__________________________________","",1,'L');
	 $pdf->Ln(4);
	$pdf->Cell(70,5,"Pediatrician (Delivery)___________________________","",1,'L');
	 $pdf->Ln(4);





	$pdf->Output();	



	//function get disposition
            function getdisposition($enc)
    {
        global $db;
        $strSQL="SELECT 
					  ser.`result_code`,
					  sed.`disp_code`,
					  sec.`cond_code` 
					FROM
					care_encounter AS ce
					LEFT JOIN seg_encounter_disposition AS sed 
					ON sed.`encounter_nr` = ce.`encounter_nr`
					  LEFT JOIN seg_encounter_result AS ser 
					    ON ser.`encounter_nr` = ce.`encounter_nr` 
					  LEFT JOIN seg_encounter_condition AS sec 
					    ON sec.`encounter_nr` = ce.`encounter_nr` 
					WHERE ce.`encounter_nr` = ".$db->qstr($enc);
        
        if($result = $db->Execute($strSQL)){
            if($result->RecordCount()){
                while ($row = $result->FetchRow()) {
                   $returned_results[] = array(
                    'result_code'=> $row['result_code'],
                    'disp_code'  => $row['disp_code'],
                    'cond_code'  => $row['cond_code'],
                    );
                }
                return $returned_results;
            }else {return false;}
        }else {return false;}   
        
    }
?>