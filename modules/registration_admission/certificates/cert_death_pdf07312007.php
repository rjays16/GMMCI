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
#	echo "deathCertInfo : <br>"; print_r($deathCertInfo); echo "<br> \n";
	extract($deathCertInfo);
	$delivery_method_tmp= substr(trim($deathCertInfo['delivery_method']),0,1);
	$delivery_method_info = substr(trim($deathCertInfo['delivery_method']),4);
/*
echo "deathCertInfo['delivery_method'] = '".$deathCertInfo['delivery_method']."' <br> \n";
echo "delivery_method_tmp = '".$delivery_method_tmp."' <br> \n";
echo "delivery_method_info = '".$delivery_method_info."' <br> \n";
*/
	$death_manner_tmp = substr(trim($deathCertInfo['death_manner']),0,1);
	$death_manner_info = substr(trim($deathCertInfo['death_manner']),4);
/*
echo "deathCertInfo['death_manner'] = '".$deathCertInfo['death_manner']."' <br> \n";
echo "death_manner_tmp = '".$death_manner_tmp."' <br> \n";
echo "death_manner_info = '".$death_manner_info."' <br> \n";
*/
	$attendant_type = substr(trim($deathCertInfo['attendant_type']),0,1);
	$attendant_type_others = substr(trim($deathCertInfo['attendant_type']),4);
/*
echo "deathCertInfo['attendant_type'] = '".$deathCertInfo['attendant_type']."' <br> \n";
echo "attendant_type = '".$attendant_type."' <br> \n";
echo "attendant_type_others = '".$attendant_type_others."' <br> \n";
*/
	$tmp_death_cause = unserialize($deathCertInfo['death_cause']);
/*
echo "tmp_death_cause  = '".$tmp_death_cause."' <br> \n";
echo "tmp_death_cause : <br> \n"; print_r($tmp_death_cause); echo"<br> \n";
*/
	$deathYear = intval(substr($death_date, 0, 4)); 
	$deathMonth = intval(substr($death_date, 5, 7)); 
	$deathDay = intval(substr($death_date, 8, 10)); 
}

	$border="1";
	$border2="0";
	$space=2;
	$fontSizeLabel=9;
	$fontSizeInput=14;
	$fontSizeHeading=15;

	//Cell(float w [, float h [, string txt [, mixed border [, int ln [, string align [, int fill [, mixed link]]]]]]])
#	$pdf = new FPDF();
#	$pdf->AddPage("P");

	$pdf = new FPDF("P","mm","Legal");
	$pdf->AddPage("P");

	$pdf->SetLeftMargin(0);

	$pdf->Ln(14);
	$pdf->SetFont("Arial","",$fontSizeLabel-2);
    $pdf->Cell(158,4,'',"",'L');	
	
	$pdf->SetFont("Arial","B",$fontSizeLabel-2);
	$pdf->Cell(40,34,'',"",'L');	
	
	$pdf->SetFont("Arial","B",$fontSizeLabel);
	
	

	
/* $pdf->SetXY(100,140);
 $pdf->MultiCell(50,20, "This is a test This is a test This is a test This is a test This is a test This is a test This is a testThis is a test This is a testThis is a testThis is a test This is a testThis is a test This is a testThis is a testThis is a test This is a testThis is a test This is a testThis is a testThis is a test This is a testThis is a test This is a testThis is a testThis is a test This is a testThis is a test This is a testThis is a testThis is a test This is a testThis is a test This is a testThis is a testThis is a test This is a testThis is a test This is a testThis is a testThis is a test This is a testThis is a test This is a testThis is a testThis is a test This is a testThis is a test This is a testThis is a testThis is a test This is a testThis is a test This is a testThis is a test", 1, 'L');
*/	
	
	$pdf->Text(25,48, "Davao del Sur");
	$pdf->Text(35, 52, "Davao City");
	$pdf->Text(110, 52, ""); # Registry No.


//1. NAME
	$pdf->Text(35, 64, strtoupper($name_first));
	$pdf->Text(75, 64, strtoupper($name_middle));
	$pdf->Text(120, 64, strtoupper($name_last));
	
//2. SEX
#	if ($sex=='m')
		$pdf->Text(8, 73, "x");   # 8, 79
#	if ($sex=='f')
		$pdf->Text(8, 78, "x");   # 8, 84
	
	
//3. RELIGION
	$pdf->Text(35, 75, $religion_name);   # 38, 82
	
//4. AGE
	$date_birth_tmp = @formatDate2Local($date_birth,$date_format);
#	$death_date = "1977-06-21";
#	$age_at_death = "22:23:24";
	if (($death_date!='0000-00-00')  && ($death_date!=""))
		$death_date_tmp = @formatDate2Local($death_date,$date_format);
	else
		$death_date_tmp='';
#echo " date_birth_tmp ='".$date_birth_tmp."' <br>\n death_date_tmp = '".$death_date_tmp."' <br>\n";
	$ageYear = $person_obj->getAge($date_birth_tmp,'',$death_date_tmp);
	if (is_numeric($ageYear) && ($ageYear>=0)){
#		echo "true :  ageYear ='".$ageYear."' <br>\n";
		if ($ageYear<1){
			$ageMonth = intval($ageYear*12);
			$ageDay = (($ageYear*12)-$ageMonth) * 30;
#echo " ageMonth ='".$ageMonth."' <br>\n ageDay = '".$ageDay."' <br>\n round(ageDay) = '".round($ageDay)."' <br>\n";

			if(($ageMonth == 0) && (round($ageDay)<1)){
				# under 1 day
				if ($age_at_death)
					list($ageHours,$ageMinutes,$ageSec) = explode(":",$age_at_death);
				$ageMonth = ''; # set age in months as empty
				$ageDay = ''; # set age in days as empty
#echo "under 1 day :  ageHours ='".$ageHours."' <br>\n ageMinutes = '".$ageMinutes."' <br>\n ageSec = '".$ageSec."' <br>\n";
			}else{
				# under 1 year but above 1 day
				$ageMonth = intval($ageYear*12);
				$ageDay = round((($ageYear*12)-$ageMonth) * 30);	
#echo "under 1 year but above 1 day : ageMonth ='".$ageMonth."' <br>\n ageDay = '".$ageDay."' <br>\n";
			}
			$ageYear = ''; # set age in years as empty
		}else{
			# above 1 year
			$ageYear = number_format($ageYear, 2);
#echo "above 1 year :  ageYear ='".$ageYear."' <br>\n";
		}
	}else{
#		echo "false :  ageYear ='".$ageYear."' <br>\n";
	}
		#$pdf->Text(57, 83, "00");
		$pdf->Text(88, 78, "00".$ageYear);
		$pdf->Text(110, 78, "11".$ageMonth);
		$pdf->Text(123, 78, "22".$ageDay);
		if (trim($ageHours."".$ageMinutes."".$ageSec)!="")
			$ageHoursMinutesSec = $ageHours." hrs ".$ageMinutes." min ".$ageSec." sec";
		$pdf->Text(130, 78, $ageHoursMinutesSec); 

//5. PLACE OF DEATH
	$pdf->Text(28, 91, "Gonzales Maranan Medical Center Incorporated");  # 28, 92
	$pdf->Text(85, 91, "Digos City");   # 90, 92
	$pdf->Text(120, 91, "Davao del Sur");   # 130, 92

//5. DATE OF DEATH
	$arrayMonth = array ("","January","February","March","April","May","June","July","August","September","October","November","December");
	$deathMonthName = $arrayMonth[$deathMonth];
/*
	$pdf->Text(38, 100, $deathDay);   # 48, 101
	$pdf->Text(58, 100, $deathMonth);
	$pdf->Text(75, 100, $deathYear);   # 78, 101
*/
	$pdf->Text(38, 100, $deathDay."  ".$deathMonthName."  ".$deathYear);
	
//7. CITIZENSHIP
	$pdf->Text(115, 100, $country_citizenship);   # 115, 100

//8. RESIDENCE
	$pdf->Text(15, 109, $street_name." ".$brgy_row['brgy_name']);   # 15, 110
	$pdf->Text(88, 109, $brgy_row['mun_name']);
	$pdf->Text(125, 109, $brgy_row['prov_name']);	
	
//2. CIVIL STATUS

#	if ($civil_status=="single")
		$pdf->Text(9, 116, "x");   # single   12, 116
#	else if ($civil_status=="married")
		$pdf->Text(9, 120, "x");   # married   12, 120
#	else if ($civil_status=="widowed")
		$pdf->Text(38, 116, "x");   # widowed   40, 116
#	else if ($civil_status=="")
		$pdf->Text(68, 116, "x");   # unknown   70, 116
#	else if ($civil_status!=""){
		$pdf->Text(38, 120, "x");   # others   40, 120
		$pdf->Text(55, 120, $civil_status);   # 62, 120
#	}

//7. OCCUPATION
	$pdf->Text(115, 120, $occupation_name);


//17. CAUSES OF DEATH
	$pdf->Text(40, 139, $tmp_death_cause['cause6']);   # immediate cause   40, 137
	$pdf->Text(12, 143, "12345678901234567890123456789012345678901234567890");   # 15, 141
	$pdf->Text(102, 143, $tmp_death_cause['interval1']);   # 102, 141

	$pdf->Text(40, 147, $tmp_death_cause['cause7']);   # antecedent cause   40, 145
	$pdf->Text(12, 151, "cause7 2nd line");   # 15, 149
	$pdf->Text(102, 151, $tmp_death_cause['interval2']);   # 102, 149

	$pdf->Text(40, 155, $tmp_death_cause['cause8']);   # underlying cause   40, 153
	$pdf->Text(12, 159, "cause8 2nd line");   # 15, 157
	$pdf->Text(102, 159, $tmp_death_cause['interval3']);   # 102, 157

	$pdf->Text(48, 163, $tmp_death_cause['cause9']);   # other cause  40, 161
	$pdf->Text(48, 167, "123456789012345678901234567890123456789012345678901234567890123");

//18. DEATH BY NON NATURAL CAUSES
	//A. Manner of death
#	if($death_manner_tmp=='1')
		$pdf->Text(10, 181, "x");   # Homecide   8, 180.5
#	else if($death_manner_tmp=='2')
		$pdf->Text(38, 181,"x");   # Suicide   30, 180.5
#	else if($death_manner_tmp=='3')
		$pdf->Text(68, 181, "x");   # Accident   66, 180.5
#	else if($death_manner_tmp=='4'){
		$pdf->Text(98, 181, "x");   # Others   100, 180.5
		$pdf->Text(128, 181, $death_manner_info);   # specific   132, 180.5
#	}
	//B. Place of occurence
	$pdf->Text(88, 185, $place_occurence);	# 84, 197.7

//19.ATTENDANT
#	if($attendant_type == '1')
		$pdf->Text(9, 196, "x");   # 8, 195
#	if($attendant_type == '2')
		$pdf->Text(9, 200, "x");	# 8, 199
#	if($attendant_type == '3')
		$pdf->Text(9, 204, "x");   # 8, 203
#	if($attendant_type == '4')
		$pdf->Text(58, 196, "x");   # 58, 195
#	if($attendant_type == '5'){
		$pdf->Text(58, 200, "x");  # 58, 199
		$pdf->Text(58, 204, $attendant_type_others);   # 58, 203
#	}

	$attendedFromDateYear = substr($attended_from_date, 0, 4); 
	$attendedFromDateDay = intval(substr($attended_from_date, 8, 10)); 
	$attendedFromDateMonth = intval(substr($attended_from_date, 5, 7)); 
	$attendedFromDateMonthName = $arrayMonth[$attendedFromDateMonth];

	$attendedToDateYear = substr($attended_to_date, 0, 4); 
	$attendedToDateDay = intval(substr($attended_to_date, 8, 10)); 
	$attendedToDateMonth = intval(substr($attended_to_date, 5, 7)); 
	$attendedToDateMonthName = $arrayMonth[$attendedToDateMonth];

	//If attended, state duration:
	$pdf->Text(103, 196, $attendedFromDateMonthName." ".$attendedFromDateDay);   # 100, 195
	$pdf->Text(132, 196, $attendedFromDateYear);   # 132, 195
	$pdf->Text(103, 200, $attendedToDateMonthName." ".$attendedToDateDay);	  # 100, 199
	$pdf->Text(132, 200, $attendedToDateYear);   # 132, 199

//20. CERTIFICATION OF DEATH
#	if ($death_cert_attended=='0')
		$pdf->Text(8,218, "x");   # 8, 217
#	if ($death_cert_attended=='1'){
		$pdf->Text(8,222, "x");   # 8, 223
#		if (($death_time !='00:00:00') && ($death_time!=""))
			$pdf->Text(82,223, "".convert24HourTo12HourLocal($death_time));   # 85, 222
#	}

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
		$tempYear = intval(substr($attendant_date_sign, 0, 4)); 
		$tempMonth = intval(substr($attendant_date_sign, 5, 7)); 
		$tempDay = intval(substr($attendant_date_sign, 8, 10)); 
		$attendant_date_sign =$tempDay."  ".$arrayMonth[$tempMonth]."  ".$tempYear;
	}else{
		$attendant_date_sign='';
	}

	$pdf->Text(25,240, $attendant_name);   # 15, 240
	$pdf->Text(28,244, $attendant_title);   # 18, 245
	$pdf->Text(16,249, $address1);   # 10, 250
	$pdf->Text(16,253, $address2);   # 10, 255
	$pdf->Text(10,257, $attendant_date_sign);   # 10, 260

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
#		$informant_date_sign = @formatDate2Local($informant_date_sign,$date_format);
		$tempYear = intval(substr($informant_date_sign, 0, 4)); 
		$tempMonth = intval(substr($informant_date_sign, 5, 7)); 
		$tempDay = intval(substr($informant_date_sign, 8, 10)); 
		$informant_date_sign =$tempDay."  ".$arrayMonth[$tempMonth]."  ".$tempYear;
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

		$pdf->Text(20, 298, $informant_name);   # 22, 298
		$pdf->Text(40, 302, $informant_relation);   # 35, 302
#		$pdf->Text(95, 294, $informant_address);
		$pdf->Text(90, 294, $address1);   # 92, 294
		$pdf->Text(90, 298, $address2);   # 92, 298
		$pdf->Text(90, 303, $informant_date_sign);   # 92, 302

//26. PREPARED BY
	if (($encoder_date_sign!='0000-00-00') && ($encoder_date_sign!="")){
#		$encoder_date_sign = @formatDate2Local($encoder_date_sign,$date_format);
		$tempYear = intval(substr($encoder_date_sign, 0, 4)); 
		$tempMonth = intval(substr($encoder_date_sign, 5, 7)); 
		$tempDay = intval(substr($encoder_date_sign, 8, 10)); 
		$encoder_date_sign =$tempDay."  ".$arrayMonth[$tempMonth]."  ".$tempYear;
	}else{
		$encoder_date_sign = '';
	}

		$pdf->Text(20, 320, $encoder_name);   # 25, 318
		$pdf->Text(22, 324, $encoder_title);   # 30, 322
		$pdf->Text(15, 327.5, $encoder_date_sign);   # 20, 328

	if ( (intval($ageYear)>0) || (intval($ageMonth)>0) || (intval($ageDay)>7) )
		$pdf->Output();   # more than 7 days old at the time of death
	
	# FOR AGES 0 TO 7 DAYS OLD
	$pdf->AddPage("P");
/*
		$birthYear = substr($date_birth, 0, 4); 
		$birthMonth = substr($date_birth, 5, 7); 
		$birthDay = substr($date_birth, 8, 10); 
*/
		$birthYear = intval(substr($date_birth, 0, 4)); 
		$birthMonth = intval(substr($date_birth, 5, 7)); 
		$birthMonthName = $arrayMonth[$birthMonth];
		$birthDay = intval(substr($date_birth, 8, 10)); 

/*
echo "date_birth = '".$date_birth."'; birthYear = '".$birthYear."'; birthMonth ='".$birthMonth."'; birthDay = '".$birthDay."'; ";
echo "intval(substr($date_birth, 5, 7)) = '".intval(substr($date_birth, 5, 7))."'; ";
echo "substr($date_birth, 5, 7) = '".substr($date_birth, 5, 7)."'; ";
echo "substr('1977-10-21', 5, 6) = '".substr('1977-10-21', 5, 6)."'; ";
*/
//11. DATE OF BIRTH
/*
		$pdf->Text(22, 32, $birthDay);   # 25, 32
		$pdf->Text(40, 32, $birthMonth);   # 45, 32
		$pdf->Text(58, 32, $birthYear);   # 60, 32
*/
		$pdf->Text(22, 32, $birthDay."  ".$birthMonthName."  ".$birthYear);


//12. AGE OF THE MOTHER
		$pdf->Text(100, 30, $m_age);   # 92, 30

//13. METHOD OF DELIVERY
#	if ($delivery_method_tmp=="1")
		$pdf->Text(134, 28, "x");   # Normal; spontaneous vertex   135, 28
#	if ($delivery_method_tmp=="2"){
		$pdf->Text(134, 31, "x");   # Others   135, 32
		$pdf->Text(165, 31, $delivery_method_info);   # specific   165, 32
#	}

//14. LENGTH OF PREGANANCY
		$pdf->Text(85, 37, $pregnancy_length);   # 80, 35

//15. TYPE OF BIRTH
#	if ($birth_type=='1')
		$pdf->Text(18, 45, "x");   # single   10, 40
#	if ($birth_type=='2')
		$pdf->Text(40, 45, "x");   # twin   25, 40
#	if ($birth_type=='3')
		$pdf->Text(60, 45, "x");   # triplet, etc.   62, 46

//16. IF MULTIPLE BIRTH, CHILD WAS
#	if ($birth_rank=='1')
		$pdf->Text(95, 45, "x");   # first   90, 40
#	if ($birth_rank=='2')
		$pdf->Text(118, 45, "x");   # second  120, 46
#	if (intval($birth_rank) > 2){
		$pdf->Text(147, 45, "x");   # others   148, 46
		$pdf->Text(178, 45, $birth_rank);   # specific   175, 46
#	}

//17. CAUSES OF DEATH
		$pdf->Text(57, 60, $tmp_death_cause['cause1']);   # Main disease/condition of infant   58, 60
		$pdf->Text(60, 63, $tmp_death_cause['cause2']);   # Other diseases/conditions of infant   60, 63
		$pdf->Text(75, 66, $tmp_death_cause['cause3']);   # Main maternal disease/condition of affecting infant   72, 66
		$pdf->Text(75, 69, $tmp_death_cause['cause4']);   # Other maternal disease/condition of affecting infant   72, 69
		$pdf->Text(52, 72, $tmp_death_cause['cause5']);   # Other relevant circumstances   50, 72

	$pdf->Output();   # less than or equal 7 days old at the time of death
	
?>