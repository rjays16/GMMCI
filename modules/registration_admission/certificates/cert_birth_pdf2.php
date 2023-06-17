<?php
#edited by VAN 05-20-08		further edited by fdp 07-16-2008
include("roots.php");
include_once($root_path."/classes/fpdf/fpdf.php");
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');

if (isset($_GET['id']) && $_GET['id']){
	$pid = $_GET['id'];
}

include_once($root_path.'include/care_api_classes/class_person.php');
$person_obj=new Person($pid);

require_once($root_path.'include/care_api_classes/class_personell.php');
$pers_obj=new Personell;

include_once($root_path.'include/care_api_classes/class_address.php');
$address_country = new Address();

if ($pid){
	if (!($basicInfo=$person_obj->getAllInfoArray($pid))){
		echo '<em class="warn"> Sorry, the page cannot be displayed!</em>';
		exit();
	}
	extract($basicInfo);
}else{
	echo '<em class="warn">Sorry, the page cannot be displayed! <br> Invalid HRN!</em>';
	exit();
}

#$birthYear = intval(substr($date_birth, 0, 4)); 
#$birthMonth = intval(substr($date_birth, 5, 7)); 
#$birthDay = intval(substr($date_birth, 8, 10));
$birthYear = date("Y",strtotime($date_birth)); 
$birthMonth = date("F",strtotime($date_birth)); 
$birthDay = date("d",strtotime($date_birth)); 

include_once($root_path.'include/care_api_classes/class_cert_birth.php');
$obj_birthCert = new BirthCertificate($pid);

$birthCertInfo = $obj_birthCert->getBirthCertRecord($pid);
if ($birthCertInfo){
	extract($birthCertInfo);
	#$marriage_type = substr($parent_marriage_info, 0, 1); 
	#$parent_marriage_info_tmp = substr($parent_marriage_info, 4); 
	$attendant_type = substr(trim($birthCertInfo['attendant_type']),0,1);
	$attendant_type_others = substr(trim($birthCertInfo['attendant_type']),4);
}

	$border="1";
	$border2="0";
	$space=2;
#	$fontSizeLabel=9;
	$fontSizeInput=11;
	#$fontSizeInput=12;
	$fontSizeHeading=14;

	$pdf = new FPDF("P","mm","Legal");
	$pdf->AddPage("P");
	
	$pdf->SetDisplayMode(real,'default');
	
	$x = $pdf->GetX();
	
	#left margin 10mm
	#top margin 30mm
	
	$y=$x*3;
	
	$pdf->SetXY($x,$y);
	$pdf->SetFont("Arial","",$fontSizeInput);
	
	$pdf->SetY(-0.5);	
	$z = $pdf->GetY();

	//$pdf->Text($x+30, $y-10.5, "Davao del Sur");	commented out by fdp 07-16-2008 in accordance with MedRec instruction
	$pdf->Text($x+30, $y-5, "Davao City");
	$pdf->Text($x+105, $y-4, $registry_nr);
	
//1. NAME
	#$pdf->SetXY($x+10, $y+3);	
	$pdf->SetXY($x+5, $y+3);	
	#$pdf->MultiCell(50, 4,strtoupper($name_first), '', 'C','0');
	#added by VAN 08-26-08
	
	if (stristr($name_first,",")){
		$name_first_new = explode(",",$name_first);
		$name1 = $name_first_new[0];
		$name2 = $name_first_new[1];
	}else{
		$name1 = $name_first;
		$name2 = "";
	}
		
	$suffix2 = array("JR","SR","JR.","SR.");
	if (($name2) && (in_array(trim($name2),$suffix2)))				
			$name2 = ", ".$name2;
	else	
			$name2 = " ".$name2;
		
	$name = $name1."".$name2;
	
	#$name_first = str_replace(" ","  ",$name_first);
	$name_first = str_replace(" ","  ",$name);
	$pdf->Cell(60, 4,strtoupper($name_first),'', '0','C');
	
	#$pdf->SetXY($x+60, $y+3);	
	$pdf->SetXY($x+65, $y+3);	
	#$pdf->MultiCell(40, 4,strtoupper($name_middle), '', 'C','0');
	$name_middle = str_replace(" ","  ",$name_middle);
	$pdf->Cell(40, 4,strtoupper($name_middle),'', '0','C');
	
	#$pdf->SetXY($x+100, $y+3);	
	$pdf->SetXY($x+105, $y+3);	
	#$pdf->MultiCell(40, 4,strtoupper($name_last), '', 'C','0');
	$name_last = str_replace(" ","  ",$name_last);
	$pdf->Cell(41, 4,strtoupper($name_last),'', '0','C');
	
//2. SEX
	if ($sex=='m')
		$pdf->Text($x+13, $y+17, "x");
	if ($sex=='f')
		$pdf->Text($x+35, $y+17, "x");

//3. DATE OF BIRTH
	$arrayMonth = array ("","January","February","March","April","May","June","July","August","September","October","November","December");
	#$birthMonthName = $arrayMonth[$birthMonth];
	$pdf->Text($x+108, $y+18.5, $birthDay."   ".$birthMonth."   ".$birthYear);

//4. PLACE OF BIRTH
	$pdf->SetFont("Arial","",$fontSizeInput+2);
	$pdf->Text($x+30, $y+34, "Gonzales Maranan Medical Center Incorporated, DAVAO CITY");

	$pdf->SetFont("Arial","",$fontSizeInput);
//5a TYPE OF BIRTH
	if ($birth_type=="1")
		$pdf->Text($x+9, $y+44, "x");
	if ($birth_type=="2")
		$pdf->Text($x+39, $y+44, "x");
	if (($birth_type!="")&&($birth_type!="1")&&($birth_type!="2"))
		$pdf->Text($x+20, $y+48, "x");

	/*
	if (intval($birth_rank) == 1)
		$pdf->Text($x+75, $y+44, "x");
	if (intval($birth_rank) == 2)
		$pdf->Text($x+107, $y+44, "x");
	if (intval($birth_rank) > 2){
		$pdf->Text($x+89, $y+48, "x");
	*/
	
	/*if ($birth_rank == '1')
		$pdf->Text($x+75, $y+44, "x");
	if ($birth_rank == '2')
		$pdf->Text($x+107, $y+44, "x");
	else{
		$pdf->Text($x+89, $y+48, "x");
			
		#$rank = $obj_birthCert->convertWord($birth_rank);
		#$pdf->Text($x+123, $y+47, $rank);
		$pdf->Text($x+123, $y+48, $birth_rank);
	}   -- -- replaced by fdp as correction (07-16-2008)
	*/
	if ((($birth_type=="2") || ($birth_type=="3"))&& $birth_rank == "first")
		$pdf->Text($x+75, $y+44, "x");
	if ((($birth_type=="2") || ($birth_type=="3"))&& $birth_rank == "second")
		$pdf->Text($x+107, $y+44, "x");
	if ($birth_type=="3" && (($birth_rank!="first") && ($birth_rank!="second"))) {
		$pdf->Text($x+89, $y+48, "x");
		$pdf->Text($x+123, $y+48, seg_ucwords($birth_rank));
	}
	
//5c.BIRTH ORDER
	$pdf->Text($x+20, $y+61, seg_ucwords($birth_order));
	
//5d. WEIGHT AT BIRTH	
	#$pdf->Text($x+108, $y+61, number_format($birth_weight));
	$pdf->Text($x+108, $y+61, $birth_weight);
	
//6. MAIDEN NAME

	#$pdf->SetXY($x+15, $y+69);	
	$pdf->SetXY($x+20, $y+69);	
	
	#$pdf->MultiCell(50, 4,strtoupper($m_name_first), '', 'C','0');
	$pdf->Cell(55, 4,strtoupper($m_name_first), '', '0','C');
	
	#$pdf->SetXY($x+60, $y+69);	
	$pdf->SetXY($x+75, $y+69);	
	#$pdf->MultiCell(40, 4,strtoupper($m_name_middle), '', 'C','0');
	$pdf->Cell(35, 4,strtoupper($m_name_middle), '', '0','C');
	
	#$pdf->SetXY($x+100, $y+69);	
	$pdf->SetXY($x+110, $y+69);	
	#$pdf->MultiCell(40, 4,strtoupper($m_name_last), '', 'C','0');
	$pdf->Cell(36, 4,strtoupper($m_name_last), '', '0','C');
	
// 7.CITIZENSHIP
	$pdf->Text($x+37, $y+86, seg_ucwords($m_citizenship));

//8. RELIGION
	//added by fdp, in accordance with MedRec specification, 07-16-2008; same with Father
	if (($m_religion_name=="Not Applicable")||($m_religion_name=="Not Indicated"))
		$m_religion_name="n/a";
	// -- -- 'til here only -- -- fdp -- -- --
	#$pdf->Text($x+110, $y+86, seg_ucwords($m_religion_name));
	$pdf->Text($x+110, $y+86, $m_religion_name);

//9a
	$pdf->Text($x+25, $y+98.5, $m_total_alive);
	$pdf->Text($x+80, $y+98.5, $m_still_living);
	$pdf->Text($x+135, $y+98.5, $m_now_dead);

//10. OCCUPATION (MOTHER)
	//added by fdp, in accordance with MedRec specification, 07-16-2008; same with Father
	if (($m_occupation_name=="Not Applicable")||($m_occupation_name=="Not Indicated"))
		$m_occupation_name="n/a";
		// -- -- -- 'til here only -- -- -- fdp -- -- --
	$pdf->Text($x+35, $y+112, $m_occupation_name);
	
//11. AGE AT THE TIME OF THIS BIRTH (MOTHER)
	$pdf->Text($x+128, $y+112, $m_age);
	
//12. RESIDENCE
	$m_address = trim($m_residence_basic);
	
	$brgy = $address_country->getMunicityByBrgy($m_residence_brgy);
	$mun = $address_country->getProvinceByBrgy($m_residence_mun);
	$prov = $address_country->getProvinceInfo($m_residence_prov);
	
	#echo $address_country->sql;
	#commented by VAN 08-01-08
	/*
	if (!empty($m_address) && !empty($m_residence_brgy)){
		$m_address = $m_address.", ".$brgy['brgy_name'];
	}else{
		$m_address = $m_address." ".$brgy['brgy_name'];
	}
	*/
#echo $prov['prov_name'];
	if (!empty($m_address) && !empty($m_residence_mun)){
		$m_address = $m_address.", ".seg_ucwords(trim($mun['mun_name']));
	}else{
		$m_address = $m_address." ".seg_ucwords(trim($mun['mun_name']));
	}
	
	#added by VAN 08-05-08
	#if ($mun['mun_name']!='Davao City'){
	if(stristr($mun['mun_name'], 'city') === FALSE){
		if (!empty($m_address)){
			$m_address = $m_address.", ".seg_ucwords(trim($prov['prov_name']));
		}else{
			$m_address = $m_address." ".seg_ucwords(trim($prov['prov_name']));
		}
	}
	/*
	if (!empty($m_address) && !empty($m_residence_prov)){
		$m_address = $m_address.", ".$prov['prov_name'];
	}else{
		$m_address = $m_address." ".$prov['prov_name'];
	}
	*/	
		
	#$pdf->Text($x+20, $y+123, $m_address);
	$pdf->SetFont("Arial","",$fontSizeInput-1.5);
	$pdf->SetXY($x+10, $y+121);	
	//$pdf->MultiCell(140, 4,strtoupper($m_address), '', 'L','0');
	#$pdf->MultiCell(140, 4,ucwords($m_address), '', 'L','0');
	$pdf->MultiCell(140, 4,$m_address, '', 'L','0');
	$pdf->SetFont("Arial","",$fontSizeInput);
	
//13. FATHER'S NAME
	if ((($f_name_first=="N/A") || ($f_name_first=="n/a"))&&(($f_name_middle=="N/A") || ($f_name_middle=="n/a"))&&(($f_name_last=="N/A") || ($f_name_last=="n/a"))){
		$pdf->SetXY($x+70, $y+130);	
		$pdf->MultiCell(10, 4,"n/a", '', 'C','0');
	}else{
		$pdf->SetXY($x+10, $y+130);	
		#$pdf->MultiCell(50, 4,strtoupper($f_name_first), '', 'C','0');
		
		if (stristr($f_name_first,",")){
			$f_name_first_new = explode(",",$f_name_first);
			$fname1 = $f_name_first_new[0];
			$fname2 = $f_name_first_new[1];
		}else{
			$fname1 = $f_name_first;
			$fname2 = "";
		}
		
		$suffix = array("JR","SR","JR.","SR.");
		if (($fname2) && (in_array(trim($fname2),$suffix)))				
				$fname2 = ", ".$fname2;
		else	
				$fname2 = " ".$fname2;
		
		$f_name = $fname1."".$fname2;
		
		#$pdf->Cell(55, 4,strtoupper($f_name_first), '', '0','C');
		$pdf->Cell(55, 4,strtoupper($f_name), '', '0','C');
		#$pdf->Cell(55, 4,str_replace(",","", strtoupper($f_name_first)), '', '0','C');
	
		#$pdf->SetXY($x+60, $y+130);	
		$pdf->SetXY($x+65, $y+130);	
		#$pdf->MultiCell(40, 4, strtoupper($f_name_middle), '', 'C','0');
		$pdf->Cell(40, 4, strtoupper($f_name_middle),'', '0','C');
	
		#$pdf->SetXY($x+100, $y+130);	
		$pdf->SetXY($x+105, $y+130);	
		#$pdf->MultiCell(40, 4,strtoupper($f_name_last), '', 'C','0');
		$pdf->Cell(41, 4,strtoupper($f_name_last),'', '0','C');
	}	
	
//14.CITIZENSHIP (FATHER)
	if (($f_citizenship=="n/a")||($f_citizenship=="N/A"))
		$f_citizenship = "";
	$pdf->Text($x+37, $y+144, seg_ucwords($f_citizenship));

//15. RELIGION
	//if ($f_religion_name=="Not Applicable")
	if (($f_religion_name=="Not Applicable")||($f_religion_name=="Not Indicated"))
		#$f_religion_name="n/a";
		$f_religion_name="";
	#$pdf->Text($x+110, $y+144, seg_ucwords($f_religion_name));
	$pdf->Text($x+110, $y+144, $f_religion_name);
	
//16. OCCUPATION (FATHER)
	//if ($f_occupation_name=="Not Applicable")
	if (($f_occupation_name=="Not Applicable")||($f_occupation_name=="Not Indicated"))
		#$f_occupation_name="n/a";
		$f_occupation_name="";
	$pdf->Text($x+35, $y+157, $f_occupation_name);
	
//17. AGE AT THE TIME OF THIS BIRTH (FATHER)
	if ($f_age==0)
		$f_age = "";
	$pdf->Text($x+128, $y+155.5, $f_age);
	
//18. DATE AND PLACE OF MARRIAGE OF PARENTS
	
	#if ($marriage_type=='1'){
	if ($is_married=='1'){
		//$parent_marriage_info_tmp = date("F d, Y",strtotime($parent_marriage_date))." at ".$parent_marriage_place;
		$parent_marriage_info_tmp = date("F d, Y",strtotime($parent_marriage_date))." - ".$parent_marriage_place;
		$pdf->Text($x+25, $y+175, $parent_marriage_info_tmp);
	}else
		$pdf->Text($x+65, $y+175, "n/a");

//19a. ATTENDANT

	if ($attendant_type=='1')
		$pdf->Text($x+9, $y+185, "x");
	if ($attendant_type=='2')
		$pdf->Text($x+9, $y+188, "x");
	if ($attendant_type=='3')
		$pdf->Text($x+69, $y+185, "x");
	if ($attendant_type=='4')	
		$pdf->Text($x+69, $y+188, "x");
	if ($attendant_type=='5'){
		$pdf->Text($x+123, $y+185, "x");
		$pdf->Text($x+105, $y+189, $attendant_type_others);
	}

//19b. CERTIFICATION OF BIRTH
	if (($birth_time !='00:00:00') && ($birth_time!=""))
		$birth_time = convert24HourTo12HourLocal($birth_time);
	else
		$birth_time = '';
	if (($attendant_date_sign!='0000-00-00') && ($attendant_date_sign!="")){
#		$attendant_date_sign = @formatDate2Local($attendant_date_sign,$date_format);
		#$tempYear = intval(substr($attendant_date_sign, 0, 4)); 
		#$tempMonth = intval(substr($attendant_date_sign, 5, 7)); 
		#$tempDay = intval(substr($attendant_date_sign, 8, 10)); 
		$tempYear = date("Y",strtotime($attendant_date_sign)); 
		$tempMonth = date("F",strtotime($attendant_date_sign)); 
		$tempDay = date("d",strtotime($attendant_date_sign)); 
		
		$attendant_date_sign =$tempDay." ".$tempMonth." ".$tempYear;
	}else{
		$attendant_date_sign = '';
	}

		$pdf->Text($x+112, $y+201, $birth_time);
		
	 if (is_numeric($attendant_name)){
		$doctor = $pers_obj->get_Person_name($attendant_name);
		
		$middleInitial = "";
		if (trim($doctor['name_middle'])!=""){
			$thisMI=split(" ",$doctor['name_middle']);	
			foreach($thisMI as $value){
				if (!trim($value)=="")
				$middleInitial .= $value[0];
			}
			if (trim($middleInitial)!="")
			$middleInitial .= ". ";
		}
		$doctor_name = $doctor["name_first"]." ".$doctor["name_2"]." ".$middleInitial.$doctor["name_last"];
		#$doctor_name = "Dr. ".ucwords(strtolower($doctor_name));
		$doctor_name = mb_strtoupper($doctor_name).", MD";
	}else{
		$doctor_name = mb_strtoupper($attendant_name).", MD";
	}	
		
		#added by VAN 08-06-08
		$pdf->SetFont("Arial","I",$fontSizeInput);
		$pdf->Text($x+20, $y+211, seg_ucwords($non_resident_status));
		
		$pdf->SetFont("Arial","",$fontSizeInput);
		#$pdf->Text($x+20, $y+216, strtoupper($attendant_name));
		$pdf->Text($x+20, $y+216, $doctor_name);
		
		$pdf->Text($x+20, $y+221, $attendant_title);
		
		$pdf->SetXY($x+91, $y+209);	
		#$pdf->MultiCell(60, 4,ucwords($attendant_address), '', 'L','0');
		#$attendant_address = str_replace(",","",trim($attendant_address));
		$attendant_address = substr_replace(trim($attendant_address)," ",20,1);
		$pdf->MultiCell(50, 4,$attendant_address, '', 'L','0');
		
		$pdf->Text($x+92, $y+221, $attendant_date_sign);

//20. INFORMANT
	if (($informant_date_sign!='0000-00-00') && ($informant_date_sign!="")){
#		$informant_date_sign = @formatDate2Local($informant_date_sign,$date_format);
		#$tempYear = intval(substr($informant_date_sign, 0, 4)); 
		#$tempMonth = intval(substr($informant_date_sign, 5, 7)); 
		#$tempDay = intval(substr($informant_date_sign, 8, 10)); 
		$tempYear = date("Y",strtotime($informant_date_sign)); 
		$tempMonth = date("F",strtotime($informant_date_sign)); 
		$tempDay = date("d",strtotime($informant_date_sign)); 
		
		$informant_date_sign =$tempDay." ".$tempMonth." ".$tempYear;
	}else{
		$informant_date_sign = '';
	}

		$pdf->Text($x+20, $y+240, strtoupper($informant_name));
		$pdf->Text($x+33, $y+246, $informant_relation);
		$pdf->SetXY($x+91, $y+233);	
		//$pdf->MultiCell(60, 4,strtoupper($informant_address), '', 'L','0');
		#$pdf->MultiCell(60, 4,ucwords($informant_address), '', 'L','0');
		$pdf->MultiCell(60, 4,trim($informant_address), '', 'L','0');
		$pdf->Text($x+92, $y+246, $informant_date_sign);

//21. PREPARED BY
	if (($encoder_date_sign!='0000-00-00') && ($encoder_date_sign!="")){
#		$encoder_date_sign = @formatDate2Local($encoder_date_sign,$date_format);
		#$tempYear = intval(substr($encoder_date_sign, 0, 4)); 
		#$tempMonth = intval(substr($encoder_date_sign, 5, 7)); 
		#$tempDay = intval(substr($encoder_date_sign, 8, 10)); 
		$tempYear = date("Y",strtotime($encoder_date_sign)); 
		$tempMonth = date("F",strtotime($encoder_date_sign)); 
		$tempDay = date("d",strtotime($encoder_date_sign));
		
		$encoder_date_sign =$tempDay." ".$tempMonth." ".$tempYear;
	}else{
		$encoder_date_sign = '';
	}
		$pdf->Text($x+21, $y+270, strtoupper($encoder_name));
		$pdf->Text($x+21, $y+274, $encoder_title);
		$pdf->Text($x+21, $y+279, $encoder_date_sign);
#echo "late = ".$is_late_reg;	
	/*
	if ($marriage_type){
		$pdf->Output();
	}elseif	($is_late_reg!=1){
		$pdf->Output();
	}
	*/
		
#	AFFIDAVIT OF ACKNOWLEDGMENT/ADMISSION OF PATERNITY
	#$pdf->AddPage("P");
	#edited by VAN 06-28-08
	if (($is_married!=1)||($is_late_reg)){
		$pdf->AddPage("P");
	}
	$x = $pdf->GetX();
	$y = $pdf->GetY();	
	$y = $y*0.8;	#readjustment by fdp; 07-16-2008 
	#$pdf->Line($x, $y, $x*20, $y);
#echo "x, y = ".$x." , ".$y;
#echo "married = ".$is_married;
	if ($is_married!=1){
		#$pdf->Text(35, 16, strtoupper("$f_name_first $f_name_middle $f_name_last"));
		if (($f_name_first=="n/a")&&($f_name_first=="n/a")&&($f_name_first=="n/a")){
			#$father = "no father"; 
			$father = ""; 
			$nofather = 1;
		}else{
		 	//$father = "$f_name_first $f_name_middle $f_name_last";	
			#$father = "$f_name_first"." ".substr($f_name_middle,0,1).". $f_name_last";	//modified according to users' specs
			#edited by VAN 08-28-08
			if (stristr($f_name_first,",")){
				$f_name_first_new = explode(",",$f_name_first);
				$fname1 = $f_name_first_new[0];
				$fname2 = $f_name_first_new[1];
			}else{
				$fname1 = $f_name_first;
				$fname2 = "";
			}
			
			$suffix = array("JR","SR","JR.","SR.");
			if (($fname2) && (in_array(trim($fname2),$suffix)))				
			#if ($fname2)
				$fname2 = ", ".$fname2;
			else	
				$fname2 = " ".$fname2;
				
			if ($f_name_middle)	
				$f_name_middle = substr($f_name_middle,0,1).". ";
									
			$father = $fname1." ".$f_name_middle.$f_name_last."".$fname2;	//modified according to users' specs
			
			$nofather = 0;
		}	
		$pdf->Text($x+25, $y+2, strtoupper($father));
		
		#if ($m_name_middle)	
				#$m_name_middle = substr($m_name_middle,0,1).". ";
		
		#$mother = $m_name_first." ".$m_name_middle.$m_name_last;
		#$pdf->Text($x+130, $y+2, strtoupper($mother));

	if (($f_com_tax_date!='0000-00-00') && ($f_com_tax_date!="")){
		#$tempYear = intval(substr($f_com_tax_date, 0, 4)); 
		#$tempMonth = intval(substr($f_com_tax_date, 5, 7)); 
		#$tempDay = intval(substr($f_com_tax_date, 8, 10)); 
		$tempYear = date("Y",strtotime($f_com_tax_date)); 
		$tempMonth = date("F",strtotime($f_com_tax_date)); 
		$tempDay = date("d",strtotime($f_com_tax_date)); 
		
		$f_com_tax_date =$tempDay." ".$tempMonth." ".$tempYear;
	}else{
		$f_com_tax_date = '';
	}

	if (($m_com_tax_date!='0000-00-00') && ($m_com_tax_date!="")){
		#$tempYear = intval(substr($m_com_tax_date, 0, 4)); 
		#$tempMonth = intval(substr($m_com_tax_date, 5, 7)); 
		#$tempDay = intval(substr($m_com_tax_date, 8, 10));
		$tempYear = date("Y",strtotime($m_com_tax_date)); 
		$tempMonth = date("F",strtotime($m_com_tax_date)); 
		$tempDay = date("d",strtotime($m_com_tax_date));   
		$m_com_tax_date =$tempDay." ".$tempMonth." ".$tempYear;
	}else{
		$m_com_tax_date = '';
	}
		/*
		$pdf->Text(44, 49, $f_com_tax_nr);
		$pdf->Text(180, 49, $m_com_tax_nr);
		$pdf->Text(30, 54, $f_com_tax_date);
		$pdf->Text(165, 54, $m_com_tax_date);
		$pdf->Text(30, 60, $f_com_tax_place);
		$pdf->Text(165, 60, $m_com_tax_place);
		*/
		$pdf->Text($x+36, $y+35, $f_com_tax_nr);
		$pdf->Text($x+170, $y+35, $m_com_tax_nr);
		$pdf->Text($x+22, $y+40, $f_com_tax_date);
		$pdf->Text($x+155, $y+40, $m_com_tax_date);
		$pdf->Text($x+22, $y+46, $f_com_tax_place);
		$pdf->Text($x+155, $y+46, $m_com_tax_place);

	if ($officer_date_sign!=""){
		#$officerYear = intval(substr($officer_date_sign, 0, 4)); 
		#$officerMonth = intval(substr($officer_date_sign, 5, 7)); 
		#$officerDay = intval(substr($officer_date_sign, 8, 10));
		#$officerMonthName = $arrayMonth[$officerMonth];
		$officerYear = date("Y",strtotime($officer_date_sign)); 
		$officerMonthName = date("F",strtotime($officer_date_sign)); 
		$officerDay = date("d",strtotime($officer_date_sign)); 
	}
		/*	
		$pdf->Text(110, 72, $officerDay);
		$pdf->Text(138, 72, $officerMonthName);
		$pdf->Text(195, 72, $officerYear);
		$pdf->Text(25, 77, $officer_place_sign);
		*/
		if ($officerDay==0)
			$officerDay = "";
		
		if ($officerYear==0)	
			$officerYear = "";
			
		//$pdf->Text($x+100, $y+58, $officerDay);
		//$pdf->Text($x+133, $y+58, $officerMonthName);
		//$pdf->Text($x+187, $y+58, $officerYear);
		#$nofather
		
		if ($nofather)
			$officer_place_sign = "";
		
		$pdf->Text($x+15, $y+62.5, $officer_place_sign);
		/*
		$address1 = "";
		$address2 = "";
		$index = strlen($officer_address);
		if (strlen($officer_address)>45){
			$temp = substr($officer_address,0,45);
			$index = strrpos($temp," ");
		}
		$address1 = trim(substr($officer_address,0,$index));
		$address2 = trim(substr($officer_address,$index));
		*/
		/*
		$pdf->Text(150, 90, $officer_title);
		$pdf->Text(130, 98, $address1);
		$pdf->Text(130, 102, $address2);
		$pdf->Text(22, 103, strtoupper($officer_name));
		*/
		$pdf->Text($x+140, $y+76, $officer_title);
		#$pdf->Text($x+120, $y, $address1);
		#$pdf->Text($x+, $y, $address2);
		$pdf->SetXY($x+130, $y+85);	
		#$pdf->MultiCell(80, 4,ucwords($officer_address), '', 'L','0');
		$pdf->MultiCell(80, 4,$officer_address, '', 'L','0');
		
		$pdf->Text($x+12, $y+88, strtoupper($officer_name));
		#$pdf->SetXY($x+12, $y+89);	
		#$pdf->MultiCell(80, 4,strtoupper($officer_name), '1', 'L','0');
	}
	
	if ($is_late_reg){
		#AFFIDAVIT OF LATE/DELAYED REGISTRATION
		if ($is_late_reg=='1'){
			$pdf->Text($x+22, $y+125, strtoupper($late_affiant_name));
			//$pdf->Text($x+85, $y+130, strtoupper($late_affiant_address));
			#$pdf->Text($x+85, $y+130, ucwords($late_affiant_address));
			$pdf->Text($x+85, $y+130, $late_affiant_address);
			$baby_name = strtoupper($name_first)." ".strtoupper($name_middle)." ".strtoupper($name_last);
			$pdf->Text($x+32, $y+152.5, $baby_name);
			$pdf->Text($x+80, $y+157, $birthDay."   ".$birthMonth."   ".$birthYear);
			$pdf->Text($x+130, $y+157, "DMC-Davao City");
			$pdf->Text($x+100, $y+162, $doctor_name);
			$pdf->Text($x+42, $y+166, "DMC-Davao City");
			$country = $address_country->getCountryInfo($late_baby_citizenship);
			#echo "sql = ".$address_country->sql;
			$pdf->Text($x+80, $y+170, $country['country_name']);
			#$parent_marriage_info_tmp = date("F d, Y",strtotime($parent_marriage_date))." at ".$parent_marriage_place;
			#if ($is_married==1){
		#added by VAN 08-12-08	
		if ($nofather){
			$pdf->Text($x+85, $y+178, "");
			$pdf->Text($x+85, $y+188, "");
			$pdf->Text($x+122, $y+176, "");
			$pdf->Text($x+100, $y+181, "");
		}else{	
			#echo "date = ".$parent_marriage_date;
			#if ($parent_marriage_date){
			if ($parent_marriage_date!='0000-00-00'){
				$pdf->Text($x+85, $y+178, "X");
				$pdf->Text($x+122, $y+176, date("F d, Y",strtotime($parent_marriage_date)));
				$pdf->Text($x+100, $y+181, $parent_marriage_place);
				#$pdf->Text($x+100, $y+181, 'San Pedro Cathedral, Davao City');
			#}else{
			#}elseif (!$parent_marriage_date){
			}else{
				$pdf->Text($x+85, $y+188, "X");
				#$father = strtoupper($f_name_first)." ".strtoupper($f_name_middle)." ".strtoupper($f_name_last);
				if (stristr($f_name_first,",")){
					$f_name_first_new = explode(",",$f_name_first);
					$fname1 = $f_name_first_new[0];
					$fname2 = $f_name_first_new[1];
				}else{
					$fname1 = $f_name_first;
					$fname2 = "";
				}
							
				$suffix = array("JR","SR","JR.","SR.");
							
				if (($fname2) && (in_array(trim($fname2),$suffix)))	
				#if ($fname2)
					$fname2 = ", ".$fname2;
				else	
					$fname2 = " ".$fname2;
				
				$father = strtoupper($fname1)." ".strtoupper($f_name_middle)." ".strtoupper($f_name_last)."".strtoupper($fname2);	
				
				$pdf->Text($x+112, $y+190, $father);	
			}	
		}	
			$pdf->Text($x+32, $y+200, strtoupper($late_reason));	
			$pdf->Text($x+32, $y+209, strtoupper($late_purpose));	
			#echo "father = ".$is_married;
			/*
			if (($is_married==1)&&($father)){
				$pdf->Text($x+19, $y+215, "X");	
				$father = strtoupper($f_name_first)." ".strtoupper($f_name_middle)." ".strtoupper($f_name_last);
				$pdf->Text($x+121, $y+215, $father);
			}else{
				$pdf->Text($x+20, $y+215, "");	
				$pdf->Text($x+121, $y+215, "");
			}
			*/
			#echo "relation = ".$late_relationship;
			#edited by VAN 08-05-08
		if ($nofather){
			$pdf->Text($x+19, $y+220, "");	
			$pdf->Text($x+123, $y+220, "");
			$pdf->Text($x+19, $y+215, "");	
			$pdf->Text($x+121, $y+215, "");
		}else{	
			if (($late_relationship)&&($late_relationship)){
				$pdf->Text($x+19, $y+220, "X");	
				$pdf->Text($x+123, $y+220, strtoupper($late_relationship));
				
				$pdf->Text($x+20, $y+215, "");	
				$pdf->Text($x+121, $y+215, "");
			}else{
				$pdf->Text($x+20, $y+220, "");	
				$pdf->Text($x+120, $y+220, "");
				
				$pdf->Text($x+19, $y+215, "X");	
				#$father = strtoupper($f_name_first)." ".strtoupper($f_name_middle)." ".strtoupper($f_name_last);
				if (stristr($f_name_first,",")){
					$f_name_first_new = explode(",",$f_name_first);
					$fname1 = $f_name_first_new[0];
					$fname2 = $f_name_first_new[1];
				}else{
					$fname1 = $f_name_first;
					$fname2 = "";
				}
				
				$suffix = array("JR","SR","JR.","SR.");
							
				if (($fname2) && (in_array(trim($fname2),$suffix)))	
					$fname2 = ", ".$fname2;
				else	
					$fname2 = " ".$fname2;	
				
				$father = strtoupper($fname1)." ".strtoupper($f_name_middle)." ".strtoupper($f_name_last)."".strtoupper($fname2);	
				
				$pdf->Text($x+121, $y+215, $father);
			}
			
			if (($affiant_com_tax_date!='0000-00-00') && ($affiant_com_tax_date!="")){
				$tempYear = intval(substr($affiant_com_tax_date, 0, 4)); 
				$tempMonth = intval(substr($affiant_com_tax_date, 5, 7)); 
				$tempDay = intval(substr($affiant_com_tax_date, 8, 10)); 
				$affiant_com_tax_date =$tempDay." ".$arrayMonth[$tempMonth]." ".$tempYear;
			}else{
				$affiant_com_tax_date = '';
			}
		}	
			
			if($affiant_com_tax_date)
				$affiant_com_tax_date = date("d F Y", strtotime($affiant_com_tax_date));
			
			$pdf->Text($x+160, $y+242, $affiant_com_tax_nr);
			$pdf->Text($x+150, $y+248, $affiant_com_tax_date);
			$pdf->Text($x+150, $y+253, $affiant_com_tax_place);

			if ($late_officer_date_sign!=""){
				$late_officerYear = intval(substr($late_officer_date_sign, 0, 4)); 
				$late_officerMonth = intval(substr($late_officer_date_sign, 5, 7)); 
				$late_officerDay = intval(substr($late_officer_date_sign, 8, 10));
				$late_officerMonthName = $arrayMonth[$late_officerMonth];
			}
		
			if ($late_officerDay==0)
				$late_officerDay = "";
		
			if ($late_officerYear==0)	
				$late_officerYear = "";
			
			//$pdf->Text($x+100, $y+265, $late_officerDay);
			//$pdf->Text($x+133, $y+265, $late_officerMonthName);
			//$pdf->Text($x+187, $y+265, $late_officerYear);
			$pdf->Text($x+15, $y+270, $late_officer_place_sign);
	
			$pdf->Text($x+140, $y+283, $late_officer_title);
			$pdf->SetXY($x+130, $y+292);	
			//$pdf->MultiCell(80, 4,$late_officer_address, '', 'L','0');
			#$pdf->MultiCell(80, 4,seg_ucwords($late_officer_address), '', 'L','0');
			$pdf->MultiCell(80, 4,$late_officer_address, '', 'L','0');
			$pdf->Text($x+12, $y+295, strtoupper($late_officer_name));
		}
	}else{
		
	}
	$pdf->Output();	
?>