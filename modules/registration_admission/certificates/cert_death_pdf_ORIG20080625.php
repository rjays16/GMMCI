<?php
							
include("roots.php");
include_once($root_path."/classes/fpdf/fpdf.php");
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');

include_once($root_path.'include/care_api_classes/class_address.php');
$address_country = new Address('country');
$address_brgy = new Address('barangay');

if (isset($_GET['id']) && $_GET['id']){
	$pid = $_GET['id'];
}

include_once($root_path.'include/care_api_classes/class_person.php');
$person_obj = new Person($pid);

require_once($root_path.'include/care_api_classes/class_personell.php');
$pers_obj=new Personell;

if($pid){
	//if(!($basicInfo = $person_obj->BasicDataArray($pid))){
	if(!($basicInfo = $person_obj->getAllInfoArray($pid))){
		echo '<em class="warn">Sorry but the pare cannot be displayed!</em> ';
		exit();
	}
	extract($basicInfo);
	$brgy_info = $address_brgy->getAddressInfo($brgy_nr,TRUE);
	if($brgy_info){
		$brgy_row = $brgy_info->FetchRow();
	}
}else{
	echo '<em class="warn"> Sorry but the page cannot be displayed! <br> Invalid PID!</em>';
	exit();
}

$birthYear = intval(substr($date_birth, 0, 4)); 
$birthMonth = intval(substr($date_birth, 5, 7)); 
$birthDay = intval(substr($date_birth, 8, 10)); 

include_once($root_path.'include/care_api_classes/class_cert_death.php');
$obj_deathCert = new DeathCertificate($pid);

# code for retrieving death certificate information
$deathCertInfo = $obj_deathCert->getDeathCertRecord($pid);
if($deathCertInfo){
	extract($deathCertInfo);
	$delivery_method_tmp= substr(trim($deathCertInfo['delivery_method']),0,1);
	$delivery_method_info = substr(trim($deathCertInfo['delivery_method']),4);

	$death_manner_tmp = substr(trim($deathCertInfo['death_manner']),0,1);
	$death_manner_info = substr(trim($deathCertInfo['death_manner']),4);

	$attendant_type = substr(trim($deathCertInfo['attendant_type']),0,1);
	$attendant_type_others = substr(trim($deathCertInfo['attendant_type']),4);

	$tmp_death_cause = unserialize($deathCertInfo['death_cause']);

	#$deathYear = intval(substr($death_date, 0, 4)); 
	#$deathMonth = intval(substr($death_date, 5, 7)); 
	#$deathDay = intval(substr($death_date, 8, 10)); 
}

	$border="1";
	$border2="0";
	$space=2;

#	$fontSizeLabel=9;
	$fontSizeInput=11;
	$fontSizeHeading=14;

	$pdf = new FPDF("P","mm","Legal");
	$pdf->AddPage("P");
	
	$pdf->SetDisplayMode(real,'default');
	
	#$x = $pdf->GetX();
	#$y=$x*3;
	
	#$pdf->Line($x,$y,$x*20,$y);
	#$pdf->SetXY($x,$y);
	

	$pdf->SetFont("Arial","B",$fontSizeInput);
	
	$pdf->Text(27, 18, "Davao del Sur");
	#$pdf->Text($x+20, $y-10.5, "Davao del Sur");
	$pdf->Text(37, 23, "Digos City");
	#$pdf->Text($x+30, $y-, "Davao City");
	$pdf->Text(120, 23, $registry_nr."11111"); # Registry No.


//1. NAME
/*
	$pdf->Text(35, 38, strtoupper($name_first));
	$pdf->Text(85, 38, strtoupper($name_middle));
	$pdf->Text(125, 38, strtoupper($name_last));
*/
	$pdf->SetXY(30, 30);	
	$pdf->MultiCell(50, 4,strtoupper($name_first)." CARLOS", '', 'L','0');
	
	$pdf->SetXY(80, 30);	
	$pdf->MultiCell(40, 4,strtoupper($name_middle), '', 'L','0');
	
	$pdf->SetXY(120, 30);	
	$pdf->MultiCell(40, 4,strtoupper($name_last), '', 'L','0');
		
//2. SEX
	if ($sex=='m')
		$pdf->Text(8, 45, "x");
	if ($sex=='f')
		$pdf->Text(8, 50, "x");
	
	
//3. RELIGION
	#$pdf->Text(35, 49, $religion_name);
	$pdf->SetXY(38, 42);	
	$pdf->MultiCell(25, 4,strtoupper($religion_name), '', 'C','0');
		
//4. AGE
	$date_birth_tmp = @formatDate2Local($date_birth,$date_format);
	if (($death_date!='0000-00-00')  && ($death_date!=""))
		$death_date_tmp = @formatDate2Local($death_date,$date_format);
	else
		$death_date_tmp='';

	$ageYear = $person_obj->getAge($date_birth_tmp,'',$death_date_tmp);
	if (is_numeric($ageYear) && ($ageYear>=0)){
		if ($ageYear<1){
			$ageMonth = intval($ageYear*12);
			$ageDay = (($ageYear*12)-$ageMonth) * 30;

			if(($ageMonth == 0) && (round($ageDay)<1)){
				# under 1 day
				if ($age_at_death)
					list($ageHours,$ageMinutes,$ageSec) = explode(":",$age_at_death);
				$ageMonth = ''; # set age in months as empty
				$ageDay = ''; # set age in days as empty

			}else{
				# under 1 year but above 1 day
				$ageMonth = intval($ageYear*12);
				$ageDay = round((($ageYear*12)-$ageMonth) * 30);	

			}
			$ageYear = ''; # set age in years as empty
		}else{
			# above 1 year
			$ageYear = number_format($ageYear, 2);
		}
	}else{
	}
		$pdf->Text(88, 50, $ageYear);
		$pdf->Text(112, 50, "11".$ageMonth);
		$pdf->Text(125, 50, "22".$ageDay);
		if (trim($ageHours."".$ageMinutes."".$ageSec)!="")
			$ageHoursMinutesSec = $ageHours." hrs ".$ageMinutes." min ".$ageSec." sec";
		$pdf->Text(145, 50, "33".$ageHoursMinutesSec); 

//5. PLACE OF DEATH
	$pdf->SetFont("Arial","B",$fontSizeInput+2);
	$pdf->Text(35, 62.5, "Gonzales Maranan Medical Center Incorporated, DAVAO CITY");

	$pdf->SetFont("Arial","B",$fontSizeInput);
	


//6. DATE OF DEATH
	$deathYear = date("Y",strtotime($death_date)); 
	$deathMonthName = date("F",strtotime($death_date)); 
	$deathDay = date("d",strtotime($death_date)); 
	
	#$arrayMonth = array ("","January","February","March","April","May","June","July","August","September","October","November","December");
	#$deathMonthName = $arrayMonth[$deathMonth];
	$pdf->Text(45, 71, $deathDay."    ".$deathMonthName."    ".$deathYear);

//7. CITIZENSHIP
	$pdf->Text(115, 71, $country_citizenship);
	
//8. RESIDENCE
	$m_address = $street_name;
	if (!empty($m_address) && !empty($brgy_row['brgy_name']))
		$m_address = $m_address.", ".$brgy_row['brgy_name'];
	else
		$m_address = $m_address." ".$brgy_row['brgy_name'];
	if (!empty($m_address) && !empty($brgy_row['mun_name']))
		$m_address = $m_address.", ".$brgy_row['mun_name'];
	else
		$m_address = $m_address." ".$brgy_row['mun_name'];
	if (!empty($m_address) && !empty($brgy_row['prov_name']))
		$m_address = $m_address.", ".$brgy_row['prov_name'];
	else
		$m_address = $m_address." ".$brgy_row['prov_name'];

	#$pdf->Text(15, 84, $m_address);
	$pdf->SetXY(15, 76.5);	
	$pdf->MultiCell(140, 4,strtoupper($m_address), '', 'L','0');

//9. CIVIL STATUS

	if ($civil_status=="single")
		$pdf->Text(10, 88, "x");   # single   12, 116
	else if ($civil_status=="married")
		$pdf->Text(10, 92, "x");   # married   12, 120
	else if ($civil_status=="widowed")
		$pdf->Text(38, 88, "x");   # widowed   40, 116
	else if ($civil_status=="")
		$pdf->Text(65, 88, "x");   # unknown   70, 116
	else if ($civil_status!=""){
		$pdf->Text(38, 92, "x");   # others   40, 120
		$pdf->Text(59, 92, $civil_status);   # 62, 120
	}

//7. OCCUPATION
	$pdf->Text(110, 92, $occupation_name);

//17. CAUSES OF DEATH
	#$pdf->Text(41, 115, $tmp_death_cause['cause6']);   # immediate cause
	$pdf->Text(41, 115, "");   # immediate cause
	$pdf->Text(10, 119, "");
	#$pdf->Text(105, 119, $tmp_death_cause['interval1']);
	$pdf->Text(105, 119, "");

	#$pdf->Text(41, 123, $tmp_death_cause['cause7']);   # antecedent cause
	$pdf->Text(41, 123, "");   # antecedent cause
	$pdf->Text(10, 127, "");
	#$pdf->Text(105, 127, $tmp_death_cause['interval2']);
	$pdf->Text(105, 127, "");

	#$pdf->Text(41, 132, $tmp_death_cause['cause8']);   # underlying cause
	$pdf->Text(41, 132, "");   # underlying cause
	#$pdf->Text(10, 136, "cause8 2nd line");
	$pdf->Text(10, 136, "");
	#$pdf->Text(105, 136, $tmp_death_cause['interval3']);
	$pdf->Text(105, 136, "");

	#$pdf->Text(47, 140, $tmp_death_cause['cause9']);   # other cause
	$pdf->Text(47, 140, "");   # other cause
	#$pdf->Text(47, 144, "12345678901234567890123456789012345678901234567890123");
	$pdf->Text(47, 144, "");

//18. DEATH BY NON NATURAL CAUSES

	# A. Manner of death
	if($death_manner_tmp=='1')
		$pdf->Text(11, 155, "x");   # Homecide   8, 180.5
	else if($death_manner_tmp=='2')
		$pdf->Text(40, 155,"x");   # Suicide   30, 180.5
	else if($death_manner_tmp=='3')
		$pdf->Text(66, 155, "x");   # Accident   66, 180.5
	else if($death_manner_tmp=='4'){
		$pdf->Text(92, 155, "x");   # Others   100, 180.5
		$pdf->Text(128, 154.5, $death_manner_info);   # specific   132, 180.5
	}
	# B. Place of occurence
	$pdf->Text(95, 159.5, ucwords(strtolower($place_occurence)));	# 84, 197.7

//19.ATTENDANT
	if($attendant_type == '1')
		$pdf->Text(11, 170, "x");   # 8, 195
	if($attendant_type == '2')
		$pdf->Text(11, 174, "x");	# 8, 199
	if($attendant_type == '3')
		$pdf->Text(11, 179, "x");   # 8, 203
	if($attendant_type == '4')
		$pdf->Text(58, 170, "x");   # 58, 195
	if($attendant_type == '5'){
		$pdf->Text(58, 174, "x");  # 58, 199
		$pdf->Text(58, 179.5, $attendant_type_others);   # 58, 203
	}
	
	#echo $attended_from_date;
	/*
	$attendedFromDateYear = substr($attended_from_date, 0, 4); 
	$attendedFromDateMonth = intval(substr($attended_from_date, 5, 7)); 
	$attendedFromDateMonthName = $arrayMonth[$attendedFromDateMonth];
	*/
	#edited by VAN 05-27-08
	$attendedFromDateYear = date("Y",strtotime($attended_from_date)); 
	$attendedFromDateDay = date("d",strtotime($attended_from_date)); 
	$attendedFromDateMonthName = date("F",strtotime($attended_from_date));
	/*
	$attendedToDateYear = substr($attended_to_date, 0, 4); 
	$attendedToDateDay = intval(substr($attended_to_date, 8, 10)); 
	$attendedToDateMonth = intval(substr($attended_to_date, 5, 7)); 
	$attendedToDateMonthName = $arrayMonth[$attendedToDateMonth];
	*/
	$attendedToDateYear = date("Y",strtotime($attended_to_date)); 
	$attendedToDateDay = date("d",strtotime($attended_to_date));
	$attendedToDateMonthName = date("F",strtotime($attended_to_date));
	

	//If attended, state duration:
	$pdf->Text(107, 170.5, $attendedFromDateMonthName." ".$attendedFromDateDay.", ");
	$pdf->Text(140, 170.5, $attendedFromDateYear);
	$pdf->Text(107, 175, $attendedToDateMonthName." ".$attendedToDateDay.", ");
	$pdf->Text(140, 175, $attendedToDateYear);


//20. CERTIFICATION OF DEATH
	if ($death_cert_attended=='0')
		$pdf->Text(10, 196.5, "x");
	if ($death_cert_attended=='1'){
		$pdf->Text(10, 201.5, "x");
		if (($death_time !='00:00:00') && ($death_time!=""))
			$pdf->Text(87.5, 200, "".convert24HourTo12HourLocal($death_time));
	}


	$address1 = "";
	$address2 = "";
	$index = strlen($attendant_address);
	if (strlen($attendant_address)>45){
		$temp = substr($attendant_address,0,45);
		$index = strrpos($temp," ");
	}
	$address1 = trim(substr($attendant_address,0,$index));
	$address2 = trim(substr($attendant_address,$index));

	if (($attendant_date_sign!='0000-00-00')  && ($attendant_date_sign!="")){
#		$attendant_date_sign = @formatDate2Local($attendant_date_sign,$date_format);
		#$tempYear = intval(substr($attendant_date_sign, 0, 4)); 
		#$tempMonth = intval(substr($attendant_date_sign, 5, 7)); 
		#$tempDay = intval(substr($attendant_date_sign, 8, 10)); 
		$tempYear = date("Y",strtotime($attendant_date_sign)); 
		$tempMonth = date("F",strtotime($attendant_date_sign)); 
		$tempDay = date("d",strtotime($attendant_date_sign)); 
		
		$attendant_date_sign =$tempDay."  ".$tempMonth."  ".$tempYear;
	}else{
		$attendant_date_sign='';
	}

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
	$doctor_name = "Dr. ".ucwords(strtolower($doctor_name));
		
	$pdf->Text(32, 215.5, $doctor_name);
	$pdf->Text(32, 221, $attendant_title);
	#$pdf->Text(20, 229, $address1);
	#$pdf->Text(20, 233, $address2);
	$pdf->SetXY(20, 223.5);	
	$pdf->MultiCell(65, 4,strtoupper($attendant_address), '', 'L','0');
	$pdf->Text(15, 236, $attendant_date_sign);

//21. CORPSE DISPOSAL
#	$pdf->Text(0, 285.5, "B");
#	$pdf->Text(0, 289.5, "C");	
#	$pdf->Text(24, 285.5, "O");
#	$pdf->Text(25, 289.5, "Specify");
	
//22. BURIAL CREMATION
#	$pdf->Text(84.5, 285.5, "number");
#	$pdf->Text(84.5, 289.5, "Date issued");

//23. AUTOPSY
#	$pdf->Text(136, 285.5, "Y");
#	$pdf->Text(136, 289.5, "N");
	
//24. NAME AND ADDRESS OF CEMETERY OR CREMATORY
#	$pdf->Text(5,300, "NAME AND ADDRESS OF CEMETERY OR CREMATORY");

//25. INFORMANT
	if (($informant_date_sign!='0000-00-00') && ($informant_date_sign!="")){
		#$tempYear = intval(substr($informant_date_sign, 0, 4)); 
		#$tempMonth = intval(substr($informant_date_sign, 5, 7)); 
		#$tempDay = intval(substr($informant_date_sign, 8, 10));
		$tempYear = date("Y",strtotime($informant_date_sign)); 
		$tempMonth = date("F",strtotime($informant_date_sign)); 
		$tempDay = date("d",strtotime($informant_date_sign)); 
		 
		$informant_date_sign =$tempDay."  ".$tempMonth."  ".$tempYear;
	}else{
		$informant_date_sign = '';
	}

	$address1 = "";
	$address2 = "";
	$index = strlen($informant_address);
	if (strlen($informant_address)>45){
		$temp = substr($informant_address,0,45);
		$index = strrpos($temp," ");
	}
	$address1 = trim(substr($informant_address,0,$index));
	$address2 = trim(substr($informant_address,$index));

		$pdf->Text(27, 276.5, strtoupper($informant_name));
		$pdf->Text(49, 281, ucwords(strtolower($informant_relation)));
		#$pdf->Text(92, 276, $address1);
		#$pdf->Text(92, 280, $address2);
		$pdf->SetXY(100, 268);	
		$pdf->MultiCell(65, 4,strtoupper($informant_address), '', 'L','0');
		$pdf->Text(100, 281, $informant_date_sign);

//26. PREPARED BY
	if (($encoder_date_sign!='0000-00-00') && ($encoder_date_sign!="")){
		#$tempYear = intval(substr($encoder_date_sign, 0, 4)); 
		#$tempMonth = intval(substr($encoder_date_sign, 5, 7)); 
		#$tempDay = intval(substr($encoder_date_sign, 8, 10)); 
		$tempYear = date("Y",strtotime($encoder_date_sign)); 
		$tempMonth = date("F",strtotime($encoder_date_sign)); 
		$tempDay = date("d",strtotime($encoder_date_sign)); 
		
		$encoder_date_sign =$tempDay."  ".$tempMonth."  ".$tempYear;
	}else{
		$encoder_date_sign = '';
	}

		$pdf->Text(28, 300, strtoupper($encoder_name));
		$pdf->Text(28, 304, $encoder_title);
		$pdf->Text(25, 307.5, $encoder_date_sign);

#echo "<br>year = ".$ageYear;
#echo "<br>month = ".$ageMonth;
#echo "<br>day = ".$ageDay;
#$ageYear = 1;
#$ageMonth = 12;
#$ageDay = 5;

	#if ( (intval($ageYear)>0) || (intval($ageMonth)>0) || (intval($ageDay)>7) )
	if ( (intval($ageYear)<0) || (intval($ageMonth)<0) || (intval($ageDay)<7) )
		$pdf->Output();   # more than 7 days old at the time of death
	
	$pdf->AddPage("P");
	#$pdf->SetTopMargin(5);
	#$x = $pdf->GetX();
	#$y = $pdf->GetY();
	
	#$y = $y + 30;
	#$pdf->Line($x, $y, $x*20, $y);
	#echo "x, y = ".$x." , ".$y;

	# FOR AGES 0 TO 7 DAYS OLD
		#$birthYear = intval(substr($date_birth, 0, 4)); 
		#$birthMonth = intval(substr($date_birth, 5, 7)); 
		#$birthMonthName = $arrayMonth[$birthMonth];
		#$birthDay = intval(substr($date_birth, 8, 10)); 
		$birthYear = date("Y",strtotime($date_birth)); 
		$birthMonthName = date("F",strtotime($date_birth)); 
		$birthDay = date("d",strtotime($date_birth)); 

//11. DATE OF BIRTH
		$pdf->Text(22, 4, $birthDay."    ".$birthMonthName."    ".$birthYear);


//12. AGE OF THE MOTHER
		$pdf->Text(100, 4, $m_age);

//13. METHOD OF DELIVERY
		#$pdf->SetY(-0.5);	
	if ($delivery_method_tmp=="1")
		$pdf->Text(141, 1, "x");   # Normal; spontaneous vertex   135, 28
	if ($delivery_method_tmp=="2"){
		$pdf->Text(141, 3, "x");   # Others   135, 32
		$pdf->Text(175, 2, "normal".$delivery_method_info);   # specific   165, 32
	}

//14. LENGTH OF PREGANANCY
		$pdf->Text(90, 9, $pregnancy_length);
		
//15. TYPE OF BIRTH
#	if ($birth_type=='1')
		$pdf->Text(18, 18, "x");   # single   10, 40
#	if ($birth_type=='2')
		$pdf->Text(41, 18, "x");   # twin   25, 40
#	if ($birth_type=='3')
		$pdf->Text(62, 18, "x");   # triplet, etc.   62, 46

//16. IF MULTIPLE BIRTH, CHILD WAS
#	if ($birth_rank=='1')
		$pdf->Text(100, 19, "x");   # first   90, 40
#	if ($birth_rank=='2')
		$pdf->Text(122, 19, "x");   # second  120, 46
#	if (intval($birth_rank) > 2){
		$pdf->Text(152, 19, "x");   # others   148, 46
		$pdf->Text(184, 19, $birth_rank);   # specific   175, 46
#	}

//17. CAUSES OF DEATH
		$pdf->Text(60, 31, "sample1".$tmp_death_cause['cause1']);   # Main disease/condition of infant   
		$pdf->Text(65, 35, "sample2".$tmp_death_cause['cause2']);   # Other diseases/conditions of infant 
		$pdf->Text(80, 38, "sample3".$tmp_death_cause['cause3']);   # Main maternal disease/condition of affecting infant
		$pdf->Text(80, 41, "sample4".$tmp_death_cause['cause4']);   # Other maternal disease/condition of affecting infant
		$pdf->Text(56, 45, "sample5".$tmp_death_cause['cause5']);   # Other relevant circumstances 

	$pdf->Output();   # less than or equal 7 days old at the time of death
	
?>