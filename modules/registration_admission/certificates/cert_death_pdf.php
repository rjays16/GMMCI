<?php
		// modified by pet from previous cert_death_pdf by burn & vanessa; june 25, 2008
function seg_ucwords($str) {
	$words = preg_split("/([\s,.-]+)/", mb_strtolower($str), -1, PREG_SPLIT_DELIM_CAPTURE);
	$words = @array_map('ucwords',$words);
	return implode($words);
}

include("roots.php");
include_once($root_path."/classes/fpdf/fpdf.php");
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');

require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
$objInfo = new Hospital_Admin();

if ($row = $objInfo->getAllHospitalInfo()) {
		$row['hosp_agency'] = strtoupper($row['hosp_agency']);
		$row['hosp_name']   = strtoupper($row['hosp_name']);
}
else {
		$row['hosp_country'] = "Republic of the Philippines";
		$row['hosp_agency']  = "DEPARTMENT OF HEALTH";
		$row['hosp_name']    = "BUKIDNON PROVINCIAL HOSPITAL - MALAYBALAY";
		$row['hosp_addr1']   = "Malaybalay, Bukidnon";
		$row['mun_name']     = "Malaybalay";
		$row['prov_name']     = "Bukidnon";
		$row['region_name']     = "Region X";
}


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
	echo '<em class="warn"> Sorry, the page cannot be displayed! <br> Invalid HRN!</em>';
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
	$death_manner_accident = substr(trim($deathCertInfo['death_manner']),4);
	$death_manner_info = substr(trim($deathCertInfo['death_manner']),4);
	$attendant_type_tmp = substr(trim($deathCertInfo['attendant_type']),0,1);
	$attendant_type_others = substr(trim($deathCertInfo['attendant_type']),4);
	$corpse_disposal_tmp= substr(trim($deathCertInfo['corpse_disposal']),0,1);
	$corpse_disposal_others = substr(trim($deathCertInfo['corpse_disposal']),4);
	$is_autopsy = substr(trim($deathCertInfo['is_autopsy']),0,1);
	$tmp_death_cause = unserialize($deathCertInfo['death_cause']);
}

	$border="1";
	$border2="0";
	$space=2;
	$fontSizeInput=11;
	$fontSizeHeading=14;

	$pdf = new FPDF("P","mm","Legal");
	$pdf->AddPage("P");
	$pdf->SetDisplayMode(real,'default');

	$x = $pdf->GetX();
	$y = $pdf->GetY();
	#$pdf->Line($x, $y, $x+200, $y);

	$y=($x*2.8)-9;

	#$y=$x*3;

	#$y = $y+12;

	$pdf->SetXY($x,$y);
	$pdf->SetFont("Arial","",$fontSizeInput);

	$pdf->SetY(-0.5);
	$z = $pdf->GetY();

	#commented by VAN 08-08-08
	#$pdf->Text($x+29, $y-1, "Davao del Sur");
	$pdf->Text($x+29, $y+3, $row['mun_name']);
	#$registry_nr = "123456";
	$pdf->Text($x+110, $y+4, $registry_nr);

//1. NAME
	$pdf->SetXY($x+5, $y+11.5);
	#$pdf->MultiCell(50, 4,mb_strtoupper($name_first), '', 'L','0');
	$pdf->Cell(60, 4,mb_strtoupper($name_first),'', '0','C');

	$pdf->SetXY($x+65, $y+11.5);
	#$pdf->MultiCell(40, 4,mb_strtoupper($name_middle), '', 'L','0');
	$pdf->Cell(40, 4,mb_strtoupper($name_middle),'', '0','C');

	$pdf->SetXY($x+105, $y+11.5);
	#$pdf->MultiCell(40, 4,mb_strtoupper($name_last), '', 'L','0');
	$pdf->Cell(41, 4,mb_strtoupper($name_last),'', '0','C');

/*
	$pdf->SetXY(30, 30);
	$pdf->MultiCell(50, 4,strtoupper($name_first)." CARLOS", '', 'L','0');

	$pdf->SetXY(80, 30);
	$pdf->MultiCell(40, 4,strtoupper($name_middle), '', 'L','0');

	$pdf->SetXY(120, 30);
	$pdf->MultiCell(40, 4,strtoupper($name_last), '', 'L','0');
*/

//2. SEX
/*	if ($sex=='m')
		$pdf->Text(8, 45, "x");
	if ($sex=='f')
		$pdf->Text(8, 50, "x");
*/

	if ($sex=='m')
		$pdf->Text($x-5, $y+26, "X");
	if ($sex=='f')
		$pdf->Text($x-5, $y+30, "X");

//3. RELIGION
/*
	$pdf->SetXY(38, 42);
	$pdf->MultiCell(25, 4,strtoupper($religion_name), '', 'C','0');
*/
	$pdf->SetFont("Arial","",$fontSizeInput-2);
	$pdf->SetXY($x+22, $y+25);
	$pdf->MultiCell(28, 4, strtoupper($religion_name), '', 'C','0');
	$pdf->SetFont("Arial","",$fontSizeInput);

//4. AGE
#edited by VAN 11-17-09
 /*
	$date_birth_tmp = @formatDate2Local($date_birth,$date_format);
	if (($death_date!='0000-00-00')  && ($death_date!=""))
		$death_date_tmp = @formatDate2Local($death_date,$date_format);
	else
		$death_date_tmp='';
		#if ($ageYear>=1) echo $age_at_death; else echo number_format(floor($ageYear));

	$ageYear = $age_at_death;

		if ($ageYear==0)
				$ageYear = $person_obj->getAge($date_birth_tmp,'',$death_date_tmp);

		if (is_numeric($ageYear) || ($ageYear>=0)){
		if (($ageYear<1) || !(is_numeric($ageYear))){

			#$ageMonth = intval($ageYear*12);
			#$ageDay = (($ageYear*12)-$ageMonth) * 30;

			if((($ageMonth == 0) && (round($ageDay)<1))||!(is_numeric($ageYear))){
				# under 1 day

				if (($age_at_death) && (is_numeric($age_at_death)))
					list($ageHours,$ageMinutes,$ageSec) = explode(":",$age_at_death);
					$ageYear = '';
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
	*/
	#-----------till here 11/17/09
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
			#    echo "false :  ageYear ='".$ageYear."' <br>\n";
			}

	/*
		//$pdf->Text(88, 50, $ageYear);
		$ageHours = $death_hour;
		$ageMinutes = $death_min;
		$ageSec = $death_sec;
		if (trim($ageHours."".$ageMinutes."".$ageSec)!=""){
			#$ageHoursMinutesSec = $ageHours." hrs ".$ageMinutes." min ".$ageSec." sec";
			$ageHoursMinutesSec = $ageHours." / ".$ageMinutes." / ".$ageSec;

			if ($ageHours<10)
				$ageHours = '0'.$ageHours;

			if ($ageMinutes<10)
				$ageMinutes = '0'.$ageMinutes;

			if (($ageSec<10)||($ageSec==0))
				$ageSec = '0'.$ageSec;

		#echo "e = ".$ageHoursMinutesSec;
		}
		*/
		#added by VAN 08-13-08
		if ($ageYear==0){
			$ageYear = "";

			if ($ageMonth==0){
				$ageMonth = "";
				if ($ageDay==0){
					$ageDay = "";

					if ($ageHours==0)
						$ageHoursMinutesSec = "00 / ".$ageMinutes." / ".$ageSec;
					elseif(($ageHours==0)&&($ageMinutes==0))
						$ageHoursMinutesSec = "00 / 00 / ".$ageSec;
					else
						$ageHoursMinutesSec = "";
				}else{
					$ageHoursMinutesSec = "";
				}
			}else{
				$ageDay = "";
				$ageHoursMinutesSec = "";
			}
		}else{
			$ageYear =  number_format(floor($ageYear));
			$ageMonth = "";
			$ageDay = "";
			$ageHoursMinutesSec = "";
		}

		//$pdf->Text(88, 50, $ageYear);
		$ageHours = $death_hour;
		$ageMinutes = $death_min;
		$ageSec = $death_sec;
				$ageHoursMinutesSec = "";

		if ((trim($ageHours."".$ageMinutes."".$ageSec)!="")&&(trim($ageHours."".$ageMinutes."".$ageSec)!="000")){
				#if (($ageYear==0)&&(trim($ageHours."".$ageMinutes."".$ageSec)=="000")){

			#$ageHoursMinutesSec = $ageHours." hrs ".$ageMinutes." min ".$ageSec." sec";
			$ageHoursMinutesSec = $ageHours." / ".$ageMinutes." / ".$ageSec;

			if ($ageHours<10)
				$ageHours = '0'.$ageHours;

			if ($ageMinutes<10)
				$ageMinutes = '0'.$ageMinutes;

			if (($ageSec<10)||($ageSec==0))
				$ageSec = '0'.$ageSec;

		#echo "e = ".$ageHoursMinutesSec;
		}

		#-------------------


		$pdf->Text($x+77, $y+30,$ageYear);
		#unnecessary, per user
		#edited by VAN 08-08-08
		#$pdf->Text(112, 56, $ageMonth);
		$pdf->Text($x+99, $y+30, $ageMonth);
		$pdf->Text($x+113, $y+30, $ageDay);

		#$pdf->Text(145, 56, $ageHoursMinutesSec);
		#$ageHoursMinutesSec = "12/59/30";
		$pdf->Text($x+126, $y+30, $ageHoursMinutesSec);
		#-------------------

//5. PLACE OF DEATH
	$pdf->SetFont("Arial","",$fontSizeInput+1);
	#$pdf->Text($x+55, $y+41, $row['hosp_name']." - ".$row['mun_name']);
	if ($death_place_basic)
		$death_place = mb_strtoupper($death_place_basic).", ";
	else
		$death_place = $row['hosp_name'];

	$pdf->Text($x+15, $y+43,$death_place." ".mb_strtoupper($death_place_mun));
	$pdf->SetFont("Arial","",$fontSizeInput);

//6. DATE OF DEATH
	$deathYear = date("Y",strtotime($death_date));
	$deathMonthName = date("F",strtotime($death_date));
	$deathDay = date("d",strtotime($death_date));
	#$pdf->Text(43, 78, $deathDay."      ".strtoupper($deathMonthName)."         ".$deathYear);
	$pdf->Text($x+31, $y+50, $deathDay."   ".strtoupper($deathMonthName)."      ".$deathYear);

//7. CITIZENSHIP
	$hdcitz_obj = $obj_deathCert->getCitizenship2($dcitizenship);
	if (empty($dcitizenship))
		$hdcitz_obj['citizenship'] = "FILIPINO";
	#$pdf->Text(115, 78, $hdcitz_obj['citizenship']);
	$pdf->Text($x+105, $y+50, $hdcitz_obj['citizenship']);

//8. RESIDENCE
	$m_address = trim($street_name);
	#commented by VAN 08-01-08

	if ((stristr($brgy_row['brgy_name'], 'barangay') === FALSE) && (stristr($brgy_row['brgy_name'], 'brgy') === FALSE)){
		if (!empty($m_address) && !empty($brgy_row['brgy_name']))
			$m_address = $m_address.", ".ucwords(trim($brgy_row['brgy_name']));
		else
			$m_address = $m_address." ".ucwords(trim($brgy_row['brgy_name']));
	}

	#commented by VAN 08-08-08
	/*
	if (!empty($m_address) && !empty($brgy_row['mun_name']))
		$m_address = $m_address.", ".ucwords($brgy_row['mun_name']);
	else
		$m_address = $m_address." ".ucwords($brgy_row['mun_name']);

	if (!empty($m_address) && !empty($brgy_row['prov_name']))
		$m_address = $m_address.", ".ucwords($brgy_row['prov_name']);
	else
		$m_address = $m_address." ".ucwords($brgy_row['prov_name']);
	*/

	if (!empty($m_address) && !empty($street_name)){
		$m_address = $m_address.", ".ucwords(trim($brgy_row['mun_name']));
	}else{
		$m_address = $m_address." ".ucwords(trim($brgy_row['mun_name']));
	}

	#added by VAN 08-05-08
	#if ($mun['mun_name']!='Davao City'){
	if(stristr($brgy_row['mun_name'], 'city') === FALSE){
		if (!empty($m_address)){
			$m_address = $m_address.", ".ucwords(trim($brgy_row['prov_name']));
		}else{
			$m_address = $m_address." ".ucwords(trim($brgy_row['prov_name']));
		}
	}
	#---------------------

	$pdf->SetXY($x+15, $y+56.5);
	//$pdf->MultiCell(140, 4,strtoupper($m_address), '', 'L','0');
	#$pdf->MultiCell(140, 4,ucwords($m_address), '', 'L','0');
	$pdf->MultiCell(140, 4,$m_address, '', 'L','0');

//9. CIVIL STATUS

	if ($civil_status=="single")
		$pdf->Text($x-2, $y+68, "X");   # single   12, 116
	else if ($civil_status=="married")
		$pdf->Text($x-2, $y+72.5, "X");   # married   12, 120
	else if ($civil_status=="widowed")
		$pdf->Text($x+25, $y+68, "X");   # widowed   40, 116
	else if (($civil_status=="child")||($civil_status=="divorced")||($civil_status=="separated")){
		$pdf->Text($x+25, $y+72.5, "X");   # others   40, 120
		#$civil_status = "single";
		$pdf->Text($x+47, $y+72.5, $civil_status);   # 62, 120
		}
	else if ($civil_status=="unknown")
		$pdf->Text($x+52, $y+68, "X");   # unknown   70, 116

//7. OCCUPATION
	$pdf->Text($x+90, $y+71, $occupation_name);

//17. CAUSES OF DEATH
/*	#$pdf->Text(41, 115, $tmp_death_cause['cause6']);   # immediate cause
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
*/
//18. DEATH BY NON NATURAL CAUSES

	# A. Manner of death

	if($death_manner_tmp=='1')
		$pdf->Text($x-2, $y+135, "X");   # Homicide   8, 180.5
	else if($death_manner_tmp=='2')
		$pdf->Text($x+26, $y+135,"X");   # Suicide   30, 180.5
	else if($death_manner_tmp=='3')
		$pdf->Text($x+52, $y+135, "X");   # Accident   66, 180.5
	else if($death_manner_tmp=='4'){
		$pdf->Text($x+81, $y+135, "X");   # Others   100, 180.5
		#$death_manner_info = "sample";
		$pdf->Text($x+115, $y+135, $death_manner_info);   # specific   132, 180.5
	}
	# B. Place of occurence
	$pdf->Text($x+82, $y+140, ucwords(strtolower($place_occurrence)));	# 84, 197.7

//19.ATTENDANT

	if($attendant_type_tmp == '1')
		$pdf->Text($x-2, $y+150.5, "X");   # 8, 195
	else if($attendant_type_tmp == '2')
		$pdf->Text($x-2, $y+155, "X");	# 8, 199
	else if($attendant_type_tmp == '3')
		$pdf->Text($x-2, $y+160, "X");   # 8, 203
	else if($attendant_type_tmp == '4')
		$pdf->Text($x+45, $y+150.5, "X");   # 58, 195
	else if($attendant_type_tmp == '5'){
		$pdf->Text($x+45, $y+155, "X");  # 58, 199
		$pdf->Text($x+43, $y+159.7, $attendant_type_others);   # 58, 203
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
	$pdf->Text($x+91, $y+151, $attendedFromDateMonthName."  ".$attendedFromDateDay);
	$pdf->Text($x+124, $y+151, $attendedFromDateYear);
	$pdf->Text($x+91, $y+155.5, $attendedToDateMonthName."  ".$attendedToDateDay);
	$pdf->Text($x+124, $y+155.5, $attendedToDateYear);


//20. CERTIFICATION OF DEATH

	if ($death_cert_attended=='0')
		$pdf->Text($x-5, $y+177, "X");
	if ($death_cert_attended=='1'){
		$pdf->Text($x-5, $y+182, "X");

	#if (($death_time !='00:00:00') && ($death_time!=""))
		if ($death_time!="")
		$death_time = convert24HourTo12HourLocal($death_time);
		//$death_time = date("H:i:s",strtotime($death_time));
	else
		$death_time = '';
	$pdf->Text($x+72, $y+181, $death_time);
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
		if (!empty($attendant_name))
			#$doctor_name = "Dr. ".ucwords(mb_strtolower($doctor_name));
			$doctor_name = mb_strtoupper($doctor_name).", MD";

	if (($attendant_date_sign!='0000-00-00') && ($attendant_date_sign!="")){
		$tempYear = date("Y",strtotime($attendant_date_sign));
		$tempMonth = date("F",strtotime($attendant_date_sign));
		$tempDay = date("d",strtotime($attendant_date_sign));

		$attendant_date_sign =$tempDay." ".$tempMonth." ".$tempYear;
	}else{
		$attendant_date_sign = '';
	}
	#190 for sign
	$pdf->SetFont("Arial","",$fontSizeInput-1);
	$pdf->Text($x+15, $y+195.7, $doctor_name);
	$pdf->Text($x+19, $y+200.5, $attendant_title);
	$pdf->SetXY($x+3, $y+203.5);
	$pdf->SetFont("Arial","",$fontSizeInput);
	#$pdf->MultiCell(65, 4,ucwords($attendant_address), '', 'L','0');
	#$attendant_address = str_replace(",","",trim($attendant_address));
	#$pdf->SetFont("Arial","",$fontSizeInput-1);
	$attendant_address = substr_replace(trim($attendant_address)," ",20,1);
	$pdf->MultiCell(55, 4,seg_ucwords($attendant_address), '0', 'L','0');
	#$pdf->SetFont("Arial","",$fontSizeInput);
	$pdf->Text($x+3, $y+216.7, $attendant_date_sign);

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
		$tempYear = date("Y",strtotime($informant_date_sign));
		$tempMonth = date("F",strtotime($informant_date_sign));
		$tempDay = date("d",strtotime($informant_date_sign));

		$informant_date_sign =$tempDay."  ".$tempMonth."  ".$tempYear;
	}else{
		$informant_date_sign = '';
	}
		$pdf->Text($x+14.5, $y+256.5, mb_strtoupper($informant_name));
		$pdf->Text($x+37, $y+261.5, ucwords(strtolower($informant_relation)));
		$pdf->SetXY($x+81.5, $y+249);
		//$pdf->MultiCell(65, 4,mb_strtoupper($informant_address), '', 'L','0');
		#$pdf->MultiCell(65, 4,ucwords($informant_address), '', 'L','0');
		$pdf->MultiCell(70, 4,$informant_address, '', 'L','0');
		$pdf->Text($x+82, $y+261, $informant_date_sign);

//26. PREPARED BY
	if (($encoder_date_sign!='0000-00-00') && ($encoder_date_sign!="")){
		$tempYear = date("Y",strtotime($encoder_date_sign));
		$tempMonth = date("F",strtotime($encoder_date_sign));
		$tempDay = date("d",strtotime($encoder_date_sign));

		$encoder_date_sign =$tempDay."  ".$tempMonth."  ".$tempYear;
	}else{
		$encoder_date_sign = '';
	}

		$pdf->Text($x+13, $y+280, mb_strtoupper($encoder_name));
		$pdf->Text($x+15, $y+284, $encoder_title);
		$pdf->Text($x+10, $y+288, $encoder_date_sign);

	#$pdf->Output();  // for less than 7 days old at the time of death

// AGES 0 to 7 Days
	//if ( (intval($ageYear)<0) && (intval($ageMonth)<0) && (intval($ageDay)<7) )	{
	//if ( (intval($ageYear)==0) && (intval($ageMonth)==0) && (intval($ageDay)<=7))
		#$pdf->AddPage("P");
	if ( (intval($ageYear)==0) && (intval($ageMonth)==0) && (intval($ageDay)<=7)){
		$pdf->AddPage("P");
		#$pdf->SetTopMargin(0);
	}else{
		$pdf->Output();
	}
	#$pdf->SetTopMargin(5);
	$x = $pdf->GetX();
	$y = $pdf->GetY();

	#$y = $y;
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
		#$pdf->Text(22, 5, $birthDay."    ".$birthMonthName."    ".$birthYear);
		#edited by VAN 07-15-08
		$pdf->Text($x+10, $y+12, $birthDay."    ".$birthMonthName."    ".$birthYear);


//12. AGE OF THE MOTHER
		#$pdf->Text(100, 5, $m_age);
		#edited by VAN 07-15-08
		$pdf->Text($x+85, $y+10, $m_age);

//13. METHOD OF DELIVERY
		#$pdf->SetY(-0.5);

	if ($delivery_method_tmp=="1")
		#$pdf->Text(139, 1, "x");   # Normal; spontaneous vertex   135, 28
		$pdf->Text($x+129, $y+5.5, "X");   # Normal; spontaneous vertex   135, 28
	elseif ($delivery_method_tmp=="2"){
		#$pdf->Text(139, 3, "x");   # Others   135, 32
		$pdf->Text($x+129, $y+10.5, "X");   # Others   135, 32
		#$pdf->Text(175, 2.5, "normal".$delivery_method_info);   # specific   165, 32
		$pdf->Text($x+165, $y+10.5, $delivery_method_info);   # specific   165, 32
	}

//14. LENGTH OF PREGNANCY
		#$pdf->Text(90, 9.5, $pregnancy_length);
		$pdf->Text($x+78, $y+17, $pregnancy_length);

//15. TYPE OF BIRTH

	if ($birth_type=='1')
		#$pdf->Text(20, 19, "x");   # single   10, 40
		$pdf->Text($x+8, $y+27, "X");   # single   10, 40
	elseif ($birth_type=='2')
		#$pdf->Text(40, 19, "x");   # twin   25, 40
		$pdf->Text($x+31, $y+27, "X");   # twin   25, 40
	elseif ($birth_type=='3')
		#$pdf->Text(65, 19, "x");   # triplet, etc.   62, 46
		$pdf->Text($x+54, $y+26.5, "X");   # triplet, etc.   62, 46

//16. IF MULTIPLE BIRTH, CHILD WAS
	#echo "rank = ".$birth_rank;
	#if ($birth_rank=='1')
	#edited by VAN 08-09-08
	if ($birth_rank=='first')
		#$pdf->Text(102, 19, "x");   # first   90, 40
		$pdf->Text($x+93, $y+27, "X");   # first   90, 40
	#elseif ($birth_rank=='2')
	elseif ($birth_rank=='second')
		#$pdf->Text(124, 19, "x");   # second  120, 46
		$pdf->Text($x+115, $y+27, "X");   # second  120, 46
	#elseif (intval($birth_rank) > 2){
	else{
		#$pdf->Text(153, 19, "x");   # others   148, 46
		$pdf->Text($x+143, $y+27, "X");   # others   148, 46
		#$pdf->Text(186, 17.5, "others".$birth_rank);   # specific   175, 46
		$pdf->Text($x+176, $y+26.5, $birth_rank);   # specific   175, 46
	}

//17. CAUSES OF DEATH
		/*
		$pdf->Text(62, 33, "sample1".$tmp_death_cause['cause1']);   # Main disease/condition of infant
		$pdf->Text(65, 36, "sample2".$tmp_death_cause['cause2']);   # Other diseases/conditions of infant
		$pdf->Text(80, 38, "sample3".$tmp_death_cause['cause3']);   # Main maternal disease/condition of affecting infant
		$pdf->Text(80, 40, "sample4".$tmp_death_cause['cause4']);   # Other maternal disease/condition of affecting infant
		$pdf->Text(56, 45, "sample5".$tmp_death_cause['cause5']);   # Other relevant circumstances
		*/
	$pdf->Output();   # less than or equal 7 days old at the time of death

?>